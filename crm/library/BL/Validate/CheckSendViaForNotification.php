<?php
/**
     * Custom Validator to get at least one send via notification
     * @author masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return boolean
     */

class BL_Validate_CheckSendViaForNotification extends Zend_Validate_Abstract {
    const CHOOSE_ONE = 'chooseOne';

    protected $_messageTemplates = array(
        self::CHOOSE_ONE => "Choose at least one send type",
    );
    
    public function isValid($value) {        
        $via_email = $_POST['email_notification'];
        $via_notification = $_POST['site_notification'];
        if(($via_email == 0) && ($via_notification  == 0)){
            $this->_error(self::CHOOSE_ONE);
            return false;
        }
        return true;
    }
}

?>