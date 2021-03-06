<?php

namespace Pwm\AdminBundle\Controller;

use AppBundle\Event\FileCreationEvent;
use Pwm\AdminBundle\Entity\Ressource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

// alias pour toutes les annotations
use FOS\RestBundle\View\View;
use AppBundle\Entity\SessionConcours;
use Pwm\AdminBundle\Entity\Commande;
use AppBundle\Event\ResultEvent;
use Pwm\MessagerBundle\Entity\Notification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Event\NotificationEvent;

/**
 * Ressource controller.
 *
 */
class RessourceController extends Controller
{

    /**
     * @Security("is_granted('ROLE_SUPERVISEUR')")
     */
    public function indexAction(SessionConcours $session = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (is_null($session) || $session->getId()==0)
            $ressources = $em->getRepository('AdminBundle:Ressource')->findAll();
        else
            $ressources = $session->getRessources();
        return $this->render('ressource/index.html.twig', array(
            'ressources' => $ressources, 'session' => $session,
        ));
    }

    /**
     * @Security("is_granted('ROLE_SUPERVISEUR')")
     */
    public function newAction(Request $request, SessionConcours $session = null)
    {
        $ressource = new Ressource();
        $form = is_null($session) ? $this->createForm('Pwm\AdminBundle\Form\RessourceSuperType', $ressource) : $this->createForm('Pwm\AdminBundle\Form\RessourceType', $ressource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($ressource->getSessions() as $session) {
                $ressource->removeSession($session);
                $ressource->addSession($session);
                $this->pushInGroup($ressource, $session);
                $this->pushNotificationEvent($ressource, $session);
            }
            foreach ($ressource->getMatieres() as $matiere) {
                foreach ($matiere->getProgramme()->getSessions() as $session) {
                    $ressource->removeSession($session);
                    $ressource->addSession($session);
                    $this->pushInGroup($ressource, $session);
                    $this->pushNotificationEvent($ressource, $session);
                }
            }
            if (empty($ressource->getSessions())&&empty($ressource->getMatieres())){
                $this->pushNotificationEvent($ressource);
            }
            $em->persist($ressource);
            $em->flush();
             $this->get('solr.client')->addDocument($ressource);
            $this->get('event_dispatcher')->dispatch('file.object.created', new FileCreationEvent($ressource));
            if(!is_null($session)){
                $this->pushInGroup($ressource, $session);
                $this->pushNotificationEvent($ressource, $session);
                return $this->redirectToRoute('ressource_show', array('id' => $ressource->getId(), 'session' => $session->getId()));
            }
            return $this->redirectToRoute('ressource_show', array('id' => $ressource->getId()));
        }
        return $this->render('ressource/new.html.twig', array(
            'ressource' => $ressource, 'session' => $session,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("is_granted('ROLE_SUPERVISEUR')")
     */
    public function pushInGroupAction(Ressource $ressource, SessionConcours $session = null)
    {
        if(!is_null($session)){
            $this->pushInGroup($ressource, $session);
            $this->pushNotificationEvent($ressource, $session);
            return $this->redirectToRoute('ressource_show', array('id' => $ressource->getId(), 'session' => $session->getId()));
        }
        $this->pushNotificationEvent($ressource);
        return $this->redirectToRoute('ressource_show', array('id' => $ressource->getId()));
    }


    public function findRegistrations($destinations)
    {
        $registrations = array();
        foreach ($destinations as $info) {
            foreach ($info->getRegistrations() as $registration) {
                if (is_null($registration->getIsFake()))
                    $registrations[] = $registration;
            }
        }
        return $registrations;

    }

    public function pushInGroup(Ressource $ressource, SessionConcours $session)
    {
            $msg = $this->getDocument($ressource);
            $url = "https://centor-concours.firebaseio.com/groupes/" . $session->getId() . "/documents.json";
            $this->get('fmc_manager')->sendOrGetData($url, $msg, 'POST', false);
    }


    /**
     * Lists all Produit entities.
     * @Rest\View(serializerGroups={"ressource"})
     */
    public function indexJsonAction(SessionConcours $session)
    {
        $em = $this->getDoctrine()->getManager();
        $sessions = $em->getRepository('AdminBundle:Ressource')->findRessources($session);
        return $sessions;
    }


    /**
     * Lists all Produit entities.
     * @Rest\View(serializerGroups={"full"})
     */
    public function showJsonAction(Request $request, Ressource $ressource)
    {
        $uid = $request->query->get('uid');
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('AdminBundle:Commande')->findOneByUserRessource($uid, $ressource);
        if ($commande != null && ($commande->getStatus() === 'PAID' || $commande->getStatus() === 'SUCCESS'))
            return $ressource->setPrice(0);
        return $ressource;
    }

    /**
     * Finds and displays a ressource entity.
     */
    public function showAction(Ressource $ressource, SessionConcours $session = null)
    {
        $deleteForm = $this->createDeleteForm($ressource);
        return $this->render('ressource/show.html.twig', array(
            'ressource' => $ressource,
            'session' => $session,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("is_granted('ROLE_SUPERVISEUR')")
     */
    public function editAction(Request $request, Ressource $ressource, SessionConcours $session = null)
    {
        $em = $this->getDoctrine()->getManager();
        $em->refresh($ressource);
        $deleteForm = $this->createDeleteForm($ressource);
        $editForm = is_null($session) ? $this->createForm('Pwm\AdminBundle\Form\RessourceSuperType', $ressource) : $this->createForm('Pwm\AdminBundle\Form\RessourceType', $ressource);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($ressource->getMatieres() as $matiere) {
                foreach ($matiere->getProgramme()->getSessions() as $session) {
                    $ressource->removeSession($session);
                    $ressource->addSession($session);
                }
            }
            foreach ($ressource->getSessions() as $key => $session) {
                    $this->pushNotificationEvent($ressource, $session);
                    $this->pushInGroup($ressource, $session, false);
                }
            if (empty($ressource->getSessions())&&empty($ressource->getMatieres())){
                $this->pushNotificationEvent($ressource);
            }
            $em->flush();
            $this->get('solr.client')->addDocument($ressource);
            $this->addFlash('success', 'Modifications  enrégistrées avec succès.');
            return $this->redirectToRoute('ressource_edit', array('id' => $ressource->getId()));
        } elseif ($editForm->isSubmitted())
            $this->addFlash('error', 'Certains champs ne sont pas corrects.');
        return $this->render('ressource/edit.html.twig', array(
            'ressource' => $ressource,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("is_granted('ROLE_SUPERVISEUR')")
     */
    public function deleteAction(Request $request, Ressource $ressource)
    {
        $form = $this->createDeleteForm($ressource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ressource);
            $em->flush();
            $this->get('solr.client')->removeDocument($ressource);
            $this->addFlash('success', 'Supprimé.');
        }
        return $this->redirectToRoute('ressource_index');
    }

    /**
     * Creates a form to delete a ressource entity.
     * @param Ressource $ressource The ressource entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ressource $ressource)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ressource_delete', array('id' => $ressource->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param Ressource $ressource
     * @return array
     */
    public function getDocument(Ressource $ressource): array
    {
        return array(
            'message' => array(
                'ressource' => array(
                    'ressource_id' => $ressource->getId(),
                    'price' => '',
                    'thubmnail' => $ressource->getImageUrl(),
                    'nom' => $ressource->getNom(),
                    'description' => $ressource->getDescription(),
                    'size' => $ressource->getSize(),
                    'style' => $ressource->getStyle()),
                'type' => 'ressource',
                'fromAdmin' => true),
            'uiniqid' => uniqid(),
            'displayName' => 'Centor .inc',
            'timestamp' => time(),
            'sentby' => 'uid',
            'photoURL' => 'https://firebasestorage.googleapis.com/v0/b/trainings-fa73e.appspot.com/o/ressources%2Ficon-blue.png?alt=media&token=b146afb4-66db-49e0-9261-0216721daa8c',
            'sentTo' => ''
        );
    }

    /**
     * @param Ressource $ressource
     * @param SessionConcours $session
     * @return array
     */
    public function pushNotificationEvent(Ressource $ressource, SessionConcours $session=null): array
    {
        $notification = new Notification('public', false, true);
        $notification->setTitre($ressource->getNom())
            ->setSousTitre( $ressource->getDescription())
            ->setText( $ressource->getDescription())
            ->setUser($this->getUser())->setType("public")
            ->setImageEntity($ressource->getFileEntity());
            $data = array('page' => 'document', 'id' => $ressource->getId());
        if (!is_null($session)) {
            $destinations = $session->getInfos();
            $registrations = $this->findRegistrations($destinations);
            $event = new NotificationEvent($registrations, $notification, $data);
            $event->setTopic('centor-group-'.$session->getId());
            $this->get('event_dispatcher')->dispatch('notification.shedule.to.send', $event);
        } else {
            $registrations = $this->getDoctrine()->getManager()->getRepository('MessagerBundle:Registration')->findAll();
            $event = new NotificationEvent($registrations, $notification, $data);
            $event->setTopic('centor-public');
            $this->get('event_dispatcher')->dispatch('notification.shedule.to.send', $event);
        }
        return array($notification, $data, $registrations, $event);
    }
}
