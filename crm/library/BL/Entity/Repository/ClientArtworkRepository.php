<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ClientArtworkRepository extends EntityRepository {

    /**
     * Function to get artwork
     * @author Sukhon
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function getArtworks($params) {
        $params['pg'] = @$params['pg'] > 0 ? $params['pg'] : 1;
        $params['itemPerPage'] = @$params['itemPerPage'] > 0 ? $params['itemPerPage'] : 12;
        $q = $this->_em->createQuery('SELECT ca FROM BL\Entity\ClientArtwork ca ' . 
                "WHERE ca.User = '$params[User]' " . ' ORDER BY ca.upload_date DESC');
        /*$q = $this->_em->createQuery("
            SELECT partial ca.{id,title,file_url,file_extension} , 
                   partial c.{id,organization_name, username}
            FROM BL\Entity\ClientArtwork ca
                   JOIN ca.User c 
            ".(@$params['User'] > 0 ? "WHERE ca.User = '$params[User]' " : "")." 
           ");*/
        
        //echo $q->getSQL();        
        $doctrine_paginator = new Paginator($q);
        $doctrine_paginator_iterator = $doctrine_paginator->getIterator(); // returns \ArrayIterator object
        $adapter = new \Zend_Paginator_Adapter_Iterator($doctrine_paginator_iterator);
        $records = new \Zend_Paginator($adapter);
        $records->setCurrentPageNumber($params['pg'])
                ->setItemCountPerPage($params['itemPerPage'])
                ->setPageRange(isset ($params['num_of_link']) ? $params['num_of_link'] : 10);
        return $records;
    }
}