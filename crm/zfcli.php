<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

define('DS', DIRECTORY_SEPARATOR);

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

require_once 'Zend/Loader/Autoloader.php';
$autoload = Zend_Loader_Autoloader::getInstance();
$autoload->registerNamespace('Razor');

$opts = new Zend_Console_Getopt('abp:');

try {
    $opts = new Zend_Console_Getopt(
                    array(
                        'help|h' => 'Displays usage information.',
                        'action|a=s' => 'Action to perform in format of module.controller.action',
                        'verbose|v' => 'Verbose messages will be dumped to the default output.',
                        'development|d' => 'Enables development mode.',
                        'include_path|p' => 'Get include path.',
                    )
    );
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    exit("Eccezione" . $e->getMessage() . "\n\n" . $e->getUsageMessage());
}

$env = $opts->getOption('e');
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (null === $env) ? 'production' : $env);

// initialize Zend_Application
$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);

$front = $application->getBootstrap()
        ->bootstrap('frontController')
        ->getResource('frontController');

$params = array_reverse(explode('.', $opts->getOption('a')));
@list($action, $controller, $module) = $params;
$module = !empty($module) ? $module : 'default';

if (isset($opts->h)) {
    echo $opts->getUsageMessage();
    exit;
}

if (isset($opts->p)) {
    echo get_include_path();
    exit;
}

if (isset($opts->a)) {
    $request = new Zend_Controller_Request_Simple($action, $controller, $module);
    $front->setRequest($request);
    $front->setRouter(new BL_Router_Cli());
    $front->setResponse(new Zend_Controller_Response_Cli());
    $front->throwExceptions(true);
    $front->addModuleDirectory(APPLICATION_PATH . DS . 'modules/');
    $application->bootstrap()->run();
    $front->dispatch();
    exit;
}