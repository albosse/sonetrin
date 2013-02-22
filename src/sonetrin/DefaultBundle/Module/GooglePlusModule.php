<?php

namespace sonetrin\DefaultBundle\Module;

use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Module\SocialNetworkInterface;
use sonetrin\DefaultBundle\Entity\Item;

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
        if (true === $this->socialNetwork->getAuthRequired())
        {
            $key = $this->socialNetwork->getApiKey();
        }

        if (!isset($this->search))
        {
            throw new \Exception("No search available!");
        }

        $searchItems = explode(',', $this->search->getName());
        $query = '';

        foreach ($searchItems as $item)
        {
            $query .= ' ' . trim($item);
        }

        $nextPageToken = '';

        for ($page = 1; $page <= $this->cycles; $page++)
        {
            $url = $this->socialNetwork->getUrl() . urlencode($query);
            $url .= '&lang=' . $this->search->getLanguage();
//            $url .=  '&until=' . $until;
            $url .= '&maxResults=' . $this->maxResults;

            if (isset($key))
            {
                $url .= '&key=' . $key;
            }

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

            $nextPageToken = isset($result->nextPageToken) ? $result->nextPageToken : '';
        }

        $this->results_raw = array_values($this->results_raw);

        curl_close($ch);

        $this->processResults();
    }

    public function processResults()
    {
        if (true === is_null($this->results_raw))
        {
            return false;
        }

        $old_res = $this->em->getRepository('sonetrinDefaultBundle:Result')
                ->findOneBy(array('search' => $this->search,
            'socialNetwork' => $this->socialNetwork));


        if (false === is_null($old_res))
        {
            $result_model = $old_res;
        } else
        {
            $result_model = new Result();
            $result_model->setSearch($this->search);
            $result_model->setSocialNetwork($this->socialNetwork);
        }

        foreach ($this->results_raw as $results)
        {

            if (false === isset($results->items))
            {
                continue;
            }

            foreach ($results->items as $tweet)
            {
                $item_exists = $this->em->getRepository('sonetrinDefaultBundle:Item')
                        ->findOneBy(array('message_id' => $tweet->id,
                    'search' => $this->search));

                if (('' != strip_tags($tweet->object->content) && (true === is_null($item_exists))))
                {
                    $item = new Item();
                    $item->setAuthor($tweet->actor->displayName);
                    $item->setAuthorId($tweet->actor->id);
                    $item->setCreated(new \DateTime($tweet->published));
                    $item->setMessage(strip_tags($tweet->object->content));
                    $item->setMessage_id($tweet->id);
                    $item->setResult($result_model);
                    $item->setSearch($this->search);
                    $item->setMessageUrl($tweet->url);

                    $result_model->addItem($item);
                }
            }
        }

        $this->em->persist($result_model);
        $this->em->flush();
    }
}

