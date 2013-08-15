<?php

class Admin_Model_Search {

    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
        $this->ct = $ct;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getActions() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'va.assignment_date', '1' => 'va.action', '2' => 'va.resolution');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');


        $records = $this->ct->em->getRepository("BL\Entity\VendorActions")->getActions($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\VendorActions")->getActions($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $v) {
            if (!is_null($v->assignment_date)) {
                $assignment_date = ( (int) $v->assignment_date->format("Y") > 0 ? $v->assignment_date->format("M d, Y H:i A") : 'N/A');
            } else {
                $assignment_date = 'N/A';
            }

            $prec[] = array(
                $assignment_date,
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", $v->client_id->organization_name)), 40),
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", (($v->action != NULL || $v->action != "") ? $v->action : "N/A"))), 80),
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", (($v->resolution != NULL || $v->resolution != "") ? $v->resolution : "N/A"))), 90),
                '<a href="javascript:;" class="vendor_actions_link" rel="' . $v->id . '">View</a>&nbsp; <a href="javascript:;" class="delete_actions" rel="d-' . $v->id . '">Delete</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getClients() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'l.id',
            '1' => 'c.organization_name',
            '2' => 'l.status',
            '3' => 'l.annual_advance',
            '4' => 'l.sharing');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');

        $records = $this->ct->em->getRepository("BL\Entity\License")->getLicenses($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\License")->getLicenses($params);
        $status_array = $this->ct->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $v) {
            $prec[] = array(
                (!is_null($v->license_agree_number) ? $v->license_agree_number : $this->ct->view->BUtils()->getLicenseAgreementNumber(array('prefix' => 'A', 'ym' => $v->applied_date->format('ym'), 'l_id' => $v->id))),
                $v->client_id->organization_name,
                (isset($status_array[$v->status]) ? $status_array[$v->status] : $v->status),
                (!is_null($v->default_renewal_fee) ? $this->ct->view->BUtils()->getCurrency($v->default_renewal_fee) : 'N/A'),
                (!is_null($v->sharing) ? ucfirst($v->sharing) : 'N/A'),
                "<a href='javascript:;' class='clients_link' rel='" . $v->id . "'>View & Edit</a>"
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getCorrespondenceClient(){
    	$params = array(
    			'search' => $this->ct->getRequest()->getParam('sSearch', ''),
    			'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
    			'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
    			'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
    	);
    	/**
    	 * Let's take care of the sorting column to be passed to doctrine.
    	 * DataTable sends params like iSortCol_0.
    	*/
    	$sorting_cols = array('0' => 'cc.note_time', '1' => 'cc.note');
    	$params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
    	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
    	$params['client_id'] = $this->ct->getRequest()->getParam('id');
    	
    	$records = $this->ct->em->getRepository("BL\Entity\ClientCorrespondence")->getCorrespondences($params)->getResult();
    	$params['show_total'] = true;
    	$records_total = $this->ct->em->getRepository("BL\Entity\ClientCorrespondence")->getCorrespondences($params);
    	/**
    	 * Datatable doesn't understand json_encode and have a custom json format
    	*/
    	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
    	$prec = array();
    	foreach ($records as $v) {
    	if (!is_null($v->note_time)) {
                $note_time = ( (int) $v->note_time->format("Y") > 0 ? $v->note_time->format("M d, Y H:i A") : 'N/A');
            } else {
                $note_time = 'N/A';
            }
    		$prec[] = array(
    				$note_time,
    				$this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", strip_tags(html_entity_decode($v->subject)))), 50),
    				$this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), " ", strip_tags(html_entity_decode($v->note)))), 120),
    				"<a href='javascript:;' class='clients_notes_link' rel='" . $v->id . "'>View</a>&nbsp; 
    				<a href='javascript:;' class='edit_notes_link' rel='" . $v->id . "'>Edit</a>&nbsp; 
    				<a href='javascript:;' class='delete_note' rel='d-" . $v->id . "'>Delete</a>"
    		);
    	}
    	$json .= Zend_Json::encode($prec);
    	$json .= '}';
    	return $json;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getCorrespondence() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'vc.note_time', '1' => 'vc.note');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');

        $records = $this->ct->em->getRepository("BL\Entity\VendorCorrespondence")->getCorrespondences($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\VendorCorrespondence")->getCorrespondences($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $first = 0;
        $prec = array();
        foreach ($records as $v) {
            if ((int) $v->note_time->format("Y") > 0) {
                $note_time = $v->note_time->format("M d, Y h:i A");
            } else if ((int) $v->create_date->format("Y") > 0) {
                $note_time = $v->create_date->format("M d, Y h:i A");
            } else if ((int) $v->update_date->format("Y") > 0) {
                $note_time = $v->update_time->format("M d, Y h:i A");
            } else {
                $note_time = 'N/A';
            }
            $prec[] = array(
                $note_time,
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", strip_tags(html_entity_decode($v->subject)))), 50),
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), " ", strip_tags(html_entity_decode($v->note)))), 120),
                "<a href='javascript:;' class='vendor_notes_link' rel='" . $v->id . "'>View</a>&nbsp;&nbsp; <a href='javascript:;' class='vendor_edit_link' rel='" . $v->id . "'>Edit</a>&nbsp;&nbsp; <a href='javascript:;' class='delete_note' rel='d-" . $v->id . "'>Delete</a>"
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getLicenseAgreements() {
        $DatatableRow = array();
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'l.license_agree_number',
            '1' => 'l.sharing',
            '2' => 'c.organization_name',
            '3' => 'l.royalty_description',
            '4' => 'l.grant_of_license',
            '5' => 'l.applied_date',
            '6' => 'l.status');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');

        $records = $this->ct->em->getRepository("BL\Entity\License")->getLicenses($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\License")->getLicenses($params);
        $status_array = $this->ct->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        foreach ($records as $v) {
            $license_agree_number = '<a class="lic_link" href="javascript:;" rel="' . $v->id . '">';
            $license_agree_number .=!is_null($v->license_agree_number) ? $v->license_agree_number : $this->ct->view->BUtils()->getLicenseAgreementNumber(array('prefix' => 'A', 'ym' => $v->applied_date->format('ym'), 'l_id' => $v->id));
            $license_agree_number .= '</a>';
            array_push(
                    $DatatableRow, array(
                $license_agree_number,
                (!is_null($v->sharing) ? ucfirst($v->sharing) : 'N/A'),
                $v->client_id->organization_name,
                (!is_null($v->royalty_description) ? $v->royalty_description : 'N/A'),
                (!is_null($v->grant_of_license) ? $v->grant_of_license : 'N/A'),
                (!is_null($v->applied_date) ? $v->applied_date->format("M d, Y H:i A") : 'N/A'),
                (!is_null($v->status) ?
			'<span class="statusText">'.$status_array[$v->status].'</span>&nbsp;<a href="javascript:;" class="editor small">Edit</a>'
			: 'N/A')
                    )
            );
        }
        $json = array(
            'iTotalRecords' => $records_total,
            'iTotalDisplayRecords' => $records_total,
            'aaData' => $DatatableRow
        );
        return Zend_Json_Encoder::encode($json);
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getDocs() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'vd.update_date', '1' => 'vd.doc_name');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');

        $records = $this->ct->em->getRepository("BL\Entity\VendorDocs")->getDocs($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\VendorDocs")->getDocs($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();

        foreach ($records as $v) {
            if (!is_null($v->update_date)) {
                $update_date = ( (int) $v->update_date->format("Y") > 0 ? $v->update_date->format("M d, Y H:i A") : 'N/A');
            } else {
                $update_date = 'N/A';
            }
            $file_path = $this->ct->view->baseUrl("assets/images/");
            $doc_icon = 'document.png';
            $action_link = '<a href="javascript:;" class="vendor_docs_link" rel="' . $v->id . '">View</a>&nbsp; <a href="javascript:;" class="delete_docs" rel="d-' . $v->id . '">Delete</a>&nbsp;';
            if (($v->doc_url != '') || ($v->doc_url != NULL)) {
                $doc_icon = $v->doc_type . ".png";
                $action_link .= ' <a href="javascript:;" class="download_docs" rel="' . $v->doc_url . '">Download</a>';
            }

            $prec[] = array(
                $update_date,
                '<img src="' . $file_path . $doc_icon . '" class="doc_logo" >',
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), "", (($v->doc_name != NULL || $v->doc_name != "") ? $v->doc_name : 'N/A'))), 120),
                $action_link
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getNotes() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'vn.updated_at', '1' => 'vn.note');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');

        $records = $this->ct->em->getRepository("BL\Entity\VendorNote")->getNotes($params)->getResult();
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\VendorNote")->getNotes($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $v) {
            if (!is_null($v->updated_at)) {
                $updated_at = ( (int) $v->updated_at->format("Y") > 0 ? $v->updated_at->format("M d, Y H:i A") : 'N/A');
            } else {
                $updated_at = 'N/A';
            }
            $prec[] = array(
                $updated_at,
                $this->ct->view->BUtils()->neat_trim(str_replace(chr(13), '', str_replace(chr(10), " ", $v->note)), 120),
                '<a href="javascript:;" class="note_details" rel="' . $v->id . '">View</a>&nbsp;
                 <a href="javascript:;" class="edit_notes" rel="' . $v->id . '">Edit</a>&nbsp;
                 <a href="javascript:;" class="delete_note" rel="d-' . $v->id . '">Delete</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
    }

    /**
     * Function to show Invoice action
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchInvoices() {
        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array('0' => 'inv.amount_due', '1' => 'inv.invoice_number');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');


        $AllRecords = $this->ct->em->getRepository("BL\Entity\Invoice")->getInvoice($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');

        $json = '{"iTotalRecords":' . $AllRecords['total_records'] . ',
         "iTotalDisplayRecords": ' . $AllRecords['total_records'] . ',
         "aaData":';
        $currency = new Zend_Currency('en_US');
        $total_due = 0;
        $prec = array();
        foreach ($AllRecords['records'] as $v) {
            $prec[] = array(
                '<a href="javascript:;" class="invoice_link" rel="' . $v['id'] . '">' . $v['invoice_number'] . '</a>',
                (( (is_null($v['fiscal_year']) || $v['fiscal_year'] == '' ) && $v['quarter'] == 0 ) ? 'N/A' : ((is_null($v['fiscal_year']) || $v['fiscal_year'] == '') ? 'N/A' : $v['fiscal_year']) . ' - ' . (($v['quarter'] == 0) ? 'N/A' : 'Q' . $v['quarter']) ),
                ( (!is_null($v['invoice_date'])) ? date("M d, Y h:i:s", strtotime($v['invoice_date'])) : "N/A" ),
                $v['invoice_type'],
                $v['invoice_status'],
                $v['payment_status'],
                $currency->toCurrency($v['amount_due']),
                $currency->toCurrency($v['amount_paid']),
                '<a href="javascript:;" class="invoices_link" rel="' . $v['id'] . '">View</a>&nbsp; <a href="javascript:;" class="delete_invoices" rel="d-' . $v['id'] . '">Delete</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to update Clients
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editLicClients() {
        $form = new Admin_Form_LicClients();
        $this->ct->view->form = $form;
        $LiscClients = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));
        $this->ct->view->client_name = $LiscClients->client_id->organization_name;
        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $LiscClients->sharing = $form->getValue('sharing');
                $LiscClients->status = $form->getValue('lic_status');
                $LiscClients->default_renewal_fee = $form->getValue('renewal_fee');
                $this->ct->em->persist($LiscClients);
                $this->ct->em->flush();
                $this->ct->view->result = array('success' => true, 'message' => 'Clients updated successfully!');
            }
        } else {
            $existing_data = array(
                'lic_agree_num' => (!is_null($LiscClients->license_agree_number) ? $LiscClients->license_agree_number : $this->ct->view->BUtils()->getLicenseAgreementNumber(array('prefix' => 'A', 'ym' => $LiscClients->applied_date->format('ym'), 'l_id' => $LiscClients->id))),
                'sharing' => (!is_null($LiscClients->sharing) ? ucfirst($LiscClients->sharing) : 'N/A'),
                'lic_status' => $LiscClients->status,
                'renewal_fee' => $LiscClients->default_renewal_fee
            );
            $form->populate($existing_data);
        }
    }

}

