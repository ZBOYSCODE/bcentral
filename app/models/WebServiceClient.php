<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;

class WebServiceClient extends Model
{
    private $client;
    public function getTicketsByUser($usr)
    {
        $this->client = $this->di->get('soapclient-servicedesk');
        $param = array(
                'keys' => array(
                    '_' => array(
                            'CallID' => ''
                        ),
                    'query' => "(callback.contact=&quot;" . $usr ."&quot; or contact.name~=&quot;" . $usr ."&quot;) and incident.id IS NOT NULL"
                )    
            );
        $response = (array)$this->client->RetrieveInteractionList($param);
        $response['request'] = $this->client->__getLastRequest();
        $response['headers'] = $this->client->__getLastRequestHeaders();
        return $response;
    }

    public function getRequerimentList()
    {
        $this->client = $this->di->get('soapclient-catalog');
        /*$param = array(
                'keys' => array(
                    'name' => ''
                ),
                'instance' => array(
                        'Active' => 'true'
                    ),
                'messages' => ''    
            );*/
        $param = new \stdClass;
        $param->model = new \stdClass;
        $param->model->keys = new \stdClass;
        $param->model->keys->name = '';
        $param->model->instance = new \stdClass;
        $param->model->instance->Active = 'true';
        $param->model->messages = '';
        $response = $this->client->RetrieveSvcCatalogKeysList($param);
        //$response['request'] = $this->client->__getLastRequest();
        //$response['headers'] = $this->client->__getLastRequestHeaders();
        return $response;
    }

