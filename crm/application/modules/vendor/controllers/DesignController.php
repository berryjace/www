<?php

class Vendor_DesignController extends Zend_Controller_Action {

    protected $em;

    public function init() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
    }

    /**
     * Function to show vendor product designs
     * @author Masud
     * @category Blueliner Marketing
     * @version 0.1
     * @access public
     * @return void
     */
    public function indexAction() {
        $params = array(
            'owner_id' => $this->_helper->BUtilities->getLoggedInUser(),
            'page' => $this->_getParam('page', 1),
            'per_page' => 22,
            'num_of_link' => 5 
        );
        $this->view->design = $this->em->getRepository("BL\Entity\ProductDesign")->getVednorDesigns($params);               
    }
    
    /**
     * Function to show vondors approved designs
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function approvedDesignAction() {
        $params = array(
            'owner_id' => $this->_helper->BUtilities->getLoggedInUser(),
            'is_approved' => 1,
            'page' => $this->_getParam('page', 1),
            'per_page' => 22,
            'num_of_link' => 5 
        );
        $this->view->design = $this->em->getRepository("BL\Entity\ProductDesign")->getVednorDesigns($params);               
    }
    
    /**
     * Function to show vendors pending designs
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function pendingDesignAction() {
        $params = array(
            'owner_id' => $this->_helper->BUtilities->getLoggedInUser(),
            'is_approved' => 0,
            'page' => $this->_getParam('page', 1),
            'per_page' => 22,
            'num_of_link' => 5 
        );
        $this->view->design = $this->em->getRepository("BL\Entity\ProductDesign")->getVednorDesigns($params);               
    }
    
    /**
     * Function to Upload multiple Design files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return void
     */
    public function viewAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $design_id = $this->_getParam('id');
        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
        $this->view->design = $this->em->getRepository("BL\Entity\ProductDesign")->findOneBy(array('id' => $design_id, 'owner_id' => $vendor_id));
    }
    
    /**
     * Function to Upload multiple Design files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function rejectedDesignAction() {
        
        $params = array(
            'owner_id' => $this->_helper->BUtilities->getLoggedInUser(),
            'is_approved' => 2,
            'page' => $this->_getParam('page', 1),
            'per_page' => 22,
            'num_of_link' => 5 
        );
        $this->view->rejected_designs = $this->em->getRepository("BL\Entity\ProductDesign")->getVednorDesigns($params);  
    }

    /**
     * Function to Upload multiple Design files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function submitDesignAction() {
//        $this->_helper->JSLibs->load_fancy_assets();
//        $this->_helper->JSLibs->load_plupload_assets();
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_plupload_assets'));
        $vendor = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());

        /**
         * Uoload is handled by PLUPLOAD component, so no uploadig here.
         * Will just the names from the pics[] array to db.
         * We're keeping a redundancy here. Having the default or the first image as
         * the event picture.
         */
        $uploaded_pics = $this->_getParam('pics');
        if (sizeof($uploaded_pics)) {
            foreach ($uploaded_pics as $pic) {
                $class = 'BL\Entity\ProductDesign';
                $design = new $class();
                $design->file_url = $pic;
                $design->owner_id = $vendor;
                $design->is_approved = 0;
                $this->em->persist($design);
            }
            $this->em->flush();
            $this->em->clear();
            $this->_helper->flashMessenger("Successfully uploaded the design", "success");
            $this->_redirect('vendor/design');
        }                
    }

    /**
     * Function to Upload multiple Design files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function resubmitDesignAction() {
        $design_id = $this->_getParam('id');
        $vendor_id = $this->_helper->BUtilities->getLoggedInUser();
        $design = $this->em->getRepository("BL\Entity\ProductDesign")->findOneBy(array('id' => $design_id, 'owner_id' => $vendor_id));
        $design->is_approved = 3;
        $this->em->persist($design);
        $this->em->flush();
        $this->em->clear();
        $this->view->success = "Design re-submitted successfully";
    }

    /**
     * Function to Upload multiple Design files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function confirmationAction() {
        //echo APPLICATION_PATH;
        $save_to = APPLICATION_PATH."/../tmp/invoice.pdf";
        echo $save_to;
    }

    /**
     * Function to Upload multiple Design files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function uploadPictureAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_plupload_assets();
        $this->_helper->BUtilities->setBlankLayout();
    }

    public function doUploadAction() {

        /**
         * Todo
         * 4. Add to DB
         */
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $targetDir = APPLICATION_PATH . '/../assets/files/design/';

        @set_time_limit(5 * 60);

        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

        $fileName = preg_replace('/[^\w\._]+/', '', $fileName);

        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        if (!file_exists($targetDir))
            @mkdir($targetDir);

        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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
                    /**
                     * Let's create thumbnails here
                     */
                    include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                    $thumb_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb" . $fileName;                    
                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(110, 110)->padding(110, 75, '#FFFFFF');
                    $thumb->save($thumb_save_path);

                    $thumb2_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb2" . $fileName;
                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(450, 450)->padding(450, 300, '#FFFFFF');
                    $thumb->save($thumb2_save_path);

                    /**
                     * And delete the tmp file
                     */
                    @unlink($_FILES['file']['tmp_name']);
                }
                else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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

        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

}

