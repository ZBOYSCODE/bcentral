<?php
namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Knowledge extends Model
{   
	public function getKnowledge($id)
	{
		$ws = new WebServiceClient();
		$result = $ws->getKnowledge($id);
		if($result['returnCode'] == '0')
		{
			$result = (array)$result['instance'];
			$Know['titulo'] = (array)$result['title'];
			$Know['titulo'] = $Know['titulo']['_'];
			$Know['id'] = (array)$result['id'];
			$Know['id'] = $Know['id']['_'];
			$Know['fecha_formateada'] = (array)$result['creationdate'];
			$Know['fecha_formateada'] = $this->dateFormatter($Know['fecha_formateada']['_']);
			$Know['texto'] = (array)$result['answer'];
			$Know['texto'] = $Know['texto']['_'];
			$Know['adjunto'] = array();
		}
		else
		{
			$Know = array();
		}
		return $Know;
	}
	public function searchKwonledge($search)
	{
		$ws = new WebServiceClient();
		$result = $ws->searchKnowledge($search);
		if($result['returnCode'] == '0')
		{
			$result = (array)$result['instance'];
			$list = array();
			$temp;
			foreach ($result as $key => $val) 
			{
				$val = (array)$val;
				$temp['id'] = (array)$val['id'];
				$temp['id'] = $temp['id']['_'];
				$temp['titulo'] = (array)$val['title'];
				$temp['titulo'] = $temp['titulo']['_'];
				$temp['minitexto'] = (array)$val['summary'];
				$temp['minitexto'] = $temp['minitexto']['_'];
				$temp['fecha_formateada'] = (array)$val['creationdate'];
				$temp['fecha_formateada'] = $this->dateFormatter($temp['fecha_formateada']['_']);
				$temp['adjunto'] = '';
				array_push($list, $temp);
			}
			return $list;
		}
		else
		{
			return array();
		}
	}
	function dateFormatter($d)
    {
         $d = str_split($d);
         $months = array(
         				'01' => 'Enero',
         				'02' => 'Febrero',
         				'03' => 'Marzo',
         				'04' => 'Abril',
         				'05' => 'Mayo',
         				'06' => 'Junio',
         				'07' => 'Julio',
         				'08' => 'Agosto',
         				'09' => 'Septiembre',
         				'10' => 'Octubre',
         				'11' => 'Noviembre',
         				'12' => 'Diciembre'
         			);
         $d=mktime(intval($d[11].$d[12], 10) - 4, intval($d[14].$d[15], 10), 0, intval($d[5].$d[6] ,10), intval($d[8].$d[9], 10), intval($d[0].$d[1].$d[2].$d[3], 10));
         // hh, mm, ss, m, d, y
         $result =  $months[date("m", $d)] . ' ' . date("d, Y h:i a", $d);
         return $result;
    }
}