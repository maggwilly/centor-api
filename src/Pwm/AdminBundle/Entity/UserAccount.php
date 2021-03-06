<?php

namespace Pwm\AdminBundle\Entity;
use Pwm\MessagerBundle\Entity\Registration;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_account_details")
 * @ORM\Entity(repositoryClass="Pwm\AdminBundle\Repository\InfoRepository")
  * @ORM\HasLifecycleCallbacks
 */
class UserAccount {
     /**
     * @var string
    * @ORM\Id
     * @ORM\Column(name="uid", type="string", length=255, nullable=false)
     */
    private $uid;

       /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;


    /**
     * @var string
     * @ORM\Column(name="displayName", type="string", length=255, nullable=true)
     */
    private $displayName;


    /**
     * @var string
     * @ORM\Column(name="photoURL", type="text",  nullable=true)
     */
    private $photoURL;

    /**
     * @var string
     *
     * @ORM\Column(name="langue", type="string", length=255, nullable=true)
     */
    private $langue;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     */
    private $phone;

    
    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="serie", type="string", length=255, nullable=true)
     */
    private $serie;

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255, nullable=true)
     */
    private $niveau;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_max", type="date", nullable=true)
     */
    private $dateMax;


    /**
     * @var string
     *
     * @ORM\Column(name="branche", type="string", length=255, nullable=true)
     */
    private $branche;


    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=255, nullable=true)
     */
    private $paymentMethod;


    /**
     * @var string
     *
     * @ORM\Column(name="enable_notifications", type="boolean", nullable=true)
     */
    private $enableNotifications;

    /**
     * @ORM\ManyToMany(targetEntity="Pwm\AdminBundle\Entity\Groupe", mappedBy="infos",  cascade={"persist","remove"})
     */
    private $groupes;
    /**
   * @ORM\OneToMany(targetEntity="Pwm\MessagerBundle\Entity\Registration", mappedBy="info", cascade={"persist","remove"})
   */
    private $registrations;

    /**
   * @ORM\OneToMany(targetEntity="Pwm\AdminBundle\Entity\Abonnement", mappedBy="info", cascade={"persist","remove"})
   */
    private $abonnements;

    private $file;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_save", type="datetime", nullable=true)
     */
    private $date;
    /**
   * @ORM\ManyToOne(targetEntity="Ambassador" )
   */
    private $ambassador;
    /**
     * Constructor
     */
    public function __construct($uid=null)
    {
         $this->uid=$uid;
        $this->date =new \DateTime();
    }


    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return UserAccount
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {         if($this->displayName==null) 
                  if($this->email==null) 
                      return $this->uid;
                  else
                     return $this->email;         
        return $this->displayName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return UserAccount
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set photoURL
     *
     * @param string $photoURL
     *
     * @return UserAccount
     */
    public function setPhotoURL($photoURL)
    {
        $this->photoURL = $photoURL;

        return $this;
    }

    /**
     * Get photoURL
     *
     * @return string
     */
    public function getPhotoURL()
    {
        return $this->photoURL;
    }

    /**
     * Set langue
     *
     * @param string $langue
     *
     * @return UserAccount
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return string
     */
    public function getLangue()
    {
        return $this->langue;
    }




    /**
     * Set uid
     *
     * @param string $uid
     *
     * @return UserAccount
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return UserAccount
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return UserAccount
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set branche
     *
     * @param string $branche
     *
     * @return UserAccount
     */
    public function setBranche($branche)
    {
        $this->branche = $branche;

        return $this;
    }

    /**
     * Get branche
     *
     * @return string
     */
    public function getBranche()
    {
        return $this->branche;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return UserAccount
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set enableNotifications
     *
     * @param boolean $enableNotifications
     *
     * @return UserAccount
     */
    public function setEnableNotifications($enableNotifications)
    {
        $this->enableNotifications = $enableNotifications;

        return $this;
    }

    /**
     * Get enableNotifications
     *
     * @return boolean
     */
    public function getEnableNotifications()
    {
        return $this->enableNotifications;
    }



    /**
     * Add registration
     *
     * @param Registration $registration
     *
     * @return UserAccount
     */
    public function addRegistration(Registration $registration)
    {
        $this->registrations[] = $registration;
        $registration->setInfo(  $this);

        return $this;
    }

    /**
     * Remove registration
     *
     * @param Registration $registration
     */
    public function removeRegistration(Registration $registration)
    {
        $this->registrations->removeElement($registration);
    }

    /**
     * Get registrations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegistrations()
    {
        return $this->registrations;
    }

   /**
     * Set serie
     *
     * @param string $serie
     *
     * @return Session
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return string
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set niveau
     *
     * @param string $niveau
     *
     * @return Session
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set dateMax
     *
     * @param \DateTime $dateMax
     *
     * @return Session
     */
    public function setDateMax($dateMax)
    {
        $this->dateMax = $dateMax;

        return $this;
    }

    /**
     * Get dateMax
     *
     * @return \DateTime
     */
    public function getDateMax()
    {
        return $this->dateMax;
    }   
   /**
     * Add abonnement
     *
     * @param Abonnement $abonnement
     *
     * @return Session
     */
    public function addAbonnement(Abonnement $abonnement)
    {
        $this->abonnements[] = $abonnement;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * @param mixed $groupes
     */
    public function setGroupes($groupes)
    {
        $this->groupes = $groupes;
    }

    /**
     * Remove abonnement
     *
     * @param Abonnement $abonnement
     */
    public function removeAbonnement(Abonnement $abonnement)
    {
        $this->abonnements->removeElement($abonnement);
    }

    /**
     * Get abonnements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAbonnements()
    {
        return $this->abonnements;
    }
    

    /**
     * Set ambassador
     *
     * @param Ambassador $ambassador
     *
     * @return UserAccount
     */
    public function setAmbassador(Ambassador $ambassador = null)
    {
        $this->ambassador = $ambassador;

        return $this;
    }

    /**
     * Get ambassador
     *
     * @return Ambassador
     */
    public function getAmbassador()
    {
        return $this->ambassador;
    }

      /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Notification
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
}
