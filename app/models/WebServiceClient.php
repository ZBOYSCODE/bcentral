<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Soap\SoapClient;
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
        
        //$this->client = new nusoap_client($wsdl, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
        $this->$client = new SoapClient($wsdl);
        $param = array( 'arg0' => $tck);
        $result = $this->$client->RetrieveInteraction($param);
        //$result = $this->client->call('RetrieveInteraction', $param, '', '', false, true);

        return $result;
    }
}
