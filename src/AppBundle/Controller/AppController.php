<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Etape controller.
 *
 */
class AppController extends Controller
{
    /**
     * Lists all etape entities.
     *
     */
    public function indexAction()

    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_DELEGUE'))
            return $this->redirectToRoute('abonnement_index');
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->redirectToRoute('user_index');
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_CONTROLEUR'))
            return $this->redirectToRoute('concours_index');
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPERVISEUR'))
            return $this->redirectToRoute('session_index');
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SAISIE'))
            return $this->redirectToRoute('partie_index');
        elseif ($this->get('security.authorization_checker')->isGranted('ROLE_MESSAGER'))
            return $this->redirectToRoute('notification_index');
        return $this->redirectToRoute('notification_index');
    }

    public function downloadAction($id) {
        try {
            $file = $this->getDoctrine ()->getRepository ( 'AppBundle:Image' )->find($id);
            if (! $file) {
                $array = array (
                    'status' => 404,
                    'message' => 'File does not exist'
                );
                $response = new JsonResponse ( $array, 404 );
                return $response;
            }
            $file_with_path = $this->container->getParameter ( 'base_data_dir' ) . "/" .$file->getWebPath();
            $response = new BinaryFileResponse ( $file_with_path );
            $response->headers->set ( 'Content-Type', 'application/pdf' );
            $response->setContentDisposition (ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getFilename());
            return $response;
        } catch ( Exception $e ) {
            $array = array (
                'status' => 400,
                'message' => 'Download error'
            );
            $response = new JsonResponse ( $array, 400 );
            return $response;
        }
    }

    public function thumbnailAction($id) {
        try {
            $file = $this->getDoctrine ()->getRepository ( 'AppBundle:Image' )->find($id);
            if (! $file) {
                $array = array (
                    'status' => 404,
                    'message' => 'File does not exist'
                );
                $response = new JsonResponse ( $array, 404 );
                return $response;
            }
            $file_with_path = $this->container->getParameter ( 'base_data_dir' ) . "/" .$file->getThumbnailPath();
            $response = new BinaryFileResponse ( $file_with_path );
            $response->headers->set ( 'Content-Type', 'image/jpeg' );
            $response->setContentDisposition (ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getThumnnail());
            return $response;
        } catch ( Exception $e ) {
            $array = array (
                'status' => 400,
                'message' => 'Download error'
            );
            $response = new JsonResponse ( $array, 400 );
            return $response;
        }
    }
    public function helpAction($topic)
    {
        return $this->render('reads/help.html.twig', array());
    }

    public function cguAction()
    {
        return $this->render('reads/cgu.html.twig', array());
    }
}