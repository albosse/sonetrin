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
class FacebookModule implements SocialNetworkInterface
{

    private $em;

    /** @var type Search */
    private $search;

    /** @var type SocialNetwork */
    private $socialNetwork;
    private $results_raw;
    private $pages = 20;
    private $accessToken;

    public function __construct($em, Search $search)
    {
        $this->em = $em;
        $this->search = $search;
        $this->socialNetwork = $this->em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findOneBy(array('name' => 'facebook'));
        $this->accessToken = $this->getAccessToken();
    }

    /**
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

        foreach ($searchItems as $item)
        {
            $query .= ' ' . trim($item);
        }

        $nextPageToken = '';

        for ($page = 1; $page <= $this->pages; $page++)
        {
            if (isset($nextPageToken) && $nextPageToken != '')
            {
                $url = $nextPageToken;
            } else
            {
                $url = $this->socialNetwork->getUrl() .
                        urlencode($query) .
                        '&type=post';
                if (isset($this->accessToken))
                {
                    $url.= '&access_token=' . $this->accessToken;
                }
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $result = json_decode(curl_exec($ch));
            $this->results_raw[] = $result;

            $nextPageToken = isset($result->paging->next) ? $result->paging->next : '';

            if (false === isset($result->paging->next))
            {
                break;
            }
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
            if (false === isset($result->data))
            {
                continue;
            }

            //each tweet
            foreach ($result->data as $tweet)
            {
                $item_exists = $this->em->getRepository('sonetrinDefaultBundle:Item')
                        ->findOneBy(array('message_id' => $tweet->id,
                    'search' => $this->search));

                if (true === is_null($item_exists))
                {
                    try
                    {
                        $item = new Item();
                        $item->setAuthor($tweet->from->name);
                        $item->setAuthorId($tweet->from->id);
                        $item->setCreated(new \DateTime($tweet->created_time));
                        $item->setMessage(strip_tags($tweet->message));
                        $item->setMessage_id($tweet->id);
                        $item->setResult($result_model);
                        $item->setSearch($this->search);

                        $urlArray = explode('_', $tweet->id);
                        $item->setMessageUrl('http://www.facebook.com/' . $urlArray[0] . '/posts/' . $urlArray[1]);

                        $result_model->addItem($item);
                        $countInserts++;
                    } catch (\Exception $e)
                    {
                        
                    }
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

    private function getAccessToken()
    {
        $url = "https://graph.facebook.com/oauth/access_token?client_id=569606039730365&client_secret=137d69d3b4352ce3dc1ac0a9d29f78b8&grant_type=client_credentials";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($ch);

        preg_match('|access_token=(.*)|', $result, $matches);

        if (isset($matches[1]))
        {
            return $matches[1];
        } else
        {
            return '';
        }
    }

}
