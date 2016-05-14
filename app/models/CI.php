<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;

class CI extends Model
{
	public $ConfigurationItem;
	public $CIType;
	public $Status;
	public $AssetTag;
	public $ConfigAdminGroup;
	public $supportgroups;

	public function getCompleteCIList()
	{
/*
		$ws = new WebServiceClient();
		$response = $ws->getCIList();	
		$response = $response['instance'];
		$ciList = array();
		foreach ($response as $key => $val) 
		{
			$val = (array)$val;
			$configItem = (array)$val['ConfigurationItem'];
			$configItem = $configItem['_'];
			$tag = (array)$val['AssetTag'];
			$tag = $tag['_'];
			if(array_key_exists($tag, $ciList))
			{
				array_push($ciList[$tag], $configItem);
			}
			else
			{
				$ciList[$tag] = array();
				array_push($ciList[$tag], $configItem);
			}
		}
		return $ciList;
		*/
		return array('servicio afectado 1' => array('ci1','ci2','ci3'),
                            'servicio afectado 2' => array('ci4','ci5','ci6'),
                            'servicio afectado 3' => array('ci7','ci8','ci9'));
	}
}