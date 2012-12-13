<?php

namespace sonetrin\DefaultBundle\Module;
use sonetrin\DefaultBundle\Entity\Search;

interface SocialNetworkInterface
{
    public function __construct($em, Search $search);

    public function findResults();

    public function processResults();

}

?>
