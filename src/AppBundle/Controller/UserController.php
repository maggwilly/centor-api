<?php

namespace AppBundle\Controller;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Event\InfoEvent;
/**
 * @Security("is_granted('ROLE_ADMIN')")
*/
class UserController extends Controller
{


     /**
     * Lists all article entities.
     *
     */
    public function indexAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
         $users = $em->getRepository('AppBundle:User')->findAll();
        return $this->container->get('templating')->renderResponse('user/index.html.twig', array(
            'users' => $users,
        ));
    }


    /**
     * Displays a form to edit an existing partie entity.
     */
    public function toggleUserAction( User $user)
    {
           $em = $this->container->get('doctrine.orm.entity_manager');
           $locked=is_null($user->getLocked())?false:$user->getLocked();
           $user->setLocked(!$locked);
           $em->flush();
           
        $this->addFlash('success', 'Configurations prises en compte');  
        return $this->redirectToRoute('user_index');
    }


    public function inviteFillProfilAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $destinations=$em->getRepository('AdminBundle:UserAccount')->findNotProfilFilled();
        $this->addFlash('success', 'Invitation envoyée à . '.count($destinations).' utilisateurs');
        $event= new InfoEvent(null);
        $this->get('event_dispatcher')->dispatch('fill.profil.invited', $event);         
       return  $this->redirectToRoute('user_index');
    }




    /**
     * Displays a form to edit an existing objectif entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);
        $em = $this->container->get('doctrine.orm.entity_manager');
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em ->flush();
             $this->addFlash('success', 'Modifications  enrégistrées avec succès.');
         return $this->redirectToRoute('user_index');
        }elseif($editForm->isSubmitted())
               $this->addFlash('error', 'Certains champs ne sont pas corrects.');

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

}
