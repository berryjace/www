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

