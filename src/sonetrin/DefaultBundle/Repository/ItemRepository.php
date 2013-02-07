<?php

namespace sonetrin\DefaultBundle\Repository;

use Doctrine\ORM\EntityRepository;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;

/**
 * KeywordRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends EntityRepository
{

    public function findAllItemsBySearch(Search $search, $filter = false, $limit = false)
    {
        $query = $this->createQueryBuilder('i');
        $query->where('i.search = :id');
        $query->setParameter('id', $search->getId());

        if ($filter == 'positive' || $filter == 'negative')
        {
            $query->andWhere('i.sentiment = :filter');
            $query->orderBy('i.created');
            $query->setParameter('filter', $filter);
        }

        if ($limit != false){
            $query->setMaxResults($limit);
        }

        $result = $query->getQuery()->getResult();

        if ($filter == 'random'){
            $result = shuffle($result);
        }

        return $result;
    }

    public function findSentimentsForBarGraph(Search $search, $scale, $start = "0", $end = "0")
    {        
        $query = $this->createQueryBuilder('i');
        $query->where('i.search = :id');
       
        if($start != "0" && $end != "0"){
            $query->AndWhere('i.created between :start and :end');
            $query->setParameter('start', $start);
            $query->setParameter('end', $end);
        }
        elseif($start != "0" && $end == "0"){
            $query->AndWhere('i.created > :start');
            $query->setParameter('start', $start);
        }
        elseif($start == "0" && $end != "0"){
            $query->AndWhere('i.created < :end');
            $query->setParameter('end', $end);
        }
        
        $query->orderBy('i.created');
        $query->setParameter('id', $search->getId());
            
        $items = $query->getQuery()->getResult();
        $data = array();
        foreach ($items as $item)
        {
            if ($scale == 'day')
            {
                $itemDate = date('d.m.y', $item->getCreated()->getTimestamp());
            } elseif ($scale == "year")
            {
                $itemDate = date('Y', $item->getCreated()->getTimestamp());
            } else
            {
                $itemDate = date('m.y', $item->getCreated()->getTimestamp());
            }

            if (!isset($data[$itemDate]))
            {
                $data[$itemDate] = array('positive' => 0, 'negative' => 0, 'neutral' => 0);
            }

            switch ($item->getSentiment())
            {
                case 'positive':
                    $data[$itemDate]['positive']++;
                    break;

                case 'negative':
                    $data[$itemDate]['negative']++;
                    break;

                default:
                    $data[$itemDate]['neutral']++;
                    break;
            }
        }

        return $data;
    }

}
