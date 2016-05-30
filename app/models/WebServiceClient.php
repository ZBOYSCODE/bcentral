<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Dispatcher\Exception;

class WebServiceClient extends Model
{
    private $client;
    public function getFields($catalogItem)
    {
        $this->client = $this->di->get('soapclient-catalog');
        $param = array(
                   'keys' => array(
                        'Name' => $catalogItem
                    )
                );
        $response = (array)$this->client->RetrieveSvcCatalogList($param);
        return (array) $response['instance'];
    }

    public function getTicketsByUser($usr)
    {
        $query = 'callback.contact="' . $usr . '" or contact.name="' . $usr . '"';//"callback.contact=&quot;" . $usr ."&quot; and contact.name=&quot;" . $usr ."&quot;";
        
        $this->client = $this->di->get('soapclient-servicedesk');
        if($this->client == false)
        {
            return null;
        }
        $param = array(
                'keys' => array(
                    '_' => array(
                            'CallID' => ''
                        ),
                    'query' => $query//$query_escaped
                )    
            );
        try
        {
            $response = (array)$this->client->RetrieveInteractionList($param);
        }
        catch (Exception $e)
        {
            $response = null;
        }
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
        if($this->client == false)
        {
            throw new Exception("Error Processing Request", 2);
        }
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
        /*$param = array(
                'keys' => array(
                        'Number' => $tck,
                        'negdatestamp' => '',
                        'TheNumber' => ''
                    )
            );*/
        return $result;
    }

