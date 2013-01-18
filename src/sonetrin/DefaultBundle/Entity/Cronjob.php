<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cronjob
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Cronjob
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var search
     *
     * @ORM\OneToOne(targetEntity="Search", inversedBy="cronjob")
     */
    private $search;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency", type="string", length=255)
     */
    private $frequency;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;
    
    /**
     * @ORM\Column(name="last_run", type="datetime", nullable=true)
     */
    protected $lastRun;
    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\preUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set frequency
     *
     * @param string $frequency
     * @return Cronjob
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    
        return $this;
    }

    /**
     * Get frequency
     *
     * @return string 
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Cronjob
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }
    
    public function getLastRun()
    {
        return $this->lastRun;
    }

    public function setLastRun($lastRun)
    {
        $this->lastRun = $lastRun;
    }

    /**
     * Set search
     *
     * @param \sonetrin\DefaultBundle\Entity\Search $search
     * @return Cronjob
     */
    public function setSearch(\sonetrin\DefaultBundle\Entity\Search $search = null)
    {
        $this->search = $search;
    
        return $this;
    }

    /**
     * Get search
     *
     * @return \sonetrin\DefaultBundle\Entity\Search 
     */
    public function getSearch()
    {
        return $this->search;
    }
}