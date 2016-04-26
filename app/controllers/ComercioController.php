<?php
namespace Gabs\Controllers;
use Gabs\Models\Personas;
 
class ComercioController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    { 
        $js = $this->getLikeJs();
        echo $this->view->render('theme_home',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>'','pcData'=>'','jsScript'=>$js));

    }

    public function consultarAction()
    {   
        echo $this->view->render('theme_home', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>'', 'pcData'=>''));    
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

        echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>$pcView, 'pcData'=>'', 'jsScript'=>$js));    
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