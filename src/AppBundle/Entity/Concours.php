<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Pwm\MessagerBundle\Entity\Notification;
use SolrBundle\Entity\SolrSearchResult;
use FS\SolrBundle\Doctrine\Annotation as Solr;
/**
 * Concours
 * @Solr\Document()
  * @Solr\SynchronizationFilter(callback="indexHandler")
 * @ORM\Table(name="concours_ecole")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConcoursRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Concours extends SolrSearchResult
{
    /**
     * @var int
     * @Solr\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Solr\Field(type="string")

     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @Solr\Field(type="string")
     * @ORM\Column(name="ecole", type="string", length=255)
     */
    private $ecole;

    /**
     * @var string
     * @Solr\Field(type="string")

     * @ORM\Column(name="abreviation", type="string", length=255, nullable=true)
     */
    private $abreviation;

    /**
     * @var string
     * @Solr\Field(type="string")
     * @ORM\Column(name="descriptionEcole", type="text", length=255, nullable=true)
     */
    private $descriptionEcole;

    /**
     * @var string
     * @Solr\Field(type="string")
     * @ORM\Column(name="descriptionConcours", type="text", length=255, nullable=true)
     */
    private $descriptionConcours;

    /**
     * @var string
     * @Solr\Field(type="string")
     * @ORM\Column(name="contacts", type="string", length=255, nullable=true)
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text",  nullable=true)
     */
    private $imageUrl;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $imageEntity;

     /**

   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Session", mappedBy="concours", cascade={"persist"})
   */
    private $sessions;

        /**
     * @var string
         * @Solr\Field(type="string")
     * @ORM\Column(name="serie", type="string", length=255, nullable=true)
     */
    private $serie;

        /**
     * @var string
         * @Solr\Field(type="string")
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
     * @ORM\ManyToOne(targetEntity="Pwm\MessagerBundle\Entity\Notification", cascade={"persist"})
     */
    private $articleDescriptif;


    /**
     * Constructor
     */
    public function __construct(Programme $programme=null)
    {
         
        $this->sessions = new ArrayCollection();
        if($programme!=null){
        $this->nom=$programme->getNom();
        $this->ecole=$programme->getEcole();
        $this->abreviation= $programme->getAbreviation();
        $this->descriptionEcole= $programme->getDescriptionEcole();
        $this->descriptionConcours= $programme->getDescriptionConcours();
        $this->imageUrl= $programme->getImage();
        $this->contacts= $programme->getContact();
        $session = new Session($this,$programme);
        $this-> addSession($session);
    }
    }


      /**
    * @ORM\PrePersist()
    */
    public function defaultDescription(){
       $this->articleDescriptif= new Notification();
       return  $this->articleDescriptif
       ->setTitre($this->getNom())
       ->setFormat('paper')
       ->setSousTitre("Découvrir  l'école/ faculté ".$this->getEcole()." et le concours de ".$this->getNom())
       ->setText('<h2>'.$this->getNom().'</h2>'.'<p>'.$this->descriptionEcole.'</p>'.'<p>'.$this->descriptionConcours.'</p>');
    }
/**
*@ORM\PostLoad()
 */
    public function indexHandler()
    {
        $this->title=$this->ecole.' - '.$this->abreviation;
        $this->description=$this->descriptionEcole.' '.$this->contacts;
        $this->resultType='Ecole';
        return true;
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
     * @return Concours
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
     * Set ecole
     *
     * @param string $ecole
     *
     * @return Concours
     */
    public function setEcole($ecole)
    {
        $this->ecole = $ecole;

        return $this;
    }

    /**
     * Get ecole
     *
     * @return string
     */
    public function getEcole()
    {
        return $this->ecole;
    }

    /**
     * Set abreviation
     *
     * @param string $abreviation
     *
     * @return Concours
     */
    public function setAbreviation($abreviation)
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    /**
     * Get abreviation
     *
     * @return string
     */
    public function getAbreviation()
    {
        return $this->abreviation;
    }

    /**
     * Set descriptionEcole
     *
     * @param string $descriptionEcole
     *
     * @return Concours
     */
    public function setDescriptionEcole($descriptionEcole)
    {
        $this->descriptionEcole = $descriptionEcole;

        return $this;
    }

    /**
     * Get descriptionEcole
     *
     * @return string
     */
    public function getDescriptionEcole()
    {
        return $this->descriptionEcole;
    }

    /**
     * Set descriptionConcours
     *
     * @param string $descriptionConcours
     *
     * @return Concours
     */
    public function setDescriptionConcours($descriptionConcours)
    {
        $this->descriptionConcours = $descriptionConcours;

        return $this;
    }

    /**
     * Get descriptionConcours
     *
     * @return string
     */
    public function getDescriptionConcours()
    {
        return $this->descriptionConcours;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     *
     * @return Concours
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return string
     */
    public function getContacts()
    {
        return $this->contacts;
    }


    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     *
     * @return Concours
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string
     */
    public function getImageUrl()
    {
        if($this->imageEntity!=null)
           return $this->imageUrl=$this->imageEntity->getUrl();
        return $this->imageUrl;
    }

    /**
     * Set imageEntity
     *
     * @param Image $imageEntity
     *
     * @return Concours
     */
    public function setImageEntity(Image $imageEntity = null)
    {
        $this->imageEntity = $imageEntity;

        return $this;
    }

    /**
     * Get imageEntity
     *
     * @return Image
     */
    public function getImageEntity()
    {
        return $this->imageEntity;
    }

    /**
     * Add session
     *
     * @param Session $session
     *
     * @return Concours
     */
    public function addSession(Session $session)
    {
       $session->setConcours($this);
        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Remove session
     *
     * @param Session $session
     */
    public function removeSession(Session $session)
    {
        $this->sessions->removeElement($session);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessions()
    {
        return $this->sessions;
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
     * Set articleDescriptif
     *
     * @param Notification $articleDescriptif
     *
     * @return Concours
     */
    public function setArticleDescriptif(Notification $articleDescriptif = null)
    {
        $this->articleDescriptif = $articleDescriptif;

        return $this;
    }

    /**
     * Get articleDescriptif
     *
     * @return Notification
     */
    public function getArticleDescriptif()
    {
        return $this->articleDescriptif;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getNom();
    }


    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->defaultDescription();
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return 'Concours';
    }

}
