<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;

class WebServiceClient extends Model
{
    private $client;
    
    public function getTicket($tck)
    {
        $proxyhost       = '';
        $proxyport      = '';
        $proxyusername  = '';
        $proxypassword  = '';

        
        
        //$this->client = new nusoap_client($wsdl, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
        

        //cargamos el SoapClient desde el injector de dependencia
        $this->client = $this->di->get('soapclient');
        $param = array( 'arg0' => $tck);
        $result = $this->client->RetrieveInteraction($param);

        //$result = $this->client->call('RetrieveInteraction', $param, '', '', false, true);

        return $result;
    }
}
