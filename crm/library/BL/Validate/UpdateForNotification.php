<?php
/**
     * Custom Validator to get at least one send via notification
     * @author masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return boolean
     */

class BL_Validate_UpdateForNotification extends Zend_Validate_Abstract {
    const CHOOSE_ONE = 'chooseOne';

    protected $_messageTemplates = array(
        self::CHOOSE_ONE => "Choose update type.",
    );
    
    public function isValid($value) {        
        $send_and_update = $_POST['send_and_update'];
        $only_update = $_POST['only_update'];
        if(($send_and_update == 0) && ($only_update  == 0)){
            $this->_error(self::CHOOSE_ONE);
            return false;
        }
        return true;
    }
}

?>