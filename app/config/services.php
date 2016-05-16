<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Simple as SimpleView;
use Phalcon\Crypt;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Collection\Manager;
use Gabs\Auth\Auth;
use Gabs\Mifaces\Mifaces;
//use Vokuro\Acl\Acl;
use Gabs\Mail\Mail;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();


/**
 * Register the global configuration as config
 */
 $di->set('config', $config);

 
 /**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);


/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {
    $view = new SimpleView();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
}, true);

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});
/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});
/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('Gabs\Controllers');
    return $dispatcher;
});
/**
 * Loading routes from the routes.php file
 */
$di->set('router', function () {
    return require __DIR__ . '/routes.php';
});
/**
 * Flash service with custom CSS classes
 */
$di->set('flash', function () {
    return new Flash(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
});

/**
 * Custom authentication component
 */
$di->set('mifaces', function () {
    return new Mifaces();
});

 $di->set('auth', function () {
    return new Auth();
});
/**
 * Mail service uses AmazonSES
 */
$di->set('mail', function () {
    return new Mail();
});
/**
 * Access Control List
 *
$di->set('acl', function () {
    return new Acl();
});
*/
/*
*   Web service component
*/
$di->set('soapclient-servicedesk', function () use ($configWs) { 
     try
    {
        $client = @new SoapClient($configWs->wsdlUriServ, 
            array(
                'login' => $configWs->wsdlUsr, 
                'password' => $configWs->wsdlPass, 
                'features' => 'SOAP_WAIT_ONE_WAY_CALLS', 
                'soap_version'   => SOAP_1_2,
                "exceptions" => true,
                'trace' => false
                )
            );
        if(is_null($client))
        {
            throw new Exception("Error Processing Request", 2);
            
        }
        
    }
    catch (Exception $e)
    {
        $client = false;
    }
    return $client;
});

$di->set('soapclient-config', function () use ($configWs) { 
    try
    {
        $client = new SoapClient($configWs->wsdlUriConf, 
            array(
                'login' => $configWs->wsdlUsr, 
                'password' => $configWs->wsdlPass, 
                'features' => 'SOAP_WAIT_ONE_WAY_CALLS', 
                'soap_version'   => SOAP_1_2,
                'exceptions' => true,
                'trace' => false
                )
            );
        return $client;
    }
    catch (Exception $e)
    {
        $client = false;
    }
    return $client;
});

$di->set('soapclient-knowledge', function () use ($configWs) { 
    return new SoapClient($configWs->wsdlUriKnow, 
        array(
            'login' => $configWs->wsdlUsr, 
            'password' => $configWs->wsdlPass, 
            'features' => 'SOAP_WAIT_ONE_WAY_CALLS', 
            'soap_version'   => SOAP_1_2,
            'exceptions' => true,
            'trace' => true
            )
        );
});

$di->set('soapclient-catalog', function () use ($configWs) { 
    return new SoapClient($configWs->wsdlUriCata, 
        array(
            'login' => $configWs->wsdlUsr, 
            'password' => $configWs->wsdlPass, 
            'features' => 'SOAP_WAIT_ONE_WAY_CALLS', 
            'soap_version'   => SOAP_1_2,
            'exceptions' => true,
            'trace' => true
            )
        );
});

$di->set('test-user', function () use ($configWs) {
    return $configWs->testUser;
});

$di->set('catalog-icons', function () use ($catalogIcons) {
    return $catalogIcons;
});

//http://localhost:8080/raggApi/Servicedesk?wsdl
//http://192.168.5.113:13080/SM/7/servicedesk.wsdl

//C:\xampp\htdocs\Servicedesk.wsdl

//http://192.168.5.113:13080/SM/7/ServiceCatalogAPI.wsdl