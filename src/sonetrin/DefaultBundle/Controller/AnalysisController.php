<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/analysis")
 */
class AnalysisController extends Controller
{

    /**
     * @Route("/", name="analysis_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/analysis/line", name="analysis_line_graph")
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
     * @Route("/analysis/cake", name="analysis_cake_graph")
     * 
     */
    public function cake()
    {
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph.php");
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph_pie.php");

        $data = array(10, 50, 30, 10);

        $graph = new \PieGraph(300, 200, "auto");
        $graph->SetShadow();

        $graph->title->Set("A simple Pie plot");
        $graph->title->SetFont(FF_FONT1, FS_BOLD);

        $p1 = new \PiePlot($data);
        $p1->SetLegends($gDateLocale->GetShortMonth());
        $p1->SetCenter(0);

        $graph->Add($p1);
        $graph->Stroke();
    }

}
