<?php

/**
 * Notification Helper Class
 * @author Zea
 * @copyright Blueliner Marketing
 * @version 0.1
 * @access public
 */
class BL_View_Helper_Notification extends Zend_View_Helper_Abstract{

    public function  __construct() {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }
    //public function Notification()
     /**
     * Function to get new  notification
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param
     * @return void
     */
    public function Notification($type=null) {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $user1 = Zend_Auth::getInstance()->getIdentity();
        $date_time = $this->notification_check($user1->id);
        $time = $date_time->format("Y-m-d H:i:s");
//        $time = date('Y-m-d H:i:s', strtotime($date_time->format("Y-m-d H:i:s")));
//        $time = new DateTime($time);
        $name = $this->em->getRepository("BL\Entity\Notification")->facebookStyleNotification($user1->id, $time, $type);
        if(!empty($name)){
            $s_notification = new Zend_Session_Namespace('notification');
            $s_notification->name = $name;
            return 1;
        }
        return 0;
    }

     /**
     * Function to check notification
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param user id
     * @return void
     */
    public function notification_check($id) {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $exist = $this->em->getRepository("BL\Entity\NotificationLastView")->findOneBy(array('user_id' => $id));
        $user_id = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $id));
        if (!isset($exist)){
            $class = 'BL\Entity\NotificationLastView';
            $notification = new $class;
            $time = date('Y-m-d H:i:s', strtotime("11/30/1982"));
            //$user1 = Zend_Auth::getInstance()->getIdentity();
            $notification->user_id = $user_id;
            $notification->time = new DateTime($time);
            $this->em->persist($notification);
            $this->em->flush();
            return $notification->time;
        } else {
            return $exist->time;
        }
    }
}
