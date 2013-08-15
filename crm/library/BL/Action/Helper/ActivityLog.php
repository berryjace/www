<?php

class BL_Action_Helper_ActivityLog extends
Zend_Controller_Action_Helper_Abstract {

    /**
     * Function to Record activity
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @param Array $param Array of activity properties
     * @access public
     * @return Integer Table Row id of the newly inserted activity
     */
    public function recordActivity($param) {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $activity = new \ZB\Entity\Activity();
        $activity->user_id = $em->find("ZB\Entity\User", (int) $param['user_id']);
        $activity->activity_type = $param['type'];
        $activity->activity_of_object = $param['obj'];
        $activity->activity_of_object_id = $param['obj_id'];
        $activity->activity_desc = $param['text'];
        $em->persist($activity);
        $em->flush();
        return $activity->id;
    }

}

