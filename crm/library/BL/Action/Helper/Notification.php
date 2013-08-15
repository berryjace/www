<?php

/**
 * Description of Notification
 *
 * @author Masud
 */
class BL_Action_Helper_Notification extends Zend_Controller_Action_Helper_Abstract {
    
    protected $em;
    
    public function Notification(){
        return $this;
    }
    
    /**
     * Function to send notification
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param array $params
     * @return String
     */    
    public function send_notification($params= array()){
        
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();        

//        $params = array(
//          'title'   => '',
//            'message' => '',
//            'send_via' => 'site_notification',
//            'for_user_type' => 'random',            
//            'created_by' => '',
//            'notification_user' => ''
//        );        
        $notification = new BL\Entity\Notification();
        $notification->title = $params['title'];
        $notification->message = $params['message'];
        $notification->send_via = $params['send_via'];
        $notification->for_user_type = $params['for_user_type'];
        $notification->time = new DateTime(date('Y-m-d H:i:s'));
        $notification->created_by = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' =>  (int) $params['created_by']));
        $this->em->persist($notification);
        $this->em->flush();      
        
        foreach ($params['notification_user'] as $user) {            
            $notificationUser = new BL\Entity\NotificationUser();
            $notificationUser->notification_id = $notification;
            $notificationUser->user_id = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' =>  (int) $user));
            $this->em->persist($notificationUser);
        }        
        $this->em->flush();        
    }
}