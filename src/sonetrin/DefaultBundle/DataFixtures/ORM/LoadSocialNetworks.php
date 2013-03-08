<?php

namespace sonetrin\DefaultBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use sonetrin\DefaultBundle\Entity\SocialNetwork;

class LoadSocialNetworks implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $sn = new SocialNetwork();
        $sn->setName("twitter");
        $sn->setUrl("http://search.twitter.com/search.json?q=");
        $sn->setAuthRequired(false);
        $sn->setDescription("Twitter");
        
        $manager->persist($sn);
        
        $sn2 = new SocialNetwork();
        $sn2->setName('googleplus');
        $sn2->setUrl("https://www.googleapis.com/plus/v1/activities?query=");
        $sn2->setAuthRequired(true);
        $sn2->setDescription("Twitter");
        $sn2->setApiKey("AIzaSyCrjO168nVV6RCsCMedcr5mEkO96P5bBSE");
       
        $manager->persist($sn2);
        
        $sn3 = new SocialNetwork();
        $sn3->setName('facebook');
        $sn3->setUrl("https://graph.facebook.com/search?q=");
        $sn3->setAuthRequired(false);
        $sn3->setDescription("Facebook");
       
        $manager->persist($sn3);

        $manager->flush();
    }
}

?>
