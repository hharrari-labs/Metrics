<?php
// src/Basicperf/MetricsBundle/Basilic/ApiBasilic.php
namespace Basicperf\MetricsBundle\Basilic;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Basicperf\MetricsBundle\Entity\Frontend;

class ApiBasilic
{
	/**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;
	protected $token;
	protected $em;

	public function __construct(Registry $doctrine, $token)
	{
		$this->doctrine      = $doctrine;
		$this->token_basilic = $token;		
	}

	public function GetMetrics($date)
	{	
		if ($date === '') {
				$date=1;
			}	
		$date_from = date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d")-$date, date("Y")));
		$date_to = date(DATE_ATOM, mktime(23, 59, 0, date("m"), date("d")-$date, date("Y")));
		$access_token = $this->token_basilic;
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
					$data_sort[] = array('date' => $date, 'pagetype' => $obj_value['ss'][0]['k'],'time' => ($obj_value['nta']['loa_e']['p50']/1000));
				}
			}		
		}
		asort($data_sort);
		curl_close($ch);
		return $data_sort;
	}
	
	public function SetMetrics($data_sort)
		{	
			
			$date_from = substr(date(DATE_ATOM, mktime(0, 0, 0, date("m"), date("d")-$data_sort[0]['date'], date("Y"))), 0,-15);

			$em = $this->doctrine
					   ->getEntityManager()
		   		   	   ->getRepository('BasicperfMetricsBundle:Frontend');

			$metrics = $em->findOneBy(array('date' => $date_from));

			if($metrics === null)
			{
				
				foreach ($data_sort as $key => $value) {
					$data_metrics = new Frontend();
					$data_metrics->setloadtime($value['time']);
					$data_metrics->setpagetype($value['pagetype']);
					$data_metrics->setdate($date_from);
					$emd = $this->doctrine->getManager();
					$emd->persist($data_metrics);
					$emd->flush();
				}
			}
		return $metrics = $em->findAll();
	}	
}