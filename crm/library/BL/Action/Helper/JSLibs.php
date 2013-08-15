<?php

class BL_Action_Helper_JSLibs extends
Zend_Controller_Action_Helper_Abstract {

    protected $view;

    public function JSLibs() {
        return $this;
    }

    /**
     * Function to call number of functions for easy loading
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @param $params String [Array]
     * @access public
     * @return String
     */
    public function do_call($params) {
        if (is_array($params)) {
            foreach ($params as $param) {
                call_user_func(array($this, $param));
            }
        } else {            
            call_user_func(array($this, $params));
        }
    }

    public function load_jqTextExt_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/plugins/jquery-TextExt-1.3.0.js"));
    }

    public function load_jqgrid_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/plugins/grid.locale-en.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plugins/jquery.jqGrid.min.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/plugins/ui.jqgrid.css"));
    }

    public function load_jquery_multiselect_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/jquery.multiselect.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/jquery.multiselect.css"));
    }

    public function load_jquery_validation() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/plugins/jquery.validate.min.js"));
    }

    public function load_jquery_fb_token() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/jquery.tokeninput.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/token-input-facebook.css"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/token-input.css"));
    }

    public function load_jqui_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/plugins/jquery-ui-1.8.16.custom.min.js"));
        $view->headLink()->appendStylesheet($view->baseUrl('assets/css/ui_themes/pepper-grinder/jquery-ui-1.8.6.custom.css'));
    }

    public function load_jqui_aristo() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/plugins/jquery-ui-1.8.16.custom.min.js"));
        //$view->headScript()->appendFile($view->baseUrl("assets/js/jquery-ui/jquery-ui-timepicker-addon.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/ui_themes/Aristo/Aristo.css"));
    }
    
    public function load_jqui_timepicker() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/jquery-ui/timepicker/jquery-ui-timepicker-addon-1.0.1.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/jquery-ui/timepicker/jquery-ui-sliderAccess.js"));        
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/ui_themes/timepicker/jquery-ui-timepicker-addon.css"));
    }
    
    public function load_tinymce_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/tiny_mce/tiny_mce.js"));
    }

    public function load_calender_assets() {
        $this->view->headScript()->appendFile($this->view->baseUrl("assets/js/plugins/fullcalendar.min.js"));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl("assets/css/plugins/fullcalendar.css"));
    }

    public function load_dataTable_assets() {
        $view = $this->getView();
        $this->view->headScript()->appendFile($this->view->baseUrl("assets/js/plugins/jquery.dataTables.min.js"));
    }

    public function load_hybrid_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/jquery.colorbox-min.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/colorbox.css"));
    }

    public function load_fancy_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/fancybox/jquery.fancybox-1.3.4.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/jquery.fancybox-1.3.4.css"));
    }

    public function load_autosuggest_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/multicomplete.js"));
    }

    public function load_fileinput_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/jQuery.fileinput.js"));
    }

    public function load_plupload_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/plupload.full.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/plupload.html5.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/plupload.html4.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/plupload.flash.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/plupload.silverlight.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/plupload.gears.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css"));
    }

    public function load_googlemap_assets() {
        $view = $this->getView();
        $view->headScript()->appendFile('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAwz5ST2VlEF9GI_ehSurO-xR-jg49YJ_zgRquJ1rr3fz7TwNTlBRxpHi7TNi0Jp4oPpYd2EXgniWvjg');
        $view->headScript()->appendFile($view->baseUrl("assets/js/jquerymaps.js"));
    }

    public function getView() {
        if (null !== $this->view) {
            return $this->view;
        }
        $controller = $this->getActionController();
        $this->view = $controller->view;
        return $this->view;
    }

}
