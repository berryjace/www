<?php

namespace BL\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ReportRepository
 *
 * @author zea
 */
class NotificationLastViewRepository extends EntityRepository {

   /* public function getVendorWithReport($data) {
        $q = $this->_em->createQuery("SELECT distinct(u), u FROM BL\Entity\Report r, BL\Entity\User u where r.vendor = u.id");
        return $q->getArrayResult();
    }
    public function getReportBy($data) {
        $q = $this->_em->createQuery("SELECT rc,r,u FROM BL\Entity\ReportContent rc LEFT JOIN rc.report r LEFT JOIN rc.user u where r.vendor = '".$data['vendor_id']."'"
                                .(!$data['status'] ? ' and r.status is null' : " and r.status = '".$data['status']."'")
                                .($data['year'] ? " and r.year='".$data['year']."'" : ""));
        return $q->getResult();
    }*/
     /**
     * Function to enter new user in the table
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param $id as user_id
     * @return void
     */
    public function insertNewAccount($id) {
        $time = date("Y-m-d h:i", strtotime("1999-01-07"));
        $q = $this->_em->createQuery("INSERT into BL\Entity\NotificationLastView (time, user_id) values('$time','$id')");
        $q->getResult();
    }
}
?>
