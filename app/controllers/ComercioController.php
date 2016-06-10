<?php
namespace Gabs\Controllers;
use Gabs\Models\Personas;
use Gabs\Models\Evaluacion;
use Gabs\Models\WebServiceClient;
use Gabs\Models\Ticket;
use Gabs\Models\Contact;
use Gabs\Models\Catalog;
use Gabs\Models\CI;
use Gabs\Models\Knowledge;
 
class ComercioController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {   
        $pcView = 'servicio/servicios_home_page';
        
        $tck = new Ticket();
        $tckList = $tck->getTickestByUser($this->auth->getName());
        $data = array('tckList' => $tckList);
        if($tckList == 2)
        {
            $pcView = 'servicio/servicios_error_page';
            $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
        }
        //$js = $this->getJsEncuesta();
        $js = $this->getLikeJs();
        echo $this->view->render('theme_default' ,array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=> $data,'jsScript'=>$js));

    }

    public function consultarAction()
    {   
        $pcView = 'servicio/servicios_base_conocimiento_tr';
        $KM = new Knowledge();
        $data = array('knowList' => $KM->searchKwonledge($this->request->getPost('searchinn')));
        
        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=>$data));    
    }

	/**MJARA**/
    public function conocimientoAction() 
    {
		$js = $this->getJsKnowsDatatables();
        $pcView = 'servicio/servicios_base_conocimiento';
        $data = array('knowList' => array());
		echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>$data,'jsScript'=>$js));
    }

    public function solicitudServicioAction($tipo) 
    {
        $js = '';
        $pcView = 'servicio/servicios_catalogo_base';

        if($tipo == "sistemas-ti") {

            $catalogoPadre = "Sistemas TI";
            $styleCssMenu = "primera-opcion";
            

        }
        else if ($tipo == "servicios-ti") {

            $catalogoPadre = "Servicios TI";
            $styleCssMenu = "segunda-opcion";

        }
        else if ($tipo == "infraestructura") {

            $catalogoPadre = "Infraestructura y Servicios Generales";
            $styleCssMenu = "tercera-opcion";

        }
        else {
            //caso de que mande por get una url trucha
            $response = new \Phalcon\Http\Response();
            return $response->redirect("");
        }
        $ctlg = new Catalog();
        $catalogoMenu = $ctlg->getServiceCatalogSP1($catalogoPadre);
        if(!$catalogoMenu)
        {
            $js = "$.bootstrapGrowl('Error Interno. Repita el procedimiento.',{type:\"warning\",align:\"center\"});";
            $pcView = 'servicio/servicios_error_page';
            $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
            echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>$data,'jsScript'=>$js));    
        }
        else
        {
            $pcData['catalogo'] = $catalogoMenu;
            $pcData['styleCssMenu'] = $styleCssMenu;
            $pcData['catalogoRutaCompleta'] = $catalogoPadre;

            //breadcrum de la siguiente forma: "opcion","active" (con hipervinculo o no), "url" (relativa), data para link con ajax (null para ignorar)
            $pcData['breadCrumbList']  = [
              array($catalogoPadre,'active',$tipo,null),
            ];

            $js = $this->getJsCatalogo();
            echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>$pcData,'jsScript'=>$js));
        }
    }

    public function solicitudServicioAjaxAction() {

        //verificamos petición se ajax
        if($this->request->isAjax() == true) {
            //verificamos post
            if($this->request->isPost() == true) {
                $this->mifaces->newFaces();

                //datos del formulario
                $catalogoPadre = $_POST['optionSelected'];
                $catalogoRutaCompleta = $_POST['catalogoRutaCompleta'];
                $catalogoRutaCompleta = $catalogoRutaCompleta."_".$catalogoPadre;
                $catalogoRutaArray = explode("_", $catalogoRutaCompleta);
                $styleCssMenu = $_POST['styleCssMenu'];

                //si NO es nodo hoja
                if(isset($_POST['is_nodo_hoja']) && $_POST['is_nodo_hoja'] == "false") {
                    $ctlg = new Catalog();
                    $catalogoMenu = $ctlg->getServiceCatalogSP1($catalogoPadre);
                    array_push($catalogoMenu, array('name' => 'Solucionar Problema', 'icon' => 'fa-check', 'description' => ''));
                    $pcData['catalogo'] = $catalogoMenu;
                    $pcData['styleCssMenu'] = $styleCssMenu;
                    $pcData['catalogoRutaCompleta'] = $catalogoRutaCompleta;
                    //seteamos que será nodo hoja directamente, se supone que el tercer nivel es nodo hoja
                    //si no deberia identificarse mediante logica de la respuesta del web service
                    $pcData['is_nodo_hoja'] = true;

                    //vista a renderizar
                    $pcView = 'servicio/servicios_catalogo_menu';
                }
                // SI es nodo hoja mostramos formulario final
                else {
                    $catalog = array(
                        'familia'   => $catalogoRutaArray[0],
                        'area'      => $catalogoRutaArray[1],
                        'subarea'   => $catalogoRutaArray[2],
                    );

                    $ctlg = new Catalog();
                    //**OJO, no se si para sacar los campos requerido solo es necesario un padre o toda la ruta
                    //opcion1
                    $campos = $ctlg->getFields($catalogoRutaArray[2]);
                    //opcion2 (ruta completa separados con '_')
                    //$catalogoMenu = $ctlg->gatCampos($catalogoRutaCompleta);
                    if($campos['ci'])
                    {
                        $ciItem = new CI();
                        $listas = $ciItem->getCompleteCIList();
                    }
                    else
                    {
                        $listas = array();
                    }
                    if($campos['detinatario'])
                    {
                        $contacto = new Contact();
                        $contactList = $contacto->getContactList();
                    }
                    else
                    {
                        $contactList = array();
                    }
                    $pcData['listas'] = $listas;
                    $pcData['campos'] = $campos;
                    $pcData['catalogo'] = $catalog;
                    $pcData['contactos'] = $contactList;

                    $pcData['styleCssMenu'] = $styleCssMenu;
                    $pcData['catalogoRutaCompleta'] = $catalogoRutaCompleta;

                    //esta ruta se para volver a la opcion anterior y que no se aniden el breadcrum sobre la anterior
                    $pcData['catalogoRutaCompletaToBack'] = '';
                    $pcData['catalogoRutaCompletaToBack'] .= $catalogoRutaArray[0];


                    $js = $this->getComponenteServAfectadoJs($listas);
                    $js .= $this->getValidationJs();

                    //Js para limitar calendario dependiendo si se muestran los campos desde-hasta, desde ó hasta.
                    if($campos['desde'] && $campos['hasta']) {
                        $js .= $this->getValidationCalendarDesdeHastaJs();
                    }
                    elseif($campos['desde']) {
                         $js .= $this->getValidationCalendarDesdeJs();
                    }
                    elseif($campos['hasta']) {
                        $js .= $this->getValidationCalendarHastaJs();
                    }
                    else {
                        //none
                    }

                    //vista a renderizar
                    $pcView = 'servicio/servicios_solicitud_general';
                }

                //seteamos breadcrum superior
                $pcData['breadCrumbList']  = array();
                $finalItem = end($catalogoRutaArray);
                foreach ($catalogoRutaArray as $item) {
                    //breadcrum de la siguiente forma: "opcion","active" (con hipervinculo o no), "url" (relativa)
                    if($finalItem == $item) {
                            $activeHipervinculo = "inactive";
                            $mapeoUrl = "";
                            $addData = null;
                    }
                    else {
                        $mapeoUrl = $this->__mapUrl($item);
                        if($mapeoUrl != 'none') {
                            $activeHipervinculo = "active";
                            $addData = null;
                        }
                        else {
                            // activa link para 3er nivel, para volver al nivel anterior.
                            // addData contiene el nodo podre
                            $activeHipervinculo = "active";
                            $mapeoUrl = ""; // no tendrá hiperviculo por get, si no la data se enviara por ajax
                            $addData = $item;
                        }
                    }
                    array_push($pcData['breadCrumbList'],array($item,$activeHipervinculo,$mapeoUrl,$addData));
                }

                $dataView['pcData'] = $pcData;

                //seteamos vistas a mostrar
                $toRend = $this->view->render($pcView,$dataView);
                $toRendBreadCrum = $this->view->render('servicio/servicios_catalogo_breadcrumb',$dataView);

                $this->mifaces->newFaces();
                $this->mifaces->addToRend('menus',$toRend);
                $this->mifaces->addToRend('breadcrumbs',$toRendBreadCrum);
                if(isset($js)) {
                    $this->mifaces->addPosRendEval($js);
                    $this->mifaces->addPosRendEval("$('.select-chosen').chosen({width:  '100%', disable_search_threshold: 4});");
                    $this->mifaces->addPosRendEval("$('.input-datepicker, .input-daterange').datepicker({weekStart: 1});");
                    $this->mifaces->addPosRendEval("$('[data-toggle=\"popoverhover\"]').popover({trigger:\"hover\", container: \"body\", animation: true});");
                }

                $this->mifaces->run();

            }
        }
        else {
            //en el caso que no sea ajax la petición o se envíe mal
            $response = new \Phalcon\Http\Response();
            return $response->redirect("");
        }
    }

    private function __mapUrl($opcion) {
        $map = [
            "Sistemas TI" => "sistemas-ti",
            "Servicios TI" => "servicios-ti",
            "Infraestructura y Servicios Generales" => "infraestructura"
        ];

        return isset($map[$opcion])?$map[$opcion]:'none';
    }

    public function solicitudSoporteAction() {

        $pcView = 'servicio/servicios_solicitud_soporte';

        echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>''));

    }

    public function listarSolicitudesAction()
    {

        $pcView = 'servicio/servicios_listar_tickets';

        $js = $this->getJsDatatables();
        $js = $js." ".$this->getLikeJs();
        $tck = new Ticket();
        $tckList = $tck->getTickestByUser($this->auth->getName());
        $data = array('tckList' => $tckList);
        if($tckList == 2)
        {
            $pcView = 'servicio/servicios_error_page';
            $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
        }
        echo $this->view->render('theme_default' ,array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView,'pcData'=> $data,'jsScript'=>$js));
    }

     public function ticketAction()
    {

        $pcView = 'servicio/servicios_ver_ticket';

        $js = '';
        $ticket = new Ticket();

        $done = $ticket->findTicket($this->request->getPost('id'));
        if($done == 0)
        {
            $data = array('tck' => $ticket);
        }
        else{
            $tckList = $ticket->getTickestByUser($this->auth->getName());
            $data = array('tckList' => $tckList);
            $pcView = 'servicio/servicios_home_page';
            $msg = "Algo salió mal, por favor intente más tarde.";
            if($done == 1)
            {
                $msg = "Ticket no encontrado, revisar información ingresada.";
            }
            elseif ($dine == 2) 
            {
                $msg = "Problemas de conexión con el servicio, por favor vuelva a intentar.";
            }
            if($done)
            $js = $this->getLikeJs() . ' ' . '$.bootstrapGrowl("' . $msg . '", { type: \'danger\', align: \'center\',width: \'auto\' });';
        }
        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=> $data, 'jsScript'=>$js));
    }

	/**MJARA**/
	public function documentoAction($id = null) 
	{
		$pcView = 'servicio/servicios_ver_documento';
		$js = '';	
		if($id == null){
			$know = null;
		}else{
		  $KM = new Knowledge();
          $know = $KM->getKnowledge($id);
			//llamar a la funcion que trae los dato por el $id....
/**************************************************************************/
			/*$know['id'] = 1;
			$know['titulo'] = 'Busqueda automatica de cambio de contraseña';
			$know['fecha_formateada'] = 'Noviembre 5, 2014 - 09:10 am';
			$know['texto'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?

	Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?

	Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?";
			$know['adjunto'] = array('http://www.mm.cl/Instrucciones.txt','http://www.mm.cl/Procedimiento.pdf');*/
/**************************************************************************/
		}


		
		echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=>$know, 'jsScript'=>$js));    
	}

     public function evaluarAtencionModalAction()     
    {

        $like = $_POST['optionLike'];
        $idticket = $_POST['ticketId'];
        $toRend=$this->view->render('servicio/servicios_encuesta_modal', array("like"=>$like, "idTicket" =>$idticket));
        $this->mifaces->addToRend('contenidomodal', $toRend);
        $this->mifaces->addPosRendEval('$("#modal-encuesta").modal("show");');
        $this->mifaces->addPosRendEval($this->getLikeEvalJs());

        $this->mifaces->run();

    }


    public function TestxmlAction()
    {
        $eval = new Evaluacion();
        $ticket = rand(1000000, 10000000);
        $eval->ticket = "SD" . $ticket;
        $eval->conforme = "S";
        $eval->preg1 = 1;
        $eval->preg2 = 1;
        $eval->preg3 = 1;
        $eval->preg4 = 1;
        $eval->preg5 = 1;
        $eval->comentario = "Minions ipsum butt para tú aaaaaah jeje poulet tikka masala jiji gelatooo butt underweaaar. Poopayee poopayee hahaha tank yuuu! Bee do bee do bee do bee do bee do bee do. Chasy belloo! Hana dul sae belloo! Tank yuuu! Aaaaaah tank yuuu! Tatata bala tu gelatooo poulet tikka masala bappleees uuuhhh bananaaaa hana dul sae tatata bala tu. Ti aamoo! tulaliloo tatata bala tu chasy jeje baboiii para tú hana dul sae ti aamoo! Bee do bee do bee do.";
        $eval->save();
        indexAction();
    }

    public function TestwsAction()
    {
        $ws = new WebServiceClient();
        $response = $ws->getTicket('SD68332');
        print_r($response);
        //$tck = new Ticket();
        //$tck->findTicket("SD68157");
        //var_dump($tck);
        
    }
    public function Testws2Action()
    {
        $ws = new WebServiceClient();
        $km = new Knowledge();
        //$response = $ws->getTicket('SD68157');
        $response['ws'] = $ws->getKnowledge('KM0257');
        $response['response'] = $km->getKnowledge('KM0257');
        var_dump($response);
        //echo "<br><br>";
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
    }
    public function Testws3Action()
    {
        //$ws = new WebServiceClient();
        //$response = $ws->getContactList();
        $usr = "ALARCON, FELIPE";
        //$response = $ws->getTicketsByUser($usr);
        $tck = new Ticket();
        $response = $tck->getTicketsByUser($usr);
        //$contacto = new Contact();
        //$response = $contacto->getContactList();

        var_dump($response);
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
        
        //echo $response['request'];
    }

    public function Testws4Action()
    {
        //$ws = new WebServiceClient();
        //$response = $ws->getCIList();
        $ciItem = new CI();
        $response = $ciItem->getCompleteCIList();
        var_dump($response);
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
    }

    public function Testws5Action()
    {
        $ws = new WebServiceClient();
        //$response = $ws->getFields('Crear, Eliminar Cuenta');
        
        //var_dump($response);
        //echo '<br><br><br>';
        //$cat = new Catalog();
        //$response = $cat->getFields('Crear, Eliminar Cuenta');
        //$response = $ws->getContact("ALARCON, FELIPE");
        //$response = $ws->getRequerimentList();
        $response = $ws->getUsername('falarcon');
        var_dump($response);
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
        //$contact = new Contact();
        //$contact->getContact("ALARCON, FELIPE");
        //var_dump($contact);
    }

     public function Testws6Action()
    {
        $ws = new WebServiceClient();
        /*$response = $ws->getCatalogStepTwo('Correo Electronico');
        var_dump($response);*/
        $response = $ws->getCatalogStepOne('Sistemas TI');
        var_dump($response);
        echo "<br><br><br><br>";
        $response = $ws->getCatalogStepOne('Servicios TI');
        var_dump($response);
        /*$cat = new Catalog();
        $response = $cat->getServiceCatalogSP1('Servicios TI');
        var_dump($response);*/
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
    }

    public function Testws7Action()
    {
        $ws = $this->di->get('soapclient-catalog');
        $dom = new \DomDocument('1.0', 'UTF-8'); 
        $dom->preserveWhiteSpace = false; 
        
        $request = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:com="http://schemas.hp.com/SM/7/Common" xmlns:ns="http://schemas.hp.com/SM/7" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xm="http://localhost/xmlmime.xml">
   <soapenv:Header/>
   <soapenv:Body>
      <ns:CreateSRCInteractionViaOneStepRequest attachmentData="" attachmentInfo="" ignoreEmptyElements="true" updateconstraint="-1">
         <ns:model query="">
            <ns:keys query="" updatecounter="">
               <ns:CartId mandatory="" readonly="" type="Decimal"/>
            </ns:keys>
            <ns:instance query="" recordid="" uniquequery="" updatecounter="">
               <ns:Service mandatory="" readonly="" type="String"/>
               <ns:RequestOnBehalf mandatory="" readonly="" type="Boolean"/>
               <ns:CallbackContactName mandatory="" readonly="" type="String">PEDRON, ALFREDO</ns:CallbackContactName>
               <ns:CallbackType mandatory="" readonly="" type="String"/>
               <ns:CartId mandatory="" readonly="" type="Decimal"/>
               <ns:cartItems type="Array">
                  <ns:cartItems type="Structure">
                     <ns:CartItemId mandatory="" readonly="" type="Long"/>
                     <ns:Delivery mandatory="" readonly="" type="String"/>
                     <ns:ItemName mandatory="" readonly="" type="String">Habilitar Acceso a Wifi de Visita</ns:ItemName>
                     <ns:OptionList mandatory="" readonly="" type="String"/>
                     <ns:Options mandatory="" readonly="" type="String"/>
                     <ns:Quantity mandatory="" readonly="" type="Decimal">1</ns:Quantity>
                     <ns:RequestedFor mandatory="" readonly="" type="String">PEDRON, ALFREDO</ns:RequestedFor>
                     <ns:RequestedForDept mandatory="" readonly="" type="String"/>
                     <ns:RequestedForType mandatory="" readonly="" type="String">individual</ns:RequestedForType>
                     <ns:ServiceSLA mandatory="" readonly="" type="Decimal"/>
                  </ns:cartItems>
               </ns:cartItems>
               <ns:ContactName mandatory="" readonly="" type="String">PEDRON, ALFREDO</ns:ContactName>
               <ns:NeededByTime mandatory="" readonly="" type="DateTime"/>
               <ns:Other mandatory="" readonly="" type="String"/>
               <ns:Urgency mandatory="" readonly="" type="String">3</ns:Urgency>
               <ns:Title mandatory="" readonly="" type="String">Test</ns:Title>
               <ns:ServiceType mandatory="" readonly="" type="String"/>
               <ns:SvcSrcXML mandatory="" readonly="" type="String"/>
               <ns:Purpose type="Array">
                  <ns:Purpose mandatory="" readonly="" type="String"/>
               </ns:Purpose>
               <ns:attachments/>
            </ns:instance>
            <ns:messages>
               <com:message mandatory="" module="" readonly="" severity="" type="String"/>
            </ns:messages>
         </ns:model>
      </ns:CreateSRCInteractionViaOneStepRequest>
   </soapenv:Body>
</soapenv:Envelope>';
        /*
        '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://schemas.hp.com/SM/7" xmlns:com="http://schemas.hp.com/SM/7/Common" xmlns:xm="http://localhost/xmlmime.xml">
   <soapenv:Header/>
   <soapenv:Body>
      <ns:CreateSRCInteractionViaOneStepRequest attachmentInfo="" attachmentData="" ignoreEmptyElements="true" updateconstraint="-1">
         <ns:model query="">
            <ns:keys query="" updatecounter="">
               <ns:CartId type="Decimal" mandatory="" readonly=""></ns:CartId>
            </ns:keys>
            <ns:instance query="" uniquequery="" recordid="" updatecounter="">
               <ns:Service type="String" mandatory="" readonly=""></ns:Service>
               <ns:RequestOnBehalf type="Boolean" mandatory="" readonly=""></ns:RequestOnBehalf>
               <ns:CallbackContactName type="String" mandatory="" readonly="">PEDRON, ALFREDO</ns:CallbackContactName>
               <ns:CallbackType type="String" mandatory="" readonly=""></ns:CallbackType>
               <ns:CartId type="Decimal" mandatory="" readonly=""></ns:CartId>
               <ns:cartItems type="Array">
                  <ns:cartItems type="Structure">
                     <ns:CartItemId type="Long" mandatory="" readonly=""></ns:CartItemId>
                     <ns:Delivery type="String" mandatory="" readonly=""></ns:Delivery>
                     <ns:ItemName type="String" mandatory="" readonly="">Habilitar Acceso a Wifi de Visita</ns:ItemName>
                     <ns:OptionList type="String" mandatory="" readonly=""></ns:OptionList>
                     <ns:Options type="String" mandatory="" readonly=""></ns:Options>
                     <ns:Quantity type="Decimal" mandatory="" readonly="">1</ns:Quantity>
                     <ns:RequestedFor type="String" mandatory="" readonly="">PEDRON, ALFREDO</ns:RequestedFor>
                     <ns:RequestedForDept type="String" mandatory="" readonly=""></ns:RequestedForDept>
                     <ns:RequestedForType type="String" mandatory="" readonly="">individual</ns:RequestedForType>
                     <ns:ServiceSLA type="Decimal" mandatory="" readonly=""></ns:ServiceSLA>
                  </ns:cartItems>
               </ns:cartItems>
               <ns:ContactName type="String" mandatory="" readonly="">PEDRON, ALFREDO</ns:ContactName>
               <ns:NeededByTime type="DateTime" mandatory="" readonly=""></ns:NeededByTime>
               <ns:Other type="String" mandatory="" readonly=""></ns:Other>
               <ns:Urgency type="String" mandatory="" readonly="">2</ns:Urgency>
               <ns:Title type="String" mandatory="" readonly="">Test</ns:Title>
               <ns:ServiceType type="String" mandatory="" readonly=""></ns:ServiceType>
               <ns:SvcSrcXML type="String" mandatory="" readonly=""></ns:SvcSrcXML>
               <ns:Purpose type="Array">
                  <ns:Purpose type="String" mandatory="" readonly=""></ns:Purpose>
               </ns:Purpose>
               <ns:attachments/>
            </ns:instance>
            <ns:messages>
               <com:message type="String" mandatory="" readonly="" severity="" module=""></com:message>
            </ns:messages>
         </ns:model>
      </ns:CreateSRCInteractionViaOneStepRequest>
   </soapenv:Body>
</soapenv:Envelope>';*/

$cadena =  '
 <CreateSRCInteractionViaOneStepRequest attachmentInfo="" attachmentData="" ignoreEmptyElements="true" updateconstraint="-1">
         <model query="">
            <keys query="" updatecounter="">
               <CartId type="Decimal" mandatory="" readonly=""></CartId>
            </keys>
            <instance query="" uniquequery="" recordid="" updatecounter="">
               <Service type="String" mandatory="" readonly=""></Service>
               <RequestOnBehalf type="Boolean" mandatory="" readonly=""></RequestOnBehalf>
               <CallbackContactName type="String" mandatory="" readonly="">PEDRON, ALFREDO</CallbackContactName>
               <CallbackType type="String" mandatory="" readonly=""></CallbackType>
               <CartId type="Decimal" mandatory="" readonly=""></CartId>
               <cartItems type="Array">
                  <cartItem>
                     <CartItemId type="Long" mandatory="" readonly=""></CartItemId>
                     <Delivery type="String" mandatory="" readonly=""></Delivery>
                     <ItemName type="String" mandatory="" readonly="">Habilitar Acceso a Wifi de Visita</ItemName>
                     <OptionList type="String" mandatory="" readonly=""></OptionList>
                     <Options type="String" mandatory="" readonly=""></Options>
                     <Quantity type="Decimal" mandatory="" readonly="">1</Quantity>
                     <RequestedFor type="String" mandatory="" readonly="">PEDRON, ALFREDO</RequestedFor>
                     <RequestedForDept type="String" mandatory="" readonly=""></RequestedForDept>
                     <RequestedForType type="String" mandatory="" readonly="">individual</RequestedForType>
                     <ServiceSLA type="Decimal" mandatory="" readonly=""></ServiceSLA>
                  </cartItem>
               </cartItems>
               <ContactName type="String" mandatory="" readonly="">PEDRON, ALFREDO</ContactName>
               <NeededByTime type="DateTime" mandatory="" readonly=""></NeededByTime>
               <Other type="String" mandatory="" readonly=""></Other>
               <Urgency type="String" mandatory="" readonly="">3</Urgency>
               <Title type="String" mandatory="" readonly="">Test</Title>
               <ServiceType type="String" mandatory="" readonly=""></ServiceType>
               <SvcSrcXML type="String" mandatory="" readonly=""></SvcSrcXML>
               <Purpose type="Array">
                  <Purpose type="String" mandatory="" readonly=""></Purpose>
               </Purpose>
               <attachments/>
            </instance>
            <messages>
               <message type="String" mandatory="" readonly="" severity="" module=""></message>
            </messages>
         </model>
      </CreateSRCInteractionViaOneStepRequest>
';
//$xmlr = new \SimpleXMLElement($request);
//print_r($xmlr);die();
        if(mb_check_encoding($request, 'UTF-8'))
        {
            echo "<h2>El request está en UTF-8</h2>";
        }
        else
        {
            echo "<h2>El request no cumple el formato UTF-8</h2>";
        }
        

        echo "<br><br><br>";

       // $response = $ws->__call('CreateSRCInteractionViaOneStep',array(new \SoapVar($cadena,XSD_ANYXML)));
        $response = $ws->__doRequest($request, 'http://192.168.5.113:13080/SM/7/ws', 'CreateSRCInteractionViaOneStep', SOAP_1_1);
        
        print_r($response);
    }

    public function testFormAction(){
        $pcView = 'test/test_validation_form';
        $js = "$('.select-chosen').chosen();";
        $js .= $this->getValidationCalendarDesdeHastaJs();
        //$js .= $this->getValidationCalendarDesdeJs();
        //$js .= $this->getValidationCalendarHastaJs();
        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=>'','jsScript' => $js));  
    }

    public function CreateInteractionAction()
    {
        $campos = array(
                'detinatario' => ($this->request->getPost('campo-detinatario') == 'true'),
                'ci' => ($this->request->getPost('campo-ci') == 'true'),
                'titulo' => ($this->request->getPost('campo-titulo') == 'true'),
                'descripcion' => ($this->request->getPost('campo-descripcion') == 'true'),
                'desde' => ($this->request->getPost('campo-desde') == 'true'),
                'impacto' => ($this->request->getPost('campo-impacto') == 'true'),
                'urgencia' => ($this->request->getPost('campo-urgencia') == 'true'),
                'interrupcion' => ($this->request->getPost('campo-interrupcion') == 'true'),
                'autorizacion' => ($this->request->getPost('campo-autorizacion') == 'true'),
                'adjunto' => ($this->request->getPost('campo-adjunto') == 'true'),
                'hasta' => ($this->request->getPost('campo-hasta') == 'true')
            );
        $catalogo = array(
                'familia' => $this->request->getPost('familia'),
                'area' => $this->request->getPost('area'),
                'subarea' => $this->request->getPost('subarea')
            );
        $form;
        if(isset($_POST["select_dest"]))
        {
            $form['contact'] = $_POST["select_dest"];
        }
        else
        {
            $form['contact'] = '';
        }
        if(isset($_POST["select_sa"]))
        {
            $form['sa'] = $_POST["select_sa"];
        }
        else
        {
            $form['sa'] = '';
        }
        if(isset($_POST["select_ci"]))
        {
            $form['ci'] = $_POST["select_ci"];
        }
        else
        {
            $form['ci'] = '';
        }
        if(isset($_POST["title"]))
        {
            $form['title'] = $_POST["title"];
        }
        else
        {
            $form['title'] = '';
        }
        if(isset($_POST["description"]))
        {
            $form['description'] = $_POST["description"];
        }
        else
        {
            $form['description'] = '';
        }
        if(isset($_POST["select_is"]))
        {
            $form['interruption'] = $_POST["select_is"];
            if($form['interruption'] == 'SI')
            {
                $form['interruption'] = 'true';
            }
            else
            {
                $form['interruption'] = 'false';
            }
        }
        else
        {
            $form['interruption'] = '';
        }
        if(isset($_POST["select_i"]))
        {
            $form['impact'] = $_POST["select_i"];
        }
        else
        {
            $form['impact'] = '';
        }
        if(isset($_POST["select_u"]))
        {
            $form['urgency'] = $_POST["select_u"];
        }
        else
        {
            $form['urgency'] = '';
        }
        if(isset($_POST["desde"]))
        {
            $form['desde'] = $_POST["desde"];
        }
        else
        {
            $form['desde'] = '';
        }
        if(isset($_POST["hasta"]))
        {
            $form['hasta'] = $_POST["hasta"];
        }
        else
        {
            $form['hasta'] = '';
        }
        if(isset($HTTP_POST_FILES['example-file-multiple-input']['size']))
        {
            $tmpfile = $HTTP_POST_FILES["example-file-multiple-input"]["tmp_name"];   // temp filename
            $filename = $HTTP_POST_FILES["example-file-multiple-input"]["name"];      // Original filename
            $handle = fopen($tmpfile, "r");                  // Open the temp file
            $contents = fread($handle, filesize($tmpfile));  // Read the temp file
            fclose($handle);                                 // Close the temp file
            $decodeContent   = base64_encode($contents);
            $form['fileName'] = $filename;
            $form['fileContent'] = $decodeContent;
        }
        else
        {
            $form['fileName'] = '';
        }
        $ws = new WebServiceClient();
        $form['catalog'] = $catalogo;
        if($catalogo['subarea']=='Solucionar Problema'){
            $response = $ws->CreateRequestSol($form);
            if($response == null)
            {
                $pcView = 'servicio/servicios_error_page';
                $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
                echo $this->view->render('theme_default' ,array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=> $data,'jsScript'=>$js));
            }
            else
            {
                $response = (array)$response['CallID'];
                $response = $response['_'];
                $pcView = 'servicio/servicios_ver_ticket';

                $js = '';
                $ticket = new Ticket();

                $done = $ticket->findTicket($response);
                if($done == 0)
                {
                    $data = array('tck' => $ticket);
                }
                else{
                    $tckList = $ticket->getTickestByUser($this->auth->getName());
                    $data = array('tckList' => $tckList);
                    $pcView = 'servicio/servicios_home_page';
                    $msg = "Algo salió mal, por favor intente más tarde.";
                    if($done == 1)
                    {
                        $msg = "Ticket no encontrado, revisar información ingresada.";
                    }
                    elseif ($done == 2) 
                    {
                        $msg = "Problemas de conexión con el servicio, por favor vuelva a intentar.";
                    }
                    if($done)
                    $js = $this->getLikeJs() . ' ' . '$.bootstrapGrowl("' . $msg . '", { type: \'danger\', align: \'center\',width: \'auto\' });';
                }
                echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=> $data, 'jsScript'=>isset($js)?$js:''));
            }
        }
        else
        {
            $response = $ws->CreateRequestInteraction($form);
            if($response == null)
            {
                $pcView = 'servicio/servicios_error_page';
                $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
                echo $this->view->render('theme_default' ,array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=> $data,'jsScript'=>$js));
            }
            /*$status = $response['status'];
            if(strpos($status, 'FAILURE'))
            {
                $pcView = 'servicio/servicios_error_page';
                $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'El WebService entrega respuesta: '.$status.' cuando se intenta crear una interacción.' );
                echo $this->view->render('theme_default' ,array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=> $data,'jsScript'=>$js));
            }
            else
            {
                var_dump($response);
            }
            */
            /*$js = "$.bootstrapGrowl('Error Interno. Repita el procedimiento.',{type:\"warning\",align:\"center\"});";
            $pcView = 'servicio/servicios_home_page';
        
            $tck = new Ticket();
            $tckList = $tck->getTickestByUser($this->auth->getName());
            $data = array('tckList' => $tckList);
            if($tckList == 2)
            {
                $pcView = 'servicio/servicios_error_page';
                $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
            }
            //$js = $this->getJsEncuesta();
            $js =$js. " ". $this->getLikeJs();
            echo $this->view->render('theme_default' ,array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=> $data,'jsScript'=>$js));*/
            var_dump($response['response']);
            echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
        }
    }

    public function TestCreateInteractionAction()
    {
        $campos = array(
                    'detinatario' => 'si',
                    'ci' => 'si',
                    'titulo' => 'si',
                    'descripcion' => 'si',
                    'desde' => 'no',
                    'impacto' =>'si',
                    'urgencia' => 'si',
                    'interrupcion' => 'si',
                    'autorizacion' => 'no',
                    'adjunto' => 'op',
                    'hasta' => 'no'
                );
        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            $catalog = array(
                    'area' => 'Administrador documental',
                    'subarea' => 'Desbloqueo de cuentas',
                );

            $contacto = new Contact();
            $contactList = $contacto->getContactList();

            $ciItem = new CI();
            $listas = $ciItem->getCompleteCIList();
            
            $pcView = 'servicio/servicios_solicitud_test';
            //$data = array('campos' => $campos, 'catalog' => $catalog, 'contactos' => $contactList, 'ci' => $ciList);

            //asi debe ser la lista de  servicios afectados y sus hijos ci
            /*$listas = array('servicio afectado 1' => array('ci1','ci2','ci3'),
                            'servicio afectado 2' => array('ci4','ci5','ci6'),
                            'servicio afectado 3' => array('ci7','ci8','ci9'));*/

            $data['listas'] = $listas;
            $data['campos'] = $campos;
            $data['catalogo'] = $catalog;
            $data['contactos'] = $contactList;
            $js = $this->getComponenteServAfectadoJs($listas);

            echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=> $data, 'jsScript'=>$js));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            try
            {
                //Aqí voy
                if(isset($_FILES["example-file-multiple-input"]["tmp_name"]))
                {
                    $tmpfile = $_FILES["example-file-multiple-input"]["tmp_name"];   // temp filename
                    $filename = $_FILES["example-file-multiple-input"]["name"];      // Original filename
                    $handle = fopen($tmpfile, "r");                  // Open the temp file
                    $contents = fread($handle, filesize($tmpfile));  // Read the temp file
                    fclose($handle);                                 // Close the temp file
                    $decodeContent   = base64_encode($contents);
                    $attach = array(
                        'content' => $decodeContent, 
                        'name' => $filename, 
                        'type' => $_FILES["example-file-multiple-input"]['type'], 
                        'size' => $_FILES["example-file-multiple-input"]["size"]
                    );
                }
                else
                {
                    $attach = array(
                        'content' => '', 
                        'name' => '', 
                        'type' => '', 
                        'size' => 0
                    );
                }
                $ws = new WebServiceClient();
                $response = $ws->createRequestTicket($this->request->getPost('select_dest'), $this->request->getPost('select_u'),
                    $this->request->getPost('description'), $this->request->getPost('area'), $this->request->getPost('subarea'),
                    $this->auth->getName(), $this->request->getPost('select_i'), $this->request->getPost('select_ci'),
                    $this->request->getPost('title'), $this->request->getPost('select_sa'), $this->request->getPost('select_is'),
                    $attach);
                //var_dump($response);
                $response = (array)$response['CallID'];
                $response = $response['_'];
                $pcView = 'servicio/servicios_ver_ticket';

                $js = '';
                $ticket = new Ticket();

                $done = $ticket->findTicket($response);
                if($done == 0)
                {
                    $data = array('tck' => $ticket);
                }
                else{
                    $tckList = $ticket->getTickestByUser($this->auth->getName());
                    $data = array('tckList' => $tckList);
                    $pcView = 'servicio/servicios_home_page';
                    $msg = "Algo salió mal, por favor intente más tarde.";
                    if($done == 1)
                    {
                        $msg = "Ticket no encontrado, revisar información ingresada.";
                    }
                    elseif ($done == 2) 
                    {
                        $msg = "Problemas de conexión con el servicio, por favor vuelva a intentar.";
                    }
                    if($done)
                    $js = $this->getLikeJs() . ' ' . '$.bootstrapGrowl("' . $msg . '", { type: \'danger\', align: \'center\',width: \'auto\' });';
                }
            }
            catch (Exception $e)
            {
                $pcView = 'servicio/servicios_error_page';
                $data = array( 'error-number' => '500 - Error interno en el servidor', 'error-description' => 'Problemas al establecer conexión a los web service, por favor revisar permisos de acceso y configuración.' );
            }
            
            echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=> $data, 'jsScript'=>$js));    
        }
    }


    /* 
        Métodos para cargar Javascript 
    */

    private function getJsCatalogo() 
    {
        $jsScript = "
            $('#btnTec').click(function() {
                
                $('.bcAplicaciones').removeClass('hidden');
            });
            
            $('#btnServ').click(function() {
                $('.sg').removeClass('hidden');
                $('.os').addClass('hidden');

            });

            $('#btnOtros').click(function() {
                $('.os').removeClass('hidden');
                $('.sg').addClass('hidden');

            });

            $('#btnCerrar').click(function() {
                $('.strap').addClass('hidden');
                $('.defaultClass').removeClass('hidden');
                $('#textInicio').removeClass('hidden');
                $('#bcInicio').addClass('hidden');
                $('.nsBoton-tecnologia').removeClass('hidden');
                $('.bcTecnologia').addClass('hidden');
                $('.nsBoton-aplicaciones').addClass('hidden');
                $('.bcTecnologia').addClass('hidden');
                $('.bcAplicaciones').addClass('hidden');    
                $('.bcTecnologia').addClass('hidden');
                $('.bcFormSolicitudes').addClass('hidden');
                $('#textFormSolicitudes').addClass('hidden');
                $('#textFormSolicitudes').html('');
            });

            $('.nsBoton').click(function(event) {
                $('.strap').addClass('hidden');
                if($(this).data('next') == 'tecnologia'){

                    $('#textInicio').addClass('hidden');
                    $('#bcInicio').removeClass('hidden');
                    $('.nsBoton-tecnologia').removeClass('hidden');
                    $('.bcTecnologia').removeClass('hidden');
                }
                if($(this).data('next') == 'aplicaciones'){
                    $('.nsBoton-aplicaciones').removeClass('hidden');
                    $('.bcTecnologia').removeClass('hidden');
                    $('.bcAplicaciones').removeClass('hidden'); 
                }

                if($(this).data('next') == 'form-solicitudes'){
                    $('#helptitle').addClass('hidden');
                    $('.bcTecnologia').removeClass('hidden');
                    $('.bcFormSolicitudes').removeClass('hidden');
                    $('#textFormSolicitudes').removeClass('hidden');
                    if($(this).data('type') == 'terceraVista'){
                        $('#textFormSolicitudes').html($(this).data('val'));                
                    } else if ($(this).data('type') == 'segundaVista') {
                        $('#textFormSolicitudes').html($(this).data('val'));                        
                    } else if ($(this).data('type') == 'primeraVista') {
                        $('#bcInicio').removeClass('hidden');
                        $('#textInicio').addClass('hidden');
                        $('#textFormSolicitudes').html($(this).data('val'));
                    }
                    $('#infoTitle').html('Información Específica');
                    $('.nsBoton-form-solicitudes').removeClass('hidden');   
                }
            });
            
        ";

        return $jsScript;
    }

    private function getJsDatatables() 
    {
        $jsScript =
        "
            var TablesDatatables = function() {

                return {
                    init: function() {
                        /* Initialize Bootstrap Datatables Integration */
                        //App.datatables();

                        /* Initialize Datatables */
                        $('#table').dataTable({
                           'paging':   true,
                            columnDefs: [ { orderable: true, targets: [ 1, 2, 3] } ]
                        });

                        /* Add placeholder attribute to the search input */
                        $('div.dataTables_length').html('');
                        $('#table_filter').html('');
                        $('#table_info').html('');
                    }
                };
            }();

            $(function(){ TablesDatatables.init(); });
        ";

        return $jsScript;
    }
	
    private function getJsKnowsDatatables() 
    {
        $jsScript =
        "
			var table_ = $('#conocimiento-table');
            var TableskDatatables = function() {

                return {
                    init: function() {
                        /* Initialize Bootstrap Datatables Integration */
                        //App.datatables();

                        /* Initialize Datatables */
                        table_.dataTable({
                           'paging':   true,
                            columnDefs: [ { orderable: false } ]
                        });

                        /* Add placeholder attribute to the search input */
                        $('div.dataTables_length').html('');
                        $('#table_filter').html('');
                        $('#table_info').html('');
                    }
                };
            }();
        ";
		
        return $jsScript;
    }	

    private function getLikeJs() 
    {
        $jsScript =
        "
            $(\"[name^='conf-']\").on('click', function() {
            $(\"#optionLike\").val($(this).val());
            $(\"#ticketId\").val($(this).data('id'));
            $(\"#evaluacionForm\").submit();
            });


        ";

        //$jsScript = '';
    
        return $jsScript;
    }

    private function getLikeEvalJs()
    {
        $jsScript =
        "
            $(\"[name^='preg0']\").on('click', function() {
                $(\"#preg01\").toggleClass('likeimgGray');
                $(\"#preg01\").toggleClass('likeimg');
                $(\"#preg02\").toggleClass('dislikeimgGray');
                $(\"#preg02\").toggleClass('dislikeimg');
            });
        ";

        return $jsScript;
    }

    private function getComponenteServAfectadoJs($lista)
    {

        //lista debe venir de la forma Array("padre"=>array("hijo1","hijo2",...),"padre2"=>array(...));
        $listajson = json_encode($lista);

        $jsScript =
        "
        $(\"#input-select_sa\").on('change', function(){
                //lista completa desde ws
                var lista_completa =".$listajson." ;

                //dejamos vacia la listaASasa
                $(\"#input-select_ci\").empty();
                $(\"#input-select_ci\").trigger(\"chosen:updated\");

                // actualizamos el select de componente
                $.each(lista_completa[$(this).val()], function(key,value) {
                
                  var option = $(\"<option></option>\")
                    .attr(\"value\", value)
                    .text(value);
                  $(\"#input-select_ci\").append(option);
                });
                $(\"#input-select_ci\").trigger(\"chosen:updated\");
        });
        ";

        return $jsScript;
    }

    private function getValidationJs() {
        $jsScript =
        '
            $("#btnGuardar").on(\'click\', function() {
            var statusForm = 1;
            $("[id^=input-]").each(function(){
                var id = $(this).attr(\'id\');
                var alert = id.split("-")[1];
                alert = "#alert-"+alert;

                if($(this).val().length === 0) {
                    statusForm = 0;
                    $(alert).css(\'display\',\'inline\');
                }
                else {
                    $(alert).css(\'display\',\'none\');
                }
            });
            if(statusForm === 1){
                $("#solicitudForm").submit();
            }
        });
        ';

        return $jsScript;
    }

    private function getValidationCalendarDesdeHastaJs() {
        $jsScript =
        '

            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

            var checkin = $("#input-desde").datepicker({

                beforeShowDay: function (date) {
                    return date.valueOf() >= now.valueOf();
                },
                autoclose: true

            }).on("changeDate", function (ev) {
                if (checkout.datepicker("getDate") == null || ev.date.valueOf() >= checkout.datepicker("getDate").valueOf()) {

                    var newDate = new Date(ev.date);
                    newDate.setDate(newDate.getDate());
                    checkout.datepicker("update", newDate);

                }
                else {
                    var newDate = new Date(ev.date);
                    newDate.setDate(newDate.getDate());
                    checkout.datepicker("update", "");
                }

                 $("#input-hasta")[0].focus();
            });


            var checkout = $("#input-hasta").datepicker({
                beforeShowDay: function (date) {
                    if (checkin.datepicker("getDate") == null) {
                        return date.valueOf() >= new Date().valueOf();
                    } else {
                        return date.valueOf() >= checkin.datepicker("getDate").valueOf();
                    }
                },
                autoclose: true

            }).on("changeDate", function (ev) {});

        ';

        return $jsScript;
    }

    private function getValidationCalendarDesdeJs() {
        $jsScript =
        '
            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

            var checkin = $("#input-desde").datepicker({

                beforeShowDay: function (date) {
                    return date.valueOf() >= now.valueOf();
                },
                autoclose: true

            });
        ';

        return $jsScript;
    }

    private function getValidationCalendarHastaJs() {
         $jsScript =
        '
            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

            var checkin = $("#input-hasta").datepicker({

                beforeShowDay: function (date) {
                    return date.valueOf() >= now.valueOf();
                },
                autoclose: true

            });
        ';

        return $jsScript;
    }

}