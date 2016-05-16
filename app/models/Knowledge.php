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
		if($d[8]=='0')
		{
			$d[8] = '';
		}
		$temp = intval($d[11].$d[12]);
		if($temp >= 12)
		{
			$t = ' pm';
			$temp = $temp - 12;

		}
		else
		{
			$t = ' am';
		}

        if($temp < 10)
     	{
     		$d[11] = '0';
     		$d[12] = (string)$temp;
     	}
     	else
     	{
     		$temp = $temp - 10;
     		$d[11] = '1';
     		$d[12] = (string)$temp;
     	}
         return $months[$d[5].$d[6]] . ' ' . $d[5] . $d[6] . ', ' . $d[0] . $d[1] . $d[2] . $d[3] . ' - ' . $d[11] . $d[12] . ':' . $d[14] . $d[15] . $t;
    }
}