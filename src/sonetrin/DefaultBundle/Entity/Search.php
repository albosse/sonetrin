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
     * @var \DateTime $endDate
     *
     * @ORM\Column(name="endDate", type="date", nullable=true)
     */
    private $endDate;


    /**
     * @ORM\OneToMany(targetEntity="Result", mappedBy="search", cascade={"remove"}, orphanRemoval=true)
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
     * @ORM\OneToOne(targetEntity="Cronjob", mappedBy="search", cascade={"remove"})
     */
    protected $cronjob;
    
    
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
     * Remove all results
     * @ORM\PreUpdate()
     */
    public function removeAllResults()
    {
        $results = $this->getResult();

        foreach ($results as $result)
        {
            $this->removeResult($result);
        }
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