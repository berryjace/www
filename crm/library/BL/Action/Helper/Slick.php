<?php

class BL_Action_Helper_Slick extends
Zend_Controller_Action_Helper_Abstract {

    protected $view;

    public function loadAssets() {
        $view = $this->getView();
        $view->headScript()->appendFile($view->baseUrl("assets/js/slick/lib/jquery.event.drag-2.0.min.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/slick/lib/jquery.event.drop-2.0.min.js"));
        $view->headScript()->appendFile($view->baseUrl("assets/js/slick/slick.grid.js"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/slick/slick.grid.css"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/slick/slick.pager.css"));
        $view->headLink()->appendStylesheet($view->baseUrl("assets/css/slick/slick.columnpicker.css"));
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

