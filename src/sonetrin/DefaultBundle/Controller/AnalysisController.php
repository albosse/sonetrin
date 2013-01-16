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
        
        return array();
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
    
    /**
     * @Route("/bar/{search}/{scale}/{start}/{end}", name="result_bar_graph", defaults={"scale" = "month", "start" = "0", "end" = "0"})
     * @Template()
     */
    public function showBarGraphAction(Search $search, $scale, $start, $end)
    {
       include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph.php");
       include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph_bar.php");
        
       $em = $this->getDoctrine()->getManager();
       $sentimentCount = $em->getRepository('sonetrinDefaultBundle:Item')
                ->findSentimentsForBarGraph($search, $scale, $start, $end);
        
        foreach($sentimentCount as $date)
        {
            $datay1[] = $date['positive'];
            $datay2[] = $date['negative'];
            $datay3[] = $date['neutral'];
        }   
      
        $graph = new \Graph(500,300,'auto');    
        $graph->ClearTheme();
        $graph->SetFrame(false);
        $graph->SetScale("textlin");
        $graph->SetShadow();
        $graph->img->SetMargin(40,30,40,40);
        $graph->xaxis->SetTickLabels(array_keys($sentimentCount));

//        $graph->xaxis->title->Set('Year ' . $search->getCreatedAt()->format('Y'));
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

//        $graph->title->Set('Group bar plot');
        $graph->title->SetFont(FF_FONT1,FS_BOLD);
                  
        $bplot1 = new \BarPlot($datay1);
        $bplot2 = new \BarPlot($datay2);
        $bplot3 = new \BarPlot($datay3);

        $bplot1->SetFillColor("#CCF5CC");
        $bplot2->SetFillColor("#FFB2B2");
        $bplot3->SetFillColor("#EDEDF3");

        $bplot1->SetShadow();
        $bplot2->SetShadow();
        $bplot3->SetShadow();

        $bplot1->SetShadow();
        $bplot2->SetShadow();
        $bplot3->SetShadow();

        $gbarplot = new \GroupBarPlot(array($bplot1,$bplot2,$bplot3));
        $gbarplot->SetWidth(0.6);
        $graph->Add($gbarplot);

        $graph->Stroke();
         return array();
    }  
}
