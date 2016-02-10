var main_datatable;

function LoadDatatableDocumentario(){
		App.datatables();
	    main_datatable = $('#main-datatable').DataTable( {
            lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']],
            "columnDefs": [
			    { className: "datatable-column-align-right", "targets": [ 2,6,7,8 ] },
			    { className: "datatable-column-align-center", "targets": [ 0,1 ] }
			  ],
	        "ajax": "http://64.79.70.107/biCorp/json/?"+varstringajax+"&AnalysisOption=table"
	    } );
	            /* Add placeholder attribute to the search input */
        $('.dataTables_filter input').attr('placeholder', 'Buscar');
}

function LoadDatatablePieDocumentario(){
		App.datatables();
	    main_datatable = $('#pie-datatable').DataTable( {
            lengthMenu: [[10, 20, 30, -1], [10, 20, 30, 'All']],
	        "ajax": "http://64.79.70.107/biCorp/json/?"+varstringajax+"&AnalysisOption=tablepie"
	    } );
	            /* Add placeholder attribute to the search input */
        $('.dataTables_filter input').attr('placeholder', 'Buscar');
}

function LoadDataRates(){
			$.ajax({
			url: "http://64.79.70.107/biCorp/json/?"+varstringajax+"&AnalysisOption=rates",
			type: "GET",
			dataType: "json",
			success: function (response) {
                        $("#rate1-valor").html(response["data"]);
                }
			});
}

function GraficoDocumentario(){
		
		var chartMain = $('#main-chart');
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

		//$.plot(chartMain, dataClientesNuevos, optionsClientesNuevos);    
		      
        
        chartMain.bind("plotclick", function (event, pos, item) {
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

			$.plot(chartMain, series, optionsClientesNuevos);
			$('#page-wrapper').removeClass('page-loading');
		}
		
		

		$.ajax({
			//url: "http://64.79.70.107/biCorp/json/?"+varstringajax+"&AnalysisOption=graph",
			url: "http://64.79.70.107/bc/analyticsGetDataAjax?"+varstringajax+"&AnalysisOption=graph",
			type: "GET",
			dataType: "json",
			success: onDataReceived
		});
}

function PieDocumentario(){

		function onDataReceived(dataresponse) {

			data = dataresponse["data"];

			$.plot('#PieGroupVisual', data, {
			    series: {
			        pie: {
			            show: true,
			            radius: 1,
			            label: {
			                show: true,
			                radius: 1,
			                formatter: function (label, series) {                
				                return '<div style="border:1px solid grey;font-size:8pt;text-align:center;padding:5px;color:white;">' +
				                label + ' : ' +
				                Math.round(series.percent) +
				                '%</div>';
				            },
			                background: {
			                    opacity: 0.8
			                }
			            }
			        }
			    },
			    legend: {
			        show: false
			    }
			});

			$('#page-wrapper').removeClass('page-loading');
		}
					
		
		$.ajax({
			url: "http://64.79.70.107/biCorp/json/?"+varstringajax+"&AnalysisOption=pie",
			type: "GET",
			dataType: "json",
			success: onDataReceived
		});
}

function BarsDocumentario(){

		function onDataReceived(dataresponse) {

			data = dataresponse["data"];
			
			$.plot($("#BarsGroupVisual"), data, {
		        series: {
		            bars: {
		                show: true
		            }
		        }
		    });


			$('#page-wrapper').removeClass('page-loading');
		}
					
		
		$.ajax({
			url: "http://64.79.70.107/biCorp/json/?"+varstringajax+"&AnalysisOption=bars",
			type: "GET",
			dataType: "json",
			success: onDataReceived
		});
}