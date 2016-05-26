<?php
namespace Gabs\Controllers;

class TestController extends ControllerBase
{

	public function LdapAction()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
        	$pcView = 'servicio/servicios_solicitud_test';
        	$js = '';
        	$pcData = '';
        	echo $this->view->render('theme_default', array('lmView'=>'menu/leftMenu', 'menuSel'=>'','pcView'=>$pcView, 'pcData'=> $pcData, 'jsScript'=>$js));
        }
	}
}