<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

$router->add('/login', array(
    'controller' => 'session',
    'action' => 'login'
));

$router->add('/', array(
    'controller' => 'comercio',
    'action' => 'index'
));

$router->add('/logout', array(
    'controller' => 'session',
    'action' => 'logout'
));

$router->add('/reset-password/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'resetPassword'
));

$router->add('/consultar', array(
    'controller' => 'comercio',
    'action' => 'consultar' 
    ));
$router->add('/servicio', array(
    'controller' => 'comercio',
    'action' => 'solicitudServicio'
    ));

$router->add('/soporte', array(
	'controller' => 'comercio',
	'action' => 'solicitudSoporte'
	));

$router->add('/solicitudes', array(
    'controller' => 'comercio',
    'action' => 'listarSolicitudes'
    ));

$router->add('/ticket', array(
    'controller' => 'comercio',
    'action' => 'ticket'
    ));

$router->add('/documento', array(
    'controller' => 'comercio',
    'action' => 'documento'
    ));



return $router;
