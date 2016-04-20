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
        $result = (array)$result['return'];
        $mess = (array)$result['messages'];
        $result = (array)$result['instance'];

        $this->CallID = $result['CallID'];
        $this->ServiceRecipient = $result['ServiceRecipient'];
        $this->Urgency = $result['Urgency'];
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
        $this->OpenTime = $result['OpenTime'];
        $this->UpdateTime = $result['UpdateTime'];
        $this->OpenedBy = $result['OpenedBy'];
        $temp = (array)$result['Description'];
        if(isset($temp))
        {
            $this->Description = $temp['Description'];
        }
        else
        {
            $this->Description = '';
        }
        
        $this->AffectedService = $result['AffectedService'];
        $this->CallOwner = $result['CallOwner'];
        $this->Status = $result['Status'];
        $this->NotifyBy = $result['NotifyBy'];
        $this->Solution = $result['Solution'];
        $this->Category = $result['Category'];
        $this->CallerDepartment = $result['CallerDepartment'];
        $this->CallerLocation = $result['CallerLocation'];
        $this->CloseTime = $result['CloseTime'];
        $this->ClosedBy = $result['ClosedBy'];
        $this->KnowledgeCandidate = $result['KnowledgeCandidate'];
        $this->SLAAgreementID = $result['SLAAgreementID'];
        $this->Priority = $result['Priority'];
        $this->ServiceContract = $result['ServiceContract'];
        $this->SiteCategory = $result['SiteCategory'];
        $this->TotalLossOfService = $result['TotalLossOfService'];
        $this->Area = $result['Area'];
        $this->Subarea = $result['Subarea'];
        $this->ProblemType = $result['ProblemType'];
        $this->FailedEntitlement = $result['FailedEntitlement'];
        $this->Location = $result['Location'];
        $this->CauseCode = $result['CauseCode'];
        $this->ClosureCode = $result['ClosureCode'];
        $this->Company = $result['Company'];
        $this->ReportedByContact = $result['ReportedByContact'];
        $this->ReportedByDifferentContact = $result['ReportedByDifferentContact'];
        $this->ReportedByPhone = $result['ReportedByPhone'];
        $this->ReportedByExtension = $result['ReportedByExtension'];
        $this->ReportedByFax = $result['ReportedByFax'];
        $this->ContactEmail = $result['ContactEmail'];
        $this->LocationFullName = $result['LocationFullName'];
        $this->ContactFirstName = $result['ContactFirstName'];
        $this->ContactLastName = $result['ContactLastName'];
        $this->ContactTimeZone = $result['ContactTimeZone'];
        $this->EnteredByESS = $result['EnteredByESS'];
        $this->SLABreached = $result['SLABreached'];
        $this->NextSLABreach = $result['NextSLABreach'];
        $this->Contact = $result['Contact'];
        $this->Update = $result['Update'];
        $this->Impact = $result['Impact'];
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
        $this->neededbytime = $result['neededbytime'];
        $this->approvalstatus = $result['approvalstatus'];
        $this->folder = $result['folder'];
        $this->subscriptionItem = $result['subscriptionItem'];
        $this->AffectedCI = $result['AffectedCI'];
        $this->Title = $result['Title'];
        $this->MetodoOrigen = $result['MetodoOrigen'];
        $this->attachments = $result['attachments'];
        $this->messages = $mess;
    }
}