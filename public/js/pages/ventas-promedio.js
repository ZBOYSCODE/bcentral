/*
 *  Document   : tablesDatatables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables Datatables page
 */


var chartMonths = [[1, 'Ene'], [2, 'Feb'], [3, 'Mar'], [4, 'Abr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Ago'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dic']]; 
 
var TablesDatatables = function() {

    return {
        init: function() {
            /* Initialize Bootstrap Datatables Integration */
            App.datatables();

            /* Initialize Datatables */
             datatableini = $('#datatable-response').dataTable({
                columnDefs: [ { orderable: false, targets: [ ] } ],
                pageLength: 10,
                lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']]
            });

            /* Add placeholder attribute to the search input */
            $('.dataTables_filter input').attr('placeholder', 'Buscar');
        }
    };
}();

$(function(){ TablesDatatables.init(); });
// funciones del data picker

function GraficoClientes(){
		
		var chartClientesNuevos = $('#chart-clientes-nuevos');
		var CheckboxDateCompare = $( "#checkbox-date-compare" );
		var DateRangeFrom2 = $('#daterange2-from');
		var DateRangeTo2 = $('#daterange2-to');
		
		var optionsClientesNuevos = {
			lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}]}},
            points: {show: true, radius: 6},
			colors: ['#3498db', '#333333'],
            legend: {show: true, position: 'ne', margin: [5, 10]},
            grid: {borderWidth: 0, hoverable: true, clickable: true},
            yaxis: {ticks: 4, tickColor: '#eeeeee'},
            xaxis: {tickColor: '#ffffff', mode: "time",minTickSize: [1, "day"]}
				
				
		};

		var dataClientesNuevos = [];
		var comparestring = "";

		//$.plot(chartClientesNuevos, dataClientesNuevos, optionsClientesNuevos);    
		      
        
        chartClientesNuevos.bind("plotclick", function (event, pos, item) {
			if (item) {
				//lo que pasa
				//alert("asf");
				plot.unhighlight();
				plot.highlight(item.series, item.datapoint);
			}
		});	   
		
		$('#page-wrapper').addClass('page-loading');

		function onDataReceived(dataresponse) {

			//dataClientesNuevos.push(series);
			series = dataresponse["datagraph"];
			$('#datatable-response').html(dataresponse["datatable"]);
			
			//datatableini.destroy();

			$.plot(chartClientesNuevos, series, optionsClientesNuevos);
			$('#page-wrapper').removeClass('page-loading');
		}
		
		
		if(CheckboxDateCompare.is(':checked')){
			comparestring = "&date2-from="+DateRangeFrom2_value+"&date2-to="+DateRangeTo2_value;
		}
		$.ajax({
			url: "json/?"+"date-from="+DateRangeFrom1_value+"&date-to="+DateRangeTo1_value+comparestring,
			type: "GET",
			dataType: "json",
			success: onDataReceived
		});



}
	


sumaFecha = function(d, fecha)
{
 var Fecha = new Date();
 var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
 var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
 var aFecha = sFecha.split(sep);
 var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
 fecha= new Date(fecha);
 fecha.setDate(fecha.getDate()+parseInt(d));
 var anno=fecha.getFullYear();
 var mes= fecha.getMonth()+1;
 var dia= fecha.getDate();
 mes = (mes < 10) ? ("0" + mes) : mes;
 dia = (dia < 10) ? ("0" + dia) : dia;
 var fechaFinal = dia+sep+mes+sep+anno;
 return (fechaFinal);
}

restaFechas = function(f1,f2)
 {
 var aFecha1 = f1.split('/'); 
 var aFecha2 = f2.split('/'); 
 var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
 var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
 var dif = fFecha2 - fFecha1;
 var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
 return dias;
 }
 
obtener_fecha_actual = function(){
var hoy = new Date();
var dd = hoy.getDate();
var mm = hoy.getMonth()+1; //hoy es 0!
var yyyy = hoy.getFullYear();
if(dd<10) {
    dd='0'+dd
} 
if(mm<10) {
    mm='0'+mm
} 
hoy = dd+'/'+mm+'/'+yyyy;
return (hoy);
}

var DateRangeFrom1_value;
var DateRangeTo1_value;
var DateRangeFrom2_value;
var DateRangeTo2_value;

