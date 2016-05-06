<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Ticket extends Model
{   
    /*
     * @var string
     */
    public $CallID;
    /*
     * @var string
     */
    public $ServiceRecipient;
    /*
     * @var string
     */
    public $Urgency;
    /*
     * @var string
     */
    public $OpenTime;
    /*
     * @var string
     */
    public $UpdateTime;
    /*
     * @var string
     */
    public $OpenedBy;
    /*
     * @var Array<string>
     */
    public $Description;
    /*
     * @var string
     */
    public $AffectedService;
    /*
     * @var string
     */
    public $CallOwner;
    /*
     * @var string
     */
    public $Status;
    /*
     * @var string
     */
    public $NotifyBy;
    /*
     * @var Array<string>
     */
    public $Solution;
    /*
     * @var string
     */
    public $Category;
    /*
     * @var string
     */
    public $CallerDepartment;
    /*
     * @var string
     */
    public $CallerLocation;
    /*
     * @var string
     */
    public $CloseTime;
    /*
     * @var string
     */
    public $ClosedBy;
    /*
     * @var string
     */
    public $KnowledgeCandidate;
    /*
     * @var string
     */
    public $SLAAgreementID;
    /*
     * @var string
     */
    public $Priority;
    /*
     * @var string
     */
    public $ServiceContract;
    /*
     * @var string
     */
    public $SiteCategory;
    /*
     * @var string
     */
    public $TotalLossOfService;
    /*
     * @var string
     */
    public $Area;
    /*
     * @var string
     */
    public $Subarea;
    /*
     * @var string
     */
    public $ProblemType;
    /*
     * @var string
     */
    public $FailedEntitlement;
    /*
     * @var string
     */
    public $Location;
    /*
     * @var string
     */
    public $CauseCode;
    /*
     * @var string
     */
    public $ClosureCode;
    /*
     * @var string
     */
    public $Company;
    /*
     * @var string
     */
    public $ReportedByContact;
    /*
     * @var string
     */
    public $ReportedByDifferentContact;
    /*
     * @var string
     */
    public $ReportedByPhone;
    /*
     * @var string
     */
    public $ReportedByExtension;
    /*
     * @var string
     */
    public $ReportedByFax;/*
     * @var string
     */
     
    public $ContactEmail;
    /*
     * @var string
     */
    public $LocationFullName;
    /*
     * @var string
     */
    public $ContactFirstName;
    /*
     * @var string
     */
    public $ContactLastName;
    /*
     * @var string
     */
    public $ContactTimeZone;
    /*
     * @var string
     */
    public $EnteredByESS;
    /*
     * @var string
     */
    public $SLABreached;
    /*
     * @var string
     */
    public $NextSLABreach;
    /*
     * @var string
     */
    public $Contact;
    /*
     * @var Array<string>
     */
    public $Update;
    /*
     * @var string
     */
    public $Impact;
    /*
     * @var string
     */
    public $neededbytime;
    /*
     * @var string
     */
    public $approvalstatus;
    /*
     * @var string
     */
    public $folder;
    /*
     * @var string
     */
    public $subscriptionItem;
    /*
     * @var string
     */
    public $AffectedCI;
    /*
     * @var string
     */
    public $Title;
    /*
     * @var string
     */
    public $MetodoOrigen;
    /*
     * @var string
     */
    public $attachments;
    /*
     * @var Array<string>
     */
    public $messages;

    public function updateTicket($update)
    {
        $wsClient = new WebServiceClient();
        $result = $wsClient->updateTicket($this->CallID, $update);
        return $result;
    }
    
    public function findTicket($tck)
    {
        $wsClient = new WebServiceClient();
        $result = $wsClient->getTicket($tck);
        if($result['returnCode'] != 0)
        {
            return false;
        }
        $result = (array)$result['model'];
        //$result = (array)$result['return'];
        //$mess = (array)$result['messages'];
        $result = (array)$result['instance'];

        $this->CallID = (array)$result['CallID'];
        $this->CallID = $this->CallID['_'];
        $this->ServiceRecipient = (array)$result['ServiceRecipient'];
        $this->ServiceRecipient = $this->ServiceRecipient['_'];
        $this->Urgency = (array)$result['Urgency'];
        $this->Urgency = $this->Urgency['_'];
        if($this->Urgency == "1")
        {
            $this->Urgency = "Crítico";
        }
        if($this->Urgency == "2")
        {
            $this->Urgency = "Alto";
        }
        if($this->Urgency == "3")
        {
            $this->Urgency = "Medio";
        }
        if($this->Urgency == "4")
        {
            $this->Urgency = "Baja";
        }

        $this->OpenTime = (array)$result['OpenTime'];
        $this->OpenTime = $this->parseDate($this->OpenTime['_']);
        $this->UpdateTime = (array)$result['UpdateTime'];
        $this->UpdateTime = $this->parseDate($this->UpdateTime['_']);
        $this->OpenedBy = (array)$result['OpenedBy'];
        $this->OpenedBy = $this->OpenedBy['_'];
        $temp = (array)$result['Description'];
        if(isset($temp))
        {
            $this->Description = (array)$temp['Description'];
            $this->Description = $this->Description['_'];
            
        }
        else
        {
            $this->Description = '';
        }
        
        $this->AffectedService = (array)$result['AffectedService'];
        $this->AffectedService = $this->AffectedService['_'];
        $this->CallOwner = (array)$result['CallOwner'];
        $this->CallOwner = $this->CallOwner['_'];
        $this->Status = (array)$result['Status'];
        $this->Status = $this->statusToEsp($this->Status['_']);
        $this->NotifyBy = (array)$result['NotifyBy'];
        $this->NotifyBy = $this->NotifyBy['_'];
        if(array_key_exists('Solution', $result))
        {
            $this->Solution = (array)$result['Solution'];    
            $this->Solution = $this->Solution['_'];
        }
        if(array_key_exists('Category', $result))
        {
            $this->Category = (array)$result['Category'];
            $this->Category = $this->Category['_'];
        }
        if(array_key_exists('CallerDepartment', $result))
        {
            $this->CallerDepartment = (array)$result['CallerDepartment'];
            $this->CallerDepartment = $this->CallerDepartment['_'];
        }
        if(array_key_exists('CallerLocation', $result))
        {
            $this->CallerLocation = (array)$result['CallerLocation'];
            $this->CallerLocation = $this->CallerLocation['_'];
        }
        if(array_key_exists('CloseTime', $result))
        {
            $this->CloseTime = (array)$result['CloseTime'];
            $this->CloseTime = $this->CloseTime['_'];
        }
        if(array_key_exists('ClosedBy', $result))
        {
            $this->ClosedBy = (array)$result['ClosedBy'];
            $this->ClosedBy = $this->ClosedBy['_'];
        }
        if(array_key_exists('KnowledgeCandidate', $result))
        {
            $this->KnowledgeCandidate = (array)$result['KnowledgeCandidate'];
            $this->KnowledgeCandidate = $this->KnowledgeCandidate['_'];
        }
        if(array_key_exists('SLAAgreementID', $result))
        {
            $this->SLAAgreementID = (array)$result['SLAAgreementID'];
            $this->SLAAgreementID = $this->SLAAgreementID['_'];
        }
        if(array_key_exists('Priority', $result))
        {
            $this->Priority = (array)$result['Priority'];
            $this->Priority = $this->Priority['_'];
        }
        if(array_key_exists('ServiceContract', $result))
        {
            $this->ServiceContract = (array)$result['ServiceContract'];
            $this->ServiceContract = $this->ServiceContract['_'];
        }
        if(array_key_exists('SiteCategory', $result))
        {
            $this->SiteCategory = (array)$result['SiteCategory'];
            $this->SiteCategory = $this->SiteCategory['_'];
        }
        if(array_key_exists('TotalLossOfService', $result))
        {
            $this->TotalLossOfService = (array)$result['TotalLossOfService'];
            $this->TotalLossOfService = $this->TotalLossOfService['_'];
        }
        if(array_key_exists('Area', $result))
        {
            $this->Area = (array)$result['Area'];
            $this->Area = $this->Area['_'];
        }
        if(array_key_exists('Subarea', $result))
        {
            $this->Subarea = (array)$result['Subarea'];
            $this->Subarea = $this->Subarea['_'];
        }
        if(array_key_exists('ProblemType', $result))
        {
            $this->ProblemType = (array)$result['ProblemType'];
            $this->ProblemType = $this->ProblemType['_'];
        }
        if(array_key_exists('FailedEntitlement', $result))
        {
            $this->FailedEntitlement = (array)$result['FailedEntitlement'];
            $this->FailedEntitlement = $this->FailedEntitlement['_'];
        }
        if(array_key_exists('Location', $result))
        {
            $this->Location = (array)$result['Location'];
            $this->Location = $this->Location['_'];
        }
        if(array_key_exists('CauseCode', $result))
        {
            $this->CauseCode = (array)$result['CauseCode'];
            $this->CauseCode = $this->CauseCode['_'];
        }
        if(array_key_exists('ClosureCode', $result))
        {
            $this->ClosureCode = (array)$result['ClosureCode'];
            $this->ClosureCode = $this->ClosureCode['_'];
        }
        if(array_key_exists('Company', $result))
        {
            $this->Company = (array)$result['Company'];
            $this->Company = $this->Company['_'];
        }
        if(array_key_exists('ReportedByContact', $result))
        {
            $this->ReportedByContact = (array)$result['ReportedByContact'];
            $this->ReportedByContact = $this->ReportedByContact['_'];
        }
        if(array_key_exists('ReportedByDifferentContact', $result))
        {
            $this->ReportedByDifferentContact = (array)$result['ReportedByDifferentContact'];
            $this->ReportedByDifferentContact = $this->ReportedByDifferentContact['_'];
        }
        if(array_key_exists('ReportedByPhone', $result))
        {
            $this->ReportedByPhone = (array)$result['ReportedByPhone'];
            $this->ReportedByPhone = $this->ReportedByPhone['_'];
        }
        if(array_key_exists('ReportedByExtension', $result))
        {
            $this->ReportedByExtension = (array)$result['ReportedByExtension'];
            $this->ReportedByExtension = $this->ReportedByExtension['_'];
        }
        if(array_key_exists('ReportedByFax', $result))
        {
            $this->ReportedByFax = (array)$result['ReportedByFax'];
            $this->ReportedByFax = $this->ReportedByFax['_'];
        }
        if(array_key_exists('ContactEmail', $result))
        {
            $this->ContactEmail = (array)$result['ContactEmail'];
            $this->ContactEmail = $this->ContactEmail['_'];
        }
        if(array_key_exists('LocationFullName', $result))
        {
            $this->LocationFullName = (array)$result['LocationFullName'];
            $this->LocationFullName = $this->LocationFullName['_'];
        }
        if(array_key_exists('ContactFirstName', $result))
        {
            $this->ContactFirstName = (array)$result['ContactFirstName'];
            $this->ContactFirstName = $this->ContactFirstName['_'];
        }
        if(array_key_exists('ContactLastName', $result))
        {
            $this->ContactLastName = (array)$result['ContactLastName'];
            $this->ContactLastName = $this->ContactLastName['_'];
        }
        if(array_key_exists('ContactTimeZone', $result))
        {
            $this->ContactTimeZone = (array)$result['ContactTimeZone'];
            $this->ContactTimeZone = $this->ContactTimeZone['_'];
        }
        if(array_key_exists('EnteredByESS', $result))
        {
            $this->EnteredByESS = (array)$result['EnteredByESS'];
            $this->EnteredByESS = $this->EnteredByESS['_'];
        }
        if(array_key_exists('SLABreached', $result))
        {
            $this->SLABreached = (array)$result['SLABreached'];
            $this->SLABreached = $this->SLABreached['_'];
        }
        if(array_key_exists('NextSLABreach', $result))
        {
            $this->NextSLABreach = (array)$result['NextSLABreach'];
            $this->NextSLABreach = $this->NextSLABreach['_'];
        }
        if(array_key_exists('Contact', $result))
        {
            $this->Contact = (array)$result['Contact'];
            $this->Contact = $this->Contact['_'];
        }
        if(array_key_exists('Update', $result))
        {
            $this->Update = (array)$result['Update'];
            $this->Update = $this->Update['Update'];
            $temp = array();
            foreach ($this->Update as $value) {
                $var = (array)$value;
                array_push($temp, $var['_']);
            }
            $this->Update = $temp;
        }
        if(array_key_exists('Impact', $result))
        {
            $this->Impact = (array)$result['Impact'];
            $this->Impact = $this->Impact['_'];
            if($this->Impact == "4")
            {
                $this->Impact = "Empresa";
            }
            if($this->Impact == "3")
            {
                
                $this->Impact = "Sitio/Depto";
            }
            if($this->Impact == "2")
            {
                $this->Impact = "Varios usuarios";
            }
            if($this->Impact == "1")
            {
                $this->Impact = "usuario";
            }
        }
        if(array_key_exists('neededbytime', $result))
        {
            $this->neededbytime = (array)$result['neededbytime'];
            $this->neededbytime = $this->neededbytime['_'];
        }
        if(array_key_exists('approvalstatus', $result))
        {
            $this->approvalstatus = (array)$result['approvalstatus'];
            $this->approvalstatus = $this->approvalstatus['_'];
        }
        if(array_key_exists('folder', $result))
        {
            $this->folder = (array)$result['folder'];
            $this->folder = $this->folder['_'];
        }
        if(array_key_exists('subscriptionItem', $result))
        {
            $this->subscriptionItem = (array)$result['subscriptionItem'];
            $this->subscriptionItem = $this->subscriptionItem['_'];
        }
        if(array_key_exists('AffectedCI', $result))
        {
            $this->AffectedCI = (array)$result['AffectedCI'];
            $this->AffectedCI = $this->AffectedCI['_'];
        }
        if(array_key_exists('Title', $result))
        {
            $this->Title = (array)$result['Title'];
            $this->Title = $this->Title['_'];
        }
        if(array_key_exists('MetodoOrigen', $result))
        {
            $this->MetodoOrigen = (array)$result['MetodoOrigen'];
            $this->MetodoOrigen = $this->MetodoOrigen['_'];
        }
        if(array_key_exists('attachments', $result))
        {
            $this->attachments = (array)$result['attachments'];
            $this->attachments = $this->attachments['_'];
        }
        //$this->messages = $mess;
        return true;
    }

    public function getTickestByUser($usr)
    {
        $wsClient = new WebServiceClient();
        $result = $wsClient->getTicketsByUser($usr);

        $tckList = array();
        $count = 1;
        foreach ($result['instance'] as $key => $value) 
        {
            if($count > 30)
            {
                break;
            }
            $value = (array)$value;
            $id = (array)$value['CallID'];
            $id = $id['_'];
            $status = (array)$value['Status'];
            $status = $status['_'];
            $title = (array)$value['Title'];
            $title = $title['_'];
            array_push($tckList, array('CallID' => $id, 'Status' => $this->statusToEsp($status), 'Title' => $title));
            $count = 1 + $count;
        }
        return $tckList;
    }

    function statusToEsp($status)
    {
        if($status == 'Accepted')
        {
            $status = "Aceptado";
        }
        if($status == 'Closed')
        {
            $status = "Cerrado";
        }
        if($status == 'Open')
        {
            $status = "Abierto";
        }
        if($status == 'Pending Change')
        {
            $status = "Pendiente cambio";
        }
        if($status == 'Pending Customer')
        {
            $status = "Pendiente cliente";
        }
        if($status == 'Pending Other')
        {
            $status = "Pendiente otro";
        }
        if($status == 'Pending Vendor')
        {
            $status = "Pendiente proveedor";
        }
        if($status == 'Referred')
        {
            $status = "Referido";
        }
        if($status == 'Rejected')
        {
            $status = "Rechazado";
        }
        if($status == 'Replaced Problem')
        {
            $status = "Remplazado por problema";
        }
        if($status == 'Resolved')
        {
            $status = "Resuelto";
        }
        if($status == 'Work In Progress')
        {
            $status = "Trabajo en progreso";
        }
        if($status == 'Open - Callback')
        {
            $status = "Abierto en espera de confirmación";
        }
        if($status == 'Open - Linked')
        {
            $status = "Abierto escalado";
        }
        if($status == 'Open - Idle')
        {
            $status = "Abierto sin atender";
        }
        return $status;
    }

    function parseDate($d)
    {
         $d = str_split($d);
         return $d[8].$d[9].'/'.$d[5].$d[6].'/'.$d[0].$d[1].$d[2].$d[3].' '.$d[11].$d[12].':'.$d[14].$d[15].':'.$d[17].$d[18];
    }
}