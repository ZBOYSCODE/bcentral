<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;

class Contact extends Model
{
	public $contact;
	public $lastname;
	public $firstname;
	public $workphone;
	public $location;
	public $email;
	public $company;
	public $activo;
	public $tipousuario;
	public $fullname;
	public $tipocontacto;

	public function getContactList()
	{
		$ws = new WebServiceClient();
		$response = (array)$ws->getContactList();
		if($response['returnCode'] == "0")
		{
			$contactList = array();
			$response = $response['keys'];
			foreach ($response as $obj => $val) 
			{
				$val = (array)$val;
				$val = (array)$val['ContactName'];
				array_push($contactList, $val['_']);
			}
			return $contactList;
		}
		else
		{
			return array();
		}

	}
}