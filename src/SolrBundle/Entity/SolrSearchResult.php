<?php


namespace SolrBundle\Entity;

use FS\SolrBundle\Doctrine\Annotation as Solr;
use JMS\Serializer\Annotation\Accessor;
/**
 * @Solr\Document()
 */
class SolrSearchResult
{
    /**
     * @Solr\Field(type="string")
     */
    protected $title;

    /**
     * @Solr\Field(type="string", getter="format('Y-m-d\TH:i:s.z\Z')")
     */
    protected $date;
    /**
     * @Solr\Field(type="string")
     */
    protected $description;

    /**
     * @Solr\Field(type="string")
     */
    protected $resultType;
    /**
     * @Solr\Id
     * @Accessor(getter="getNumericId",setter="setId")
     */
     protected $id;
    /**
     * @Solr\Field(type="string")
     */
    protected $url;

    /**
     * @Solr\Field(type="string")
     */
    private $serie;

    /**
     * @Solr\Field(type="string")
     */
    private $dateMax;
    /**
     * @Solr\Field(type="string")
     */
    private $dateConcours;


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $content
     */
    public function setDescription($content)
    {
        $this->description = $content;
    }

    /**
     * @return mixed
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param mixed $serie
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    /**
     * @return mixed
     */
    public function getDateMax()
    {
        return $this->dateMax;
    }

    /**
     * @param mixed $dateMax
     */
    public function setDateMax($dateMax)
    {
        $this->dateMax = $dateMax;
    }

    /**
     * @return mixed
     */
    public function getDateConcours()
    {
        return $this->dateConcours;
    }

    /**
     * @param mixed $dateConcours
     */
    public function setDateConcours($dateConcours)
    {
        $this->dateConcours = $dateConcours;
    }



    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getResultType()
    {
        return $this->resultType;
    }

    /**
     * @param mixed $type
     */
    public function setResultType($type)
    {
        $this->resultType = $type;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    public function getNumericId()
    {
        $property=$this->id;
        if (($pos = strrpos($property, '_')) !== false) {
            return substr($property, 0, $pos);
        }

        return $property;
    }
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        if (($pos = strrpos($id, '_')) !== false) {
            return  intval($this->id=substr($id, $pos+1, strlen ($id)));
        }
        $this->id = intval($id);
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }


}