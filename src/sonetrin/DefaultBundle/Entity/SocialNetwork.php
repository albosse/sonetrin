<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * sonetrin\DefaultBundle\Entity\SocialNetwork
 *
 * @ORM\Table(name="socialNetwork")
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
     * @var boolean $hashtags
     *
     * @ORM\Column(name="hashtags", type="boolean")
     */
    private $hashtags;

    /**
     * @var boolean $authRequired
     *
     * @ORM\Column(name="authRequired", type="boolean", options={"default" = 0})
     */
    private $authRequired;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string language
     *
     * @ORM\Column(name="language", type="string", length=2, options={"default" = "en"})
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
     * Set hashtags
     *
     * @param boolean $hashtags
     * @return SocialNetwork
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
     * Set username
     *
     * @param string $username
     * @return SocialNetwork
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return SocialNetwork
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
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

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }


}