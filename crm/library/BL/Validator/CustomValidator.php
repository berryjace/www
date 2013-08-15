<?php

class BL_Validator_CustomValidator extends Zend_Validate_Abstract {
    const MSSG = '';
    protected $_messageTemplates = array(
        self::MSSG => "'%value%' is already in database."
    );

    public function isValid($value) {
        $this->_setValue($value);
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $usr = $em->getRepository("ZB\Entity\User")->findOneBy(array('email'=>$value));
        if (sizeof($usr)) {
            $this->_error(self::MSSG);
            return false;
        }
        return true;
    }
}