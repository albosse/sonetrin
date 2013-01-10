<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use sonetrin\DefaultBundle\Entity\Search;

/**
 * @Route("/analysis")
 */
class AnalysisController extends Controller
{
    
     /**
     * @Route("/cake/{search}", name="result_cake_graph")
     * 
     */
    public function cakeAction(Search $search)
    {
        $em = $this->getDoctrine()->getManager();
        $sentimentCount = $em->getRepository('sonetrinDefaultBundle:Result')
                ->findRecordsSentiments($search->getId());
        
        
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph.php");
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph_pie.php");

        $data = array($sentimentCount['positive'],
            $sentimentCount['negative'],
            $sentimentCount['neutral']);

        $graph = new \PieGraph(400, 400);
        $graph->ClearTheme();
        $graph->SetShadow();
        $graph->SetFrame(false);
        $graph->SetMargin(10,10,10,10);
        $graph->title->SetFont(FF_FONT1, FS_BOLD, 20);     
        $graph->legend->SetPos(0.5, 0.8, 'center', 'bottom');
        $graph->legend->SetColumns(3);
        $graph->legend->SetFont(FF_FONT1, FS_BOLD, 24);

        $p1 = new \PiePlot($data);
        $p1->SetLegends(array('positive', 'negative', 'neutral'));
        $p1->SetSliceColors(array('#CCF5CC','#FFB2B2','#EDEDF3'));  
//        $p1->SetSize(0.3);
        $p1->SetCenter(0.5, 0.35);


        $graph->Add($p1);
        $graph->Stroke();
    }

    /**
     * @Route("/line/{search}", name="result_line_graph")
     * 
     */
    public function showLineGraph()
    {
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph.php");
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph_line.php");

        // Die Werte der 2 Linien in ein Array speichern
        $ydata = array();
        $ydata2 = array();

        for ($i = 1; $i <= 10; $i++)
        {
            $ydata[] = rand(1, 100);
            $ydata2[] = rand(1, 100);
        }


        // Grafik generieren und Grafiktyp festlegen
        $graph = new \Graph(300, 200, "auto");
        $graph->SetScale("textlin");

        // Die Zwei Linien generieren 
        $lineplot = new \LinePlot($ydata);

        $lineplot2 = new \LinePlot($ydata2);

        // Die Linien zu der Grafik hinzufügen
        $graph->Add($lineplot);
        $graph->Add($lineplot2);

        // Grafik Formatieren
        $graph->img->SetMargin(40, 20, 20, 40);
        $graph->title->Set("Example 4");
        $graph->xaxis->title->Set("X-title");
        $graph->yaxis->title->Set("Y-title");

        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

        $lineplot->SetColor("blue");
        $lineplot->SetWeight(2);

        $lineplot2->SetColor("orange");
        $lineplot2->SetWeight(2);

        $graph->yaxis->SetColor("red");
        $graph->yaxis->SetWeight(2);
        $graph->SetShadow();

        // Grafik anzeigen
        $graph->Stroke();
    }
}
