<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * sonetrin\DefaultBundle\Entity\Search
 *
 * @ORM\Table(name="search")
 * @ORM\Entity(repositoryClass="sonetrin\DefaultBundle\Repository\SearchRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Search
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var SocialNetwork $socialNetwork
     * 
     * @ORM\ManyToMany(targetEntity="SocialNetwork")
     * @ORM\JoinTable(name="search_networks",
     *      joinColumns={@ORM\JoinColumn(name="search_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="network_id", referencedColumnName="id")}
     *      )
     */
    private $socialNetwork;
    
    /**
     * @var \DateTime $startDate
     *
     * @ORM\Column(name="startDate", type="date")
     */
    private $startDate;

    /**
     * @var \DateTime $endDate
     *
     * @ORM\Column(name="endDate", type="date")
     */
    private $endDate;

    /**
     * @var boolean $semantic
     *
     * @ORM\Column(name="semantic", type="boolean")
     */
    private $semantic;
    
     /**
     * @var boolean $hashtags
     *
     * @ORM\Column(name="hashtags", type="boolean", nullable=true)
     */
    private $hashtags;
    
     /**
     * @ORM\OneToMany(targetEntity="Result", mappedBy="search", cascade={"remove"})
     */
    private $result;
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @ORM\prePersist
     */
    public function prePersist() {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\preUpdate
     */
    public function preUpdate() {
        $this->updatedAt = new \DateTime();
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
     * Set name
     *
     * @param string $name
     * @return Search
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Search
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Search
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
     * Set semantic
     *
     * @param boolean $semantic
     * @return Search
     */
    public function setSemantic($semantic)
    {
        $this->semantic = $semantic;
    
        return $this;
    }

    /**
     * Get semantic
     *
     * @return boolean 
     */
    public function getSemantic()
    {
        return $this->semantic;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->socialNetwork = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add socialNetwork
     *
     * @param sonetrin\DefaultBundle\Entity\SocialNetwork $socialNetwork
     * @return Search
     */
    public function setSocialNetwork(\sonetrin\DefaultBundle\Entity\SocialNetwork $socialNetwork)
    {
        $this->socialNetwork[] = $socialNetwork;
    
        return $this;
    }

    /**
     * Remove socialNetwork
     *
     * @param sonetrin\DefaultBundle\Entity\SocialNetwork $socialNetwork
     */
    public function removeSocialNetwork(\sonetrin\DefaultBundle\Entity\SocialNetwork $socialNetwork)
    {
        $this->socialNetwork->removeElement($socialNetwork);
    }

    /**
     * Get socialNetwork
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }

    /**
     * Add socialNetwork
     *
     * @param sonetrin\DefaultBundle\Entity\SocialNetwork $socialNetwork
     * @return Search
     */
    public function addSocialNetwork(\sonetrin\DefaultBundle\Entity\SocialNetwork $socialNetwork)
    {
        $this->socialNetwork[] = $socialNetwork;
    
        return $this;
    }

    /**
     * Add result
     *
     * @param sonetrin\DefaultBundle\Entity\Result $result
     * @return Search
     */
    public function addResult(\sonetrin\DefaultBundle\Entity\Result $result)
    {
        $this->result[] = $result;
    
        return $this;
    }

    /**
     * Remove result
     *
     * @param sonetrin\DefaultBundle\Entity\Result $result
     */
    public function removeResult(\sonetrin\DefaultBundle\Entity\Result $result)
    {
        $this->result->removeElement($result);
    }

    /**
     * Get result
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getResult()
    {
        return $this->result;
    }
    
    public function hasResult()
    {
        $count = $this->getResult()->count();
        
        return $count > 0 ? true : false;
    }

    /**
     * Set hashtags
     *
     * @param boolean $hashtags
     * @return Search
     */
    public function setHashtags($hashtags)
    {
        $this->hashtags = $hashtags;
    
        return $this;
    }

    /**
     * Get hashtags
     *
     * @return boolean 
     */
    public function getHashtags()
    {
        return $this->hashtags;
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
}