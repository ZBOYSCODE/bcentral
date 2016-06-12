<?php
/*
 *	$Id: wsdlclient5.php,v 1.4 2007/11/06 14:49:10 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL
 *	Payload: rpc/encoded
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');

$endpoint='http://192.168.5.113:13080/SM/7/ServiceCatalogAPI.wsdl';

$client = new nusoap_client($endpoint, false);
//Setting credentials for Authentication 
$client->setCredentials("falcon","","basic");

$msg = $client->serializeEnvelope('<ns:CreateSRCInteractionViaOneStepRequest attachmentData="" attachmentInfo="" ignoreEmptyElements="true" updateconstraint="-1">
         <ns:model>
 <ns:keys>
 <ns:CartId/>
 </ns:keys>
 <ns:instance>
 <ns:Service/>
 <ns:RequestOnBehalf/>
 <ns:CallbackContactName>PEDRON, ALFREDO</ns:CallbackContactName>
 <ns:CallbackType/>
 <ns:CartId/>
 <ns:cartItems>
 <ns:cartItems>
 <ns:CartItemId/>
 <ns:Delivery/>
 <ns:ItemName>Habilitar Acceso a Wifi de Visita</ns:ItemName>
 <ns:OptionList/>
 <ns:Options/>
 <ns:Quantity>1</ns:Quantity>
 <ns:RequestedFor>PEDRON, ALFREDO</ns:RequestedFor>
 <ns:RequestedForDept/>
 <ns:RequestedForType>individual</ns:RequestedForType>
 <ns:ServiceSLA/>
 </ns:cartItems>
 </ns:cartItems>
 <ns:ContactName>PEDRON, ALFREDO</ns:ContactName>
 <ns:NeededByTime/>
 <ns:Other/>
 <ns:Urgency>2</ns:Urgency>
 <ns:Title>Test</ns:Title>
 <ns:ServiceType/>
 <ns:SvcSrcXML/>
 <ns:Purpose>
 <ns:Purpose/>
 </ns:Purpose>
               <ns:attachments>
                  <com:attachment action="add" attachmentType="" charset="" contentId="" href="" len="" name="test.txt" type="" upload.by="" upload.date="" xm:contentType="application/?">RXN0ZSBlcyBz82xvIHVuIGFyY2hpdm8gZGUgcHJ1ZWJhIQ==</com:attachment>
               </ns:attachments>
            </ns:instance>
            <ns:messages>
               <com:message mandatory="" module="" readonly="" severity="" type="String"/>
            </ns:messages>
         </ns:model>
      </ns:CreateSRCInteractionViaOneStepRequest>',false,array('ns'=>'http://schemas.hp.com/SM/7','com'=>'http://schemas.hp.com/SM/7/Common','xm'=>'http://www.w3.org/2005/05/xmlmime'));

//print_r($msg);die();
//$client->usePersistentConnection();
$result=$client->send($msg, 'Create');
print_r($client);
print_r($result);

?>