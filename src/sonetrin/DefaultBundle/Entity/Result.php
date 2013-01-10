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
 * @ORM\Entity(repositoryClass="sonetrin\DefaultBundle\Repository\ResultRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Result
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
     *
     * @var integer $item 
     * 
     * @ORM\OneToMany(targetEntity="Item", mappedBy="result", cascade={"remove", "persist"})
     */
    private $item;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
 
    /**
     * Add item
     *
     * @param \sonetrin\DefaultBundle\Entity\Item $item
     * @return Result
     */
    public function addItem(\sonetrin\DefaultBundle\Entity\Item $item)
    {
        $this->item[] = $item;
    
        return $this;
    }

    /**
     * Remove item
     *
     * @param \sonetrin\DefaultBundle\Entity\Item $item
     */
    public function removeItem(\sonetrin\DefaultBundle\Entity\Item $item)
    {
        $this->item->removeElement($item);
    }

    /**
     * Get item
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItem()
    {
        return $this->item;
    }
}