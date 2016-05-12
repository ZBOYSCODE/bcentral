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
        $icons = $this->di->get('catalog-icons');
        foreach ($respnse as $key => $value) 
        {
            $temp = (array)$value;
            $temp = (array)$temp['Name'];
            if(array_key_exists($temp['_'], $icons))
            {
                $tempIcon = $icons[$temp['_']];
            }
            else
            {
                $tempIcon = $icons['default'];
            }
            array_push($result, array('name' => $temp['_'], 'icon' => $tempIcon));
        }
        return $result;
    }
}