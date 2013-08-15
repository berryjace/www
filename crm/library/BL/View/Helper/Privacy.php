<?php

class BL_View_Helper_Privacy extends Zend_View_Helper_Abstract {

    public function Privacy() {
        return $this;
    }

    /**
     * Function to get Privacy Settings Array
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getPrivacySettings() {
        return array(
            "about me" => "About me",
            "my events" => "Events I'm hosting",
            "sent recommendations" => "Recommendations I've made",
            "received recommendations" => "Recommendations I've received",
            "my deals" => "Deals I'm offering",
            "my administered groups" => "Groups I administer",
            "my joined groups" => "Groups I've joined"
        );
    }

    /**
     * Function to let the user know if the item is allowed as per privacy array;
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function isPublic($setting, $privacy_array) {
        $settings = array();
        foreach ($privacy_array as $setting_item) {
            $settings[$setting_item->item] = array($setting_item->setting => $setting_item->setting_value);
        }        
        if (array_key_exists($setting, $settings)) {
            return $settings[$setting]['show'] == "yes" ? true : false;
        }
        return true;
    }

}