<?php

namespace Pwm\AdminBundle\Controller;

use Pwm\AdminBundle\Entity\Abonnement;
use Pwm\MessagerBundle\Entity\Notification;
use Pwm\AdminBundle\Entity\UserAccount;
use Pwm\AdminBundle\Entity\Commande;
use Pwm\AdminBundle\Entity\Tarifaire;
use Pwm\MessagerBundle\Controller\NotificationController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; 
use AppBundle\Entity\SessionConcours;
use AppBundle\Event\CommandeEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Abonnement controller.
 *
 */
class AbonnementController extends Controller
{
    const ZERO_PRICE_ID = 0;

    /**
   * @Security("is_granted('ROLE_DELEGUE')")
  */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $abonnements = $em->getRepository('AdminBundle:Abonnement')->findList();
         $extrats = $em->getRepository('AdminBundle:Abonnement')->findSinceDate();
        $concours = $em->getRepository('AppBundle:SessionConcours')->findList();
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
    public function indexJsonAction(UserAccount $info)
    {
        $em = $this->getDoctrine()->getManager();
        $abonnements = $em->getRepository('AdminBundle:Abonnement')->findForMe($info);
        return  $abonnements;
    }

    

    /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"commande"})
     */
    public function startCommandeAction(Request $request, UserAccount $info, $product, $package)
    {
         if($package=='ressource')
            $commande=$this->loadCommandeForRessource($info, $product);
         else{
             $product=$product!=0?$product:self::ZERO_PRICE_ID;
             $commande=$this->loadCommandeForSesson($info, $product,$package);
         }
        return $commande;
    }

  public function loadCommandeForRessource($info, $produit){
         $em = $this->getDoctrine()->getManager();
         $ressource = $em->getRepository('AdminBundle:Ressource')->find($produit);
          $commande=$em->getRepository('AdminBundle:Commande')->findOneByUserRessource($info,$ressource);
            if(is_null($commande)|| ! is_null($commande->getStatus())){
               $commande= new Commande($info);
               $commande->setDate(new \DateTime())
                   ->setRessource($ressource)
                   ->setOrderId($this->generateOrderId());
                  $em->persist($commande);
           } if(!is_null($ressource))
               $commande->setAmount($ressource->getPrice())->setDate(new \DateTime())
                   ->setOrderId($this->generateOrderId());
        $em->flush();
        return $commande;
      }

  public function loadCommandeForSesson($info, $produit, $package){
          $em = $this->getDoctrine()->getManager();
          $session = $em->getRepository('AppBundle:SessionConcours')->find($produit);
          $commande=$em->getRepository('AdminBundle:Commande')->findOneByUserSession($info,$session);
            if(is_null($commande)  || ! is_null($commande->getStatus())){
               $commande= new Commande($info, $session);
               $commande->setDate(new \DateTime())
                   ->setSession($session)
                   ->setPackage($package)
                   ->setOrderId($this->generateOrderId());
               $em->persist($commande);
           }
              $session->removeInfo($info);
              $session->addInfo($info);
             $commande->setAmount($this->getForSessonCommande($session->getPrice(),$package))
                 ->setPackage($package)
                 ->setDate(new \DateTime())
                 ->setSession($session)
                 ->setOrderId($this->generateOrderId());
                 $em->flush();
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
           return new Tarifaire();
        }

    /**
     * Lists all Produit entities.
     * @Rest\View(serializerGroups={"commande"})
     */
    public function confirmCommandeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($this->get("request")->getContent(), true);
        if (array_key_exists('orderid', $data) && array_key_exists('status', $data)) {
            $commande = $em->getRepository('AdminBundle:Commande')->findOneByOrderId($data['orderid']);
            if (!is_null($commande)) {
                $form = $this->createForm('Pwm\AdminBundle\Form\CommandeType', $commande);
                $form->submit($data, false);
                if ($form->isValid()) {
                    $commande->setStatus($data['status']);
                    $em->flush();
                    if (!is_null($commande->getSession()) && 'PAID' == $data['status']) {
                        $abonnement = $em->getRepository('AdminBundle:Abonnement')->findMeOnThis($commande->getInfo(), $commande->getSession());
                        if (is_null($abonnement)) {
                            $abonnement = new Abonnement($commande);
                            $commande->getSession()->removeInfo($commande->getInfo());
                            $commande->getSession()->addInfo($commande->getInfo());
                            $commande->getSession()->setNombreInscrit($commande->getSession()->getNombreInscrit() + 1);
                            $em->persist($abonnement);
                        }
                        $abonnement->setPlan($commande->getPackage());
                        $abonnement->setPrice($commande->getAmount());
                        $commande->setAbonnement($abonnement);
                    }
                    $em->flush();
                    $event = new CommandeEvent($commande);
                    $this->get('event_dispatcher')->dispatch('commande.confirmed', $event);
                }
            }
        }
        return new JsonResponse("Thanks", 200);
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
    public function showJsonAction(UserAccount $info, SessionConcours $session=null){
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

    /**
     * @return string
     */
    public function generateOrderId(): string
    {
        return 'CT' . uniqid() . 'CD';
    }
}
