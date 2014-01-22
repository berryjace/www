<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initDoctype() {
        $admins = new Zend_Application_Module_Autoloader(array('namespace' => 'Admin', 'basePath' => APPLICATION_PATH . '/modules/admin'));
        $vendors = new Zend_Application_Module_Autoloader(array('namespace' => 'Vendor', 'basePath' => APPLICATION_PATH . '/modules/vendor'));
        $clients = new Zend_Application_Module_Autoloader(array('namespace' => 'Client', 'basePath' => APPLICATION_PATH . '/modules/client'));
        $view = $this->bootstrap('view')->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initHelperPath() {
        $view = $this->bootstrap('view')->getResource('view');
    }

    protected function _initAppKeysToRegistry() {

    }

    /**
     * Loads app-wide constants from ini file
     */
    protected function _initDefineConstants() {
        $sqlTablesFile = APPLICATION_PATH . '/configs/constants.ini';
        $iniParser = new Zend_Config_Ini($sqlTablesFile);
        $constants = $iniParser->toArray();
        foreach ($constants as $constName => $constantVal) {
            if (defined($constName)) {
                throw new Zend_Exception('Constant ' . $constName . ' is already defined.');
            }
            define($constName, $constantVal);
        }
    }

    protected function _initRouter() {
        if (PHP_SAPI == 'cli') {
            $this->bootstrap('frontcontroller');
            $front = $this->getResource('frontcontroller');
            $front->setRouter(new BL_Router_Cli());
            $front->setRequest(new Zend_Controller_Request_Simple());
        } else {
	    $front = Zend_Controller_Front::getInstance();
	    $router = $front->getRouter();
	    $route = new Zend_Controller_Router_Route(
			'client/wl-pages/:slug',
			    array(
				  'module'     => 'client',
				  'controller' => 'wl-pages',
				  'action'     => 'index',
				  )
			    );
	    $router->addRoute('sluggy', $route);
	}


    }

    /**
     *  Gearman related bootstrap
     */
//    protected function _initGearmanWorker() {
//        $options = $this->getOptions();
//        $gearmanworker = new GearmanWorker();
//        if (isset($options['gearmanworker']) && isset($options['gearmanworker']['servers'])) {
//            $gearmanworker->addServers($options['gearmanworker']['servers']);
//        } else {
//            $gearmanworker->addServer();
//        }
//        return $gearmanworker;
//    }

    public function run() {
        if (PHP_SAPI == 'cli') {
            global $argv;
            if (!isset($argv[1])) {
                throw new InvalidArgumentException('A Worker Name Must Be Passed In');
            }
            $worker = ucwords(basename($argv[1]));
            $workerName = $worker . 'Worker';
            $workerFile = APPLICATION_PATH . '/workers/' . $workerName . '.php';

            if (!file_exists($workerFile)) {
                throw new InvalidArgumentException('The worker file does not exist: ' . $workerFile);
            }
            require $workerFile;
            if (!class_exists($workerName)) {
                throw new InvalidArgumentException('The worker class: ' . $workerName . ' does not exist in file: ' . $workerFile);
            }
            $worker = new $workerName($this);
        } else {
            parent::run();
        }
    }

}
/*
set_error_handler('oops');

function oops($type, $msg, $file, $line, $context) {

    echo "<h1>Error!</h1>";

    echo "An error occurred while executing this script. Please contact the <a href=mailto:webmaster@somedomain.com>webmaster</a> to report this error.";

    echo "<p />";

    echo "Here is the information provided by the script:";

    echo "<hr><pre>";

    echo "Error code: $type<br />";

    echo "Error message: $msg<br />";

    echo "Script name and line number of error: $file:$line<br />";

    $variable_state = array_pop($context);

    echo "Variable state when error occurred: ";

    print_r($variable_state);

    echo "</pre><hr>";
	$format = "[%s] - %s (%s:%s)\r\n";
        $logcode = date('Ymdhis').rand();
        $log_line = sprintf($format, $logcode, $error['message'], $error['file'], $error['line']);

        // Write fatal error to file
        $fh = fopen(dirname(__FILE__) . '/fatal.log', "a");
        fwrite($fh, $log_line);
        fclose($fh);
        header('Location: '.'http://centos.softura.com/crm/index/error/id/'.$logcode);
    die('---------');
}
*/
register_shutdown_function( "fatal_handler" );
function fatal_handler() {
  $errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;
  
  $error = error_get_last();

  if( $error !== NULL ) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];
    //echo "Fatal error has occured <br/>Error number ".$errno." in file ".$errfile."<br/>On line ".$errline." error message ".$errstr;
	// Create log line 
        $format = "[%s] - %s (%s:%s)\r\n";
	$logcode = date('Ymdhis').rand();
        $log_line = sprintf($format, $logcode, $error['message'], $error['file'], $error['line']); 
        
        // Write fatal error to file 
        $fh = fopen(dirname(__FILE__) . '/fatal.log', "a"); 
        fwrite($fh, $log_line); 
        fclose($fh); 
    header('Location: '.'http://centos.softura.com/crm/index/error/id/'.$logcode);
  }
}
?>
