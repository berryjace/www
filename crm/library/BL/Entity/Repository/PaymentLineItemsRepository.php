<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PaymentLineItemsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentLineItemsRepository extends EntityRepository {

    public function getLineItemsByPayment($payment_id) {
	/*
        $q = $this->_em->createQuery("select t from BL\Entity\PaymentLineItems t where t.pmt_id='{$payment_id}'");  //ex- payment_id
	return $q->getResult();
	 *
	 */

	 $query = '
            SELECT t, c,p,i
            FROM BL\Entity\PaymentLineItems t
            LEFT JOIN t.client c
            LEFT JOIN t.pmt_id p
	    LEFT JOIN p.invoice i
	    Where p.id = '.$payment_id.'
            ';

        $query = $this->_em->createQuery($query);
//	\Zend_Debug::dump($query->getArrayResult());	exit();
        return $query->getResult();
    }
    public function getRowsBySaveId($payment_id) {
        $q = $this->_em->createQuery("SELECT t from BL\Entity\PaymentLineItems t where t.pmt_id='{$payment_id}'");
        return $q->getResult();
    }
    public function deletePaymentLineItem($payment_id) {
        $q = $this->_em->createQuery("DELETE BL\Entity\PaymentLineItems t where t.pmt_id='{$payment_id}'");
	$q->execute();
	return true;

    }
    public function getPaymentReportByClient($year) {
        $q = $this->_em->createQuery("SELECT r FROM BL\Entity\PaymentLineItems r where r.payment_year='$year' group by r.client");
        return $q->getResult();
    }
}