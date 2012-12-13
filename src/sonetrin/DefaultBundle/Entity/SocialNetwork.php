<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * sonetrin\DefaultBundle\Entity\SocialNetwork
 *
 * @ORM\Table(name="socialnetwork")
 * @ORM\Entity(repositoryClass="sonetrin\DefaultBundle\Repository\SocialNetworkRepository")
 */
class SocialNetwork
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var boolean $authRequired
     *
     * @ORM\Column(name="authRequired", type="boolean", options={"default" = 0}, nullable=true)
     */
    private $authRequired;

    /**
     * @var string $api_key
     *
     * @ORM\Column(name="api_key", type="string", length=255, nullable=true)
     */
    private $api_key;

    /**
     * @var string language
     *
     * @ORM\Column(name="language", type="string", length=2, options={"default" = "en"}, nullable=true)
     */
    private $language;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


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
     * @return SocialNetwork
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
     * Set url
     *
     * @param string $url
     * @return SocialNetwork
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
     * Set authRequired
     *
     * @param boolean $authRequired
     * @return SocialNetwork
     */
    public function setAuthRequired($authRequired)
    {
        $this->authRequired = $authRequired;
    
        return $this;
    }

    /**
     * Get authRequired
     *
     * @return boolean 
     */
    public function getAuthRequired()
    {
        return $this->authRequired;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return SocialNetwork
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return SocialNetwork
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set api_key
     *
     * @param string $apiKey
     * @return SocialNetwork
     */
    public function setApiKey($apiKey)
    {
        $this->api_key = $apiKey;
    
        return $this;
    }

    /**
     * Get api_key
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->api_key;
    }
}