var DatePicker = function() {

    return {
        init: function() {
		
		var CheckboxDateCompare = $( "#checkbox-date-compare" );
		var SelectDateCompare = $("#select-date-compare");
		var DateRangeFrom1 = $('#daterange1-from');
		var DateRangeTo1 = $('#daterange1-to');
		var DateRangeFrom2 = $('#daterange2-from');
		var DateRangeTo2 = $('#daterange2-to');
		var DatePicker2Container = $( "#date-picker-compare" );
		
		DateRangeFrom1_value = DateRangeFrom1.val();
		DateRangeTo1_value = DateRangeTo1.val();
		DateRangeFrom2_value = DateRangeFrom2.val();
		DateRangeTo2_value = DateRangeTo2.val();


		if(DateRangeFrom1_value == ""){
		  	var hoy = obtener_fecha_actual();
		  	var ayer = sumaFecha(-1,hoy);
		  	var mes_pasado = sumaFecha(-30,ayer);
		  	
		  	DateRangeFrom1.datepicker("setDate", mes_pasado);
		  	DateRangeFrom1_value = mes_pasado;
		  	DateRangeTo1.datepicker("setDate", ayer);
		  	DateRangeTo1_value = ayer;
		}	         
			         
		CheckboxDateCompare.click(function() {
			DateRangeFrom1_value = DateRangeFrom1.val();
			DateRangeTo1_value = DateRangeTo1.val();
			DateRangeFrom2_value = DateRangeFrom2.val();
			DateRangeTo2_value = DateRangeTo2.val();		
			DatePicker2Container.toggle( "slow"); 
			if (SelectDateCompare.attr("disabled")) {
			    //que select es?//
				if(SelectDateCompare.val() == 1){
			    	dias_diferencia = restaFechas(DateRangeFrom1_value,DateRangeTo1_value) + 1;
					DateRangeFrom2_value = sumaFecha(-1*dias_diferencia,DateRangeFrom1_value);
					DateRangeTo2_value = sumaFecha(-1*dias_diferencia,DateRangeTo1_value);
			    	DateRangeFrom2.datepicker("setDate", DateRangeFrom2_value);
			    	DateRangeTo2.datepicker("setDate", DateRangeTo2_value);
				}
				if(SelectDateCompare.val() == 2){
					DateRangeFrom2_value = sumaFecha(-365,DateRangeFrom1_value);
					DateRangeTo2_value = sumaFecha(-365,DateRangeTo1_value);
			    	DateRangeFrom2.datepicker("setDate", DateRangeFrom2_value);
			    	DateRangeTo2.datepicker("setDate", DateRangeTo2_value);
				}
				//que select es?//  
			     SelectDateCompare.removeAttr("disabled");
			} else {
			    SelectDateCompare.attr("disabled", "disabled");
			}
			});
				
			SelectDateCompare.change(function() {
				DateRangeFrom1_value = DateRangeFrom1.val();
				DateRangeTo1_value = DateRangeTo1.val();
				DateRangeFrom2_value = DateRangeFrom2.val();
				DateRangeTo2_value = DateRangeTo2.val();
			    //que select es?//
				if(SelectDateCompare.val() == 1){
			    	dias_diferencia = restaFechas(DateRangeFrom1_value,DateRangeTo1_value) + 1;
					DateRangeFrom2_value = sumaFecha(-1*dias_diferencia,DateRangeFrom1_value);
					DateRangeTo2_value = sumaFecha(-1*dias_diferencia,DateRangeTo1_value);
			    	DateRangeFrom2.datepicker("setDate", DateRangeFrom2_value);
			    	DateRangeTo2.datepicker("setDate", DateRangeTo2_value);
				}
				if(SelectDateCompare.val() == 2){
					DateRangeFrom2_value = sumaFecha(-365,DateRangeFrom1_value);
					DateRangeTo2_value = sumaFecha(-365,DateRangeTo1_value);
					DateRangeFrom2.datepicker("setDate", DateRangeFrom2_value);
			    	DateRangeTo2.datepicker("setDate", DateRangeTo2_value);
				}
				if(SelectDateCompare.val() == 3){
					DateRangeFrom2_value = "";
					DateRangeTo2_value = "";
			    	DateRangeFrom2.datepicker("setDate", "");
			    	DateRangeTo2.datepicker("setDate", ""); 
				}				
				//que select es?// 
			});
			$( "#change-date-range-action" ).click(function() {
				DateRangeFrom1_value = DateRangeFrom1.val();
				DateRangeTo1_value = DateRangeTo1.val();
				DateRangeFrom2_value = DateRangeFrom2.val();
				DateRangeTo2_value = DateRangeTo2.val();
			  GraficoClientes();
			});
			
			
			GraficoClientes();
        }

    };
}();
		
	
var CompCharts = function() {

    return {
        init: function() {
            /* Mini Bar/Line Charts with jquery.sparkline plugin, for more examples you can check out http://omnipotent.net/jquery.sparkline/#s-about */
             /*
             * Flot Jquery plugin is used for charts
             *
             * For more examples or getting extra plugins you can check http://www.flotcharts.org/
             * Plugins included in this template: pie, resize, stack, time
             */

            // Get the elements where we will attach the charts
            var chartClassic = $('#chart-classic');
            var chartBars = $('#chart-bars');
            var chartBarsRegion = $('#chart-bars-region');

			var chartClientesNuevos = $('#chart-clientes-nuevos');

            // Random data for the charts
            var dataEarnings = [[1, 1560], [2, 1650], [3, 1320], [4, 1950], [5, 1800], [6, 2400], [7, 2100], [8, 2550], [9, 3300], [10, 3900], [11, 4200], [12, 4500]];
            var data_ventas_sector = [[1, 150], [2, 200], [3, 250], [4, 300], [5, 420], [6, 350]];
			var data_ventas_region = [[1, 150], [2, 200], [3, 250], [4, 300], [5, 420], [6, 350], [7, 1345], [8, 678], [9, 23], [10, 345], [11, 234], [12, 122], [13, 344], [14, 321], [15, 350]];
			
			var dataClientesNuevos = [[1, 3], [2, 5], [3, 0], [4, 4], [5, 12], [6, 2], [7, 0], [8, 4], [9, 8], [10, 3], [11, 2], [12, 8]];
            // Array with month labels used in Classic and Stacked chart
            var chartMonths = [[1, 'Ene'], [2, 'Feb'], [3, 'Mar'], [4, 'Abr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Ago'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dic']];



			var chartsectors = [[1, 'S01'], [2, 'S02'], [3, 'S03'], [4, 'S04'], [5, 'S05'], [6, 'S06']];
			var chartregion = [[1, 'Arica y Parinacota'], [2, 'Tarapacá'], [3, 'Antofagasta'], [4, 'Atacama'], [5, 'Coquimbo'], [6, 'Valparaíso'], [7, 'Santiago'], [8, "O'Higgins"], [9, 'Maule'], [10, 'Bío-Bío'], [11, 'La Araucanía'], [12, 'Los Ríos'], [13, 'Los Lagos'], [14, 'Aysén'], [15, 'Magallanes']];
            // Classic Chart
            if(chartClassic.length > 0){
	            $.plot(chartClassic,
	                [
	                    {
	                        label: 'Ventas',
	                        data: dataEarnings,
	                        lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}]}},
	                        points: {show: true, radius: 6}
	                    },
	
	                ],
	                {
	                    colors: ['#3498db', '#333333'],
	                    legend: {show: true, position: 'nw', margin: [15, 10]},
	                    grid: {borderWidth: 0, hoverable: true, clickable: true},
	                    yaxis: {ticks: 4, tickColor: '#eeeeee'},
	                    xaxis: {ticks: chartMonths, tickColor: '#ffffff'}
	                }
	            );	            
            }

            if(chartBars.length > 0){
            // Bars Chart
		
		
	            $.plot(chartBars,
	                [
	                    {
	                        label: 'Ventas',
	                        data: data_ventas_sector,
	                        bars: {show: true, lineWidth: 0, barWidth: 0.6, align: "center", fillColor: {colors: [{opacity: 0.5}, {opacity: 0.5}]}}
	                    }
	                ],
	                {
	                    colors: ['#337AB7'],
	                    grid: {borderWidth: 0},
	                    yaxis: {ticks: 4, tickColor: '#eeeeee'},
	                    xaxis: {ticks: chartsectors, tickColor: '#ffffff'}
	                }
	            );
            }

            if(chartBarsRegion.length > 0){
            // Bars Chart
		
		
	            $.plot(chartBarsRegion,
	                [
	                    {
	                        label: 'Ventas',
	                        data: data_ventas_region,
	                        bars: {show: true, lineWidth: 0, barWidth: 0.6, align: "center", fillColor: {colors: [{opacity: 0.5}, {opacity: 0.5}]}}
	                    }
	                ],
	                {
	                    colors: ['#337AB7'],
	                    grid: {borderWidth: 0},
	                    yaxis: {ticks: 4, tickColor: '#eeeeee'},
	                    xaxis: {ticks: chartregion, tickColor: '#ffffff'}
	                }
	            );
            }
            
            if(chartClientesNuevos.length > 0){
         
	            
	                   
            }            
            
            
            
        }
    };
}();