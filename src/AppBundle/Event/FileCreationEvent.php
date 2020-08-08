<?php


namespace AppBundle\Event;
use AppBundle\Entity\FileObject;
use Symfony\Component\EventDispatcher\Event;

class FileCreationEvent extends Event
{
    protected $fileObject;

    /**
     * FileCreationEvent constructor.
     * @param $fileObject
     */
    public function __construct($fileObject)
    {
        $this->fileObject = $fileObject;
    }

    /**
     * @return mixed
     */
    public function getFileObject()
    {
        return $this->fileObject;
    }

}