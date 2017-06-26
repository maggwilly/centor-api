<?php
// src/Sdz/BlogBundle/Bigbrother/MessagePostEvent.php
namespace AppBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Info;
class InfoEvent extends Event
{

protected $info;
public function __construct(Info $info)
{
$this->info = $info;

} 

public function getInfo()
{
return $this->info;
} 

}