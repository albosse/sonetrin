<?php

namespace sonetrin\DefaultBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * KeywordRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SearchRepository extends EntityRepository
{

    public function findAllWithResults($id)
    {
        $query = $this->createQueryBuilder('s')
//            ->select('s','r')   
                ->leftJoin('sonetrinDefaultBundle:Result', 'r', Expr\Join::WITH, 'r.search = s.id')
                ->where('s.id = :id')
                ->setParameter('id', $id)
                ->getQuery();


        return $query->getResult();
    }

    public function findExistingSearchNames()
    {
        $searches =  $this->createQueryBuilder('s')
                ->select('s.name')
                ->getQuery()
                ->getResult();
        
        $names = array();
        foreach($searches as $search){
            $names[] = $search['name'];
        }     
        return $names;
    }

}
