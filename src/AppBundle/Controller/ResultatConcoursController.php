<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ResultatConcours;
use AppBundle\Event\FileCreationEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use FOS\RestBundle\View\View; 
use Symfony\Component\HttpFoundation\Response;
use Pwm\MessagerBundle\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Event\ResultEvent;
use AppBundle\Event\NotificationEvent;
/**
 * ResultatConcours controller.
 *
 */
class ResultatConcoursController extends Controller
{
  /**
   * @Security("is_granted('ROLE_MESSAGER')")
  */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $resultats = $em->getRepository('AppBundle:ResultatConcours')->findAll();

        return $this->render('resultat/index.html.twig', array(
            'resultats' => $resultats,
        ));
    }
        /**
     * Lists all Produit entities.
     *@Rest\View(serializerGroups={"resultat"})
     */
     public function jsonIndexAction(Request $request)
     {
         $all=$request->query->get('all');
         $start=$request->query->get('start');
         $em = $this->getDoctrine()->getManager();
         $resultats =$em->getRepository('AppBundle:ResultatConcours')->findList($start,$all);
         return  $resultats;
     }

  /**
   * @Security("is_granted('ROLE_MESSAGER')")
  */
    public function newAction(Request $request)
    {
        $resultat = new ResultatConcours();
        $form = $this->createForm('AppBundle\Form\ResultatType', $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($resultat);
            $em->flush();
            $this->get('solr.client')->addDocument($resultat);
            $this->get('event_dispatcher')->dispatch('file.object.created', new FileCreationEvent($resultat));
            $this->dispatchNotificationEvent($resultat);
            $this->addFlash('success', 'Enrégistrement effectué');
           return   $this->redirectToRoute('resultat_index');
        }elseif($form->isSubmitted())
               $this->addFlash('error', 'Certains champs ne sont pas corrects.');
        return $this->render('resultat/new.html.twig', array(
            'resultat' => $resultat,
            'form' => $form->createView(),
        ));
       }
        public function dispatchNotificationEvent(ResultatConcours $resultat){
                $em = $this->getDoctrine()->getManager();
               $notification = new Notification('public',false,true);
               $notification->setTitre($resultat->getDescription())
                ->setSousTitre($resultat->getDescription()." dispobible en téléchargement ")
                ->setText($resultat->getDescription()." Sont disponibles ")
                ->setUser($this->getUser())
                ->setImageEntity($resultat->getFileEntity())->setType("public")
                 ->setIncludeMail(false);
                 $registrations = $em->getRepository('MessagerBundle:Registration')->findAll();
                 $data=array('page'=>'resultat');
                 $event=new NotificationEvent($registrations,$notification, $data);
                 $this->get('event_dispatcher')->dispatch('notification.shedule.to.send', $event);
                }

    /**
     * Finds and displays a resultat entity.
     *
     */
    public function showAction(ResultatConcours $resultat)
    {
        $deleteForm = $this->createDeleteForm($resultat);

        return $this->render('resultat/show.html.twig', array(
            'resultat' => $resultat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

        /**
     * Finds and displays a resultat entity.
     *
     */
    public function getAction(ResultatConcours $resultat)
    {
        $deleteForm = $this->createDeleteForm($resultat);

        return $this->redirect($resultat->getUrl());
    }

  /**
   * @Security("is_granted('ROLE_MESSAGER')")
  */
    public function editAction(Request $request, ResultatConcours $resultat)
    {
        $deleteForm = $this->createDeleteForm($resultat);
        $editForm = $this->createForm('AppBundle\Form\ResultatType', $resultat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
           $this->addFlash('success', 'Modifications  enrégistrées avec succès.');
            return $this->redirectToRoute('resultat_edit', array('id' => $resultat->getId()));
        }elseif($editForm->isSubmitted())
               $this->addFlash('error', 'Certains champs ne sont pas corrects.');

        return $this->render('resultat/edit.html.twig', array(
            'resultat' => $resultat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a resultat entity.
     *
     */
    public function deleteAction(Request $request, ResultatConcours $resultat)
    {
        $form = $this->createDeleteForm($resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($resultat);
            $em->flush();
            $this->get('solr.client')->removeDocument($resultat);
            $this->addFlash('success', 'Supprimé.');
        }

        return $this->redirectToRoute('resultat_index');
    }

    /**
     * Creates a form to delete a resultat entity.
     *
     * @param ResultatConcours $resultat The resultat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ResultatConcours $resultat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('resultat_delete', array('id' => $resultat->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
