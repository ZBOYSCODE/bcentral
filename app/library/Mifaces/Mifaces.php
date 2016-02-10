<?php
namespace Gabs\Mifaces;

use Phalcon\Mvc\User\Component;

class Mifaces extends Component{

	private $_preRendEval;
	private $_posRendEval;
	private $_toRend;
	private $_toErrorForm;
	private $_toRedir;
	private $_toNewWin;
	private $_toValue;
	private $_toMsg;
	private $_toDataSelect;
	private $_socket;

    public function __construct() {
		$this->newFaces();
	}

    public function newFaces() {
		$this->_preRendEval		= array();
		$this->_posRendEval		= array();
		$this->_toRend			= array();
		$this->_toErrorForm		= array();
		$this->_toRedir			= null;
		$this->_toNewWin		= null;
		$this->_toValue			= array();
		$this->_toMsg			= array();
		$this->_toDataSelect	= array();
		$this->_socket			= array();
    }

	public function addToDataSelect($to, $data){
		$this->_toDataSelect[$to]=$data;
	}

	public function addToMsg($type, $msg, $run = false){
		$this->_toMsg[$type]=$msg;
		if($run){
			$outputs[] = array('type' => 'msg',		'msgs'		=> $this->_toMsg );
			echo json_encode($outputs);
			exit();
		}

	}

	public function addToValue($id, $value){
		$this->_toValue[$id]=$value;
	}

	public function addNewWin($htmlString, $name, $run = false){
		if($name!=null && $htmlString!=null){
			$this->_toNewWin['name']=$name;
			$this->_toNewWin['htmlString']=$htmlString;
			
			if($run){
				$outputs[] = array('type' => 'newWin', 'win' =>$this->_toNewWin['htmlString'], 'name'=>$this->_toNewWin['name']);
				echo json_encode($outputs);			
			}			
		}
	}

	public function addRedir($url, $run = false){
		if($url!=null){
			$this->_toRedir=$url;
			if($run){
				$outputs[] = array('type' => 'redir', 'redir'=>$url);
				echo json_encode($outputs);			
			}			
		}
	}

	public function addPreRendEval($jquery){
		$this->_preRendEval[]=$jquery;
	}

	public function addPosRendEval($jquery){
		$this->_posRendEval[]=$jquery;
	}

	public function addSocket($jquery){
		$this->_socket[]=$jquery;
	}
	
	public function addToRend($div,$htmlString, $run = false){
		$this->_toRend[$div]=$htmlString;
		if($run){
			$outputs[] = array('type' => 'render',	'renders'	=> $this->_toRend );
			echo json_encode($outputs);
			exit();
		}
	}

	public function addErrorForm($idElement, $descripcion, $run = false){
		$this->_toErrorForm[$idElement]=$descripcion;
		if($run){
			$outputs[] = array('type' => 'errorForm', 'errors' =>$this->_toErrorForm);
			echo json_encode($outputs);
			exit();
		}
	}	

	public function addErrorsForm($arreglo, $run = false){
		if($arreglo!=null && $arreglo!=''){
			foreach ($arreglo as $key => $value) {
				$this->_toErrorForm[$key]=$value;
			}
		}
		if($run){
			$outputs[] = array('type' => 'errorForm', 'errors' =>$this->_toErrorForm);
			echo json_encode($outputs);
			exit();
		}		
	}	

	public function run(){

		$outputs = array();
		
		if(count($this->_toErrorForm)>0)		
			$outputs[] = array('type' => 'errorForm', 'errors' =>$this->_toErrorForm); 

		if(count($this->_toValue)>0)
			$outputs[] = array('type' => 'val',		'renders'	=> $this->_toValue );

		if($this->_toRedir!=null)
			$outputs[] = array('type' => 'redir',	'redir'		=> $this->_toRedir );

		if(count($this->_preRendEval)>0)
			$outputs[] = array('type' => 'eval',	'evals'		=> $this->_preRendEval );

		if(count($this->_toRend)>0)
			$outputs[] = array('type' => 'render',	'renders'	=> $this->_toRend );

		if(count($this->_posRendEval)>0)
			$outputs[] = array('type' => 'eval',	'evals'		=> $this->_posRendEval );

		if(count($this->_socket)>0)
			$outputs[] = array('type' => 'socket',	'sockets'		=> $this->_socket );			
		
		if($this->_toNewWin!=null)
			$outputs[] = array('type' => 'newWin', 	'win'		=> $this->_toNewWin['htmlString'], 'name' => $this->_toNewWin['name']);

		if(count($this->_toMsg)>0)
			$outputs[] = array('type' => 'msg',		'msgs'		=> $this->_toMsg );

		if(count($this->_toDataSelect)>0){
			foreach ($this->_toDataSelect as $key => $value) {
				$outputs[] = array('type' => 'dataSelect', 'renders' => $value,'to'=>$key);
			}
		}

		echo json_encode($outputs);
	}
}
