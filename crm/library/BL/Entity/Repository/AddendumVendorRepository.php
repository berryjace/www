<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class AddendumVendorRepository extends EntityRepository {


    public function getVendorAddendums($params = array()) {
//        print_r($params);        
//        WHERE  $conditions   $order_clause
        $q = $this->_em->createQuery(
            "SELECT partial a.{id, reason, content, create_date} , 
                   partial av.{id, signator_name, signator_title}                    
            FROM BL\Entity\AddendumVendor av
                   RIGHTJOIN av.addendum_id a                                
                 "
                );
        $results = $q->getSQL(); //getResult();
        return $results;
    }
}