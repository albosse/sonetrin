<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Log
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
     * @ORM\ManyToOne(targetEntity="Result", inversedBy="log")
     */
    private $result;

     /**
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=false)
     * @param \DateTime $createdAt
     * @return Log
     */
    private $createdAt;
    
    
     /**
     * @var $notice
     *
     * @ORM\Column(name="notice", type="text")
     */
    private $notice;


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
     * Set notice
     *
     * @param string $notice
     * @return Log
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    
        return $this;
    }

    /**
     * Get notice
     *
     * @return string 
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set result
     *
     * @param \sonetrin\DefaultBundle\Entity\Result $result
     * @return Log
     */
    public function setResult(\sonetrin\DefaultBundle\Entity\Result $result = null)
    {
        $this->result = $result;
    
        return $this;
    }

    /**
     * Get result
     *
     * @return \sonetrin\DefaultBundle\Entity\Result 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set createdAt
     * 
     * @ORM\PrePersist()
     * 
     * @param \DateTime $createdAt
     * @return Log
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
}