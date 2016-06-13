<?php
namespace Gabs\Models;

use Gabs\Auth\Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Knowledge extends Model
{   
	public function getKnowledge($id)
	{
		$ws = new WebServiceClient();
		$result = $ws->getKnowledge($id);
		try
		{
			$Know['titulo'] = $result['title'];
			$Know['id'] = $result['id'];
			$Know['fecha_formateada'] = $this->dateFormatter($result['creationdate']);
			$Know['texto'] = $result['answer'];
			$Know['adjunto'] = array();
			foreach ($result['attachments'] as $val)
			{
				$href = $this->di->get('km-config'); //'http://192.168.5.113:13080/SM/9/rest/knowledges/<km>/attachments/<href>';
				$href = str_replace('<km>', $Know['id'], $href);
				$href = str_replace('<href>', $val['href'], $href);
				array_push($Know['adjunto'], array('href' => $href, 'name' => $val['name']));
			}
		}
		catch (Exception $e)
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