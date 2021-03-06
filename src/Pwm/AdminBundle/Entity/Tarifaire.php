<?php

namespace Pwm\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarifaire
 *
 * @ORM\Table(name="price")
 * @ORM\Entity(repositoryClass="Pwm\AdminBundle\Repository\PriceRepository")
 */
class Tarifaire
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
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="starter", type="integer")
     */
    private $starter;

    /**
     * @var int
     *
     * @ORM\Column(name="standard", type="integer")
     */
    private $standard;

    /**
     * @var int
     *
     * @ORM\Column(name="premium", type="integer")
     */
    private $premium;

    /**
     * @var int
     *
     * @ORM\Column(name="starter_delay", type="integer", options={"default" : 10})
     */
    private $starterDelay;

    /**
     * @var int
     *
     * @ORM\Column(name="standard_delay", type="integer", options={"default" : 45})
     */
    private $standardDelay;

    /**
     * @var int
     *
     * @ORM\Column(name="premium_delay", type="integer", options={"default" : 100})
     */
    private $premiumDelay;


    /**
     * @var int
     *
     * @ORM\Column(name="starter_desc", type="text", options={"default" : "Cours, TD, et anciens sujets, évaluation"})
     */
    private $starterDesc;

    /**
     * @var int
     *
     * @ORM\Column(name="standard_desc", type="text", options={"default" : "Cours,TD, et anciens sujets, évaluation, assistance des professeurs."})
     */
    private $standardDesc;

    /**
     * @var int
     *
     * @ORM\Column(name="premium_desc", type="text",  options={"default" : "Accès à toutes les ressources, conseils et astuces par mail."})
     */
    private $premiumDesc;
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
     * @return Tarifaire
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
        if(!is_null($this->nom))
        return $this->nom .'> '.'standard->'.$this->standardDelay.'jrs:'.$this->standard.'FCFA,'.'premium->'.$this->premiumDelay.'jrs:'.$this->premium.'FCFA';
        return $this->nom ;
    }

    /**
     * Constructor
     */
    public function __construct()
    {

        $this->nom ='Sans nom';

    }
    /**
     * Set starter
     *
     * @param integer $starter
     *
     * @return Tarifaire
     */
    public function setStarter($starter)
    {
        $this->starter = $starter;

        return $this;
    }

    /**
     * Get starter
     *
     * @return int
     */
    public function getStarter()
    {
        return $this->starter;
    }

    /**
     * Set standard
     *
     * @param integer $standard
     *
     * @return Tarifaire
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }

    /**
     * Get standard
     *
     * @return int
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set premium
     *
     * @param integer $premium
     *
     * @return Tarifaire
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;

        return $this;
    }

    /**
     * Get premium
     *
     * @return int
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * Set starterDelay
     *
     * @param integer $starterDelay
     *
     * @return Tarifaire
     */
    public function setStarterDelay($starterDelay)
    {
        $this->starterDelay = $starterDelay;

        return $this;
    }

    /**
     * Get starterDelay
     *
     * @return integer
     */
    public function getStarterDelay()
    {
        return $this->starterDelay;
    }

    /**
     * Set standardDelay
     *
     * @param integer $standardDelay
     *
     * @return Tarifaire
     */
    public function setStandardDelay($standardDelay)
    {
        $this->standardDelay = $standardDelay;

        return $this;
    }

    /**
     * Get standardDelay
     *
     * @return integer
     */
    public function getStandardDelay()
    {
        return $this->standardDelay;
    }

    /**
     * Set premiumDelay
     *
     * @param integer $premiumDelay
     *
     * @return Tarifaire
     */
    public function setPremiumDelay($premiumDelay)
    {
        $this->premiumDelay = $premiumDelay;

        return $this;
    }

    /**
     * Get premiumDelay
     *
     * @return integer
     */
    public function getPremiumDelay()
    {
        return $this->premiumDelay;
    }

    /**
     * Set starterDesc
     *
     * @param string $starterDesc
     *
     * @return Tarifaire
     */
    public function setStarterDesc($starterDesc)
    {
        $this->starterDesc = $starterDesc;

        return $this;
    }

    /**
     * Get starterDesc
     *
     * @return string
     */
    public function getStarterDesc()
    {
        return $this->starterDesc;
    }

    /**
     * Set standardDesc
     *
     * @param string $standardDesc
     *
     * @return Tarifaire
     */
    public function setStandardDesc($standardDesc)
    {
        $this->standardDesc = $standardDesc;

        return $this;
    }

    /**
     * Get standardDesc
     *
     * @return string
     */
    public function getStandardDesc()
    {
        return $this->standardDesc;
    }

    /**
     * Set premiumDesc
     *
     * @param string $premiumDesc
     *
     * @return Tarifaire
     */
    public function setPremiumDesc($premiumDesc)
    {
        $this->premiumDesc = $premiumDesc;

        return $this;
    }

    /**
     * Get premiumDesc
     *
     * @return string
     */
    public function getPremiumDesc()
    {
        return $this->premiumDesc;
    }
}
