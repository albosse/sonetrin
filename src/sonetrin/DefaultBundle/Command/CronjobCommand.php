<?php

namespace sonetrin\DefaultBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use basecom\WrapperBundle\ContainerAware\ContainerAwareCommand;
use sonetrin\DefaultBundle\Module\GooglePlusModule;
use sonetrin\DefaultBundle\Module\TwitterModule;
use Symfony\Component\HttpFoundation\Response;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Module\FacebookModule;

class CronjobCommand extends ContainerAwareCommand
{

    private $output;

    protected function configure()
    {
        $this
                ->setName('sonetrin:cronjob:run')
                ->setDescription('Run Cronjobs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $em = $this->getEntityManager();
        $cronjobs = $em->getRepository('sonetrinDefaultBundle:Cronjob')->findBy(array('active' => true));

        foreach ($cronjobs as $cronjob)
        {
            $now = new \DateTime();
            $lastRun = $cronjob->getLastRun();

            if (is_null($lastRun))
            {
                $lastRun = new \DateTime('2000-01-01');
            }

            switch ($cronjob->getFrequency())
            {
                case 'hourly':
                    $mustRun = $lastRun->add(new \DateInterval('PT1H'));
                    break;

                case 'daily':
                    $mustRun = $lastRun->add(new \DateInterval('P1D'));
                    break;

                case 'monthly':
                    $mustRun = $lastRun->add(new \DateInterval('P1M'));
                    break;
            }

            if ($mustRun < $now)
            {
                $output->write('Performing search for "' . $cronjob->getSearch()->getName() . '"...');
                //perform cronjob
                $this->runAjaxAction($cronjob->getSearch()->getId());
                //update lastRun
                $cronjob->setLastRun($now);
                //Logfile
                $output->writeln('done');
            }
        }
        $em->flush();
        $output->writeln("All cronjobs finished.");
    }

    private function runAjaxAction($id)
    {
        $em = $this->getEntityManager();
        $search = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        foreach ($search->getSocialNetwork() as $sn)
        {
            switch ($sn->getName())
            {
                case 'twitter':
                    $status = $this->getTwitterResults($search);
                    break;
                case 'googleplus':
                    $status = $this->getGooglePlusResults($search);
                    break;
                case 'facebook':
                   $status = $this->getFacebookResults($search);
                break;
            }
        }

        $em->refresh($search);
        return $this->analyzeResultsAction($search);
    }

    private function getTwitterResults($search)
    {
        $em = $this->getEntityManager();
        $tm = new TwitterModule($em, $search);
        $tm->findResults();
    }

    private function getGooglePlusResults($search)
    {
        $em = $this->getEntityManager();
        $gpm = new GooglePlusModule($em, $search);
        $gpm->findResults();
    }
    
     private function getFacebookResults($search)
    {
        $em = $this->getDoctrine()->getManager();
        $fbm = new FacebookModule($em, $search);
        $fbm->findResults();
    }

    private function analyzeResultsAction(Search $search)
    {
        $this->output->write('analyzing results...');
        $em = $this->getEntityManager();

        $items = $em->getRepository('sonetrinDefaultBundle:Item')->findBy(array('search' => $search->getId(), 'sentiment' => null));

        $keywords = $em->getRepository('sonetrinDefaultBundle:Keyword')->findBy(array('language' => $search->getLanguage()));

        if (true === is_null($keywords))
        {
            $this->output->writeln('There are no keywords fitting the language for your search.');
            return;
        }

        foreach ($items as $item)
        {
            //if item already has a sentiment
            if (false === is_null($item->getSentiment()))
            {
                continue;
            }

            $message = $item->getMessage();

            //reset counter
            $pos = 0;
            $neg = 0;

            foreach ($keywords as $keyword)
            {
                if (true == preg_match('| [#]*' . preg_quote($keyword->getExpression()) . '\b|i', $message))
                {
                    if ($keyword->getAssociation() == 'positive')
                    {
                        $pos++;
                    } else
                    {
                        $neg++;
                    }
                }
            }
            if ($pos > $neg)
            {
                $item->setSentiment('positive');
            } elseif ($pos < $neg)
            {
                $item->setSentiment('negative');
            } else
            {
                $item->setSentiment('neutral');
            }
        }

        $em->flush();
        return new Response('finished');
    }

}

?>