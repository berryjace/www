<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * author:zea
 */
class BL_Validate_DateGreaterToday extends Zend_Validate_Abstract
{
    const DATE_INVALID = 'dateInvalid';
    const FALSEFORMAT    = 'dateFalseFormat';

    protected $_messageTemplates = array(
        self::DATE_INVALID => "'%value%' is not valid start date",
        self::FALSEFORMAT    => "%value% does not fit the date format mm/dd/YYYY"
    );

    public function isValid($value) {
        $this->_setValue($value);

        $date = new Zend_Date($value);
        $date->addDay(1);
        $now = new Zend_Date(date('Y-d-m'));
       if (!preg_match('~(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d~', date("m/d/Y", strtotime($date)))) {
                $this->_error(self::FALSEFORMAT);
                return false;
        }
        if ($now->isLater($date)) {
            $this->_error(self::DATE_INVALID);
            return false;
        }
        return true;
    }
}

?>
