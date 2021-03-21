<?php

namespace Pwm\AdminBundle\Entity;

use AppBundle\Entity\SessionConcours;
use Doctrine\ORM\Mapping as ORM;
use Pwm\MessagerBundle\Entity\Registration;

/**
 * Groupe
 *
 * @ORM\Table(name="desc_groupe")
 * @ORM\Entity(repositoryClass="Pwm\AdminBundle\Repository\GroupeRepository")
 */
class Groupe
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

   /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SessionConcours", inversedBy="groupe")
   */
    private $session;

    /**
     * @ORM\ManyToMany(targetEntity="Pwm\AdminBundle\Entity\UserAccount", inversedBy="groupes",  cascade={"persist","remove"})
     * @ORM\JoinTable(name="groupe_info",
     *      joinColumns={@ORM\JoinColumn(name="groupe_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="info_id", referencedColumnName="uid")}
     * )
     */
    private $infos;

        /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255, options={"default" : "groupe"})
     */
    private $tag; 

    /**
     * Constructor
     */
    public function __construct($nom, SessionConcours $session = null, $tag='public')
    {
        $this->date =new \DateTime();
        $this->nom =$nom;
        $this->session =$session;
        $this->tag =$tag;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Groupe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Groupe
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
     * Set session
     *
     * @param SessionConcours $session
     *
     * @return Notification
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Notification
     */
    public function setTag($titre)
    {
        $this->tag = $titre;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * @param mixed $infos
     */
    public function setInfos($infos)
    {
        $this->infos = $infos;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }     
}