    public function getTicketTrace($tck)
    {
        $param = array(
                'keys' => array(
                        'Number' => $tck,
                        'negdatestamp' => '',
                        'TheNumber' => ''
                    )
            );
        $this->client = $this->di->get('soapclient-catalog');
        if($this->client == false)
        {
            throw new Exception("Error Processing Request", 2);
        }
        $result = $this->client->RetrieveActivityServiceMgtList($param);
        $result = (array)$result;
        if($result['returnCode'] != '0')
        {
            return array();
        }
        if(array_key_exists('TheNumber', $result['instance']))
        {
            $temp = array();
            array_push($temp, $result['instance']);
            return $temp;
        }
        return $result['instance'];
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
    public function getContact($name)
    {
        $this->client = $this->di->get('soapclient-config');
        $param = array( 'model' => array(
                            'keys' => array(
                                'ContactName' => $name
                            ),
                            'instance' => '',
                            'messages' => ''/*array(
                                'messages' => ''
                            )*/
                        )
                    );
        $result = $this->client->RetrieveContact($param);

        return (array)$result;
    }

    public function getUsername($name)
    {
        $this->client = $this->di->get('soapclient-config');
        $param = array( 'model' => array(
                            'keys' => array(
                                'ContactName' => ''
                            ),
                            'instance' => array(
                                    'IdOperador' => $name
                                ),
                            'messages' => ''/*array(
                                'messages' => ''
                            )*/
                        )
                    );
        $result = (array)$this->client->RetrieveContact($param);
        $status = $result['returnCode'];
        if($status != '0')
        {
            return null;
        }
        $result = (array)$result['model'];
        $result = (array)$result['instance'];
        $result = (array)$result['ContactName'];
        return $result['_'];
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
        $result = $this->client->UpdateInteraction($param);

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
                            'query' => 'Status="In Use"'
                        )                    
                    );
        $response = (array)$this->client->RetrieveDeviceList($param);
        return $response;
    }

    public function getCatalogStepOne($option)
    {
        $this->client = $this->di->get('soapclient-catalog');
        if($this->client == false)
        {
            return null;
        }
        $param = array('keys' => array(
                            '_' => '',/*array(
                                    'ConfigurationItem' => ''
                                ),*/
                            'query' => 'active="true" and Parent="' . $option . '"'
                        )                    
                    );
        $response = (array)$this->client->RetrieveSvcCatalogList($param);
        return $response['instance'];
    }
    public function getCatalogStepTwo($option)
    {
        $this->client = $this->di->get('soapclient-catalog');
        $param = array(
                        'model' => array(
                            'keys' => array(
                                'Name' => ''
                            ),
                            'instance' => array(
                                'Parent' => array(
                                    'Parent' => $option
                                ),
                                'Active' => 'true'
                            ),
                            'messages' => ''
                        )                
                    );
        $response = (array)$this->client->RetrieveSvcCatalogKeysList($param);
        return $response;
    }
    public function CreateRequestInteraction($form)
    {
        $this->client = $this->di->get('soapclient-catalog');
        if($this->client == false)
        {
            return null;
        }
        if($form['fileName'] != '')
        {
            $attach = array(
                    '_' => $form['fileContent'],
                    'href' => '<![CDATA[<' . $form['fileName'] . '>]]>',
                    'action' => 'add',
                    'name' => $form['fileName']
                );    
        }
        else
        {
            $attach = '';
        }
        
        $param = array(
                'model' => array(
                    'keys' => array(
                        'CartId' => ''
                    ),
                    'instance' => array(
                        'Service' => $form['ci'],
                        'CallbackContactName' => $form['contact'],
                        'cartItems' => array(
                            'cartItems' => array(
                                'type' => 'Structure',
                                '_' => array(
                                    'ItemName' => $form['catalog']['subarea'],
                                    'Quantity' => '1',
                                    'RequestedFor' => $this->di->get('test-user'),
                                    'RequestedForType' => 'individual'
                                )
                            )
                        ),
                        //'NeededByTime' => $form['hasta'],
                        'ContactName' => $form['contact'],
                        'Urgency' => $form['urgency'],
                        'Title' => $form['title'],
                        'Purpose' => array(
                            'Purpose' => $form['description']
                        ),
                        'attachments' => array(
                            'attachments' => $attach
                        )
                    )
                )
            );
        $response = $this->client->CreateSRCInteractionViaOneStep($param);
        return (array)$response;
    }

     public function CreateRequestSol($form)
    {
        $this->client = $this->di->get('soapclient-servicedesk');
        if($this->client == false)
        {
            return null;
        }
        if($form['fileName'] != '')
        {
            $attach = array(
                    '_' => $form['fileContent'],
                    'href' => '<![CDATA[<' . $form['fileName'] . '>]]>',
                    'action' => 'add',
                    'name' => $form['fileName']
                );    
        }
        else
        {
            $attach = '';
        }
        $contact = new Contact();
        $contact->getContact($this->di->get('test-user'));
        $param = array(
                'model' => array(
                    'keys' => '',
                    'instance' => array(
                        'ServiceRecipient' => $form['contact'], //quien recibe
                        'Urgency' => $form['urgency'], // urgencia
                        'OpenedBy' => $this->di->get('test-user'), //usuario que crea el ticket
                        'Description' => array(
                            'Description' => $form['description']//Descripcion
                        ),
                        'AffectedService' => $form['sa'], //servicio afectado
                        'NotifyBy' => 'Telephone',
                        'Solution' => '',
                        'Category' => 'incident',
                        'Area' => $form['catalog']['area'],
                        'Subarea' => $form['catalog']['subarea'],
                        'ContactEmail' => $contact->email,
                        'ContactFirstName' => $contact->firstname,
                        'ContactLastName' => $contact->lastname,
                        'FailedEntitlement' => $form['interruption'],
                        'EnteredByESS' => 'true',
                        'Contact' => $this->di->get('test-user'),
                        'Update' => '',
                        'Impact' => $form['impact'],
                        'AffectedCI' => $form['ci'],//parte dos de ci
                        'Title' => $form['title'],
                        'ReportedByContact' => $this->di->get('test-user'),
                        'MetodoOrigen' => 'Autoservicio',
                        'attachments' => array(
                            'attachments' => $attach
                        )
                    ),
                    'messages' => ''
                )
            );
        $response = $this->client->CreateInteraction($param);
        $response = (array)$response;
        $response = (array)$response['model'];
        $response = (array)$response['keys'];
        return $response;
    }

    public function createRequestTicket($recipent, $urgency, $description, $area, $subarea, $contact, $impact, $ci, $title, $servicio, $caida, $attach)
    {
        $this->client = $this->di->get('soapclient-servicedesk');
        if($caida == 'SI')
        {
            $caida = 'true';
        }
        else
        {
            $caida = 'false';
        }
        $contact = new Contact();
        $contact->getContact($this->di->get('test-user'));
        if($attach['content'] !== '')
        {
            $attachment = array(
                            'attachment' => array(
                                    '_' => $attach['content'],
                                    'name' => $attach['name'],
                                    'attachmentType' => $attach['type']
                                    )
                                );
        }
        else
        {
            $attachment = array('attachment' => '');
        }
        $param = array(
                'model' => array(
                    'keys' => '',
                    'instance' => array(
                        'ServiceRecipient' => $recipent, //quien recibe
                        'Urgency' => $urgency, // urgencia
                        'OpenedBy' => $this->di->get('test-user'), //usuario que crea el ticket
                        'Description' => array(
                            'Description' => $description//Descripcion
                        ),
                        'AffectedService' => $servicio, //servicio afectado
                        'NotifyBy' => 'Telephone',
                        'Solution' => '',
                        'Category' => 'incident',
                        'Area' => $area,
                        'Subarea' => $subarea,
                        'ContactEmail' => $contact->email,
                        'ContactFirstName' => $contact->firstname,
                        'ContactLastName' => $contact->lastname,
                        'FailedEntitlement' => $caida,
                        'EnteredByESS' => 'true',
                        'Contact' => $this->di->get('test-user'),
                        'Update' => '',
                        'Impact' => $impact,
                        'AffectedCI' => $ci,//parte dos de ci
                        'Title' => $title,
                        'ReportedByContact' => $this->di->get('test-user'),
                        'MetodoOrigen' => 'Autoservicio',
                        'attachments' => $attachment
                    ),
                    'messages' => ''
                )
            );
        $response = $this->client->CreateInteraction($param);
        $response = (array)$response;
        $response = (array)$response['model'];
        $response = (array)$response['keys'];
        return $response;
    }

    public function searchKnowledge($search)
    {
        $this->client = $this->di->get('soapclient-knowledge');
        $param = array('keys' => array(
                            '_' => '',/*array(
                                    'ConfigurationItem' => ''
                                ),*/
                            'query' => 'title#"' . $search . '"'
                        )                    
                    );
        $response = (array)$this->client->RetrieveKnowledgeList($param);
        return $response;
    }
    public function getKnowledge($id)
    {
        $this->client = $this->di->get('soapclient-knowledge');
        $param = array('keys' => array(
                            'id' => $id
                        )                    
                    );
        $response = (array)$this->client->RetrieveKnowledgeList($param);
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
