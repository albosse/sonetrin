<?php

namespace sonetrin\DefaultBundle\Module;

use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Module\SocialNetworkInterface;
use sonetrin\DefaultBundle\Entity\Item;

/**
 * Module to perform all Twitter specific operations
 */
class TwitterModule implements SocialNetworkInterface
{

    private $em;
    private $search;
    //json
    private $results_raw;
    //array with date:user:text
    private $rpp = 100;
    private $pages = 1;
    private $lang = 'en';
    private $socialNetwork;

    public function __construct($em, Search $search)
    {
        $this->em = $em;
        $this->search = $search;
        $this->socialNetwork = $this->em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findOneBy(array('name' => 'Twitter'));
    }

    /**
     * 
     * @param type $search
     * @return type An array of tweet results
     */
    public function findResults()
    {
        if (!isset($this->search))
        {
            throw new \Exception("No search available!");
        }

        $searchItems = explode(',', $this->search->getName());
        $query = '';

        $lastItem = trim(end($searchItems));

        foreach ($searchItems as $item)
        {
            $item = trim($item);
            if ($item[0] == '#')
            {
                $query .= '%23' . str_replace('#', '', $item);
            } elseif ($item[0] == '@')
            {
                $query .= '%40' . str_replace('#', '', $item);
            } else
            {
                $query .= '' . $item;
            }
            
            if ($lastItem != $item)
            {
                $query .= '+OR+';
            }
        }



        $until = $this->search->getEndDate()->format('Y-m-d');

        for ($page = 1; $page <= $this->pages; $page++)
        {
            $url = $this->socialNetwork->getUrl() .
                    $query .
                    '&lang=' . $this->lang .
                    '&until=' . $until .
                    '&rpp=' . $this->rpp;
            
//            die(var_dump($url));
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            $this->results_raw[] = curl_exec($ch);
        }

        curl_close($ch);

        $this->processResults();
    }

    public function processResults()
    {
        if (false === is_null($this->results_raw))
        {
            $result_model = new Result();
            $result_model->setSearch($this->search);
            $result_model->setSocialNetwork($this->socialNetwork);
            
            foreach ($this->results_raw as $result)
            {
                $result = json_decode($result);

                foreach ($result->results as $tweet)
                {
                    $item = new Item();
                    $item->setAuthor($tweet->from_user);
                    $item->setCreated(new \DateTime($tweet->created_at));
                    $item->setMessage($tweet->text);
                    $item->setMessage_id($tweet->id_str);
                    $item->setResult($result_model);
                    $result_model->addItem($item);
                }
            }           
            $this->em->persist($result_model);
            $this->em->flush();
        }

        return true;
    }
}
