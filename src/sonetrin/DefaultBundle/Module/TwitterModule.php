<?php

namespace sonetrin\DefaultBundle\Module;

use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Module\SocialNetworkInterface;

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
    private $results_array;
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

        $lastItem = end($searchItems);

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
                $query .= '%20';
            }
        }

//die(var_dump($query));

        $until = $this->search->getEndDate()->format('Y-m-d');

        for ($page = 1; $page <= $this->pages; $page++)
        {
            $url = "http://search.twitter.com/search.json?q=" .
                    $query .
                    '&lang=' . $this->lang .
                    '&until=' . $until .
                    '&rpp=' . $this->rpp;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            $this->results_raw[] = curl_exec($ch);
        }

        curl_close($ch);

        $this->returnResults();
    }

    public function returnResults()
    {
        if (false === is_null($this->results_raw))
        {
            foreach ($this->results_raw as $result)
            {
                $result = json_decode($result);


                foreach ($result->results as $tweet)
                {
                    $this->results_array[] = array(
                        'id' => $tweet->id_str,
                        'date' => $tweet->created_at,
                        'user' => $tweet->from_user,
                        'text' => $tweet->text);
                }
            }
        }

        if (count($this->results_array) === 0 || $this->results_array == null)
        {
            return false;
        } else
        {
            return $this->results_array;
        }
    }

    public function saveResults()
    {
        if (count($this->results_array) >= 1)
        {
            $result = new Result($this->results_array);
            $result->setSearch($this->search);
            $result->setSocialNetwork($this->socialNetwork);

            $this->em->persist($result);
            $this->em->flush();

            return true;
        } else
        {
            return false;
        }
    }

}
