<?php

class ProcessWorker extends BL_Gearman_Worker {

    protected $_registerFunction = 'amcwork';
    protected $_timeout = 600000; // 10 minutes
    protected $_memory = 256; // 256 MB

    protected function init() {
        //mail("pappu687@gmail.com", "supervisor started", "Supervisor started Automatically");
        $this->write_log("Supervisor Started");
    }

    protected function timeout() {
        // this function will be called whenever the
        // timeout limit has been reached.  this means
        // that there has been no jobs worked on for this
        // particular amount of time.  this is extremely
        // useful for database connections that do not
        // automatically restart.
    }

    protected function shutdown() {
        // if you have any open connections
        // these should be closed down here.
        // otherwise you can also destruct any
        // other objects you might have.
    }

    protected function _work() {
        $workload = $this->getWorkload();
        switch ($workload) {
            case "sendInvoices":
                $this->write_log("Generating Invoices");
                BL_Processes::sendInvoices();
                break;
            default:
                break;
        }
    }

    public function write_log($str) {
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/../tmp/applog.txt");
        $logger = new Zend_Log($writer);
        $logger->info($str);
    }

}
