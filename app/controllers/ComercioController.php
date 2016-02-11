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
        $jsScript = "
            // Get the elements where we will attach the charts
            var dashWidgetChart = $('#dash-widget-chart');

            // Random data for the chart
            var dataEarnings = [[1, 1560], [2, 1650], [3, 1320], [4, 1950], [5, 1800]];
            var dataSales = [[1, 800], [2, 847], [3, 480], [4, 950], [5, 600]];
            var dataPendientes = [[1, 583], [2, 782], [3, 749], [4, 273], [5, 356]];
            var dataCurso = [[1, 291], [2, 385], [3, 185], [4, 592], [5, 527]];

            // Array with month labels used in chart
            var chartMonths = [[1, 'Enero'], [2, 'Febrero'], [3, 'Marzo'], [4, 'Abril'], [5, 'Mayo']];

            // Initialize Dash Widget Chart
            $.plot(dashWidgetChart,
                [
                    {
                        data: dataEarnings,
                        lines: {show: true, fill: false},
                        points: {show: true, radius: 6, fillColor: 'green'}
                    },
                    {
                        data: dataSales,
                        lines: {show: true, fill: false},
                        points: {show: true, radius: 6, fillColor: '#CACA08'}
                    },
                    {
                        data: dataPendientes,
                        lines: {show: true, fill: false},
                        points: {show: true, radius: 6, fillColor: '#C30101'}
                    },
                    {
                        data: dataCurso,
                        lines: {show: true, fill: false},
                        points: {show: true, radius: 6, fillColor: 'grey'}
                    }                   
                ],
                {
                    colors: ['#ffffff', 'white','white','white'],
                    legend: {show: false},
                    grid: {borderWidth: 0, hoverable: true, clickable: true},
                    yaxis: {show: false},
                    xaxis: {show: false, ticks: chartMonths}
                }
            );          

            // Creating and attaching a tooltip to the widget
            var previousPoint = null, ttlabel = null;
            dashWidgetChart.bind('plothover', function(event, pos, item) {

                if (item) {
                    if (previousPoint !== item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $('#chart-tooltip').remove();
                        var x = item.datapoint[0], y = item.datapoint[1];

                        // Get xaxis label
                        var monthLabel = item.series.xaxis.options.ticks[item.dataIndex][1];

                        if (item.seriesIndex === 1) {
                            ttlabel = '<strong>' + y + '</strong> Cerradas en <strong>' + monthLabel + '</strong>';
                        } else if(item.seriesIndex === 2) {
                            ttlabel = '<strong>' + y + '</strong> Pendientes en <strong>' + monthLabel + '</strong>';
                        } else if(item.seriesIndex === 3) {
                            ttlabel = '<strong>' + y + '</strong> En Curso en <strong>' + monthLabel + '</strong>';
                        } else {
                            ttlabel = '<strong>' + y + '</strong> Abiertas en <strong>' + monthLabel + '</strong>';
                        }


                        $('<div id=chart-tooltip class=chart-tooltip >' + ttlabel + '</div>')
                            .css({top: item.pageY - 50, left: item.pageX - 50}).appendTo('body').show();
                    }
                }
                else {
                    $('#chart-tooltip').remove();
                    previousPoint = null;
                }
            });

            $.plot('#placeholder', data, {
    series: {
        pie: { 
            show: true,
            radius: 1,
            label: {
                show: true,
                radius: 3/4,
                formatter: labelFormatter,
                background: { 
                    opacity: 0.5,
                    color: '#000'
                }
            }
        }
    },
    legend: {
        show: false
    }
}); ";
		echo $this->view->render('themeLudico',array('lmView'=>'menu/leftMenu','pcView'=>'solicitudes/dashboard','pcData'=>'', 'jsScript'=>$jsScript));
    }

    public function consultarAction()
    {
    	echo $this->view->render('themeLudico', array('lmView'=>'menu/leftMenu', 'pcView'=>'solicitudes/consultaSolicitud', 'pcData'=>''));    
    }

    public function evaluarAction()
    {
        $jsScript = "$('.stp-trat-btn').click(
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
        echo $this->view->render('themeLudico', array('lmView'=>'menu/leftMenu', 'pcView'=>'solicitudes/evaluarSolicitud', 'pcData'=>'', 'jsScript'=>$jsScript));    
    }

    public function migueloAction()
    {
    	echo $this->view->render('themeLudicoM', array('lmView'=>'menu/leftMenu', 'pcView'=>'solicitudes/consultaSolicitud', 'pcData'=>''));    
    }


}