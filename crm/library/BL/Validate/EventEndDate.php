<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * author:zea
 */

class BL_Validate_EventEndDate extends Zend_Validate_Abstract {
    const END_DATE_INVALID = 'endDateInvalid';
    const END_DATE_NULL = 'endDateNull';
    const FALSEFORMAT    = 'dateFalseFormat';


    protected $_messageTemplates = array(
        self::END_DATE_INVALID => "%value% is wrong End Date",
        self::END_DATE_NULL => "End Date can't be empty for mulple day event",
        self::FALSEFORMAT    => "%value% does not fit the date format mm/dd/YYYY"
    );
    protected $_start_date;
  

    public function __construct($start_date) {
        $this->_start_date = $start_date;
    }

    public function isValid($value) {
        $this->_setValue($value);
        $EndDate = new Zend_Date($value);        
        $startDate = new Zend_Date($this->_start_date);
        if(!isset($value)){
            $this->_error(self::END_DATE_NULL);
            return false;
        }
        if (!preg_match('~(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d~', date("m/d/Y", strtotime($EndDate)))) {
                $this->_error(self::FALSEFORMAT);
                return false;
            }
        if ($startDate->isLater($EndDate)) {
            $this->_error(self::END_DATE_INVALID);
            return false;
        }       
        return true;
    }

}

?>