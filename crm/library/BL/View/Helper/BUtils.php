<?php

class BL_View_Helper_BUtils extends Zend_View_Helper_Abstract {

    public function BUtils() {
        return $this;
    }

    /**
     * Set the file name [Ported from Codeigniter ]
     *
     * This function takes a filename/path as input and looks for the
     * existence of a file with the same name. If found, it will append a
     * number to the end of the filename to avoid overwriting a pre-existing file.
     *
     * @access	public
     * @param	string
     * @param	string
     * @return	string
     */
    function set_filename($path, $filename, $include_paths=true, $encrypt=false) {
        $extension = "." . end(explode('.', $filename));
        if ($encrypt == TRUE) {
            mt_srand();
            $filename = md5(uniqid(mt_rand())) . $extension;
        }
        if (!file_exists($path . $filename)) {
            return $include_paths ? $path . "/" . $filename : $filename;
        }

        $filename = str_replace($extension, '', $filename);
        $new_filename = '';
        for ($i = 1; $i < 100; $i++) {
            if (!file_exists($path . $filename . $i . $extension)) {
                $new_filename = $filename . $i . $extension;
                break;
            }
        }
        if ($new_filename == '') {
            return FALSE;
        } else {
            return $include_paths ? $path . "/" . $new_filename : $new_filename;
        }
    }

    /**
     * Function to Generate thumb path for a given filename
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    function thumb_path($file, $return_default=true) {
        if (!empty($file)) {
            $ext = strrchr($file, ".");
            return str_replace($ext, "_thumb" . $ext, $file);
        } else {
            if ($return_default) {
                return 'no-img-available.jpg';
            } else {
                return '';
            }
        }
    }

    /**
     * Function: Print vars using print_r with pre tag
     *
     * @description :
     * @param  $ : $var: Input Var, $die: whether execution should end after pre
     * @return : void
     * @author : Fayaz.
     */
    function pre($var, $die = FALSE) {
        if (is_array($var) || is_object($var) || is_resource($var)) {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        } else {
            echo "<br /><b>$var</b><br />";
        }
        if ($die) {
            exit(0);
        }
    }

    function doctrine_dump($var, $die = FALSE) {
        echo "<pre>";
        Doctrine\Common\Util\Debug::dump($var);
        echo "</pre>";
        if ($die) {
            exit(0);
        }
    }

    function get_root_path() {
        return dirname(APPLICATION_PATH);
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

    function readable_time($timestamp, $num_times = 2) { // this returns human readable time when it was uploaded (array in seconds)
        $times = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        $now = time();
        $secs = $now - $timestamp; // Fix so that something is always displayed
        if ($secs == 0) {
            $secs = 1;
        }
        $count = 0;
        $time = '';
        foreach ($times as $key => $value) {
            if ($secs >= $key) { // time found
                $s = '';
                $time .= floor($secs / $key);
                if ((floor($secs / $key) != 1))
                    $s = 's';
                $time .= ' ' . $value . $s;
                $count++;
                $secs = $secs % $key;
                if ($count > $num_times - 1 || $secs == 0) {
                    break;
                } else {
                    $time .= ', ';
                }
            }
        }
        return $time;
    }

    public function _getParam($key, $default="") {
        return Zend_Controller_Front::getInstance()->getRequest()->getParam($key, $default);
    }

    /**
     * Function to trim a string to desired length
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    function neat_trim($str, $n, $delim = '...') {
        $len = strlen($str);
        if ($len > $n) {
            preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
            return sizeof($matches) ? rtrim($matches[1]) . $delim : "";
        } else {
            return $str;
        }
    }

    /**
     * Function to get logged in user's id. Repeat function in action helper 
     * because view script can't access action helpers
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getLoggedInUser() {
        $auth_usr = Zend_Auth::getInstance()->getIdentity();
        if (!$auth_usr) {
            return null;
        }
        $logged_in_user = $auth_usr->id;
        return $logged_in_user;
    }

    /**
     * Function to get logged in user's role
     * @author Masud
     * @copyright iVive Labs
     * @version 0.1
     * @access public
     * @return String
     */     
    public function getLoggedInUserRole() {
        $auth_usr = Zend_Auth::getInstance()->getIdentity();
        if (!$auth_usr) {
            return null;
        }        
        return $auth_usr->account_type;
    }
            
    /**
     * Function to get currency
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $amount
     * @return String
     */
    public function getCurrency($amount=NULL) {
        $currency = new Zend_Currency('en_US');
        return $currency->toCurrency($amount);
    }

    /**
     * Function to generate license agreement number
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $params<array>
     * @return String
     */
    public function getLicenseAgreementNumber($params=array()) {
        return $params['prefix'] . $params['ym'] . $params['l_id'];
    }

    /**
     * Function to get invoice number
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $vendor_id
     * @return String
     */    
     public function getInvoiceNumber($vendor_id) {
         return "INV_" . $vendor_id . date('YmdHis'); 
     }

     public function fix_isset($val,$out,$default=""){
        if (! isset ( $var ) || is_null ( $var ) || empty ( $var ))
        {
            $default;
        }        
        return $out;
     }
     
     /**
     * Function to generate PDF invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param <array>
     * @return String
     */     
    public function getPDF($params=array()) {
        require_once('ThirdParty/tcpdf/config/lang/eng.php');
        require_once('ThirdParty/tcpdf/tcpdf.php');
                
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($params['author']);
        $pdf->SetTitle($params['title']);
        $pdf->SetSubject($params['subject']);
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        // set default header data
//        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 065', PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);   //for margin footer and add page number in each page
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        // ---------------------------------------------------------
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        // Set font
//        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFont('dejavusans', '', 8, '', true);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();
        
//        $pdf_html = $licensing_agreement;              
        $pdf->writeHTML($params['pdf_content'], true, 0, true, 0);                
        $save_to = $params['file_path'] . $params['file_name'] . ".pdf";
        $pdf->Output($save_to, $params['output_type']);
        return $save_to;    
    }
}