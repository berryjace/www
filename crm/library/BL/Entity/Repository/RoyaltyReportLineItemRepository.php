<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of RoyaltyReportLineItemRepository
 *
 * @author Masud
 */
class RoyaltyReportLineItemRepository extends EntityRepository {

    /**
     * Function to get royalty commission
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param Array<$params>
     * @return String
     */
    public function getRoyaltyReportsHistory($params=array()) {
        $q = $this->_em->createQuery("
             SELECT partial l.{id, product_sold_to, product_desc, quantity, unit_price, gross_sales},
                    partial r. {id, year, quarter, status, submission_type, invoice_date}
            FROM BL\Entity\RoyaltyReportLineItem l 
               JOIN l.royalty_id r
             WHERE r.vendor_id = $params[vendor_id] ORDER BY r.year, r.quarter");     
//        echo $q->getSQL();
        
        return $q->getResult();
    }            

}
