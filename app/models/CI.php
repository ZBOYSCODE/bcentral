<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;

class CI extends Model
{
	public ConfigurationItem;
	public CIType;
	public Status;
	public AssetTag;
	public ConfigAdminGroup;
	public support.groups;

	public function getCompleteCIList()
	{

		$ws = new WebServiceClient();
		$response = (array)$ws->getCIList();	
		
	}
}