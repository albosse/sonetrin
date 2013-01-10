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
        $results = $search->getResult();
        $items = array();

        foreach ($results as $result)
        {
            $query = $this->createQueryBuilder('r');
            $query->where('r.result = :id');
            $query->setParameter('id', $result->getId());

            if ($filter == 'positive' || $filter == 'negative')
            {
                $query->andWhere('r.sentiment = :filter');
                $query->setParameter('filter', $filter);
            }

            if ($limit != false)
            {
                $query->setMaxResults($limit);
            }

            $items = array_merge($items, $query->getQuery()->getResult());

            if ($filter == 'random')
            {
                shuffle($items);
            }
        }

        $array = ($items);

        return $array;
    }

    public function findSentimentsForBarGraph(Search $search, $scale)
    {
        $query = $this->createQueryBuilder('i');
        $query->where('i.search = :id');
        $query->orderBy('i.created');
        $query->setParameter('id', $search->getId());

        $items = $query->getQuery()->getResult();
        $data = array();
        foreach ($items as $item)
        {
            if ($scale == 'day')
            {
                $itemDate = date('d.m.y', $item->getCreated()->getTimestamp());
            } elseif ($scale == "month")
            {
                $itemDate = date('m.y', $item->getCreated()->getTimestamp());
            }
            elseif ($scale == "year")
            {
                $itemDate = date('y', $item->getCreated()->getTimestamp());
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

            if ($data[$itemDate]['positive'] == 0 && $data[$itemDate]['negative'] == 0)
            {
                unset($data[$itemDate]);
            }
        }

        return $data;
    }

}
