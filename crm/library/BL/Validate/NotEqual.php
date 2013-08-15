<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class to check equality of two form elements
 *
 * @author masud
 */
class BL_Validate_NotEqual extends Zend_Validate_Abstract {
    
    const NOT_EQUAL = 'notEqual';
    
    private $firstInput;
    private $secondInput;    
    
    protected $_messageTemplates = array(
        self::NOT_EQUAL => "did not match"           
    );
    
    public function __construct($params=array()) {
        $this->firstInput = $params['0'];
        $this->secondInput = $params['1'];        
    }

    public function isValid($value) {  
        $this->_setValue($value);                                
        if(trim($_POST["$this->firstInput"]) != trim($_POST["$this->secondInput"])){
            $this->_error(self::NOT_EQUAL);
            return false;
        }
        return true;
    }
    
}

?>
