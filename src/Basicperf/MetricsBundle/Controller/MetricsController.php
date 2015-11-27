<?php

namespace Basicperf\MetricsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Basicperf\MetricsBundle\Entity\Frontend;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MetricsController extends Controller
{
	/**
     * @Route("/", name="home")
     * @Template
	*/
    public function indexAction()
    {
    	$repository = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('BasicperfMetricsBundle:Frontend');
	   	$metrics = $repository->FindBy([], ['date' => 'ASC']);
        $type_article = $repository->FindBytype("article");
        $type_groupement = $repository->FindBytype("groupement");
        $type_home = $repository->FindBytype("home");
        $type_list = $repository->FindBytype("list");
        $type_diaporama = $repository->FindBytype("diaporama");
      	return array(
            'metrics'         => $metrics,
            'type_article'    => $type_article, 
            'type_groupement' => $type_groupement, 
            'type_home'       => $type_home, 
            'type_list'       => $type_list ,
            'type_diaporama'  => $type_diaporama 
                         );
    }

    /**
     * @Route("/get_metrics", name="get_metrics")
     */
    public function getMetricsAction()
    {

    	$metrics_basilic = $this->container->get('basicperf_metrics.apibasilic');
    	$get_metrics = $metrics_basilic->GetMetrics(1);
    	$set_metrics = $metrics_basilic->SetMetrics($get_metrics);
		return array('metrics' => $set_metrics);    	
    }
    	
    /**
     * @Route("/data/json/{pagetype}", name="data_json")
     * @Template("BasicperfMetricsBundle:Metrics:data.html.twig")
     */
    public function getDataJsonAction($pagetype)
    {
        $metrics = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('BasicperfMetricsBundle:Frontend')
                           ->findByPagetype($pagetype);
        return array('metrics' => $metrics);                   
    }    
}
