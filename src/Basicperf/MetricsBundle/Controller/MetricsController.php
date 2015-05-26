<?php

namespace Basicperf\MetricsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MetricsController extends Controller
{
    public function indexAction()
    {
		return $this->render('BasicperfMetricsBundle:Metrics:index.html.twig');
    }

    public function AddDataAction()
    {
    	       $pagetype = array('0' => 'home', '1' => 'content', '2' => 'diapo','3' => 'video','4' => 'groupement','5' => 'list');

		foreach ($pagetype as $key => $value) {

			$access_token = "5b3d65b96d62b186b5cd2c0443d3e4912fcb373a6846d92ccfd3dc4c0f78088f";
			$headers = array('Authorization: Bearer ' . $access_token);
			$cul_url_basilic = "https://pestoapi.basilic.io/v0/data/metrics.json?application_id=83&granularity=1D&segment_name=pagetype&segment_value=".$value."";
			$ch = curl_init($cul_url_basilic);

			// Will return the response, if false it print the response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// Execute
			$result = curl_exec($ch);

			$obj = json_decode($result, true);
			var_dump($obj); die();
			$items_times = array();
			$count_time = 0;
			$array_count_time = count($obj["data"]);

			for ($i = 0; $i < $array_count_time; ++$i) {
			   $items_times[$count_time++] = $obj['data'][$i]['t'];
		/*	   $items_timimg[$count_time++] = $obj['data'][$i]['nta']['loa_e']['p50']*/
			}
			
			$items_times[] = asort($items_times);
			$date_start = current($items_times);
			array_pop($items_times);
		 	$date_end = end($items_times)-1;

			foreach ($items_times as $key => $value_time) {
				 if ($value_time == $date_end){
				 		$key_time = $key;
				 }
			}

			$timing = ($obj['data'][$key_time]['nta']['loa_e']['p50']); 

			$month_end = substr($date_end, 4,-2);
			$year_end = substr($date_end, 0,-4);
			$day_end = substr($date_end, 6);
			$text_end = $day_end."-".$month_end."-".$year_end; 

			$result_total = $timing / 1000;
			$result_total = round($result_total,1);

			curl_close($ch);
		}
		return $this->render('BasicperfMetricsBundle:Metrics:index.html.twig', array(
     		 'data' => $result_total,));
    }
}
