<?php

namespace sonetrin\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * sonetrin\DefaultBundle\Entity\Keyword
 *
 * @ORM\Table(name="keyword")
 * @ORM\Entity(repositoryClass="sonetrin\DefaultBundle\Repository\KeywordRepository")
 */
class Keyword
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
     * @var string $english
     *
     * @ORM\Column(name="english", type="string", length=255)
     */
    private $english;
    
     /**
     * @var string $association
     *
     * @ORM\Column(name="association", type="string", length=255)
     */
    private $association;


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
     * Set english
     *
     * @param string $english
     * @return Keyword
     */
    public function setEnglish($english)
    {
        $this->english = $english;
    
        return $this;
    }

    /**
     * Get english
     *
     * @return string 
     */
    public function getEnglish()
    {
        return $this->english;
    }

    /**
     * Set german
     *
     * @param string $german
     * @return Keyword
     */
    public function setGerman($german)
    {
        $this->german = $german;
    
        return $this;
    }

    /**
     * Get german
     *
     * @return string 
     */
    public function getGerman()
    {
        return $this->german;
    }

    /**
     * Set association
     *
     * @param string $association
     * @return Keyword
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    
        return $this;
    }

    /**
     * Get association
     *
     * @return string 
     */
    public function getAssociation()
    {
        return $this->association;
    }
}