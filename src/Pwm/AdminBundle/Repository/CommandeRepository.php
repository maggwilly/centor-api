<?php

namespace Pwm\AdminBundle\Repository;
use Pwm\AdminBundle\Entity\Ressource;
use Pwm\AdminBundle\Entity\UserAccount;
use AppBundle\Entity\SessionConcours;
/**
 * CommandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommandeRepository extends \Doctrine\ORM\EntityRepository
{
 public function findOneByUserRessource($info,Ressource $ressource){
 $qb =$this->createQueryBuilder('a')
         ->where('a.info=:info')
         ->setParameter('info', $info)
         ->andWhere('a.ressource=:ressource')
         ->andWhere('a.status=:status')
         ->setParameter('status', 'PAID')
         ->setParameter('ressource', $ressource)
         ->orderBy('a.date', 'desc');
        return  $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
 }	

  public function findOneByUserSession(UserAccount $info, SessionConcours $session=null){
 $qb =$this->createQueryBuilder('a')
       ->where('a.info=:info')
        ->setParameter('info', $info)
        ->andWhere('a.status is NULL');
        return   $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
 }

  public function findOneByOrderId($order_id){
      $qb =$this->createQueryBuilder('a')
        ->where('a.order_id=:order_id') ->setParameter('order_id', $order_id);
         return  $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
  }
}
