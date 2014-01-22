<?php

/**
 * Class BL_AMC
 * @tutorial Class for handling AMC specific functions mainly related to dates
 * @author Mahbubur Rahman
 * @uses Zend_Date
 * @copyright Blueliner Marketing LLC
 */
class BL_AMC {
    const GRACE = 37;

    public static $quarters = array(
        '1' => array('start' => 219, 'end' => '310'), 
        '2' => array('start' => 311, 'end' => '37'), 
        '3' => array('start' => 38, 'end' => '127'),
        '4' => array('start' => 128, 'end' => '218'),
    );

    /**
     * Function to get Current Quarter
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @return Integer
     */
    public static function getCurrentQarter() {
        $date = new Zend_Date();
        return self::getQuarter($date);
    }

    public static function getCurrentFiscalYear()
    {
	$date = new Zend_Date();
        $quarter = self::getCurrentQarter($date);
	if($quarter == '1' || $quarter == '2') {
	    return Date('Y') . "-" . (Date('y') + 1);
	} else {
	    return (Date('Y') - 1) . "-" . Date('y') ;
	}
    }
    
    public static function getNextFiscalYear(){
    	$date = new Zend_Date();
    	$quarter = self::getCurrentQarter($date);
    	if($quarter == '1' || $quarter == '2') {
    		return (Date('Y') + 1) . "-" . (Date('y') + 2);
    	} else {
    		return (Date('Y')) . "-" . (Date('y') + 1) ;
    	}
    }

    /**
     * Function to get Last Quarter
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @return Integer
     */
    public static function getLastQarter() {
        $current_quarter = self::getCurrentQarter();
        if ($current_quarter == 1) {
            return 4;
        }
        return $current_quarter - 1;
    }

    /**
     * Function to Get Quarter depending the day of year
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @return Integer
     * @param  Zend_Date $date Required
     */
    public static function getQuarter(Zend_Date $date) {
        $day_of_year = $date->get('D');
        /**
         * US fiscal Quarter which works in the following way
         */
        foreach (self::$quarters as $quarter => $val) {
        	if ($val['start'] < $val['end']){
        	
	            if (self::isInRange($day_of_year, $val['start'], $val['end'])) {
	                return $quarter;
	            }
        	} else {
        		if (self::isInrange($day_of_year, 0, $val['start']) || self::isInRange($day_of_year, $val['end'], 0)){
        		 return $quarter;
        		}
        	}
        }
	return 2;
    }

    /**
     * Function to determine whether the given number is within the range of two numbers
     * @author Nathaniel Higgins [ Forrst ]
     * @return Boolean
     * @param  integer $num Required
     * @param  integer $min Optional
     * @param  integer $max Optional
     */
    public static function isInRange($num, $min=1, $max=999) {
        if ((is_numeric($num) and is_numeric($min) and is_numeric($max)) and ($num >= $min AND $num <= $max)) {
            return true;
        }
        return false;
    }

    /**
     * Function to check if the due is within the grace period for the given quarter
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Boolean
     * @param integer $quarter Required
     * @param Zend_Date $date Required
     */
    public static function isWithinGracePeriod(Zend_Date $date) {
        $last_quarter = self::getLastQarter();
        $quarter_n_grace = self::$quarters[$last_quarter]['end'] + self::GRACE;
        $quarter_n_grace = $quarter_n_grace > 365 ? $quarter_n_grace - 365 : $quarter_n_grace;
        return $date->get('D') <= $quarter_n_grace;
    }

    /**
     * Function to Parse a YAML file (Also in BUtilities but this can be accessed from anywhere in APP)
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     * @uses Symfony\Component\Yaml
     */
    public static function parseYAML($file) {
        $array = \Symfony\Component\Yaml\Yaml::parse($file);
        return $array;
    }

    /**
     * Function to doctrine dump inside controller action
     * @author Masud from BUtils
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public static function doctrine_dump($var, $die = FALSE) {
        echo "<pre>";
        Doctrine\Common\Util\Debug::dump($var);
        echo "</pre>";
        if ($die) {
            exit(0);
        }
    }

    /**
     * Determines the difference between two timestamps. [ Taken From Wordpress ]
     *
     * The difference is returned in a human readable format such as "1 hour",
     * "5 mins", "2 days".
     *
     * @since 1.5.0
     *
     * @param int $from Unix timestamp from which the difference begins.
     * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
     * @return string Human readable time difference.
     */
    public static function human_time_diff($from, $to = '') {
        if (empty($to))
            $to = time();
        $diff = (int) abs($to - $from);
        if ($diff <= 3600) {
            $mins = round($diff / 60);
            if ($mins <= 1) {
                $mins = 1;
            }
            /* translators: min=minute */
            $since = sprintf(_n('%s min', '%s mins', $mins), $mins);
        } else if (($diff <= 86400) && ($diff > 3600)) {
            $hours = round($diff / 3600);
            if ($hours <= 1) {
                $hours = 1;
            }
            $since = sprintf(_n('%s hour', '%s hours', $hours), $hours);
        } elseif ($diff >= 86400) {
            $days = round($diff / 86400);
            if ($days <= 1) {
                $days = 1;
            }
            $since = sprintf(_n('%s day', '%s days', $days), $days);
        }
        return $since;
    }

}
