<?php

namespace Pwm\AdminBundle\Entity;

use AppBundle\Entity\SessionConcours;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="Pwm\AdminBundle\Repository\CommandeRepository")
 */
class Commande
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;



    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

        /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=255, unique=true)
     */
    private $order_id;


            /**
     * @var string
     *
     * @ORM\Column(name="txnid", type="string", length=255, nullable=true)
     */
    private $txnid;

    /**
     * @var string
     *
     * @ORM\Column(name="package", type="string", length=255, nullable=true)
     */
    private $package;

      /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SessionConcours" )
   */
    private $session;

    /**
   * @ORM\ManyToOne(targetEntity="Pwm\AdminBundle\Entity\Ressource" )
   */
    private $ressource; 

    /**
     * @var string
     *
     * @ORM\Column(name="ressource_url", type="string", length=255, nullable=true)
     */
    private $ressourcesUrl;

  /**
   * @ORM\ManyToOne(targetEntity="Abonnement")
   */
    private $abonnement;

      /**
   * @ORM\ManyToOne(targetEntity="UserAccount" )
    * @ORM\JoinColumn(referencedColumnName="uid")
   */
    private $info;

        /**
     * Constructor
     */
    public function __construct(UserAccount $info=null, SessionConcours $session=null, $package=null, $amount=null, Ressource $ressource = null)
    {
        $this->date =new \DateTime();  
         $this->info = $info; 
          $this->session =$session; 
            $this->package =$package; 
              $this->amount =$amount; 
               $this->currency ='XAF';
                   $this->ressource =$ressource;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUId() {
        return strtoupper(uniqid());
    }
    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }



    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Commande
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Commande
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set package
     *
     * @param string $package
     *
     * @return Commande
     */
    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set session
     *
     * @param SessionConcours $session
     *
     * @return Commande
     */
    public function setSession(SessionConcours $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return SessionConcours
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set info
     *
     * @param \Pwm\AdminBundle\Entity\UserAccount $info
     *
     * @return Commande
     */
    public function setInfo(\Pwm\AdminBundle\Entity\UserAccount $info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return \Pwm\AdminBundle\Entity\UserAccount
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Commande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set txnid
     *
     * @param string $txnid
     *
     * @return Commande
     */
    public function setTxnid($txnid)
    {
        $this->txnid = $txnid;

        return $this;
    }

    /**
     * Get txnid
     *
     * @return string
     */
    public function getTxnid()
    {
        return $this->txnid;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return Commande
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set abonnement
     *
     * @param \Pwm\AdminBundle\Entity\Abonnement $abonnement
     *
     * @return Commande
     */
    public function setAbonnement(\Pwm\AdminBundle\Entity\Abonnement $abonnement = null)
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    /**
     * Get abonnement
     *
     * @return \Pwm\AdminBundle\Entity\Abonnement
     */
    public function getAbonnement()
    {
        return $this->abonnement;
    }

    /**
     * Set ressourcesUrl
     *
     * @param string $ressourcesUrl
     *
     * @return Commande
     */
    public function setRessourcesUrl($ressourcesUrl)
    {
        $this->ressourcesUrl = $ressourcesUrl;

        return $this;
    }

    /**
     * Get ressourcesUrl
     *
     * @return string
     */
    public function getRessourcesUrl()
    {
        return $this->ressourcesUrl;
    }

    /**
     * Set ressource
     *
     * @param \AdminBundle\Entity\Ressource $ressource
     *
     * @return Commande
     */
    public function setRessource(Ressource $ressource = null)
    {
        $this->ressource = $ressource;

        return $this;
    }

    /**
     * Get ressource
     *
     * @return \AdminBundle\Entity\Ressource
     */
    public function getRessource()
    {
        return $this->ressource;
    }
}
