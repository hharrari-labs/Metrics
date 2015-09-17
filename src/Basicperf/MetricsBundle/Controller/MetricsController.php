<?php

namespace Basicperf\MetricsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Basicperf\MetricsBundle\Entity\Frontend;

class MetricsController extends Controller
{
    public function indexAction()
    {
    	$repository = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('BasicperfMetricsBundle:Frontend');
	   	$metrics = $repository->findAll();
      	return $this->render('BasicperfMetricsBundle:Metrics:index.html.twig', array(
		'metrics' => $metrics
		));
    }

    public function getMetricsAction()
    {

		$date_from = date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
		$date_to = date(DATE_ATOM, mktime(23, 59, 0, date("m"), date("d")-1, date("Y")));
		$access_token = "155843694f57bfa1c47780c53e1a5c72f4ee5821b9ff10f3a9e9df96f5b8ca69";
		$headers = array('Authorization: Bearer ' . $access_token);
		$cul_url_basilic = "https://pestoapi.basilic.io/v0/data/top.json?application_id=83&segment_name=pagetype&limit=500&date_from=".$date_from."&date_to=".$date_to."";
		$ch = 	curl_init($cul_url_basilic);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);

		$obj = json_decode($result, true);
		$page_name_check = array('_article_','_groupement_','_home_','_list_','_diapo_','_diaporama_');

		foreach ($obj['data'] as $key => $obj_value) 
		{ 
			if (preg_match('/' . implode('|', $page_name_check) . '/', $obj_value['ss'][0]['k'])){
				if (isset($obj_value['nta']['loa_e']['p50'])) {
					$data_sort[] = array('pagetype' => $obj_value['ss'][0]['k'],'time' => ($obj_value['nta']['loa_e']['p50']/1000));
				}
			}		
		}
		asort($data_sort);
		curl_close($ch);
		$date_from = substr(date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))), 0,-15);

		$repository = $this->getDoctrine()
						   ->getManager()
						   ->getRepository('BasicperfMetricsBundle:Frontend');

		$metrics = $repository->findOneBy(array('date' => $date_from));

		if($metrics === null)
		{
			
			foreach ($data_sort as $key => $value) {
				$data_metrics = new Frontend();
				$data_metrics->setloadtime($value['time']);
				$data_metrics->setpagetype($value['pagetype']);
				$data_metrics->setdate($date_from);
				$em = $this->getDoctrine()->getManager();
				$em->persist($data_metrics);
				$em->flush();
			}
		}
		$metrics = $repository->findAll();
		return $this->render('BasicperfMetricsBundle:Metrics:index.html.twig', array(
		'metrics' => $metrics
		));
    }
}
