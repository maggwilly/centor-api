<?php


namespace AppBundle\Event;


use AppBundle\Entity\Image;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class DoctrineEventListener implements EventSubscriber
{
    private $router;
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getSubscribedEvents()
    {
        return array('postLoad');
     }
    public function postLoad(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $entity->setUrl($this->router->generate('download_file', array('id'=>$entity ->getId()), UrlGeneratorInterface::ABSOLUTE_URL));
            $entity->setThumnnailUrl($this->router->generate('download_thumbnail', array('id'=>$entity ->getId()), UrlGeneratorInterface::ABSOLUTE_URL));
        }
    }
}