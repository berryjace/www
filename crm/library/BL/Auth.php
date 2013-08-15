<?php

class BL_Auth extends Zend_Auth {

    /**
     * Singleton instance
     *
     * @var Project_Application_Auth
     */
    protected static $_instance = null;

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @tutorial Function to check whether logged in or not in the system. 
     * This will return true only hasIdentity has a ID which means logged into
     * the system with system's db auhentication
     * 
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $obj Object BL_Auth->getIdentity();
     * @return String Provider name e.g Google, Yahoo
     */
    public static function isLoggedIn() {
        $instance = self::getInstance();
        return ($instance->hasIdentity() AND isset($instance->getIdentity()->id));
    }

    /**
     * Function to get the provider
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $obj Object BL_Auth->getIdentity();
     * @return String Provider name e.g Google, Yahoo
     */
    public static function getProvider($obj) {
        $provider = "";
        /**
         * There's no pattern of how to detect the social response. So have to
         * check one by one
         *          
         * Check Based on criteria
         * Zend_Auth : the $obj is type Object
         * Facebook : properties[link] has mention of facebook
         * Twitter : has element properties[followers_count] 
         * Yahoo : has string me.yahoo in element identity 
         * Google : has string .google. in element identity 
         * 
         */
        if (is_object($obj)) {
            $provider = 'zend';
            return $provider;
        }
        if (isset($obj['properties']) AND isset($obj['properties']['link']) AND strstr($obj['properties']['link'], 'facebook.com')) {
            $provider = 'facebook';
            return $provider;
        }
        if (isset($obj['properties']) AND isset($obj['properties']['followers_count'])) {
            $provider = 'twitter';
            return $provider;
        }
        if (isset($obj['identity']) AND strstr($obj['identity'], 'me.yahoo.')) {
            $provider = 'yahoo';
            return $provider;
        }
        if (isset($obj['identity']) AND strstr($obj['identity'], '.google.')) {
            $provider = 'google';
            return $provider;
        }
        return "none";
    }

    /**
     * Function to get the provider specific db field mapping values
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param String $provider Social Provider like Facebook , twitter etc
     * @return Array Fields to query to social_signin db
     */
    public static function getIdentifiers($provider) {
        $auth = self::getInstance()->getIdentity();
        switch ($provider) {
            case "facebook":
                return array(
                    "provider" => $provider,
                    "identity_element" => "identity",
                    "identity_element_value" => $auth['identity']
                );
                break;
            case "twitter":
                return array(
                    "provider" => $provider,
                    "identity_element" => "identity",
                    "identity_element_value" => $auth['identity']
                );
                break;
            case "google":
                return array(
                    "provider" => $provider,
                    "identity_element" => "identity",
                    "identity_element_value" => $auth['identity']
                //"identity_element_value" => $auth['properties']['email']
                );
                break;
            case "yahoo":
                return array(
                    "provider" => $provider,
                    "identity_element" => "identity",
                    "identity_element_value" => $auth['identity']
                );
                break;
            default:
                return array();
                break;
        }
    }

    /**
     * Function to populate some info in the form from social signins for signup
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Array Fields with values to be populated in the form
     */
    public function populate_from_social() {
        $identity = self::getInstance()->getIdentity();
        $provider = self::getProvider($identity);
        if ($provider <> "") {
            switch ($provider) {
                case "facebook":
                    return array(
                        "customer_name" => $identity['properties']['name'],
                        "customer_email" => $identity['properties']['email']
                    );
                    break;
                case "twitter":
                    return array(
                        "customer_name" => $identity['properties']['name']
                    );
                    break;
                case "google":
                    return array(
                        "customer_email" => $identity['properties']['email']
                    );
                    break;
                case "yahoo":
                    return array(
                        "customer_email" => $identity['properties']['email']
                    );
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Function to know if the user is logged in via social and which one
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public static function hasSocialIdentity() {
        $identity = self::getInstance()->hasIdentity();
        $provider = self::getProvider(self::getInstance()->getIdentity());
        return ($identity AND ($provider <> "zend" AND $provider <> "none"));
    }

}
