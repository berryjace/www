<?php

class Client_ArtworksController extends Zend_Controller_Action {

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to List product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function indexAction() {
        $this->_helper->JSLibs->load_fancy_assets(); //added for artwork popup view
        $this->view->artworks = $this->em->getRepository("BL\Entity\ClientArtwork")->getArtworks(array('pg' => $this->_getParam('page', 1), 'User' => $this->_helper->BUtilities->getLoggedInUser()));
//        $this->view->BUtils()->doctrine_dump($this->view->artworks,1);
    }

    /**
     * Function to add artwork
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addArtworkAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_plupload_assets'));
        $client = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
        $this->view->client = $client;
        if ($this->getRequest()->isPost()) {
            //$this->_helper->BUtilities->setNoLayout();
            $formData = $this->getRequest()->getPost();
            $uploaded_pic = $formData['pics'];
            $titles = $formData['title'];
            //print_r($formData);
            if ($uploaded_pic) {
                foreach ($uploaded_pic as $key => $pic) {
                    $class = 'BL\Entity\ClientArtwork';
                    $artwork = new $class();
                    $artwork->title = $titles[$key];
                    $artwork->file_url = $pic;
		    $parts = explode('.',$pic);
                    $ext = end($parts);
                    $artwork->file_extension = $ext;
                    $artwork->User = $client;
                    $artwork->upload_date = new DateTime();
                    $this->em->persist($artwork);
                }
                $this->em->flush();
                $this->em->clear();
                //$this->_redirect('admin/vendors/sample/confirmation');

                $this->view->message = array('success' => true, 'message' => 'Successfully uploaded');
            } else {
                $this->view->message = array('success' => false, 'message' => 'There is no file uploaded');
            }
        }
    }

    /**
     * Function to save product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveArtworkAction() {
        $this->_helper->BUtilities->setNoLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $formData = $this->getRequest()->getPost();
            $title = $formData['title'];
            $artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $formData['id'], 'User' => $this->_helper->BUtilities->getLoggedInUser()));
            //print_r($formData);
            if (sizeof($artwork)) {
                $artwork->title = $title;
                $this->em->persist($artwork);
                $this->em->flush();
                $this->em->clear();
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully updated', 'title' => $artwork->title));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'no update'));
            }
        }
    }

    /**
     * Function to delete product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxDelArtworkAction() {
        if ($this->_request->isXmlHttpRequest()) {
            $artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $this->_getParam('id', ''), 'User' => $this->_helper->BUtilities->getLoggedInUser()));
            if (sizeof($artwork)) {
                $targetDir = APPLICATION_PATH . '/../assets/files/artworks/';
		if(file_exists($targetDir . $artwork->file_url))
                        @unlink($targetDir . $artwork->file_url);
                if(file_exists($targetDir . 'thumbs/_thumb' . $artwork->file_url))
                        @unlink($targetDir . 'thumbs/_thumb' . $artwork->file_url);
                $this->em->remove($artwork);
                $this->em->flush();
                $this->em->clear();
                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully deleted'));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => false, 'message' => 'Invalid Artwork'));
            }
        }
        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to Upload multiple Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function uploadFilesAction() {
        $this->_helper->JSLibs->do_call(array('load_plupload_assets'));
        $this->_helper->BUtilities->setBlankLayout();
    }

    /**
     * Function to Upload multiple Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
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

        $targetDir = APPLICATION_PATH . '/../assets/files/artworks/';

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

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755);
            @mkdir($targetDir . 'thumbs', 0755);
        }
        
        if (!is_dir($targetDir . 'originals')){
        	@mkdir($targetDir . 'originals', 0755);
        }

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
                    $thumb_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . base64_encode("_thumb") . $fileName;

                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(100, 100)->padding(110, 75, '#FFFFFF');
                    $thumb->save($thumb_save_path);

                    $thumb2_save_path = $targetDir . DIRECTORY_SEPARATOR . "large" . DIRECTORY_SEPARATOR . $fileName;
                    $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                    $thumb->resize(450, 450)->padding(450, 300, '#FFFFFF');
                    $thumb->save($thumb2_save_path);
                    
                    $original_path = $targetDir . DIRECTORY_SEPARATOR . "originals" . DIRECTORY_SEPARATOR . $fileName;
                    
                    $path = $_FILES['file']['tmp_name'];
                    
                    copy($path, $original_path);

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

    /**
     * Function to list view artworks
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function viewArtworkAction() {
        $this->_helper->BUtilities->setAjaxLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->view->artwork = $this->em->getRepository("BL\Entity\ClientArtwork")->findOneBy(array('id' => $this->_getParam('id'), 'User' => $this->_helper->BUtilities->getLoggedInUser()));
    }

    /**
     * Function to view usage guide
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function usageGuidesAction() {        
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_dataTable_assets'));
    }

    /**
     * Function to get usage guide
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetUsageGuidesDtAction() {
        $this->_helper->BUtilities->setNoLayout();

        $sorting_cols = array(
            '1' => 'cug.guide_type',
            '2' => 'cug.guide_name',
            '3' => 'cug.guide_content'
        );
        $post_params = $this->_getAllParams();
        $params = array(
            'base_url' => $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl(),
            'user_id' => $this->_helper->BUtilities->getLoggedInUser(),
            'search' => isset($post_params['sSearch']) ? $post_params['sSearch'] : '',
            'current_page' => isset($post_params['iDisplayStart']) ? $post_params['iDisplayStart'] : 1,
            'draw_count' => isset($post_params['sEcho']) ? $post_params['sEcho'] : "",
            'per_page' => isset($post_params['iDisplayLength']) ? $post_params['iDisplayLength'] : 10,
            'sort_key' => isset($sorting_cols[$post_params['iSortCol_0']]) ? $sorting_cols[$post_params['iSortCol_0']] : '',
            'search_op' => isset($post_params['search_op']) ? $post_params['search_op'] : 'AND',
            'sort_dir' => isset($post_params['sSortDir_0']) ? $post_params['sSortDir_0'] : 'ASC',
        );
        //print_r($params);
        $json = $this->em->getRepository('BL\Entity\ClientUsageGuide')->getUsageGuides($params);
        echo $json;
    }

    /**
     * Function to add usage guide
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addUsageGuidesAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
        $this->_helper->JSLibs->load_tinymce_assets();
        $form = new Client_Form_UsageGuide();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            //$this->ajaxValidate($form, $formData);
            if ($form->isValid($formData)) {
                $clientUsageGuide = new \BL\Entity\ClientUsageGuide();
                $clientUsageGuide->guide_name = $form->getValue('guide_name');
                $clientUsageGuide->guide_content = $form->getValue('guide_content');
                $clientUsageGuide->user_id = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
                $destination_dir = APPLICATION_PATH . '/../assets/files/usage_guides/';                
                try {
                    $adapter = new Zend_File_Transfer_Adapter_Http();                    
                    $adapter->setDestination($destination_dir);
                    $files = $adapter->getFileInfo();
//                    print_r($files);                                        
                    if (($adapter->isUploaded($files['guide_document']['name'])) && ($adapter->isValid($files['guide_document']['name']))) {
                        $extension = substr($files['guide_document']['name'], strrpos($files['guide_document']['name'], '.') + 1);
                        $filename = 'file_' . $this->_helper->BUtilities->getLoggedInUser() . '_' . date('Ymdhs') . '.' . $extension;
                        $adapter->addFilter('Rename', array('target' => $destination_dir . $filename, 'overwrite' => true));
                        $adapter->receive($files['guide_document']['name']);
                        $clientUsageGuide->guide_url = $filename;
                        $clientUsageGuide->guide_type = $extension;
                    }                    
                    $this->em->persist($clientUsageGuide);
                    $this->em->flush();
                    $this->em->clear();
                    $this->view->msg = "Usage guide added successfuly!";
                    $this->_helper->flashMessenger($this->view->msg, "Info");
                    $this->_redirect('/client/artworks/usage-guides');
//                    $form->reset();
                } catch (Exception $ex) {
                    $this->view->msg = $ex->getMessage();
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to view usage guideline
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function viewUsageGuideAction() {
        $this->_helper->BUtilities->setPopupLayout();
        $this->view->usage_guide = $this->em->getRepository('BL\Entity\ClientUsageGuide')->findOneBy(array('id' => $this->_getParam('id'), 'user_id' => $this->_helper->BUtilities->getLoggedInUser()));
    }

    /**
     * Function to edit usage guideline
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editUsageGuideAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $clientUsageGuide = $this->em->getRepository('BL\Entity\ClientUsageGuide')->findOneBy(array('id' => $this->_getParam('id'), 'user_id' => $this->_helper->BUtilities->getLoggedInUser()));
        $form = new Client_Form_UsageGuide('edit');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->ajaxValidate($form, $formData);
            if ($form->isValid($formData)) {
                $clientUsageGuide->guide_name = $form->getValue('guide_name');
                $clientUsageGuide->guide_content = $form->getValue('guide_content');
                $this->em->persist($clientUsageGuide);
                $this->em->flush();
                $this->em->clear();
                $this->view->msg = $form->getValue('guide_name') . " updated successfuly!";
            } else {
                $form->populate($formData);
            }
        } else {
            $existing_data = array(
                'guide_name' => $clientUsageGuide->guide_name,
                'guide_content' => $clientUsageGuide->guide_content
            );
            $form->populate($existing_data);
        }
    }

    /**
     * Function to delete usage guide
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function deleteUsageGuideAction() {
        $this->_helper->BUtilities->setNoLayout();
        if ($this->_request->isXmlHttpRequest()) {
            $Ids = explode(',', $this->_getParam('id'));
            foreach ($Ids as $id) {
                $guide = $this->em->getRepository('BL\Entity\ClientUsageGuide')->findOneBy(array('id' => $id));
                if ($guide) {
                    $targetDir = APPLICATION_PATH . '/../assets/files/usage_guides/';
                    @unlink($targetDir . $guide->guide_url);                    
                    $this->em->remove($guide);
                    $this->em->flush();
                    $this->em->clear();
                } else {
                    throw new Zend_Controller_Action_Exception("Required Parameter Missing or Incorrect", 404);
                }
            }
        }
    }

    /**
     * Function to Validate on the form using AJAX
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxValidate($form, $formData) {
        if ($this->_request->isXmlHttpRequest()) {
            if (!$form->isValid($formData)) {
                $json = $form->processAjax($formData);
                echo $json;
                exit(0);
            } else {
                echo Zend_Json::encode(array());
                exit(0);
            }
        }
    }

    /**
     * Function to download file
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function downloadAction() {
        $file = $this->_getParam('file');
        $ext = explode('.', $file);
        $targetDir = APPLICATION_PATH . '/../assets/files/artworks/';
       
        $path = $targetDir . 'originals/' . $file;
        
        if (!file_exists($path)){
        	$path = $targetDir . $file;
        }
        
        header('Content-Type: image/' . $ext[sizeof($ext) - 1]);
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($path);
        $this->_helper->BUtilities->setNoLayout();
    }

    /**
     * Function to download file
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function downloadUsageGuideAction() {
        $file = $this->_getParam('file');
        $ext = explode('.', $file);
        $targetDir = APPLICATION_PATH . '/../assets/files/usage_guides/';

        //header('Content-Type: image/' . $ext[sizeof($ext) - 1]);
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile($targetDir . $file);
        $this->_helper->BUtilities->setNoLayout();
    }

}

