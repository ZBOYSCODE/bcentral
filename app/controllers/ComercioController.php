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
        $tckList = $tck->getTickestByUser($this->di->get('test-user'));
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

            //breadcrum de la siguiente forma: "opcion","active" (con hipervinculo o no), "url" (relativa)
            $pcData['breadCrumbList']  = [
              array($catalogoPadre,'active',$tipo),
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
                    $js = $this->getComponenteServAfectadoJs($listas);
                    $js .= $this->getValidationJs();

                    //vista a renderizar
                    $pcView = 'servicio/servicios_solicitud_general';
                }

                //seteamos breadcrum superior
                $pcData['breadCrumbList']  = array();

                foreach ($catalogoRutaArray as $item) {
                    //breadcrum de la siguiente forma: "opcion","active" (con hipervinculo o no), "url" (relativa)
                    $mapeoUrl = $this->__mapUrl($item);
                    if($mapeoUrl != 'none') {
                        $activeHipervinculo = "active";
                    }
                    else {
                        $activeHipervinculo = "inactive";
                        $mapeoUrl = "";
                    }
                    array_push($pcData['breadCrumbList'],array($item,$activeHipervinculo,$mapeoUrl));
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
        $tckList = $tck->getTickestByUser($this->di->get('test-user'));
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
            $tckList = $ticket->getTickestByUser($this->di->get('test-user'));
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
        $response = $ws->getTicket('SD12544');
        var_dump($response);
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
        $response['km'] = $km->getKnowledge('KM0257');
        var_dump($response);
        echo "<br><br>";
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
        $response = $ws->getContact("ALARCON, FELIPE");
        //$response = $ws->getRequerimentList();

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

    public function testFormAction(){
        $pcView = 'test/test_validation_form';
        $js = "$('.select-chosen').chosen();";
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
                    $tckList = $ticket->getTickestByUser($this->di->get('test-user'));
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
            $status = $response['status'];
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
                    $this->di->get('test-user'), $this->request->getPost('select_i'), $this->request->getPost('select_ci'),
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
                    $tckList = $ticket->getTickestByUser($this->di->get('test-user'));
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


    public function LdapAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            $pcView = 'test/ldap';
            $js = '';
            $pcData = '';
            echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=> $pcData, 'jsScript'=>$js));
        }
        else
        {
            // Active Directory server
            $ldap_host = $this->request->getPost('ip');
         
            // Active Directory DN
            $ldap_dn = $this->request->getPost('dn');
            $user = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');
            $ldap_usr_dom = $this->request->getPost('dom');
            // Active Directory user group
            $ldap_user_group = "WebUsers";
         
            // Active Directory manager group
            $ldap_manager_group = "WebManagers";
         
            // Domain, for purposes of constructing $user
            $ldap_usr_dom = $this->request->getPost('dom');
         
            // connect to active directory
            $ldap = ldap_connect($ldap_host);
            if(!$ldap)
            {
                echo "<br>Error de conexión a LDAP";
            }
            // verify user and password
            if($bind = @ldap_bind($ldap, $user.$ldap_usr_dom, $password)) {
                // valid
                // check presence in groups
                $filter = "(sAMAccountName=".$user.")";
                $attr = array("memberof");
                $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
                $entries = ldap_get_entries($ldap, $result);
                ldap_unbind($ldap);
         
                // check groups
                foreach($entries[0]['memberof'] as $grps) {
                    // is manager, break loop
                    if(strpos($grps, $ldap_manager_group)) { $access = 2; break; }
         
                    // is user
                    if(strpos($grps, $ldap_user_group)) $access = 1;
                }
         
                if($access != 0) {
                    // establish session variables
                    $_SESSION['user'] = $user;
                    $_SESSION['access'] = $access;
                    echo "<br>Session variables set";
                } else {
                    // user has no rights
                    echo "<br>User without rights";
                }
         
            } else {
                // invalid name or password
                echo "<br>Invalid user or password";
            }
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
            var TablesDatatables = function() {

                return {
                    init: function() {
                        /* Initialize Bootstrap Datatables Integration */
                        //App.datatables();

                        /* Initialize Datatables */
                        $('#table').dataTable({
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

            $(function(){ TablesDatatables.init(); });
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

}