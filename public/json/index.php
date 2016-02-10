<?

require_once("../../include/json.php");
require_once("../../config/cfg/conexion.inc.php");

	$fecha_from = implode("-", array_reverse(split("/", $_REQUEST["date-from"])));
	$fecha_to = implode("-", array_reverse(split("/", $_REQUEST["date-to"])));
	$arrayjson = array();

if($_REQUEST["option"] == "graph"){
	
	$sql = "SELECT * FROM `bicorp` WHERE `datetime` BETWEEN '".$fecha_from."' AND '".$fecha_to."' GROUP BY DAY(datetime),MONTH(datetime),YEAR(datetime) ORDER BY datetime ASC";
	
	//echo($sql);
	
	$arrayjson = array();
	$arrayoutgraph = array();
	$result = mysql_query($sql, $link);	
	
	while ($row = mysql_fetch_array($result)){
		$fechatemp = split(" ", $row['datetime']);
		$fecha  = strtotime($fechatemp[0])*1000;
		$monto = $row['monto'];
		
		array_push($arrayjson, array((int)$fecha,(float)$monto));
	}
	
	//se mete a las series los datos normales
	array_push($arrayoutgraph, array("label" => "", "data" => $arrayjson));	
	
	//se agregan otras series	
	if($_REQUEST["date2-from"]){
	
		$arrayjson = array();
		$fecha2_from = implode("-", array_reverse(split("/", $_REQUEST["date2-from"])));
		$fecha2_to = implode("-", array_reverse(split("/", $_REQUEST["date2-to"])));
	
		
		$sql = "SELECT * FROM `bicorp` WHERE `datetime` BETWEEN '".$fecha2_from."' AND '".$fecha2_to."' GROUP BY DAY(datetime),MONTH(datetime),YEAR(datetime) ORDER BY datetime ASC";
		
	
		$result = mysql_query($sql, $link);	
		
		//sacamos la diferencia entre fechas para igualar los periodosn de las series
		$diferenciatime = strtotime($fecha_from)-strtotime($fecha2_from);
		
		
		while ($row = mysql_fetch_array($result)){
			$fechatemp = split(" ", $row['datetime']);
			$fecha  = (strtotime($fechatemp[0])*1000)+$diferenciatime*1000;
			$monto = $row['monto'];
			
			array_push($arrayjson, array((int)$fecha,(float)$monto));
		}
		
		
		array_push($arrayoutgraph, array("label" => "Período anterior", "data" => $arrayjson));	
	}	
	echo json_encode(array("datagraph"=>$arrayoutgraph));
	exit;
}


if($_REQUEST["option"] == "table"){


	$sql = "SELECT * FROM `bicorp` WHERE `datetime` BETWEEN '".$fecha_from."' AND '".$fecha_to."' ORDER BY datetime DESC";
	

	$result = mysql_query($sql, $link);	
	
	
	while ($row = mysql_fetch_array($result)){

		array_push($arrayjson, array($row["id_factura"],$row["datetime"],$row["rut"],$row["empresa"],"","","$".number_format(round($row["monto"]), 0, '', ','),"$".number_format(round(ceil($row["monto"]*0.18)), 0, '', ','),"$".number_format(round(ceil($row["monto"]*0.82)), 0, '', ','),'<a href="http://www.sii.cl/factura_electronica/969493705.jpg" target="_blank">Imágen</a>'));
		
	}

	
	echo json_encode(array("data"=>$arrayjson));
	exit;
}


if($_REQUEST["option"] == "rates"){


	$sql = "SELECT SUM(monto) as total FROM `bicorp` WHERE `datetime` BETWEEN '".$fecha_from."' AND '".$fecha_to."' ORDER BY datetime DESC";
	

	$result = mysql_query($sql, $link);	
	
	
	while ($row = mysql_fetch_array($result)){

		array_push($arrayjson, number_format(round($row["total"]), 0, '', '.'));
		
	}

	
	echo json_encode(array("data"=>$arrayjson));
	exit;
}
									            


	
?>