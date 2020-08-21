<?php

namespace AppBundle\Event;

use AppBundle\Entity\Partie;
use AppBundle\Service\FMCManager;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Question;
use AppBundle\Entity\Resultat;
use Pwm\AdminBundle\Entity\Ressource;
use Spatie\PdfToImage\Pdf;

class FileObjectCreationListener
{

    protected $cloudinaryWrapper;
    protected $_em;
    protected $twig;
    protected $fcm;
    protected $base_data_dir;
    const HEADERS = array(
        "Authorization: key=AAAAJiQu4xo:APA91bH63R7-CeJ7jEgGtb2TNVkCx0TDWAYbu32mO1_4baLtrrFidNrbNy98Qngb6G67efbuJ8BpInpJiCeoTp-p5mt2706P2hXbXqrTXOWlaJFTDHza2QVWSlwsbF27eBhD2PZVJKuu",
        "content-type: application/json"
    );
    const FCM_URL = "https://fcm.googleapis.com/fcm/send";

    public function __construct(EntityManager $_em, FMCManager $fcm,$base_data_dir)
    {
        $this->_em = $_em;
        $this->fcm = $fcm;
        $this->base_data_dir=$base_data_dir;
    }


    public function onFileObjectCreated(FileCreationEvent $event)
    {
        $fileObject = $event->getFileObject();
        $upladdir = '/';
        $filename = '';
        $fileEntity = $fileObject->getFileEntity();
        if ($fileEntity != null) {
            if ($fileObject instanceof Question) {
                $upladdir = 'questions/';
                $filename = 'question_' . $fileObject->getId() .'.'. $fileEntity->getExtension();
            } elseif ($fileObject instanceof Ressource) {
                $upladdir = 'documents/';
                $filename = str_replace(' ', '_', $fileObject->getNom()) .'.'. $fileEntity->getExtension();
            } elseif ($fileObject instanceof Resultat) {
                $upladdir = 'arretes/';
                $filename = str_replace(' ', '_', $fileObject->getDescription()) .'.'. $fileEntity->getExtension();
            }elseif ($fileObject instanceof Partie) {
                $upladdir = 'cours/';
                $filename = str_replace(' ', '_', $fileObject->getTitre()) .'.'. $fileEntity->getExtension();
            }
            if ($fileEntity->upload($this->base_data_dir,$upladdir, $filename)) {
                if($fileEntity->getExtension()=='pdf')
                   $fileEntity->setThumnnail($this->createThumbnail($upladdir, $filename));
                $this->_em->flush();
            }
        }
    }

    public function createThumbnail($upladdir, $filename){
        $filenameImg=$filename.'.jpg';
        $pdf = new Pdf($this->base_data_dir.'/'.$upladdir.$filename);
        $pdf->saveImage($this->base_data_dir.'/'.$upladdir.$filenameImg);
        return $filenameImg;
    }

}