<?php

include_once('ChromePhp.php');

class BL_ChromeLogger extends ChromePhp {
    
    public static function log() {
        self::useFile("tmp/chromelogs", 'tmp/chromelogs');
        $logger = self::getInstance();
        $args = func_get_args();
        $severity = count($args) == 3 ? array_pop($args) : '';

        // save precious bytes in the cookie
        if ($severity == self::LOG) {
            $severity = '';
        }

        return self::_log($args + array('type' => $severity));
    }

}
