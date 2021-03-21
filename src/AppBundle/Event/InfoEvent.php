<?php
namespace AppBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use Pwm\AdminBundle\Entity\UserAccount;
class InfoEvent extends Event
{

protected $info;
public function __construct(UserAccount $info=null)
{
$this->info = $info;

} 

public function getInfo()
{
return $this->info;
} 

}
