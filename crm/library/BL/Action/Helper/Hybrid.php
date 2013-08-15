<?php

class BL_Action_Helper_Hybrid extends
Zend_Controller_Action_Helper_Abstract {

    /**
     * Function to Return array of the hybrid config. Ported to helper so that we have a known context of the object
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getConfig() {
        return
                array(
                    "base_url" => $this->getActionController()->view->getHelper("BUrl")->site_url("library/ThirdParty/hybridauth/"),
                    "providers" => array(
                        // google
                        "Google" => array(
                            "enabled" => true
                        ),
                        // yahoo
                        "Yahoo" => array(
                            "enabled" => true
                        ),
                        // facebook
                        "Facebook" => array(// 'id' is your facebook application id
                            "enabled" => true,
                            //"keys" => array("id" => "139701912741289", "secret" => "a26ef26dc10234e1ce6cc4d49c097e87")
                            "keys" => array("id" => "128242450615915", "secret" => "27bd129efc214e92a561b341c367c0c1") // Mahbub
                        ),
                        // myspace
                        "MySpace" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                        ),
                        // twitter
                        "Twitter" => array(
                            "enabled" => true,
                            "keys" => array("key" => "Fr5qLg6epLJILGzhy0Gbw", "secret" => "gVHIbciv7cd5RrJFOqNma15uKu9vXbalv7EwcI")
                        ),
                        // windows live
                        "Live" => array(
                            "enabled" => false,
                            "keys" => array("id" => "", "secret" => "")
                        ),
                        // linkedin
                        "LinkedIn" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                        ),
                        // tumblr
                        "Tumblr" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                        ),
                        // vimeo
                        "Vimeo" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                        ),
                        // foursquare
                        "Foursquare" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                        ),
                        // gowalla
                        "Gowalla" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                        ),
                        // PayPal
                        "PayPal" => array(
                            "enabled" => false,
                        ),
                    ),
                    "debug_mode" => false,
                    "debug_file" => "",
        );
    }

}

