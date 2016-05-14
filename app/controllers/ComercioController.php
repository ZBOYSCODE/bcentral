<?php
namespace Gabs\Controllers;
use Gabs\Models\Personas;
use Gabs\Models\Evaluacion;
use Gabs\Models\WebServiceClient;
use Gabs\Models\Ticket;
use Gabs\Models\Contact;
use Gabs\Models\Catalog;
use Gabs\Models\CI;
 
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
        $pcView = 'servicio/servicios_base_conocimiento';
        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=>''));    
    }

	/**MJARA**/
    public function conocimientoAction() 
    {

		$pcView = 'servicio/servicios_base_conocimiento';
		$data = array('knowList' => array());
		echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>$data));
    }

    public function solicitudServicioAction($tipo) 
    {

        $js = '';
        if($tipo == "tecnologia") {
            $js = $this->getJsCatalogo();
            $pcView = 'servicio/servicios_solicitud_tecnologia';
        }
        else if ($tipo == "generales") {
            $js = $this->getJsCatalogo();
            $pcView = 'servicio/servicios_solicitud_sg';
        }
        else if ($tipo == "otros") {
            $js = $this->getJsCatalogo();
            $pcView = 'servicio/servicios_solicitud_otros';
        }
        else {
            $this->indexAction();
            exit;
        }

        echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>'','jsScript'=>$js));

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
		
			//llamar a la funcion que trae los dato por el $id....
/**************************************************************************/
			$know['id'] = 1;
			$know['titulo'] = 'Busqueda automatica de cambio de contraseña';
			$know['fecha_formateada'] = 'Noviembre 5, 2014 - 09:10 am';
			$know['texto'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?

	Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?

	Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?";
			$know['adjunto'] = array('http://www.mm.cl/Instrucciones.txt','http://www.mm.cl/Procedimiento.pdf');
/**************************************************************************/
		}


		
		echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=>$know, 'jsScript'=>$js));    
	}

     public function evaluarAtencionModalAction()     
    {

        $like = $_POST['optionLike'];
        $toRend=$this->view->render('servicio/servicios_encuesta_modal', array("like"=>$like));
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
        //$ws = new WebServiceClient();
        //$response = $ws->getTicket('SD68157');
        //var_dump($response);
        $tck = new Ticket();
        $tck->findTicket("SD68157");
        var_dump($tck);
        
    }
    public function Testws2Action()
    {
        $ws = new WebServiceClient();
        $response = $ws->getTicket('SD68157');
        
        var_dump($response);
        echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
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
        //$ws = new WebServiceClient();
        //$response = $ws->getContact("ALARCON, FELIPE");
        //$response = $ws->getRequerimentList();

        //var_dump($response);
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
        $contact = new Contact();
        $contact->getContact("ALARCON, FELIPE");
        var_dump($contact);
    }

     public function Testws6Action()
    {
        //$ws = new WebServiceClient();
        //$response = $ws->getCatalogStepOne('Servicios TI');
        $cat = new Catalog();
        $response = $cat->getServiceCatalog('Servicios TI');
        var_dump($response);
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
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
                    'adjunto' => 'no',
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
                $ws = new WebServiceClient();
                $response = $ws->createRequestTicket($this->request->getPost('select_dest'), $this->request->getPost('select_u'),
                    $this->request->getPost('description'), $this->request->getPost('area'), $this->request->getPost('subarea'),
                    $this->di->get('test-user'), $this->request->getPost('select_i'), $this->request->getPost('select_ci'),
                    $this->request->getPost('title'), $this->request->getPost('select_sa'), $this->request->getPost('select_is'));
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
                    elseif ($dine == 2) 
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
                            columnDefs: [ { orderable: true, targets: [ 1, 2, 3] } ]
                        });

                        /* Add placeholder attribute to the search input */
                        $('div.dataTables_length').html('');
                        $('#table_filter').html('');
                        $('#table_info').html('');
                        $('#table_paginate').html('');
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
            $(\"[name^='conf']\").on('click', function() {
            $(\"#optionLike\").val($(this).val());
            $(\"#ticketId\").val('SD1234');
            $(\"#evaluacionForm\").submit();
            });


        ";
    
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
        $(\"#select_sa\").on('change', function(){
                //lista completa desde ws
                var lista_completa =".$listajson." ;

                //dejamos vacia la listaASasa
                $(\"#select_ci\").empty();
                $(\"#select_ci\").trigger(\"chosen:updated\");

                // actualizamos el select de componente
                $.each(lista_completa[$(this).val()], function(key,value) {
                
                  var option = $(\"<option></option>\")
                    .attr(\"value\", value)
                    .text(value);
                  $(\"#select_ci\").append(option);
                });
                $(\"#select_ci\").trigger(\"chosen:updated\");
        });
        ";

        return $jsScript;
    }

}