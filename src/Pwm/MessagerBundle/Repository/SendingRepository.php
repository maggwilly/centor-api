<?php

namespace Pwm\MessagerBundle\Repository;
use Pwm\MessagerBundle\Entity\Notification;
use Pwm\MessagerBundle\Entity\Registration;
/**
 * SendingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SendingRepository extends \Doctrine\ORM\EntityRepository
{


  public function findList($registration,$uid,$start){
       $qb = $this->createQueryBuilder('a')->join('a.registration','r');
       $qb->where('a.registration=:registration')
        ->orWhere('r.info=:uid')
        ->setParameter('registration',$registration)->setParameter('uid',$uid);
        $qb->orderBy('a.date', 'desc'); 
         $query=$qb->getQuery();
         $query->setFirstResult($start)->setMaxResults(20);
          return $query->getResult();
  }

      /**
  *Nombre de synchro effectue par utilisateur 
  */
  public function findByNotInfo( $notification=null, $registration=null){
         $qb = $this->createQueryBuilder('a')
          ->where('a.registration=:registration')
          ->setParameter('registration',$registration)
          ->andWhere('a.notification=:notification')
          ->setParameter('notification',$notification);
          return $qb->getQuery()->getResult();
  }

      /**
  *Nombre de synchro effectue par utilisateur 
  */
  public function findReading(Notification $notification){
         $qb = $this->createQueryBuilder('a')
          ->where('a.notification=:notification')
          ->setParameter('notification',$notification)
          ->select('count(a.readed) as readed');
          return $qb->getQuery()->getSingleScalarResult();
  }
  	  /**
  *Nombre de synchro effectue par utilisateur 
  */
  public function findCount($registration,$uid){
          //connected and registed
       $qb = $this->createQueryBuilder('a')->join('a.registration','r');
       $qb->where('a.registration=:registration or r.info=:uid')
      ->setParameter('registration',$registration)->setParameter('uid',$uid);
       $qb->andWhere('a.readed is NULL')->select('count(a.id)');
        return $qb->getQuery()->getSingleScalarResult();
  }

    public function findNotRead(Notification $notification=null){
         $qb = $this->createQueryBuilder('s')
         ->join('s.registration','r')
         ->where('s.readed is NULL') ->andWhere('r.isFake is NULL');
         if (!is_null($notification)) {
           $qb->andWhere('s.notification=:notification')->setParameter('notification',$notification);
         }
         $qb ->select('r.registrationId'); 
          return $qb->getQuery()->getArrayResult();
  }

    public function findSendDest(Notification $notification=null){
         $qb = $this->createQueryBuilder('s')
         ->join('s.registration','r')
         ->where('r.isFake is NULL');
         if (!is_null($notification)) {
           $qb->andWhere('s.notification=:notification')->setParameter('notification',$notification);
         }
         $qb ->select('r.registrationId'); 
          return $qb->getQuery()->getArrayResult();
  }  
}
