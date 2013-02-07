<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Entity\Search;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Item
 *
 * @ORM\Table(name="result_item")
 * @ORM\Entity(repositoryClass="sonetrin\DefaultBundle\Repository\ItemRepository")
 */
class Item
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
     * @var string $message_id
     *
     * @ORM\Column(name="message_id",  type="string", length=255)
     */
    private $message_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="date")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;
    
       /**
     * @var string
     *
     * @ORM\Column(name="author_id", type="string", length=255, nullable=true)
     */
    private $author_id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;
    
     /**
     * @var string
     *
     * @ORM\Column(name="message_url", type="string", length=255)
     */
    private $message_url;
    
    

    /**
     * @var string
     *
     * @ORM\Column(name="sentiment", type="string", length=255, nullable=true)
     */
    private $sentiment;
    
    /**
     * @var Result result
     * 
     * @ORM\ManyToOne(targetEntity="Result", inversedBy="item")
     */
    private $result;
    
    /**
     * @var Result result
     * 
     * @ORM\ManyToOne(targetEntity="Search")
     */
    private $search;
    
    public function getSearch()
    {
        return $this->search;
    }

    public function setSearch(Search $search)
    {
        $this->search = $search;
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
     * Set created
     *
     * @param \DateTime $created
     * @return Item
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Item
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Item
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sentiment
     *
     * @param string $sentiment
     * @return Item
     */
    public function setSentiment($sentiment)
    {
        $this->sentiment = $sentiment;
    
        return $this;
    }

    /**
     * Get sentiment
     *
     * @return string 
     */
    public function getSentiment()
    {
        return $this->sentiment;
    }
    
    public function getResult()
    {
        return $this->result;
    }

    public function setResult(Result $result)
    {
        $this->result = $result;
    }
    
    public function getMessage_id()
    {
        return $this->message_id;
    }

    public function setMessage_id($message_id)
    {
        $this->message_id = $message_id;
    }


    /**
     * Set message_id
     *
     * @param string $messageId
     * @return Item
     */
    public function setMessageId($messageId)
    {
        $this->message_id = $messageId;
    
        return $this;
    }

    /**
     * Get message_id
     *
     * @return string 
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * Set author_id
     *
     * @param string $authorId
     * @return Item
     */
    public function setAuthorId($authorId)
    {
        $this->author_id = $authorId;
    
        return $this;
    }

    /**
     * Get author_id
     *
     * @return string 
     */
    public function getAuthorId()
    {
        return $this->author_id;
    }

    /**
     * Set message_url
     *
     * @param string $messageUrl
     * @return Item
     */
    public function setMessageUrl($messageUrl)
    {
        $this->message_url = $messageUrl;
    
        return $this;
    }

    /**
     * Get message_url
     *
     * @return string 
     */
    public function getMessageUrl()
    {
        return $this->message_url;
    }
}