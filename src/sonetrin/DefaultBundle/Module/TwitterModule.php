<?php

namespace sonetrin\DefaultBundle\Module;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;

/**
 * Module to perform all Twitter specific operations
 */
class TwitterModule

{   private $em; 
    private $search;
    //json
    private $results_raw;
    //array with date:user:text
    private $results_array;
    private $rpp;
    private $pages;
    private $lang;
    private $socialNetwork;
    
    public function __construct($em, Search $search) 
    {     
        $this->em = $em; 
        $this->search = $search;
        $this->rpp = 100;
        $this->pages = 1;
        $this->lang = 'en';
        $this->socialNetwork = $this->em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findOneBy(array('name' => 'Twitter'));       
    }   
        
    /**
     * 
     * @param type $search
     * @return type An array of tweet results
     */
    public function findTwitterResults() 
    {
        if(!isset($this->search))
        {
            throw new \Exception("No search available!");
        }
        

        
        $until = $this->search->getEndDate()->format('Y-m-d');
        
        /**
        *
        * Set back to 15 if production 
        */
        for ($page = 1; $page <= $this->pages; $page++) 
        {
            $url = "http://search.twitter.com/search.json?q=%23" . str_replace('#', '', $this->search->getName()) . '&lang=' . $this->lang;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            $this->results_raw[] = curl_exec($ch);                    
        }

        curl_close($ch);

        if (false === is_null($this->results_raw)) 
        {
            foreach ($this->results_raw as $result) {
                $result = json_decode($result);

                foreach ($result->results as $tweet) {
                    $this->results_array[] = array('date' => $tweet->created_at,
                                                   'user' => $tweet->from_user,
                                                   'text' => $tweet->text );
                }
            }
        } 
        
        return $this->results_array;
    }
    
    public function saveResult()
    {  
        $result = new Result($this->results_array);
        $result->setSearch($this->search);
        $result->setSocialNetwork($this->socialNetwork);

        $this->em->persist($result);
        $this->em->flush();
        
        return true;                     
    }
}
