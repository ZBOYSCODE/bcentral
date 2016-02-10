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
//estas funciones (de arriba) son para calcular fechas


//variables para ajax
//el analisis que queremos ver
var Analysis;
//la opcion de analisis grafico, tabla, tarta, barra, etc
var AnalysisOption;
//valor de la fecha DESDE
var DateRangeFrom1_value;
//valor de la fecha HASTA
var DateRangeTo1_value;
//flag de comparacion
var DateCompare_value;
//valor de la fecha DESDE de la comparativa
var DateRangeFrom2_value;
//valor de la fecha Hasta de la comparativa
var DateRangeTo2_value;
//valor de opcion de la dimension primaria
var Dimension_value;
//valor de opcion de agrupacion de fecha (dia, mes año)
var DateGroup_value;
//valor de opcion de agrupacion de dimension (por ejemplo sector, region)
var DimensionGroup_value;
//valor de opcion de agrupacion de dimension pero para graficar (grilla, pie, barras)
var DimensionGroupVisual_value;

//se convierten los objetos en variables (mas comodo para manejar)
var CheckboxDateCompare = $( "#checkbox-date-compare" );
var SelectDateCompare = $("#select-date-compare");
var DateRangeFrom1 = $('#daterange1-from');
var DateRangeTo1 = $('#daterange1-to');
var DateRangeFrom2 = $('#daterange2-from');
var DateRangeTo2 = $('#daterange2-to');
var DatePicker2Container = $( "#date-picker-compare" );

var Dimension = $( "#dimension" );
var DateGroup = $( ".btn-dategroup" );
var DimensionGroup = $( ".dimensiongroup" );
var DimensionGroupVisual = $( ".btn-dimensiongroupvisual" );

//variables que va a juntar las variables en un string
var varstringajax;

//se inicializan
DateRangeFrom1_value = DateRangeFrom1.val();
DateRangeTo1_value = DateRangeTo1.val();
DateRangeFrom2_value = DateRangeFrom2.val();
DateRangeTo2_value = DateRangeTo2.val();

//toma los valores y fabrica un string de variables
createajaxvarstring = function(){
	varstringajax = "analysis="+Analysis+"&date-from="+DateRangeFrom1_value+"&date-to="+DateRangeTo1_value+"&datecompare="+DateCompare_value+"&date2-from="+DateRangeFrom2_value+"&date2-to="+DateRangeTo2_value+"&dimension="+Dimension_value+"&dategroup="+DateGroup_value+"&dimensiongroup="+DimensionGroup_value+"&dimensiongroupvisual="+DimensionGroupVisual_value;
}


var DatePicker = function() {

    return {
        init: function() {
		


		if(DateRangeFrom1_value == ""){
		  	var hoy = obtener_fecha_actual();
		  	var ayer = sumaFecha(-1,hoy);
		  	var mes_pasado = sumaFecha(-30,ayer);
		  	
		  	DateRangeFrom1.datepicker("setDate", mes_pasado);
		  	DateRangeFrom1_value = mes_pasado;
		  	DateRangeTo1.datepicker("setDate", ayer);
		  	DateRangeTo1_value = ayer;
		}	         
		if(CheckboxDateCompare.is(':checked')){
			DatePicker2Container.toggle(0); 
		}
		CheckboxDateCompare.click(function() {
			DateRangeFrom1_value = DateRangeFrom1.val();
			DateRangeTo1_value = DateRangeTo1.val();
			DateRangeFrom2_value = DateRangeFrom2.val();
			DateRangeTo2_value = DateRangeTo2.val();
			DateCompare_value = CheckboxDateCompare.is(':checked');		
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
				
				//llama al que construye el request
				createajaxvarstring();
				window.location = "?"+varstringajax;
			});
			
			
        }

    };
}();



//revisa cuando cambia la dimension y llama al que construye el request
Dimension.change(function() {
	Dimension_value = Dimension.val();
	createajaxvarstring();
	window.location = "?"+varstringajax;
});

//revisa cuando cambia la dimension y llama al que construye el request
DateGroup.click(function() {
	//alert("construye request");
	DateGroup_value = $(this).attr("value");
	createajaxvarstring();
	window.location = "?"+varstringajax;
});

//revisa cuando cambia la dimension grupal y llama al que construye el request
DimensionGroup.click(function() {
	//alert("construye request");
	DimensionGroup_value = $(this).attr("value");
	createajaxvarstring();
	window.location = "?"+varstringajax;
});

//revisa cuando cambia la dimension grupal para vista de data en grafico y llama al que construye el request
DimensionGroupVisual.click(function() {
	//alert("construye request");
	DimensionGroupVisual_value = $(this).attr("value");
	createajaxvarstring();
	window.location = "?"+varstringajax;
});