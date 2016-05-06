<?php
namespace Gabs\Controllers;
use Gabs\Models\Personas;
use Gabs\Models\Evaluacion;
use Gabs\Models\WebServiceClient;
use Gabs\Models\Ticket;
use Gabs\Models\Contact;
 
class ComercioController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()

    {   
        $pcView = 'servicio/servicios_home_page';
        $tck = new Ticket();
        $tckList = $tck->getTickestByUser("ALARCON, FELIPE");
        //$js = $this->getJsEncuesta();
        $js = $this->getLikeJs();
        echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>array('tckList' => $tckList),'jsScript'=>$js));

    }

    public function consultarAction()
    {   
        $pcView = 'servicio/servicios_base_conocimiento';
        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=>''));    
    }

    public function conocimientoAction() {

        $pcView = 'servicio/servicios_base_conocimiento';

        echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>''));
    }

    public function solicitudServicioAction($tipo) {

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

        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=>'', 'jsScript'=>$js));    
    }

     public function ticketAction()
    {

        $pcView = 'servicio/servicios_ver_ticket';

        $js = '';
        $ticket = new Ticket();

        $done = $ticket->findTicket($this->request->getPost('id'));
        if($done)
        {
            $data = array('tck' => $ticket);
        }
        else{
            $tckList = $ticket->getTickestByUser("ALARCON, FELIPE");
            $data = array('tckList' => $tckList);
            $pcView = 'servicio/servicios_home_page';
            $js = $this->getLikeJs() . ' ' . '$.bootstrapGrowl("Ticket no encontrado, revisar información ingresada", { type: \'danger\', align: \'center\',width: \'auto\' });';
        }
        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=> $data, 'jsScript'=>$js));    
    }

     public function documentoAction() {
        $pcView = 'servicio/servicios_ver_documento';

        $js = '';

        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=>'', 'jsScript'=>$js));    
     }

     public function evaluarAtencionModalAction()  {

        $like = $_POST['optionLike'];
        $toRend=$this->view->render('servicio/servicios_encuesta_modal', array("like"=>$like));
        $this->mifaces->addToRend('contenidomodal', $toRend);
        $this->mifaces->addPosRendEval('$("#modal-encuesta").modal("show");');
        $this->mifaces->addPosRendEval($this->getLikeEvalJs());

        $this->mifaces->run();

     }



    private function getJsCatalogo() {
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

    private function getJsDatatables() {
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
        $ws = new WebServiceClient();
        $response = $ws->getCIList();
        var_dump($response);
        echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
    }

    public function Testws5Action()
    {
        $ws = new WebServiceClient();
        $response = $ws->getRequerimentList();
        var_dump($response);
        //echo '<br/><br/>Request : <br/><xmp>'. $response['request'] . '</xmp>';
    }

    private function getLikeJs() {
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

    private function getLikeEvalJs() {
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
}