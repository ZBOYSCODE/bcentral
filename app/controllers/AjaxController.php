<?php
namespace Gabs\Controllers;
use Gabs\Models\Personas;
 
class AjaxController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {

	
	
		$toRend=$this->view->render('ajax/chart_test',array());
		$this->mifaces->addToRend('contenidomodal', $toRend);
		
		
		$this->mifaces->addPosRendEval('
					$graph=$("#graph");
					$("#graph").css("height","300px").css("width","100%");
					var chartMonths = [[1, "Ene"], [2, "Feb"], [3, "Mar"], [4, "Abr"], [5, "May"], [6, "Jun"], [7, "Jul"], [8, "Ago"], [9, "Sep"], [10, "Oct"], [11, "Nov"], [12, "Dic"]];
					var options = {
	                    colors: ["#3498db", "#333333"],
	                    legend: {show: true, position: "nw", margin: [15, 10]},
	                    grid: {borderWidth: 0, hoverable: true, clickable: true},
	                    yaxis: {tickFormatter: function numberWithCommas(x) {
                                      return x.toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ".");
                                }, tickColor: "#eeeeee"},
	                    xaxis: {ticks: chartMonths, tickColor: "#ffffff"},
						lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}]}},
	                    points: {show: true, radius: 6}
	                };
					$.plot("#graph", [{"label":"Ventas Promedio (2014)","data":[[1,223051],[2,395018],[3,327369],[4,479781],[5,329013],[6,237118],[7,294326],[8,364281],[9,220785],[10,853165],[11,557843],[12,347478]]}], options);
					$("#modal-large").modal("show");');
					
		$this->mifaces->run();
    }	
}