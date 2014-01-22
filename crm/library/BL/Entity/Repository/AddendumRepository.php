<?php
namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AddendumRepository extends EntityRepository {


    function getAddendums($params = array()) {               
        $page = $params['page'] > 0 ? $params['page'] : 1;
        $per_page = $params['per_page'] > 0 ? $params['per_page'] : 4;
        $condition = isset ($params['is_draft']) ? 'WHERE ad.is_draft = '.$params['is_draft'] : '';                
        $q = $this->_em->createQuery("SELECT ad FROM BL\Entity\Addendum ad $condition ORDER BY ad.create_date DESC");        
        //$q = $this->_em->getRepository('BL\Entity\Addendum')->findBy(array(), array('id'=>'DESC'));
        $doctrine_paginator = new Paginator($q);
        $doctrine_paginator_iterator = $doctrine_paginator->getIterator(); // returns \ArrayIterator object
        $adapter = new \Zend_Paginator_Adapter_Iterator($doctrine_paginator_iterator);
        $records = new \Zend_Paginator($adapter);
        $records->setCurrentPageNumber($page)
                ->setItemCountPerPage($per_page)
                ->setPageRange($params['num_of_link']);
        return $records;                        
    }
    
    /**
     * Function to get vendor addendums
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function getVendorAddendums($params = array()) {
        
        $SQL = "SELECT *
                FROM addendums
                LEFT JOIN addendums_vendors ON ( addendums.id = addendums_vendors.addendum_id )";
        
        
        $qb = $this->_em->createQueryBuilder()                       
                ->select('*')
                ->from('addendums', 'a')                
                ->leftJoin('a', 'addendums_vendors', 'av', 'a.id = av.addendum_id');
//                ->where("a.is_draft = $params[is_draft]");
        
        
        echo $qb->getDQL();
//        $conn   = $this->_em->getConnection();  
//        print_r($conn->fetchAll($q));
//                ->rightJoin('a', 'addendums_vendors', 'p');                       
        
        
        
//        $doctrine_paginator = new Paginator($q);
//        $doctrine_paginator_iterator = $doctrine_paginator->getIterator(); // returns \ArrayIterator object
//        $adapter = new \Zend_Paginator_Adapter_Iterator($doctrine_paginator_iterator);        
//        $records = new \Zend_Paginator($adapter);
//        $records->setCurrentPageNumber($params['page'])
//                ->setItemCountPerPage($params['per_page'])
//                ->setPageRange($params['num_of_link']);
//        return $records;                   
//  
//        $paginator = \Zend_Paginator::factory($qb->getQuery());
//        $paginator->setItemCountPerPage($params['per_page']);
//        $paginator->setCurrentPageNumber($params['page']);
//        $paginator->setPageRange($params['num_of_link']);
//        return $paginator;
//        print_r($q->getQuery());
    }
    
    
}