<?php
/**
     * Custom Validator to get at least one vendor or client is selected .
     * @author zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return boolean
     */

class BL_Validate_CheckVendorClientForEvent extends Zend_Validate_Abstract {
    const CHOOSE_ONE = 'chooseOne';

    protected $_messageTemplates = array(
        self::CHOOSE_ONE => "Choose at least one vendor or client",
    );
    
    public function isValid($value) {        
        $all_client = $_POST['send_seperate_invitation_client'];
        $all_vendor = $_POST['send_seperate_invitation_vendor'];       
        if((!isset($_POST['right_vendor'])) &&(!isset($_POST['right_client'])) && ($all_client==0) && ($all_vendor==0)){
            $this->_error(self::CHOOSE_ONE);
            return false;
        }
        return true;
    }
}

?>