<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Dispatcher\Exception;

class WebServiceClient extends Model
{
    private $client;
	private $auth;
	
   public function initialize()
   {
		$this->auth = $this->getDi()->getShared('auth');
   }   
	
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
        if($this->client == false)
        {
            return null;
        }
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
        try
        {
            $status = $result['returnCode'];
            if($status != '0')
            {
                return false;
            }
            $result = (array)$result['model'];
            $result = (array)$result['instance'];
            $result = (array)$result['ContactName'];
            return $result['_'];    
        }
        catch(Exception $e)
        {
            return null;
        }
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
    function write_log($cadena,$tipo)
    {
        $arch = fopen(realpath( '.' )."/logs/milog_".date("Y-m-d").".txt", "a+");

        fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".$_SERVER['REMOTE_ADDR']." ".
            $_SERVER['HTTP_X_FORWARDED_FOR']." - $tipo ] ".$cadena."\n");
        fclose($arch);
    }
    public function  CreateRequestInteraction($form)
    {
        require_once(APP_DIR . '/library/nusoap-0.9.5/lib/nusoap.php');

        $configWs = $this->di->get('configWs');
        $client = new \nusoap_client($configWs->wsdlUriCata, false);
        //Setting credentials for Authentication
        $client->setCredentials("falcon","","basic");

        $msg = $client->serializeEnvelope($this->getSRCInteractionViaOneStepRequestMsg($form),
            false,
            array('ns'=>'http://schemas.hp.com/SM/7',
                'com'=>'http://schemas.hp.com/SM/7/Common',
                'xm'=>'http://www.w3.org/2005/05/xmlmime'));

        $result=$client->send($msg, 'Create');
        if(strpos($result, 'Your request has been submitted'))
        {
            $result = explode(' ', $result);
            foreach($result as $val)
            {
                $val = str_replace('.','',$val);
                if(preg_match("/^SD(\d{1,})/", $val))
                {
                    return $val;
                }
            }
        }
        else
        {
            return null;
        }
        return $result;
    }
    public function CreateRequestInteractionOld($form)
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
            //$attach = '<attachments/>';//'<attachments><attachments href="<![CDATA[<'. $form['fileName'] .'>]]>" action="add" name="'. $form['fileName'] .'">'. $form['fileContent'] .'</attachments></attachments>';
        }
        else
        {
            $attach = '';
        }
        
        //$param = new \SoapVar('<keys><CartId></CartId></keys><instance><Service>'. $form['ci'] .'</Service><CallbackContactName>'. $form['contact'] .'</CallbackContactName><CallbackType></CallbackType><CartId></CartId><cartItems><cartItems type="Structure"><CartItemId></CartItemId><Delivery></Delivery><ItemName>'. $form['catalog']['subarea'] .'</ItemName><OptionList></OptionList><Options></Options><Quantity>1</Quantity><RequestedFor>'. $this->auth->getName() .'</RequestedFor><RequestedForDept></RequestedForDept><RequestedForType>individual</RequestedForType><ServiceSLA></ServiceSLA></cartItems></cartItems><ContactName>'. $form['contact'] .'</ContactName><NeededByTime></NeededByTime><Other></Other><Urgency>'. $form['urgency'].'</Urgency><Title>'. $form['title'].'</Title><ServiceType></ServiceType><SvcSrcXML></SvcSrcXML><Purpose><Purpose>'. $form['description'] .'</Purpose></Purpose><attachments>'. $attach .'</attachments></instance><messages/>',  XSD_ANYXML, null, null, "model");
        
        $cartItems = new \SoapVar('<cartItems type="Array"><cartItems type="Structure"><CartItemId type="Long"></CartItemId><Delivery></Delivery><ItemName>'. $form['catalog']['subarea'] .'</ItemName><OptionList></OptionList><Options></Options><Quantity type="Decimal">1</Quantity><RequestedFor>'. $this->auth->getName() .'</RequestedFor><RequestedForDept></RequestedForDept><RequestedForType>individual</RequestedForType><ServiceSLA type="Decimal"></ServiceSLA></cartItems></cartItems>', XSD_ANYXML);
        /*$cartItems = array(
                                'type' => 'Structure',
                                '_' => array(
                                    'CartItemId' => '',
                                    'Delivery' => '',
                                    'ItemName' => $form['catalog']['subarea'],
                                    'OptionList' => '',
                                    'Options' => '',
                                    'Quantity' => '1',
                                    'RequestedFor' => $this->auth->getName(),
                                    'RequestedForDept' => '',
                                    'RequestedForType' => 'individual',
                                    'ServiceSLA' => ''
                                )
                            );
        $encoded = new \SoapVar($cartItems, SOAP_ENC_OBJECT);*/
        $cart = array(
                    '_' =>array(

                        'Delivery' => '',
                        'ItemName' => $form['catalog']['subarea'],
                        'OptionList' => '',
                        'Options' => '',
                        'Quantity' => 1,
                        'RequestedFor' => $this->auth->getName(),
                        'RequestedForDept' => '',
                        'RequestedForType' => 'individual',

                    ),
                    'type' => 'Structure'
                );
        $param = array(
                'model' => array(
                    'keys' => array(
                        'CartId' => ''
                    ),
                    'instance' => array(
                        'Service' => $form['ci'],
                        'CallbackContactName' => $form['contact'],
                        'CallbackType' => '',
                        'CartId' => '',
                        'cartItems' => array(
                            'cartItems' => $cart
                        ),
                        //'NeededByTime' => $form['hasta'],
                        'ContactName' => $form['contact'],
                        'NeededByTime' => '',
                        'Other' => '',
                        'Urgency' => $form['urgency'],
                        'Title' => $form['title'],
                        'ServiceType' => '',
                        'SvcSrcXML' => '',
                        'Purpose' => array(
                            'Purpose' => $form['description']
                        ),
                        'attachments' => array(
                            'attachments' => $attach
                        )
                    ),
                    'messages' => array(
                        'messages' => ''
                    )
                )
            );
        /*$param = new \stdClass;
        $param->model = new \stdClass;
        $param->model->keys = new \stdClass;
        $param->model->keys->CartId = '';
        $param->model->instance = new \stdClass;
        $param->model->instance->Service = $form['ci'];
        $param->model->instance->CallbackContactName = $form['contact'];
        $param->model->instance->CallbackType = '';
        $param->model->instance->CartId = '';
        //$param->model->instance->cartItems = new \stdClass;
        $param->model->instance->cartItems =  array('cartItems' => $cartItems);/*= new \stdClass;
        $param->model->instance->cartItems->cartItems->CartItemId = '';
        $param->model->instance->cartItems->cartItems->Delivery = '';
        $param->model->instance->cartItems->cartItems->ItemName = $form['catalog']['subarea'];
        $param->model->instance->cartItems->cartItems->OptionList = '';
        $param->model->instance->cartItems->cartItems->Options = '';
        $param->model->instance->cartItems->cartItems->Quantity = '1';
        $param->model->instance->cartItems->cartItems->RequestedFor = $this->auth->getName();
        $param->model->instance->cartItems->cartItems->RequestedForDept = '';
        $param->model->instance->cartItems->cartItems->RequestedForType = '';
        $param->model->instance->cartItems->cartItems->ServiceSLA = '';
        $param->model->instance->ContactName = $form['contact'];
        $param->model->instance->NeededByTime = '';
        $param->model->instance->Other = '';
        $param->model->instance->Urgency = $form['urgency'];
        $param->model->instance->Title = $form['title'];
        $param->model->instance->ServiceType = '';
        $param->model->instance->SvcSrcXML = '';
        $param->model->instance->Purpose = new \stdClass;
        $param->model->instance->Purpose->Purpose = $form['description'];
        $param->model->instance->attachments = new \stdClass;
        $param->model->instance->attachments->attachments = $attach;
        $param->model->messages = new \stdClass;
        $param->model->messages->messages = '';*/

        $response['response'] = (array)$this->client->CreateSRCInteractionViaOneStep($param);
        $response['request'] = $this->client->__getLastRequest();
        return $response;
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
        $contact->getContact($this->auth->getName());
        $param = array(
                'model' => array(
                    'keys' => '',
                    'instance' => array(
                        'ServiceRecipient' => $form['contact'], //quien recibe
                        'Urgency' => $form['urgency'], // urgencia
                        'OpenedBy' => $this->auth->getName(), //usuario que crea el ticket
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
                        'EnteredByESS' => true,
                        'Contact' => $this->auth->getName(),
                        'Update' => '',
                        'Impact' => $form['impact'],
                        'AffectedCI' => $form['ci'],//parte dos de ci
                        'Title' => $form['title'],
                        'ReportedByContact' => $this->auth->getName(),
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
        $contact->getContact($this->auth->getName());
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
                        'OpenedBy' => $this->auth->getName(), //usuario que crea el ticket
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
                        'EnteredByESS' => true,
                        'Contact' => $this->auth->getName(),
                        'Update' => '',
                        'Impact' => $impact,
                        'AffectedCI' => $ci,//parte dos de ci
                        'Title' => $title,
                        'ReportedByContact' => $this->auth->getName(),
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
        require_once(APP_DIR . '/library/nusoap-0.9.5/lib/nusoap.php');

        $configWs = $this->di->get('configWs');
        $client = new \nusoap_client($configWs->wsdlUriCata, false);
        //Setting credentials for Authentication
        $client->setCredentials("falcon","","basic");

        $msg = $client->serializeEnvelope($this->getRetrieveKnowledgeMsg($id),
            false,
            array('ns'=>'http://schemas.hp.com/SM/7',
                'com'=>'http://schemas.hp.com/SM/7/Common',
                'xm'=>'http://www.w3.org/2005/05/xmlmime'));
        $xml = $client->send($msg, 'Retrieve');
        $xml = str_replace(' type="String"', '', $xml);
        $xml = str_replace(' type="DateTime"', '', $xml);
        $response = array();
        $response['title'] = explode('title>', $xml);
        $response['title'] = str_replace('</', '', $response['title'][1]);
        $response['answer'] = explode('answer>', $xml);
        $response['answer'] = str_replace('</', '', $response['answer'][1]);
        $response['id'] = explode('id>', $xml);
        $response['id'] = str_replace('</', '', $response['id'][1]);
        $response['creationdate'] = explode('creationdate>', $xml);
        $response['creationdate'] = str_replace('</', '', $response['creationdate'][1]);
        $response['attachments'] = array();
        $attach = explode('attachment ', $xml);
        foreach ($attach as $val)
        {
            if(strpos($val, '"cid:'))
            {
                $line = explode('"', $val);
                array_push($response['attachments'], array('href' => $line[1], 'name' => $line[7]));
            }
        }
        return $response;

        /*$this->client = $this->di->get('soapclient-knowledge');
        $param = array('model' => array(
                        array(
                            'keys' => array(
                                    'id' => $id
                                ),
                            'instance' => '',
                            'messages' => ''
                            )
                        );

        $response = (array)$this->client->RetrieveKnowledge($param);
        return $response;*/
    }

    function f_remove_odd_characters($string){
        $string = str_replace("\n","[NEWLINE]",$string);
        $string=htmlentities($string);
        $string=preg_replace('/[^(\x20-\x7F)]*/','',$string);
        $string=html_entity_decode($string);       
        $string = str_replace("[NEWLINE]","\n",$string);
        return $string;
      }

    private function getSRCInteractionViaOneStepRequestMsg($form) {
        if($form['fileName'] != '')
        {
            //$attach = '<com:attachment action="add" attachmentType="" charset="" contentId="" href="" len="" name="'. $form['fileName'] .'" type="" upload.by="" upload.date="" xm:contentType="application/?">'. $form['fileContent'] .'</com:attachment>';
            $attach = '<com:attachment xm:contentType="application/?" href="" contentId="" action="add" name="'. $form['fileName'] .'" type="" len="" charset="" upload.by="" upload.date="" attachmentType="">'. $form['fileContent'] .'</com:attachment>';
        }
        else
        {
            $attach = '<com:attachment xm:contentType="application/?" href="" contentId="" action="" name="" type="" len="" charset="" upload.by="" upload.date="" attachmentType="">cid:600603579599</com:attachment>';
        }
        return '
<ns:CreateSRCInteractionViaOneStepRequest attachmentData="" attachmentInfo="" ignoreEmptyElements="true" updateconstraint="-1">
	<ns:model>
		<ns:keys>
			<ns:CartId/>
		</ns:keys>
		<ns:instance>
			<ns:Service>'.$form['ci'].'</ns:Service>
			<ns:RequestOnBehalf/>
			<ns:CallbackContactName>'.$form['contact'].'</ns:CallbackContactName>
			<ns:CallbackType/>
			<ns:CartId/>
			<ns:cartItems>
				<ns:cartItems>
					<ns:CartItemId/>
					<ns:Delivery/>
					<ns:ItemName>'.$form['catalog']['subarea'].'</ns:ItemName>
					<ns:OptionList/>
					<ns:Options/>
					<ns:Quantity>1</ns:Quantity>
					<ns:RequestedFor>'.$this->auth->getName().'</ns:RequestedFor>
					<ns:RequestedForDept/>
					<ns:RequestedForType>individual</ns:RequestedForType>
					<ns:ServiceSLA/>
				</ns:cartItems>
			</ns:cartItems>
			<ns:ContactName>'.$form['contact'].'</ns:ContactName>
			<ns:NeededByTime/>
			<ns:Other/>
			<ns:Urgency>'.$form['urgency'].'</ns:Urgency>
			<ns:Title>'.$form['title'].'</ns:Title>
			<ns:ServiceType/>
			<ns:SvcSrcXML/>
			<ns:Purpose>
				<ns:Purpose/>
			</ns:Purpose>
			<ns:attachments>
				'. $attach .'
			</ns:attachments>
		</ns:instance>
		<ns:messages>
		   <com:message mandatory="" module="" readonly="" severity="" type="String"/>
		</ns:messages>
	</ns:model>
</ns:CreateSRCInteractionViaOneStepRequest>';
    }

    public function  getRetrieveKnowledgeMsg($id)
    {
        return '
<ns:RetrieveKnowledgeRequest attachmentInfo="true" attachmentData="" ignoreEmptyElements="true" updatecounter="" handle="" count="" start="">
     <ns:model query="">
        <ns:keys query="" updatecounter="">
           <ns:id type="String" mandatory="" readonly="">'.$id.'</ns:id>
        </ns:keys>
        <ns:instance query="" uniquequery="" recordid="" updatecounter="">
        </ns:instance>
        <ns:messages/>
     </ns:model>
</ns:RetrieveKnowledgeRequest>';
    }

}


