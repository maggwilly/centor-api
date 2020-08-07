<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SolrBundle\Entity\SolrSearchResult;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use FS\SolrBundle\Doctrine\Annotation as Solr;

/**
 * Resultat
 * @Solr\Document()
 * @Solr\SynchronizationFilter(callback="indexHandler")
 * @ORM\Table(name="resultat_concours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResultatRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Resultat extends SolrSearchResult
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
     * @ORM\Column(name="url", type="text")
     */
    protected $url;

    /**
     * @var string
     * @Solr\Field(type="string")
     * @ORM\Column(name="image", type="text",  nullable=true)
     */
    private $imageUrl;
    /**
     * @var string
     * @Solr\Field(type="string")
     * @ORM\Column(name="description", type="text", length=255)
     */
    protected $description;

    /**
     * @var \DateTime
     * @Solr\Field(type="date", getter="format('Y-m-d\TH:i:s.z\Z')")
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    protected $date;


      private $file;

    /**
     *@ORM\PostLoad()
     */
    public function indexHandler()
    {
        $this->description=$this->description;
        $this->title=$this->description;
        $this->resultType='Resultat';
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
     * Set url
     *
     * @param string $url
     *
     * @return Resultat
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Resultat
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Resultat
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
     * Constructor
     */
    public function __construct()
    {
        $this->date=new \DateTime();
    }
    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getDate();
    }


    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->getDescription();
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return 'Resultat';
    }

          /**
           * Sets file.
           *
           * @param UploadedFile $file
           */
          public function setFile(UploadedFile $file = null)
          {
              $this->file = $file;
          }

          /**
           * Get file.
           *
           * @return UploadedFile
           */
          public function getFile()
          {
              return $this->file;
          }
}

