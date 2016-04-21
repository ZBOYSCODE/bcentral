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


// Simple database connection to localhost
/*
$di->set('mongo', function () {
    $mongo = new MongoClient();
    return $mongo->selectDB("gadapps");
}, true);


$di->set('collectionManager', function(){
        return new Manager();
    }, true);
*/
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
 /*
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));
});
*/
/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 *
$di->set('modelsMetadata', function () use ($config) {
    return new MetaDataAdapter(array(
        'metaDataDir' => $config->application->cacheDir . 'metaData/'
    ));
});
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
$di->set('soapclient-servicedesk', function () {
    return new SoapClient('http://64.79.70.107:8080/raggApi/Servicedesk?wsdl', array('login'=>'falcon'));
});

//http://192.168.5.113:13080/SM/7/servicedesk.wsdl