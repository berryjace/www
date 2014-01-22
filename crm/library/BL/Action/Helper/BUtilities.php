<?php

class BL_Action_Helper_BUtilities extends
Zend_Controller_Action_Helper_Abstract {

    public function BUtilities() {
        return $this;
    }

    public function addLogs($user_id = null, $module = null, $url = null, $activity_type = null, $details = null) {

        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $class = 'ZB\Entity\Activity';

        $activity = new $class();

        $activity->user_id = $user_id;
        $activity->module = $module;
        $activity->url = $url;
        $activity->activity_type = $activity_type;
        $activity->details = $details;
        $activity->recorded_at = new DateTime(date("F j, Y, g:i a"));

        $em->persist($activity);
        $em->flush();
    }

    public function GetUserNameById($user_id) {

        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $users = $em->getRepository("ZC\Entity\User")->getUser($user_id);
        return $users[0]['username'];
    }

    function replied_message_format($user_from = "", $user_to = "", $message_id = "", $message_type = "reply") {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $user_from = $em->getRepository("ZB\Entity\User")->getUser($user_from);
        $user_to = $em->getRepository("ZB\Entity\User")->getUser($user_to);
        $message_body = $em->getRepository("ZB\Entity\Message")->getDetailmessage($message_id);

        if ($message_type == "reply") {
            return $msg_body = "<br><br>" . "On " . $message_body[0]->msg_timestamp->format("D, M d, Y") . " at " . $message_body[0]->msg_timestamp->format("h:m a") . " " . $user_from[0]['username'] . " wrote : <br>" . $message_body[0]->msg_body;
        } else {
            return $msg_body = "<br><br>" . "---------- Forwarded message ----------<br>Date: " . $message_body[0]->msg_timestamp->format("D, M d, Y") . " at " . $message_body[0]->msg_timestamp->format("h:m a") . "<br>Subject: " . $message_body[0]->msg_subject . "<br>To : " . $user_from[0]['username'] . "<br><br>" . $message_body[0]->msg_body;
        }
    }

    function subject_format($subject = "", $action = "Re:") {
        $opt = array(
            'Re:',
            'Fw:'
        );
        if (in_array(substr($subject, 0, 3), $opt)) {
            return $action . substr($subject, 3);
        } else {
            return "{$action} " . $subject;
        }
    }

    public function setNoLayout() {
        $this->_actionController->getHelper("layout")->disableLayout();
        $this->_actionController->getHelper("viewRenderer")->setNoRender(true);
    }

    public function setAjaxLayout() {
        $this->_actionController->getHelper("layout")->setLayout('layout/ajax-layout');
        $this->_actionController->getHelper("viewRenderer")->setNoRender(false);
    }

    public function setBlankLayout() {
        $this->_actionController->getHelper("layout")->setLayout('layout/blank');
        $this->_actionController->getHelper("viewRenderer")->setNoRender(false);
    }

    public function setEmptyLayout() {
        $this->_actionController->getHelper("layout")->setLayout('layout/empty');
        $this->_actionController->getHelper("viewRenderer")->setNoRender(false);
    }

    /**
     * Function to add layout for popup views
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function setPopupLayout() {
        $this->_actionController->getHelper("layout")->setLayout('layout/popup-layout');
        $this->_actionController->getHelper("viewRenderer")->setNoRender(false);
    }
    
    public function getLoggedInUser() {
        $auth_usr = Zend_Auth::getInstance()->getIdentity();
        if (!$auth_usr) {
            return null;
            //$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            //$redirector->gotoUrl('login');
        }
        $logged_in_user = $auth_usr->id;
        return $logged_in_user;
    }

    /**
     * Function to check if a user is logged in or not
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function isLoggedIn() {
        $auth_usr = Zend_Auth::getInstance()->getIdentity();
        return (!is_object($auth_usr)) ? FALSE : TRUE;
    }

    /**
     * Element
     *
     * Lets you determine whether an array index is set and whether it has a value.
     * If the element is empty it returns FALSE (or whatever you specify as the default value.)
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	mixed
     * @return	mixed	depends on what the array contains
     */
    public function element($item, $array, $default = FALSE) {
        if (!isset($array[$item]) OR $array[$item] == "") {
            return $default;
        }

        return $array[$item];
    }

    /**
     * Random Element - Takes an array as input and returns a random element
     *
     * @access	public
     * @param	array
     * @return	mixed	depends on what the array contains
     */
    public function random_element($array) {
        if (!is_array($array)) {
            return $array;
        }
        return $array[array_rand($array)];
    }

    /**
     * Function to get a key value from the config
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function config_item($section, $key) {
        $options = $this->_actionController->getInvokeArg('bootstrap')->getOptions();
        return isset($options[$section][$key]) ? $options[$section][$key] : 0;
    }

    /**
     * Function: site_encrypt
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @description : Encrypt function.
     * for now base64 is used with replacing these:
     * '=' => 'i-n'
     * '*' => 'i_n'
     * '/' => '-'
     * '+' => '_'
     * @param  $ : $val
     * @return : String
     */
    public function site_encrypt($val = "") {
        if (!empty($val)) {
            $val = base64_encode($val);
            $search = array(
                '=',
                '*',
                '/',
                '+'
            );
            $replace = array(
                'i-n',
                'i_n',
                '-',
                '_'
            );
            $val = str_replace($search, $replace, $val);
        }
        return $val;
    }

    /**
     * Function: site_decrypt
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @param  $ : $val
     * @return : String
     */
    public function site_decrypt($val = "") {
        if (!empty($val)) {
            $replace = array(
                '=',
                '*',
                '/',
                '+'
            );
            $search = array(
                'i-n',
                'i_n',
                '-',
                '_'
            );
            $val = str_replace($search, $replace, $val);
            $val = base64_decode($val);
        }
        return $val;
    }

    public function __toString() {
        return "It's just a helper. No output";
    }

    /**
     * Function: url_title
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @return : String
     */
    function url_title($str, $separator = 'underscore', $lowercase = FALSE) {
        if ($separator == 'dash') {
            $search = '_';
            $replace = '-';
        } else {
            $search = '-';
            $replace = '_';
        }

        $trans = array(
            '&\#\d+?;' => '',
            '&\S+?;' => '',
            '\s+' => $replace,
            '[^a-z0-9\-\._]' => '',
            $replace . '+' => $replace,
            $replace . '$' => $replace,
            '^' . $replace => $replace,
            '\.+$' => ''
        );

        $str = strip_tags($str);

        foreach ($trans as $key => $val) {
            $str = preg_replace("#" . $key . "#i", $val, $str);
        }

        if ($lowercase === TRUE) {
            $str = strtolower($str);
        }

        return trim(stripslashes($str));
    }

    /**
     * Function to get SMTP transport to send email
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function get_mail_transport($config_key = "default") {
        $smtp_config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/smtp.ini", $config_key);
        return new Zend_Mail_Transport_Smtp($smtp_config->server, array(
                    'ssl' => 'tls',
                    'auth' => 'login',
                    'username' => $smtp_config->user,
                    'password' => $smtp_config->password
                ));
    }

    public function allowParams($req_params, $allowed_params) {
        foreach ($req_params as $param => $val) {
            if (!in_array($param, $allowed_params)) {
                throw new Exception('Sorry, but you made an invalid page request', 404);
                exit(0);
            }
        }
    }

    public function rangeMonth($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
        $res['end'] = date('Y-m-d', strtotime('last day of this month', $dt));
        return $res;
    }

    public function rangeWeek($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        $res['start'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        $res['end'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
        return $res;
    }

    /**
     * Function to send email
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function send_mail($params) {
        $mail = new Zend_Mail();
        $mail->setType(Zend_Mime::MULTIPART_RELATED);
        $mail->addTo($params['to'], $params['to_name']);
        $mail->setFrom($params['from'], $params['from_name']);
        $mail->setSubject($params['subject']);
        $mail->setBodyHtml($params['body']);

        if(isset ($params['cc'])){
            $mail->addCc($params['cc']);
        }
        
        if (isset($params['bcc'])){
        	$mail->addBcc($params['bcc']);
        }
        
        if (isset($params['file'])) {
            $attachment = new Zend_Mime_Part(file_get_contents($params['file']));
            $attachment->filename = basename($params['file']);
            $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = Zend_Mime::ENCODING_BASE64;
            $mail->addAttachment($attachment);
        }
        return $mail->send();
    }

    /**
     * Function to debug using Chrome PHP
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ChromePHP_log($log, $key='') {
        ChromePhp::log($key, $log);
    }

    /**
     * Function to Parse a YAML file
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     * @uses Symfony\Component\Yaml
     */
    public function parseYAML($file) {
        $array = \Symfony\Component\Yaml\Yaml::parse($file);
        return $array;
    }

    /**
     * Function to convert array to Object
     * @author Mahbub from JR
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Object
     */
    function arrayToObject($d) {
        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return (object) array_map(__FUNCTION__, $d);
        } else {
            // Return object
            return $d;
        }
    }

    /**
     * Function to convert an Object to Array
     * @author Mahbub from JR
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return array_map(__FUNCTION__, $d);
        } else {
            // Return array
            return $d;
        }
    }
    
    /**
     * Function to doctrine dump inside controller action
     * @author Masud from BUtils
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    function doctrine_dump($var, $die = FALSE) {
        echo "<pre>";
        Doctrine\Common\Util\Debug::dump($var);
        echo "</pre>";
        if ($die) {
            exit(0);
        }
    }

     /**
     * Function to send mail with cc
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param mails parameter
     * @return void
     */
    public function send_mail_with_cc($params) {
        $mail = new Zend_Mail();
        $mail->setType(Zend_Mime::MULTIPART_RELATED);
        $mail->addTo($params['to'], $params['to_name']);
        $mail->setFrom($params['from'], $params['from_name']);
        $mail->setSubject($params['subject']);
        $mail->setBodyHtml($params['body']);
        $mail->addCc($params['cc']);

        if (isset($params['file'])) {
            $attachment = new Zend_Mime_Part(file_get_contents($params['file']));
            $attachment->filename = basename($params['file']);
            $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
            $attachment->encoding = Zend_Mime::ENCODING_BASE64;
            $mail->addAttachment($attachment);
        }
        return $mail->send();
    }

    /**
     * Function to call billhighway API for online payment
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param Array
     * @return Object
     */
    public function bill_highway_api($params=array()) {
        $client = new SoapClient('https://staging.billhighway.com/api/payment.asmx?WSDL');

        $apiKey = '785A571C-17E3-482C-97EE-A207E2C7F8F4';
        $featureID = '3';
        $clientId = '1199';
        $groupId = '18976';
        $key = '1161B2BB9524849CC206FB0D';
        $EncryptedRTN = '';
        $EncryptedAcctNumber = '';
	error_log($params['account_number']."\n", 3, "./account.log");
        error_log($params['routing_number']."\n", 3, "./routing.log");

        $EncryptedAcctNumber = $this->TripeDesEncrypt($key, $params['account_number']);
        $EncryptedRTN = $this->TripeDesEncrypt($key, $params['routing_number']);
        $api_params = array(
            'apiKey' => $apiKey,
            'featureID' => $featureID,
            'clientID' => $clientId,
            'groupID' => $groupId,
            'Amount' => $params['amount'],
            'rtn' => $EncryptedRTN,
            'accountNumber' => $EncryptedAcctNumber,
            'payerName' => 'Test Payer',
            'checkNumber' => '123',
            'email' => 'testpayer@billhighway.com',
            'memo' => 'Test ACH group payment',
            'occurs' => 'once',
            'numberOfOccurrences' => 1
        );
        $response = $client->__soapCall('eCheckPaymentByGroup', array("parameters" => $api_params));
	$arr = (array) $response;
        $res = print_r($arr,true);
        error_log($arr['eCheckPaymentByGroupResult']->anyType[4]."\n", 3, "./response_echeck.log");
        error_log($res."\n", 3, "./response.log");

        return $response;
    }

    /**
     * Function to encrypt account and RTN number using billhighway private key
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $privateKey string private key
     * @param $str string string to encrypt
     * @return encrypted data
     */
    private function TripeDesEncrypt($privateKey, $str) {            
        $iv = chr(18) . chr(52) . chr(86) . chr(120) . chr(144) . chr(171) . chr(205) . chr(239);
        $block = mcrypt_get_block_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC);
        $len = strlen($str);
        $padding = $block - ($len % $block);
        $str .= str_repeat(chr($padding), $padding);
        $enc = mcrypt_encrypt(MCRYPT_TRIPLEDES, $privateKey, $str, MCRYPT_MODE_CBC, $iv);
        return base64_encode($enc);
    }
}
