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
      	return array('metrics' => $metrics);
    }

    /**
     * @Route("/get_metrics", name="_get_metrics")
     * @Template("BasicperfMetricsBundle:Metrics:index.html.twig")
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
