<?php

namespace sonetrin\DefaultBundle\Module;

use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Module\SocialNetworkInterface;

//require_once '../Resources/api/googleapi/src/Google_Client.php';
//require_once '../Resources/api/googleapi/src/contrib/Google_PlusService.php';
//include_once 'TwitterModule.php';

/**
 * Module to perform all Twitter specific operations
 */
class GooglePlusModule implements SocialNetworkInterface
{

    private $em;
    private $search;
    //json
    private $results_raw;
    //array with date:user:text
    private $results_array;
    private $lang = 'en';
    private $socialNetwork;
    private $maxResults = 20;
    private $cycles = 20;

    public function __construct($em, Search $search)
    {
        $this->em = $em;
        $this->search = $search;
        $this->socialNetwork = $this->em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findOneBy(array('name' => 'googleplus'));
    }

    /**
     * 
     * @param type $search
     * @return type An array of tweet results
     */
    public function findResults()
    {
        $key = "AIzaSyCrjO168nVV6RCsCMedcr5mEkO96P5bBSE";

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
            }elseif ($item[0] == '@')
            {
                $query .= '%40' . str_replace('#', '', $item);
            }  
            else
            {
                $query .= '' . $item;
            }
            if ($lastItem != $item)
            {
                $query .= '%20';
            }
        }
        $until = $this->search->getEndDate()->format('Y-m-d');

        $nextPageToken = '';

        for ($page = 1; $page <= $this->cycles; $page++)
        {
            $url = "https://www.googleapis.com/plus/v1/activities?query=" .
                    $query .
                    //                    '&lang=' . $this->lang .
                    //                    '&until=' . $until .
//                    '&maxResults=100' . 
                    '&key=' . $key;

            if (isset($nextPageToken))
            {
                $url .= '&pageToken=' . $nextPageToken;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $result = json_decode(curl_exec($ch));

            $this->results_raw[] = $result;

            $nextPageToken = $result->nextPageToken;
        }

        $this->results_raw = array_values($this->results_raw);

        curl_close($ch);

        $this->returnResults();
    }

    public function returnResults()
    {
        if (false === is_null($this->results_raw))
        {
//            $result = json_decode($this->results_raw);
            foreach ($this->results_raw as $results)
            {
                foreach ($results->items as $tweet)
                {
                    if ('' == strip_tags($tweet->object->content))
                    {
                        continue;
                    }
                    $this->results_array[] = array(
                        'id' => $tweet->id,
                        'date' => $tweet->published,
                        'user' => $tweet->actor->displayName,
                        'title' => $tweet->title,
                        'text' => strip_tags($tweet->object->content));
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
