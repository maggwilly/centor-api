<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Pwm\MessagerBundle\Entity\Notification;

class NotificationEvent extends Event
{

    protected $notifcation;
    protected $descs;
    protected $topic;
    protected $data;

    public function __construct($descs, Notification $notifcation, $data = array())
    {
        $this->notifcation = $notifcation;
        $this->descs = $descs;
        $this->data = $data;
    }

    public function getNotification()
    {
        return $this->notifcation;
    }

    /**
     * @return mixed
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param mixed $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    public function getDescs()
    {
        return $this->descs;
    }

    public function getData()
    {
        return $this->data;
    }
}