<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of CheckPaymentRepository
 *
 * @author Masud
 */
class CheckPaymentRepository extends EntityRepository {

    /**
     * Function to get check payments
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getCheckPayments() {
        $q = $this->_em->createQuery("SELECT cp FROM BL\Entity\CheckPayment cp");
        return $q->getResult();
    }

}
