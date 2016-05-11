<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Catalog extends Model
{   
    public function getServiceCatalog($name)
    {
        $wsClient = new WebServiceClient();
        $respnse = $wsClient->getCatalogStepOne($name);
        $result = array();
        foreach ($respnse as $key => $value) 
        {
            $temp = (array)$value;
            $temp = (array)$temp['Name'];
            array_push($result, $temp['_']);
        }
        return $result;
    }
}