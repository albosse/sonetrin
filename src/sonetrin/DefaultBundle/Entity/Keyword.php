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
     * @var string $expression
     *
     * @ORM\Column(name="expression", type="string", length=255)
     */
    private $expression;
    
    /**
     * @var string $language
     *
     * @ORM\Column(name="language", type="string", length=2)
     */
    private $language;
    
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

    /**
     * Set expression
     *
     * @param string $expression
     * @return Keyword
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
    
        return $this;
    }

    /**
     * Get expression
     *
     * @return string 
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Keyword
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
}