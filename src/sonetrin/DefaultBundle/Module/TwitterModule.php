<?php

namespace sonetrin\DefaultBundle\Module;

use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Module\SocialNetworkInterface;
use sonetrin\DefaultBundle\Entity\Item;
use sonetrin\DefaultBundle\Entity\Log;

/**
 * Module to perform all Twitter specific operations
 */
class TwitterModule implements SocialNetworkInterface
{
    private $em;
    /** @var type Search */
    private $search;
    /** @var type SocialNetwork */
    private $socialNetwork;
    private $results_raw;     
    private $rpp = 100;
    private $pages = 15;
 
    public function __construct($em, Search $search)
    {
        $this->em = $em;
        $this->search = $search;
        $this->socialNetwork = $this->em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findOneBy(array('name' => 'Twitter'));
    }

    /**
     * @param type $search
     * @return type An array of tweet results
     */
    public function findResults()
    {
        if (!isset($this->search)){
            throw new \Exception("No search available!");
        }

        $searchItems = explode(',', $this->search->getName());
        $query = '';
        
        foreach ($searchItems as $item){
            $query .= ' ' . trim($item);           
        }       
        
        for ($page = 1; $page <= $this->pages; $page++)
        {
            $url = $this->socialNetwork->getUrl() .
                    urlencode($query) .
                    '&lang=' .  $this->search->getLanguage() .
//                    '&until=' . $until .
                    '&rpp=' . $this->rpp .
                    '&page=' . $page;

            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $this->results_raw[] = json_decode(curl_exec($ch));
            

        }
        curl_close($ch);
        $this->processResults();
    }

    public function processResults()
    {
        //funktioniert so nicht
        if (true === is_null($this->results_raw))
        {
            return false;
        }

        $old_res = $this->em->getRepository('sonetrinDefaultBundle:Result')
                ->findOneBy(array('search' => $this->search, 'socialNetwork' => $this->socialNetwork));

           
        if (false === is_null($old_res))
        {
            $result_model = $old_res;
        } else
        {
            $result_model = new Result();
            $result_model->setSearch($this->search);
            $result_model->setSocialNetwork($this->socialNetwork);
        }
        
        //count all inserts
        $countInserts = 0;
            
        //for each page
        foreach ($this->results_raw as $result)
        {   
            //if no results for this page were found
            if(false === isset($result->results)){
                continue;
            }
                        
            //each tweet
            foreach ($result->results as $tweet)
            {
                $item_exists = $this->em->getRepository('sonetrinDefaultBundle:Item')
                        ->findOneBy(array('message_id' => $tweet->id_str,
                                              'search' => $this->search));
                
                if (true === is_null($item_exists))
                {                       
                    $item = new Item();
                    $item->setAuthor($tweet->from_user);
                    $item->setAuthorId($tweet->from_user_id);
                    $item->setCreated(new \DateTime($tweet->created_at));
                    $item->setMessage(strip_tags($tweet->text));
                    $item->setMessage_id($tweet->id_str);                  
                    $item->setResult($result_model);
                    $item->setSearch($this->search);
                    
                    $url = "https://twitter.com/" . $tweet->from_user  . "/status/" .  $tweet->id_str;
                    $item->setMessageUrl($url);
                    
                    $result_model->addItem($item);
                    $countInserts++;    
                }
            }
        }
        
        $log = new Log();
        $log->setResult($result_model);
        $log->setNotice('Items added: ' . $countInserts);
        $result_model->addLog($log);
        
        $this->em->persist($result_model);
        $this->em->flush();

        return true;
    }

}
