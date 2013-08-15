<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * Author: Zea
 */
class EventUserRepository extends EntityRepository {

    
  /*  public function get_current_roles($user_id) {
        $q = $this->_em->createQuery("SELECT u, ur FROM BL\Entity\User u LEFT JOIN u.roles ur where u.id='" . $user_id . "'");
        return $q->getArrayResult();
    }*/

   /**
     * Function to get ALl Events user of clients
     * @author Zea
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */

    public function getEventUsersClientVendor($eventId){
        $q = $this->_em->createQuery("SELECT eu.id,eu.user_id,
                                             u.id,u.organization_name,u.account_type
                                     FROM BL\Entity\EventUser eu,
                                     BL\Entity\User u WHERE eu.user_id=u.id AND eu.event_id = $eventId");
        return $q->getResult();
    }
    


}