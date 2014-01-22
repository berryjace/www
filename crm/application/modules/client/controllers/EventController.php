<?php

/**
 * Description of EventController
 *
 * @author Masud
 */
class Client_EventController extends Zend_Controller_Action {

    /**
     * Function to initialization 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        $this->_helper->JSLibs->load_jqui_assets();
        $this->_helper->JSLibs->load_dataTable_assets();
        $this->_helper->JSLibs->load_fancy_assets();
    }

    /**
     * Function to ajax get client events list
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetEventsDtAction() {
        $this->_helper->BUtilities->setNoLayout();
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'id' => $this->_helper->BUtilities->getLoggedInUser()
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'e.start_time',
            '1' => 'e.title',
            '2' => 'e.location'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
        $params['vendor_id'] = $this->_helper->BUtilities->getLoggedInUser();
        $params['page'] = $this->_getParam('page', 'upcoming');
        $json = $this->em->getRepository("BL\Entity\Event")->getClientEvents($params);
        echo $json;
    }

    /**
     * Function to show particular Event
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param event id
     * @return void
     */
    public function eventDetailsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->view->events = $this->em->getRepository("BL\Entity\Event")->findOneBy(array('id' => $this->_getParam('id')));
    }

    /**
     * Function to get vendor events for sidebar calender
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <string> $page
     * @return <array>
     */
    public function ajaxGetCalendarAction() {
        $this->_helper->BUtilities->setAjaxLayout();
        $params['id'] = $this->_helper->BUtilities->getLoggedInUser();
        $params['user_type_1'] = "all";
        $params['user_type_2'] = "all_client";
        $params['user_type_3'] = "random";
        $params['user_type_4'] = "c_vrandom";
        $params['user_type_5'] = "v_crandom";
        $params['page'] = $this->_getParam('page', 1);
        $params['per_page'] = 5;
        $params['num_of_link'] = 5;
        $this->view->events = $this->em->getRepository('BL\Entity\Event')->getCalendarEvents($params);
    }

}

?>
