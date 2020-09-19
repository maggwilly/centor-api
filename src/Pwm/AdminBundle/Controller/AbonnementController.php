<?php

namespace Pwm\AdminBundle\Controller;

use Pwm\AdminBundle\Entity\Abonnement;
use Pwm\MessagerBundle\Entity\Notification;
use Pwm\AdminBundle\Entity\Info;
use Pwm\AdminBundle\Entity\Commande;
use Pwm\AdminBundle\Entity\Price;
use Pwm\MessagerBundle\Controller\NotificationController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; 
use AppBundle\Entity\Session;
use AppBundle\Event\CommandeEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * Abonnement controller.
 *
 */
class AbonnementController extends Controller
{

  /**
   * @Security("is_granted('ROLE_DELEGUE')")
  */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $abonnements = $em->getRepository('AdminBundle:Abonnement')->findList();
         $extrats = $em->getRepository('AdminBundle:Abonnement')->findSinceDate();
        $concours = $em->getRepository('AppBundle:Session')->findList();
         foreach ($extrats as $key => $abonnement) {
          $url="https://trainings-fa73e.firebaseio.com/session/".$abonnement->getSession()->getId()."/members/.json";
        $info=$abonnement->getInfo();
        $data = array($info->getUid() => array('isActive' => true,'uid' => $info->getUid(),'displayName' => $info->getDisplayName(),'photoURL' => $info->getPhotoURL()));
        // $this->get('fmc_manager')->sendOrGetData($url,$data,'PATCH'); 

         }
        return $this->render('AdminBundle:abonnement:index.html.twig', array(
            'abonnements' => $abonnements, 'concours' => $concours,
        ));
    }


    /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"abonnement"})
     */
    public function indexJsonAction(Info $info)
    {
        $em = $this->getDoctrine()->getManager();
        $abonnements = $em->getRepository('AdminBundle:Abonnement')->findForMe($info);
        return  $abonnements;
    }

    
    /**
     * Lists all Produit entities.
     *@Rest\View()
     */
    public function tokenAction()
    {
    $res=$this->get('payment_service')->getToken();

  return  $res;
}


    public function goToPaiementAction(Request $request,Commande $commande)
    {
      if( $commande->getStatus()==='PAID')
            return $this->redirect('http://help.centor.org/return.html');
        $res=$this->get('payment_service')->getPayementUrl($commande);
        if(array_key_exists('payment_url', $res))
            return $this->redirect($res['payment_url']);
      return  new Response('Une erreur se produit. Prevenez un administrateur svp');
}

    /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"commande"})
     */
    public function startCommandeAction(Request $request,Info $info, $product=null,$package)
    {
    $em = $this->getDoctrine()->getManager();
            $commande= new Commande($info);
         if($package=='ressource')
            $commande=$this->loadCommandeForRessource($info, $product);
         else
             $commande=$this->loadCommandeForSesson($info, $product,$package);
          $em->flush();
        return $commande;
    }

  public function loadCommandeForRessource($info, $produit){
         $em = $this->getDoctrine()->getManager();
         $ressource = $em->getRepository('AdminBundle:Ressource')->find($produit);
          $commande=$em->getRepository('AdminBundle:Commande')->findOneByUserRessource($info,$ressource);
            if(is_null($commande)){
               $commande= new Commande($info);
               $commande->setDate(new \DateTime())
               ->setRessource($ressource);
               $em->persist($commande);
           } if(!is_null($ressource))
               $commande->setAmount($ressource->getPrice());
        return $commande;
      }

  public function loadCommandeForSesson($info, $produit, $package){
          $em = $this->getDoctrine()->getManager();
          $session = $em->getRepository('AppBundle:Session')->find($produit);
          $commande=$em->getRepository('AdminBundle:Commande')->findOneByUserSession($info,$session);
            if(is_null($commande)){
               $commande= new Commande($info, $session);
               $commande->setDate(new \DateTime())
               ->setSession($session);
               $em->persist($commande);
           }
           $price= new Price();
          if(!is_null($session)){
             $price= $session->getPrice();
              $session->removeInfo($info);
              $session->addInfo($info);
             }
           else
             $price = $em->getRepository('AdminBundle:Price')->find(8);
             $commande->setAmount($this->getForSessonCommande($price,$package))
             ->setPackage($package);
        return $commande;
      }

  public function getForSessonCommande($price ,$package){
        switch ($package) {
          case 'starter':
            return  $price->getStarter();
          case 'standard':
              return  $price->getStandard();
          case 'premium':
              return $price-> getPremium();
           }
           return new Price();
        }

    /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"commande"})
     */
    public function confirmCommandeAction(Request $request)
    {    $em = $this->getDoctrine()->getManager();
         $data=json_decode($this->get("request")->getContent(),true);
         $commande=$em->getRepository('AdminBundle:Commande')->findOneByOrderId($data['orderid']);
         $form = $this->createForm('Pwm\AdminBundle\Form\CommandeType', $commande);
         $form->submit($data,false);
        if ($form->isValid()&&$commande->getStatus()=='PAID') {
            if (is_null($commande->getRessource())) {
             $abonnement=$em->getRepository('AdminBundle:Abonnement')->findMeOnThis($commande->getInfo(), $commande->getSession());
             if($abonnement==null){
                 $abonnement=new Abonnement($commande); 
                 if(!is_null($commande->getSession())){
                   $commande->getSession()->removeInfo($commande->getInfo());
                   $commande->getSession()->addInfo($commande->getInfo());
                   $commande->getSession()->setNombreInscrit($commande->getSession()->getNombreInscrit()+1);
                  }              
                 $em->persist($abonnement);
                }
              $abonnement->setPlan($commande->getPackage());
              $abonnement->setPrice($commande->getAmount());
              $commande->setAbonnement($abonnement); 
              }
              $em->flush();
              $event= new CommandeEvent($commande);
              $this->get('event_dispatcher')->dispatch('commande.confirmed', $event);
            return $commande;
        }
        return $form;
    }


    /**
     * Lists all Produit entities.
     *@Rest\View()
     */
    public function cancelCommandeAction(Request $request,Commande $commande)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commande);
            $em->flush($commande);
        return  array('success'=>true);
    }



        /**
     * Displays a form to edit an existing analyse entity.
     *
     */
    public function editAction(Request $request, Abonnement $abonnement)
    {
        $form = $this->createForm('Pwm\AdminBundle\Form\AbonnementType', $abonnement);
         $form->submit($request->request->all(),false);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $abonnement;
        }
        return $form;
    }



    /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"abonnement"})
     */
    public function showJsonAction(Info $info, Session $session=null){
        $em = $this->getDoctrine()->getManager();
         $abonnement = $em->getRepository('AdminBundle:Abonnement')->findMeOnThis($info, $session);
          if ( $abonnement!=null&&$session!=null) {
          $info= $abonnement->getInfo();
          $url="https://trainings-fa73e.firebaseio.com/session/".$session->getId()."/members/.json";
          $data = array($info->getUid() => array('uid' => $info->getUid(),'displayName' => $info->getDisplayName(),'photoURL' => $info->getPhotoURL()));
           $this->get('fmc_manager')->sendOrGetData($url,$data,'PATCH');
        }
        return $abonnement;
    }

        /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"abonnement"})
     */
    public function showOneJsonAction(Abonnement $abonnement){
          if ( $abonnement!=nulll&&$abonnement-> getSession()!=null) {
          $info= $abonnement->getInfo();
          $url="https://trainings-fa73e.firebaseio.com/session/".$abonnement-> getSession()->getId()."/members/.json";
          $data = array($info->getUid() => array('uid' => $info->getUid(),'displayName' => $info->getDisplayName(),'photoURL' => $info->getPhotoURL()));
           $this->get('fmc_manager')->sendOrGetData($url,$data,'PATCH');
        }
        return $abonnement;
    }

    /**
     * Creates a new abonnement entity.
     *
     */
    public function newAction(Request $request)
    {
        $abonnement = new Abonnement();
        $form = $this->createForm('Pwm\AdminBundle\Form\AbonnementType', $abonnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($abonnement);
            $em->flush($abonnement);
            return $this->redirectToRoute('abonnement_show', array('id' => $abonnement->getId()));
        }

        return $this->render('AdminBundle:abonnement:new.html.twig', array(
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ));
    }

  /**
   * @Security("is_granted('ROLE_DELEGUE')")
  */
    public function showAction(Abonnement $abonnement)
    {

        if ( $abonnement!=null) {
          $info= $abonnement->getInfo();
          $url="https://trainings-fa73e.firebaseio.com/session/".$abonnement-> getSession()->getId()."/members/.json";
          $data = array($info->getUid() => array('uid' => $info->getUid(),'displayName' => $info->getDisplayName(),'photoURL' => $info->getPhotoURL()));
           $this->get('fmc_manager')->sendOrGetData($url,$data,'PATCH');
        } 
        $deleteForm = $this->createDeleteForm($abonnement);
        return $this->render('AdminBundle:abonnement:show.html.twig', array(
            'abonnement' => $abonnement,
            'delete_form' => $deleteForm->createView(),
        ));
    }



  /**
   * @Security("is_granted('ROLE_DELEGUE')")
  */
    public function deleteAction(Request $request, Abonnement $abonnement)
    {
        $form = $this->createDeleteForm($abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($abonnement);
            $em->flush($abonnement);
        }

        return $this->redirectToRoute('abonnement_index');
    }

    /**
     * Creates a form to delete a abonnement entity.
     *
     * @param Abonnement $abonnement The abonnement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Abonnement $abonnement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('abonnement_delete', array('id' => $abonnement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
