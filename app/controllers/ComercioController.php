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
        $js = $this->getJsEncuesta();
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

         $js = $js." "."$('.select-chosen').select2({width: '100%'});";

        echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>'','jsScript'=>$js));

    }

    public function solicitudSoporteAction() {

        $pcView = 'servicio/servicios_solicitud_soporte';

        echo $this->view->render('theme_default',array('lmView'=>'menu/leftMenu','menuSel'=>'','pcView'=>$pcView,'pcData'=>''));

    }

    public function listarSolicitudesAction()
    {

        $pcView = 'servicio/servicios_listar_tickets';

        $js = $this->getJsEncuesta();
        $js = $js." ".$this->getJsDatatables();

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

    private function getJsEncuesta() {
             $jsScript = "

                $('#cerrarModal').click(
                    function(){
                        $('#barra-progreso').css('width','10%'); 
                        $('#barra-progreso').removeClass('progress-bar-success');
                        $('#barra-progreso').addClass('progress-bar-danger');
                        $('#stp-trat-pregunta1 .stp-trat-btn').addClass('active');
                        $('#stp-trat-pregunta1').css('display','block'); 
                        $('#stp-trat-resultado').css('display','none'); 
                    }
                );

                $('.stp-trat-btn').click(
                    function(){

                        $('#stp-trat-'+$(this).data('stp')+' .stp-trat-btn').removeClass('active');
                        $(this).addClass('active');

                        $('.stp-trat').css('display','none');
                        
                        $('#stp-trat-'+$(this).data('next')).css('display','block');                
                        
                        if($(this).data('type')=='pregunta1'){
                            $('#barra-progreso').css('width','20%');
                        }

                        if($(this).data('type')=='pregunta2'){
                            $('#barra-progreso').css('width','40%');
                            $('#barra-progreso').removeClass('progress-bar-danger');
                            $('#barra-progreso').addClass('progress-bar-warning');
                        }

                        if($(this).data('type')=='pregunta3'){
                            $('#barra-progreso').css('width','60%');
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-success');
                        }
                        if($(this).data('type')=='pregunta4'){
                            $('#barra-progreso').css('width','80%');
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-success');
                        }
                        if($(this).data('type')=='pregunta5'){
                            $('#barra-progreso').css('width','100%');
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-success');
                        }
                    }
                );

                $('.stp-trat-btn-menu').click(
                    function(){

                        if($(this).data('next') == 'pregunta1')
                        {
                            $('#barra-progreso').css('width','20%');    
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-danger');
                        }

                        if($(this).data('next') == 'pregunta2')
                        {
                            $('#barra-progreso').css('width','40%');    
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-danger');
                        }
                        if($(this).data('next') == 'pregunta3')
                        {
                            $('#barra-progreso').css('width','60%');    
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-danger');
                        }  
                        if($(this).data('next') == 'pregunta4')
                        {
                            $('#barra-progreso').css('width','80%');    
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-danger');
                        }   
                        if($(this).data('next') == 'pregunta5')
                        {
                            $('#barra-progreso').css('width','100%');    
                            $('#barra-progreso').removeClass('progress-bar-warning');
                            $('#barra-progreso').addClass('progress-bar-danger');
                        }                 
              

                        $('.stp-trat').css('display','none');
                        $('#stp-trat-'+$(this).data('next')).css('display','block');
                    }
                );   

                $(function() {
                    $('#search').on('keyup', function() {
                        var pattern = $(this).val();
                        $('.searchable-container .items').hide();
                        $('.searchable-container .items').filter(function() {
                            return $(this).text().match(new RegExp(pattern, 'i'));
                        }).show();
                    });
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
                            columnDefs: [ { orderable: true, targets: [ 1, 2, 3, 4 ] } ]
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

}