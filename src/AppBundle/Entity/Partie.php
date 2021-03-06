<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partie
 *
 * @ORM\Table(name="partie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PartieRepository")
 */
class Partie implements FileObject
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
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Partie" )
   */
    private $auMoinsdeMemeQue;

    private $qcm;

    private $isAvalable;

    private $analyse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;
        /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    private $endDate;


        /**
     * @var string
     *
     * @ORM\Column(name="sources", type="text",nullable=true)
     */
    private $sources;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=500)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="cours", type="string", length=255,nullable=true)
     */
    private $cours;

    /**
     * @var string
     *
     * @ORM\Column(name="index_number", type="integer",nullable=true)
     */
    private $index;

        /**
     * @var string
     *
     * @ORM\Column(name="prerequis", type="text",nullable=true)
     */
    private $prerequis;

    /**
     * @var string
     *
     * @ORM\Column(name="objectif", type="text",nullable=true)
     */
    private $objectif;

   /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Matiere", inversedBy="parties")
   */
    private $matiere;


    /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Question", mappedBy="partie", cascade={"persist","remove"})
   * @ORM\OrderBy({ "id" = "ASC"})
   */
    private $questions; 

   /**
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\Article", inversedBy="partie")
   */
    private $article;

    /**
   * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SessionConcours", mappedBy="parties", cascade={"persist"})
   */
    private $sessions;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $fileEntity;

    /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
   */
    private $user;



        /**
     * @var string
     *
     * @ORM\Column(name="contain_math", type="boolean",nullable=true)
     */
    private $containMath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date=new \DateTime();
    } 

    /**
     * Set ecole
     *
     * @param string $ecole
     * @return Concours
     */
    public function setAuMoinsdeMemeQue(\AppBundle\Entity\Partie $programme= null)
    {
        $this->auMoinsdeMemeQue = $programme;

        return $this;
    }

    /**
     * Get ecole
     *
     * @return string 
     */
    public function getAuMoinsdeMemeQue()
    {    if($this->auMoinsdeMemeQue==$this)
            return null;
        return $this->auMoinsdeMemeQue;
    }

  
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Partie
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getCours()
    {
        if($this->cours!=null)
            return $this->cours;
        return ($this->fileEntity!=null)?$this->fileEntity->getUrl():"";
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Partie
     */
    public function setCours($titre)
    {
        $this->cours = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

        /**
     * Get titre
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->titre.' > '.$this->matiere->getTitre().' > '.$this->matiere->getProgramme()->getNom();
    }
    /**
     * Set prerequis
     *
     * @param string $prerequis
     * @return Partie
     */
    public function setPrerequis($prerequis)
    {
        $this->prerequis = $prerequis;

        return $this;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return SessionConcours
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

      /**
     * Set type
     *
     * @param string $type
     *
     * @return SessionConcours
     */
    public function setSources($type)
    {
        $this->sources = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getSources()
    {
        return $this->sources;
    }  
    /**
     * Get prerequis
     *
     * @return string 
     */
    public function getPrerequis()
    { 
        if($this->prerequis==null&&$this->matiere!=null)
      return $this->prerequis='Avoir Maitrisé le programme de '.$this->matiere->getTitre().' du niveau requis pour ce concours';
    return $this->prerequis;
    }
 

    /**
     * Set matiere
     *
     * @param \AppBundle\Entity\Matiere $matiere
     * @return Partie
     */
    public function setMatiere(\AppBundle\Entity\Matiere $matiere = null)
    {
        $this->matiere = $matiere;
         
        return $this;
    }

    /**
     * Get matiere
     *
     * @return \AppBundle\Entity\Matiere 
     */
    public function getMatiere()
    {
        return $this->matiere;
    }
    /**
     * Set matiere
     *
     * @param \AppBundle\Entity\Article $matiere
     * @return Partie
     */
    public function setArticle(\AppBundle\Entity\Article $matiere = null)
    {
        $this->article = $matiere;

        return $this;
    }

    /**
     * Get matiere
     *
     * @return \AppBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set matiere
     *
     * @param \AppBundle\Entity\Article $matiere
     * @return Partie
     */
    public function setAnalyse(\Pwm\AdminBundle\Entity\Analyse $analyse = null)
    {
        $this->analyse = $analyse;

        return $this;
    }

    /**
     * Get matiere
     *
     * @return \AppBundle\Entity\Article 
     */
    public function getAnalyse()
    {
        return $this->analyse;
    }

    /**
     * Remove objectifs
     *
     * @param \AppBundle\Entity\Objectif $objectifs
     */
    public function setObjectif($objectifs)
    {
        $this->objectif=$objectifs;
    }

    /**
     * Get objectifs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObjectif()
    {
        if($this->objectif!=null)
          return  $this->objectif;
      return $this->objectif=$this->getTitre();
    }

    /**
     * Add questions
     *
     * @param \AppBundle\Entity\Question $questions
     * @return Partie
     */
    public function addQuestion(\AppBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;

        return $this;
    }


    /**
     * Remove questions
     *
     * @param \AppBundle\Entity\Question $questions
     */
    public function removeQuestion(\AppBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }


    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {  
        if($this->auMoinsdeMemeQue!=null&&$this->auMoinsdeMemeQue!=$this)
             return $this->auMoinsdeMemeQue->getQuestions();
        return $this->questions;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getQcm()
    {
        if($this->auMoinsdeMemeQue!=null&&$this->auMoinsdeMemeQue!=$this)
            return $this->qcm= $this->auMoinsdeMemeQue->getId();
        return $this->qcm=$this->id;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getIsAvalable()
    {
        return $this->isAvalable;
    }

        /**
     * Set date
     *
     * @param \DateTime $date
     * @return Etape
     */
    public function setIsAvalable($isAvalable)
    {
        $this->isAvalable = $isAvalable;

        return $this;
    }
    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Etape
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
     * Add session
     *
     * @param \AppBundle\Entity\SessionConcours $session
     *
     * @return Partie
     */
    public function addSession(\AppBundle\Entity\SessionConcours $session)
    {
        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Remove session
     *
     * @param \AppBundle\Entity\SessionConcours $session
     */
    public function removeSession(\AppBundle\Entity\SessionConcours $session)
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
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Pub
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }  

       /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Question
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
 

    /**
     * Set index
     *
     * @param integer $index
     *
     * @return Partie
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Get index
     *
     * @return integer
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set containMath
     *
     * @param boolean $containMath
     *
     * @return Partie
     */
    public function setContainMath($containMath)
    {
        $this->containMath = $containMath;

        return $this;
    }

    /**
     * Get containMath
     *
     * @return boolean
     */
    public function getContainMath()
    {
        return $this->containMath;
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
        return $this->imageUrl;
    }

    /**
     * @return mixed
     */
    public function getFileEntity()
    {
        return $this->fileEntity;
    }

    /**
     * @param mixed $fileEntity
     */
    public function setFileEntity($fileEntity)
    {
        $this->fileEntity = $fileEntity;
    }

}
