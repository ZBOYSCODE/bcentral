<?php
return new \Phalcon\Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'z3nta',
        'dbname' => 'fw_auth'
    ],
    'application' => [
        'controllersDir' => APP_DIR . '/controllers/',
        'modelsDir' => APP_DIR . '/models/',
        'formsDir' => APP_DIR . '/forms/',
        'viewsDir' => APP_DIR . '/views/',
        'libraryDir' => APP_DIR . '/library/',
        'pluginsDir' => APP_DIR . '/plugins/',
        'cacheDir' => APP_DIR . '/cache/',
        'baseUri' => '/bancocentral/',
        'publicUrl' => '/bancocentral',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D'
    ],
    'mail' => [
        'fromName' => 'Vokuro',
        'fromEmail' => 'phosphorum@phalconphp.com',
        'smtp' => array(
            'server' => 'smtp.gmail.com',
            'port' => 587,
            'security' => 'tls',
            'username' => '',
            'password' => ''
        )
    ],
    'amazon' => [
        'AWSAccessKeyId' => '',
        'AWSSecretKey' => ''
    ],
	'noAuth' => //noAuth -> configuracion de controller y acciones que no tienen que pasar por la autentificacion
	array('session'=>array('login'=>true,'logout'=>true/*,'*'=>true*/)//
	/*,'otro'=>array('login'=>true)*/,'comercio'=>array('*'=>true)//
	/*,'otro'=>array('login'=>true)*/),
	'appTitle'=>'Portal de Servicios',
	'appName'=>"Portal de Servicios",
	'appAutor'=>'Zenta',
	'appAutorLink'=>'http://zentagroup.com',
]);