<?php

/**
 * Extra DB instance to interace with CRM pages
 */
class CMS_DB extends wpdb {

    protected static $instance = null;

    public static function getInstance() {        
        if (!self::$instance) {
            self::$instance = new CMS_DB('greekamc', 'am@kMg!RcA47', 'admin_amc', 'localhost');
        }
        return self::$instance;
    }

}
