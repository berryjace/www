<?php

class Admin_LicenseController extends Zend_Controller_Action
{
    protected $em;
    public function init()
    {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction()
    {
        // action body
    }

    public function deleteFileAction()
    {
        // action body
    }

    public function changeRequiredFlagsAction()
    {
        // action body
    }

    public function showAction()
    {
        // action body
    }

    public function getPendingListAction()
    {
        // action body
    }

    public function getLicenseTemplateAction()
    {
        // action body
    }

    public function getClientListAction()
    {
        // action body
    }

    public function submitLicenseAction()
    {
        // action body
    }
    
    public function statusAction() {
        //$this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_jqgrid_assets();
        //$this->view->vendors = $this->em->getRepository("BL\Entity\User")->getUsersByRole(2, $this->_getParam('page', 1));
        //die($this->view->vendors->getTotalItemCount().'===='.sizeof($this->view->vendors));
        //$products = $this->em->getRepository("BL\Entity\User")->findBy(array());
    }
    
    public function popupUploadAgreementAction(){
    	$this->_helper->JSLibs->do_call(array('load_plupload_assets'));
    	$this->_helper->BUtilities->setBlankLayout();
    
    	$vendor_id = $this->_getParam('vendor_id');
    	$this->view->vendor_id = (!empty($vendor_id))? $vendor_id : '';
    
    	$license_number= $this->_getParam('license_number');
    	$this->view->license_number = (!empty($license_number))? $license_number : '';
    }
    
    public function uploadAction() {
    	error_log("\nuploadAction() - licenseController", 3, "./errorLog.log");
    	/**
    	 * Todo
    	 * 4. Add to DB
    	*/
    	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	header("Cache-Control: post-check=0, pre-check=0", false);
    	header("Pragma: no-cache");
    
    	$targetDir = APPLICATION_PATH . '/../assets/files/licenses/';
    
    	$cleanupTargetDir = true; // Remove old files
    	$maxFileAge = 50 * 3600; // Temp file age in seconds
    
    	@set_time_limit(5 * 60);
    
    	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
    	$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
    
    	$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
    
    
    
    
    	// Make sure the fileName is unique but only if chunking is disabled
    	if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
    		$ext = strrpos($fileName, '.');
    		$fileName_a = substr($fileName, 0, $ext);
    		$fileName_b = substr($fileName, $ext);
    
    		$count = 1;
    		while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
    			$count++;
    
    		$fileName = $fileName_a . '_' . $count . $fileName_b;
    	}
    
    	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
    
    	// Create target dir
    	if (!file_exists($targetDir))
    		@mkdir($targetDir);
    
    	// Remove old temp files
    	if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
    		while (($file = readdir($dir)) !== false) {
    			$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
    
    			// Remove temp file if it is older than the max age and is not the current file
    			if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
    				@unlink($tmpfilePath);
    			}
    		}
    
    		closedir($dir);
    	} else
    		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
    
    
    	// Look for the content type header
    	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
    		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
    
    	if (isset($_SERVER["CONTENT_TYPE"]))
    		$contentType = $_SERVER["CONTENT_TYPE"];
    
    	if (strpos($contentType, "multipart") !== false) {
    		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
    			// Open temp file
    			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
    			if ($out) {
    				// Read binary input stream and append it to temp file
    				$in = fopen($_FILES['file']['tmp_name'], "rb");
    
    				if ($in) {
    					while ($buff = fread($in, 4096))
    						fwrite($out, $buff);
    				} else
    					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    				fclose($in);
    				fclose($out);
    				@unlink($_FILES['file']['tmp_name']);
    			} else
    				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    		} else
    			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    	} else {
    		// Open temp file
    		$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
    		if ($out) {
    			// Read binary input stream and append it to temp file
    			$in = fopen("php://input", "rb");
    
    			if ($in) {
    				while ($buff = fread($in, 4096))
    					fwrite($out, $buff);
    			} else
    				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    
    			fclose($in);
    			fclose($out);
    		} else
    			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    	}
    	if (!$chunks || $chunk == $chunks - 1) {
    		rename("{$filePath}.part", $filePath);
    	}
    
    	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

 }















