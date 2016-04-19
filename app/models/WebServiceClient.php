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
        $this->client = $this->di->get('soapclient-servicedesk');
        $param = array( 'model' => array(
                            'keys' => array(
                                'CallID' => $tck
                            ),
                            'instance' => array(
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
                            ),
                            'messages' => array(
                                'messages' => ''
                            )
                        )
                    );
        $result = $this->client->RetrieveInteraction($param);

        //$result = $this->client->call('RetrieveInteraction', $param, '', '', false, true);
        $result = (array) $result;
        return $result;
    }
}
