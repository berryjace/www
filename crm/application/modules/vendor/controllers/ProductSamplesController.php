<?php

class Vendor_ProductSamplesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->view->action = $this->getRequest()->getParam('action');
        $this->view->controller = $this->getRequest()->getParam('controller');
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
    }

    /**
     * Function to List product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function indexAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets', 'load_plupload_assets'));
        $vendor = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
        $this->view->vendor = $vendor;
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->BUtilities->setNoLayout();
            $formData = $this->getRequest()->getPost();
            $uploaded_pic = $this->_getParam('pics');
            //print_r($formData);
            if ($uploaded_pic) {
                $class = 'BL\Entity\VendorSampleFile';
                $sample = new $class();
                $sample->title = substr($uploaded_pic, -15, 15);
                $sample->file_url = $uploaded_pic;
                $sample->file_extension = end(explode('.', $uploaded_pic));
                $sample->Vendor = $vendor;
                //$sample->upload_date = '';
                $this->em->persist($sample);
                $this->em->flush();
                $this->em->clear();
                //$this->_redirect('admin/vendors/sample/confirmation');

                echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully uploaded'));
            } else {
                echo Zend_Json_Encoder::encode(array('success' => false, 'message' => 'There is no file uploaded'));
            }
        }
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxGetVendorSamplesDtAction() {
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'vendor_id' => $this->_helper->BUtilities->getLoggedInUser(),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 's.upload_date',
            '1' => 's.title'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');

        $records = $this->em->getRepository("BL\Entity\VendorSampleFile")->getSamples($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->em->getRepository("BL\Entity\VendorSampleFile")->getSamples($params);
        //echo($records_total.'===');
        $this->_helper->BUtilities->setNoLayout();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
            //$this->view->BUtils()->doctrine_dump($v->upload_date,1);
            $json .= '["' . $v->upload_date->format('m/d/Y h:s A') . '",
              "<a href=\"' . $this->view->baseUrl("assets/files/samples/") . $v->file_url . '\" class=\"vendor_sample_link\" target=\"_blank\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->title)) . '</a>",
              "<a href=\"javascript:;\" class=\"remove\" rel=\"' . $v->id . '\"><img src=\"' . $this->view->baseUrl("assets/images/") . 'delete.png\" /></a>"]';
        }
        $json .= ']}';
        echo $json;
    }

    /**
     * Function to delete product Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function ajaxDelProductSamplesAction() {
        $sample = $this->em->getRepository("BL\Entity\VendorSampleFile")->findOneBy(array('id'=>$this->_getParam('id', ''), 'Vendor'=>$this->_helper->BUtilities->getLoggedInUser()));
        if(sizeof($sample)) {
            $targetDir = APPLICATION_PATH . '/../assets/files/samples/';
            @unlink($targetDir . $sample->file_url);
            $this->em->remove($sample);
            $this->em->flush();
            $this->em->clear();
            echo Zend_Json_Encoder::encode(array('success' => true, 'message' => 'Successfully deleted'));
        } else {
            echo Zend_Json_Encoder::encode(array('success' => false, 'message' => 'Invalid sample'));
        }
        $this->_helper->BUtilities->setNoLayout();
    }
    
    /**
     * Function to Upload multiple Sample files
     * @author Sukhon
     * @copyright Blueliner Marketing
     */
    public function uploadFilesAction() {
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

        $targetDir = APPLICATION_PATH . '/../assets/files/samples/';

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
            @mkdir($targetDir, 0755);

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
                    /**
                      include("ThirdParty/PhpThumb/ThumbLib.inc.php");
                      $thumb_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb" . $fileName;
                      $thumb2_save_path = $targetDir . DIRECTORY_SEPARATOR . "thumbs" . DIRECTORY_SEPARATOR . "_thumb2" . $fileName;
                      $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                      $thumb->adaptiveResize(110, 75);
                      $thumb->save($thumb_save_path);

                      $thumb = PhpThumbFactory::create($targetDir . DIRECTORY_SEPARATOR . $fileName);
                      $thumb->adaptiveResize(450, 300);
                      $thumb->save($thumb2_save_path);
                     */
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

