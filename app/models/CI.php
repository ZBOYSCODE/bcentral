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
	}
}