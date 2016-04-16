<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Nusoap\nusoap_client;

class WebServiceClient extends Model
{
    private $client;
    
    public function getTicket($tck)
    {
        $proxyhost       = '';
        $proxyport      = '';
        $proxyusername  = '';
        $proxypassword  = '';

        $wsdl = 'http://64.79.70.107:8080/raggApi/Servicedesk?wsdl';//TODO, esta direccion talvez deveria venir del config
        //require(__DIR__."\\..\\library\\Nusoap\\nusoap.php");
        $this->client = new nusoap_client($wsdl, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);

        $param = array( 'arg0' => $tck);
        $this->client->charencoding = false;

        $result = $this->client->call('RetrieveInteraction', $param, '', '', false, true);
        /*$this->$client = new SoapClient($servicio);
        $param = array('arg0' => $tck);
        $result = $this->$client->RetrieveInteraction($param);*/
        return $result;
    }
}
