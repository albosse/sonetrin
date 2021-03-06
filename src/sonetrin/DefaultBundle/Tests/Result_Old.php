<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\SocialNetwork;

/**
 * sonetrin\DefaultBundle\Entity\Result
 *
 * @ORM\Table(name="result")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Result_Old
{

    /**
     * 
     * @param $network The network name
     * @param $file The file to create
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=255)
     * @Assert\NotBlank
     */
    private $path;

    /**
     *  @var integer $search
     * 
     * @ORM\ManyToOne(targetEntity="Search", inversedBy="result")
     */
    private $search;

    /**
     * @var integer $socialNetwork
     * 
     * @ORM\ManyToOne(targetEntity="SocialNetwork")
     */
    private $socialNetwork;

    /**
     * @var integer $entriesCount
     * 
     * @ORM\Column(name="entriesCount", type="integer")
     */
    private $entriesCount;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     *
     * @ORM\PrePersist()
     *
     * @param \DateTime $createdAt
     * @return Search
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     * @param \DateTime $updatedAt
     * @return Search
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     *
     * @var File $file 
     */
    private $file;

    /**
     * 
     * @return File
     */
    public function getFile()
    {
        if (file_exists($this->getAbsolutePath()))
        {
            return json_decode(file_get_contents($this->getAbsolutePath()));
        }
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Result
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
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
     * Set path
     *
     * @param string $path
     * @return Result
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    //File Upload specific code

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'results';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file)
        {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.txt';
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file)
        {
            return;
        }
        if (!is_dir($this->getUploadRootDir()))
        {
            mkdir($this->getUploadRootDir());
        }
        return file_put_contents($this->getUploadRootDir() . '/' . $this->path, json_encode($this->file));
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath())
        {
            unlink($file);
        }
    }

    /**
     * Set search
     *
     * @param Search $search
     * @return Result
     */
    public function setSearch(Search $search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return Search 
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set socialNetwork
     *
     * @param SocialNetwork $socialNetwork
     * @return Result
     */
    public function setSocialNetwork(SocialNetwork $socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    /**
     * Get socialNetwork
     *
     * @return SocialNetwork 
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }

    public function getEntriesCount()
    {
        return $this->entriesCount;
    }

    /**
     *
     * @ORM\PrePersist()
     *
     * @param \DateTime $createdAt
     * @return Search
     */
    public function setEntriesCount()
    {
        $this->entriesCount = count($this->file);

        return $this;
    }

}