<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
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
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="upload_dir", type="string", length=255, nullable=true)
     */
    private $uploadDir;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="thumnnail", type="string", length=255, nullable=true)
     */
    private $thumnnail;

    private $thumnnailUrl;

    private $file;

    private $tempFilename;

    private $baseDir;
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
     * Set url
     *
     * @param string $url
     * @return Image
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
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension(string $extension)
    {
        $this->extension = $extension;
    }

    public function setFile($file)
    {
        $this->file = $file;
        if (null !== $this->extension) {
            $this->tempFilename = $this->extension;
            $this->extension = null;
            $this->alt = null;
        }
         $this->preUpload();
    }


    public function preUpload()
    {
        if (null === $this->getFile()) {
            return;
        }
        $this->extension = $this->getFile()->guessExtension();
        $this->alt = $this->getFile()->getClientOriginalName();
        $this->filename = $this->getFile()->getFileName();
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }


    public function upload($baseDir,$uploadDir, $fileName=null)
    {
        $this->baseDir=$baseDir;
        $this->uploadDir=$uploadDir;
        $this->filename=$fileName!=null?$fileName:$this->filename;
        if (null === $this->getFile()) {
            return false;
        }
        if (null !== $this->tempFilename) {
             $oldFile =  $this->getUploadRootDir() . '/' . $this->filename ;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->filename
        );
        return true;
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFilename = $this->getUploadRootDir() . '/' . $this->filename;
    }


    public function remove($baseDir=__DIR__)
    {    $this->baseDir=$baseDir;
        if (file_exists($this->baseDir.$this->getWebPath())) {
            unlink($this->baseDir."/" .$this->getWebPath());
        }
    }


    protected function getUploadRootDir(): string
    {
        return $this->baseDir."/" . $this->getUploadDir();
    }


    public function getWebPath(): string
    {
        return $this->getUploadDir().$this->filename;
    }

    public function getThumbnailPath(): string
    {
        return $this->getUploadDir().$this->thumnnail;
    }
    /**
     * @return string
     */
    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    /**
     * @return string
     */
    public function getThumnnail(): string
    {
        return $this->thumnnail;
    }

    /**
     * @param string $thumnnail
     */
    public function setThumnnail(string $thumnnail)
    {
        $this->thumnnail = $thumnnail;
    }

    /**
     * @return mixed
     */
    public function getThumnnailUrl()
    {
        return $this->thumnnailUrl;
    }

    /**
     * @param mixed $thumnnailUrl
     */
    public function setThumnnailUrl($thumnnailUrl)
    {
        $this->thumnnailUrl = $thumnnailUrl;
    }

}
