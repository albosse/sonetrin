<?php

namespace sonetrin\DefaultBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use sonetrin\DefaultBundle\Entity\Keyword;

class LoadKeywordData  implements FixtureInterface {

    public function load(ObjectManager $manager)
    {
        $keyword = new Keyword();
        $keyword->setEnglish('good');
        $keyword->setGerman('gut');
        $manager->persist($keyword);
        
        $manager->flush();
    }

}

?>
