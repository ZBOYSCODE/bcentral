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
		echo $this->view->render('themeLudicoM2',array('lmView'=>'menu/leftMenu','menuSel'=>'dashboard','pcView'=>'solicitudes/dashboardM','pcData'=>''));
    }

    public function consultarAction()
    {   
    	echo $this->view->render('themeLudicoM2', array('lmView'=>'menu/leftMenu', 'menuSel'=>'consultarSol','pcView'=>'solicitudes/consultaSolicitud', 'pcData'=>''));    
    }

    public function evaluarAction()
    {
        $jsScript = "
        $('.stp-trat-btn').click(
            function(){

                $('#stp-trat-'+$(this).data('stp')+' .stp-trat-btn').removeClass('active');
                $(this).addClass('active');

                $('.stp-trat').css('display','none');
                
                $('#stp-trat-'+$(this).data('next')).css('display','block');                
                
                if($(this).data('type')=='pregunta1'){
                    $('#barra-progreso').css('width','33%');
                }
                if($(this).data('type')=='pregunta2'){
                    $('#barra-progreso').css('width','66%');
                    $('#barra-progreso').removeClass('progress-bar-danger');
                    $('#barra-progreso').addClass('progress-bar-warning');
                }

                if($(this).data('type')=='pregunta3'){
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
                    $('#barra-progreso').css('width','10%');    
                    $('#barra-progreso').removeClass('progress-bar-warning');
                    $('#barra-progreso').addClass('progress-bar-danger');
                }

                if($(this).data('next') == 'pregunta2')
                {
                    $('#barra-progreso').css('width','33%');    
                    $('#barra-progreso').removeClass('progress-bar-warning');
                    $('#barra-progreso').addClass('progress-bar-danger');
                }               

                $('.stp-trat').css('display','none');
                $('#stp-trat-'+$(this).data('next')).css('display','block');
            }
        );      ";
        echo $this->view->render('themeLudicoM2', array('lmView'=>'menu/leftMenu', 'menuSel'=>'evaluarSol','pcView'=>'solicitudes/evaluarSolicitud', 'pcData'=>'', 'jsScript'=>$jsScript));    
    }

    public function migueloAction()
    {
    	echo $this->view->render('themeLudicoM', array('lmView'=>'menu/leftMenu', 'pcView'=>'solicitudes/consultaSolicitud', 'pcData'=>''));    
    }
    public function dashboardMAction()
    {
    	echo $this->view->render('themeLudicoM', array('lmView'=>'menu/leftMenu','menuSel'=>'dashboard', 'pcView'=>'solicitudes/dashboardM', 'pcData'=>''));    
    }	
public function dashboardM2Action()
    {
    	echo $this->view->render('themeLudicoM2', array('lmView'=>'menu/leftMenu','menuSel'=>'dashboard', 'pcView'=>'solicitudes/dashboardM', 'pcData'=>''));    
    }	

}