    public function getTicket($tck)
    {
        $proxyhost       = '';
        $proxyport      = '';
        $proxyusername  = '';
        $proxypassword  = '';

        
        
        //$this->client = new nusoap_client($wsdl, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
        $tck = $this->f_remove_odd_characters($tck);

        //cargamos el SoapClient desde el injector de dependencia
        $this->client = $this->di->get('soapclient-servicedesk');
        $param = array( 'model' => array(
                            'keys' => array(
                                'CallID' => $tck
                            ),
                            'instance' => '',/*array(
                                'CallID' => '',
                                'ServiceRecipient' => '',
                                'Urgency' => '',
                                'OpenTime' => '',
                                'UpdateTime' => '',
                                'OpenedBy' => '',
                                'Description' => array(
                                    'Description' => ''
                                ),
                                'AffectedService' => '',
                                'CallOwner' => '',
                                'Status' => '',
                                'NotifyBy' => '',
                                'Solution' => array(
                                    'Solution' => ''
                                ),
                                'Category' => '',
                                'CallerDepartment' => '',
                                'CallerLocation' => '',
                                'CloseTime' => '',
                                'ClosedBy' => '',
                                'KnowledgeCandidate' => '',
                                'SLAAgreementID' => '',
                                'Priority' => '',
                                'ServiceContract' => '',
                                'SiteCategory' => '',
                                'TotalLossOfService' => '',
                                'Area' => '',
                                'Subarea' => '',
                                'ProblemType' => '',
                                'FailedEntitlement' => '',
                                'Location' => '',
                                'CauseCode' => '',
                                'ClosureCode' => '',
                                'Company' => '',
                                'ReportedByContact' => '',
                                'ReportedByDifferentContact' => '',
                                'ReportedByPhone' => '',
                                'ReportedByExtension' => '',
                                'ReportedByFax' => '',
                                'ContactEmail' => '',
                                'LocationFullName' => '',
                                'ContactFirstName' => '',
                                'ContactLastName' => '',
                                'ContactTimeZone' => '',
                                'EnteredByESS' => '',
                                'SLABreached' => '',
                                'NextSLABreach' => '',
                                'Contact' => '',
                                'Update' => array(
                                    'Update' => ''
                                ),
                                'Impact' => '',
                                'neededbytime' => '',
                                'approvalstatus' => '',
                                'folder' => '',
                                'subscriptionItem' => '',
                                'AffectedCI' => '',
                                'Title' => '',
                                'MetodoOrigen' => '',
                                'attachments' => array(
                                    'attachments' => ''
                                )
                            ),*/
                            'messages' => ''/*array(
                                'messages' => ''
                            )*/
                        )
                    );
        $result = $this->client->RetrieveInteraction($param);
        //$result = $this->client->call('RetrieveInteraction', $param, '', '', false, true);
        $result = (array) $result;
        $result['request'] = $this->client->__getLastRequest();
        $result['headers'] = $this->client->__getLastRequestHeaders();
        $result['params'] = $param;
        return $result;
    }
    public function getContactList()
    {
        $this->client = $this->di->get('soapclient-config');
        $param = array( 'model' => array(
                            'keys' => array(
                                'ContactName' => ''
                            ),
                            'instance' => array(
                                'activo' => 'true'
                            ),
                            'messages' => ''/*array(
                                'messages' => ''
                            )*/
                        )
                    );
        $result = $this->client->RetrieveContactKeysList($param);
        
        return $result;
    }
    public function updateTicket($CallID, $Update)
    {
        $this->client = $this->di->get('soapclient-servicedesk');
        $param = array( 'model' => array(
                            'keys' => array(
                                'CallID' => $CallID
                            ),
                            'instance' => array(
                                /*'CallID' => '',
                                'ServiceRecipient' => '',
                                'Urgency' => '',
                                'OpenTime' => '',
                                'UpdateTime' => '',
                                'OpenedBy' => '',
                                'Description' => array(
                                    'Description' => ''
                                ),
                                'AffectedService' => '',
                                'CallOwner' => '',
                                'Status' => '',
                                'NotifyBy' => '',
                                'Solution' => array(
                                    'Solution' => ''
                                ),
                                'Category' => '',
                                'CallerDepartment' => '',
                                'CallerLocation' => '',
                                'CloseTime' => '',
                                'ClosedBy' => '',
                                'KnowledgeCandidate' => '',
                                'SLAAgreementID' => '',
                                'Priority' => '',
                                'ServiceContract' => '',
                                'SiteCategory' => '',
                                'TotalLossOfService' => '',
                                'Area' => '',
                                'Subarea' => '',
                                'ProblemType' => '',
                                'FailedEntitlement' => '',
                                'Location' => '',
                                'CauseCode' => '',
                                'ClosureCode' => '',
                                'Company' => '',
                                'ReportedByContact' => '',
                                'ReportedByDifferentContact' => '',
                                'ReportedByPhone' => '',
                                'ReportedByExtension' => '',
                                'ReportedByFax' => '',
                                'ContactEmail' => '',
                                'LocationFullName' => '',
                                'ContactFirstName' => '',
                                'ContactLastName' => '',
                                'ContactTimeZone' => '',
                                'EnteredByESS' => '',
                                'SLABreached' => '',
                                'NextSLABreach' => '',
                                'Contact' => '',*/
                                'Update' => array(
                                    'Update' => $Update
                                ),
                                /*'Impact' => '',
                                'neededbytime' => '',
                                'approvalstatus' => '',
                                'folder' => '',
                                'subscriptionItem' => '',
                                'AffectedCI' => '',
                                'Title' => '',*/
                                'Reitera' => 'Si'/*,
                                'MetodoOrigen' => '',
                                'attachments' => array(
                                    'attachments' => ''
                                )*/
                            ),
                            'messages' => ''/*array(
                                'messages' => ''
                            )*/
                        )
                    );
        $result = $this->client->UpdateInteractionRequest($param);

        //$result = $this->client->call('RetrieveInteraction', $param, '', '', false, true);
        $result = (array) $result;
        return $result;
    }

    public function getCIList()
    {
        $this->client = $this->di->get('soapclient-config');
        $param = array('keys' => array(
                            '_' => '',/*array(
                                    'ConfigurationItem' => ''
                                ),*/
                            'query' => "Status=&quot;In Use&quot;"
                        )                    
                    );
        $response = (array)$this->client->RetrieveDeviceList($param);
        $response['request'] = $this->client->__getLastRequest();
        $response['headers'] = $this->client->__getLastRequestHeaders();
        return $response;
    }

    function f_remove_odd_characters($string){
        $string = str_replace("\n","[NEWLINE]",$string);
        $string=htmlentities($string);
        $string=preg_replace('/[^(\x20-\x7F)]*/','',$string);
        $string=html_entity_decode($string);       
        $string = str_replace("[NEWLINE]","\n",$string);
        return $string;
      }
}
