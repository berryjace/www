<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of VendorRoyaltyReportSaveRepository
 *
 * @author Mahbub
 */
class VendorRoyaltyReportSaveRepository extends EntityRepository {

    /**
     * Function to get all royalty report by fiscal year
     * @author Bal
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getRoyaltyReports($params=array()) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';

        $conditions = "WHERE v.organization_name IS NOT NULL AND v.account_type=" . ACC_TYPE_VENDOR;
        $search_clause = !empty($searchStr) ? " AND v.organization_name like '$searchStr%'" : "";
        $order_clause = !empty($sort_by) ? "ORDER BY $sort_by $sort_dir" : "";
        $gourp_clause = "GROUP BY r.vendor, r.year, r.quarter ";

        if (isset($params['report_status']) && ($params['report_status'] != '')) {
            $status = explode(',', $params['report_status']);
            $conditions .= " AND ( ";
            $count = 1;
            foreach ($status as $s) {
                $conditions .= " r.status like '%" . $s . "%'";
                if ($count < count($status)) {
                    $conditions .= " OR";
                }
                $count++;
            }
            $conditions .=") ";
        }

        if (isset($params['year_status']) && ($params['year_status'] != '')) {
            $status = explode(',', $params['year_status']);
            $conditions .= " AND ( ";
            $count = 1;
            foreach ($status as $s) {
                $conditions .= " r.year like '%" . $s . "%'";
                if ($count < count($status)) {
                    $conditions .= " OR";
                }
                $count++;
            }
            $conditions .=") ";
        }

        if (isset($params['quarter_status']) && ($params['quarter_status'] != 0)) {
            $status = $params['quarter_status'];
            $conditions .= " AND ";
            $conditions .= " r.quarter = " . $status;
            $conditions .=" ";
        }

        /**
         * We have to build the query here
         */
        $q = $this->_em->createQuery("
            SELECT r
            FROM BL\Entity\VendorRoyaltyReportSave r
            LEFT JOIN r.vendor v
            $conditions
            $search_clause
            $gourp_clause
            $order_clause
       ");

//        echo $q->getSQL();
        if (isset($params['show_total']) AND $params['show_total'] === true) {
            return count($q->getResult());
//            return \DoctrineExtensions\Paginate\Paginate::getTotalQueryResults($q);
        } else {
            $records = $q->setFirstResult($currentPage)->setMaxResults($itemPerPage); // Step 2
            return $records;
        }
    }

    /**
     * Function to get Vendors' pending reports
     * @author Bal
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getPendingReports() {
        $q = $this->_em->createQuery("SELECT r from ");
        return $q->getResult();
    }

    /**
     * Function to return an array of array of report history by vendor name and by year
     * @author Bal
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getReportHistory($vendor_id, $year, $quarter=NULL, $submission_type=NULL) {
        $condition = isset($quarter) ? " AND r.quarter={$quarter}" : "";
        $condition .= isset($submission_type) ? " AND r.submission_type='{$submission_type}'" : "";
        $q = $this->_em->createQuery("SELECT r FROM BL\Entity\VendorRoyaltyReportSave r WHERE r.vendor={$vendor_id} AND r.year='{$year}' $condition GROUP BY r.submission_type, r.quarter");
        return $q->getResult();
    }

    /**
     * Function to return an array Distinct Fiscal Year
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getDistinctFiscalYear() {
        $q = $this->_em->createQuery("SELECT r.year FROM BL\Entity\VendorRoyaltyReportSave r GROUP BY r.year ORDER BY r.year DESC");
        return $q->getResult();
    }

    /**
     * Function to get ALl Royalty Reports with status to show in the admin dashboard
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getAllRoyaltyReports($params=array()) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';

        $order_clause = !empty($sort_by) ? "ORDER BY $sort_by $sort_dir" : "";




        /**
         * We have to build the query here
         */
        $q = $this->_em->createQuery("
            SELECT distinct r.save_id, r.year, r.quarter, r.uploaded_on
            FROM BL\Entity\VendorRoyaltyReportSave r
            WHERE r.vendor={$params['vendor_id']}
            $order_clause
       ");



	//\Zend_Debug::dump($q->getArrayResult());


        if (isset($params['show_total']) AND $params['show_total'] === true) {
            return \DoctrineExtensions\Paginate\Paginate::getTotalQueryResults($q);
        } else {
            $records = $q->setFirstResult($currentPage)->setMaxResults($itemPerPage); // Step 2

	    //\Zend_Debug::dump($records->getResult());

	    $recs = $records->getArrayResult();
	    $records_total = count($recs);

	    $json = '{"iTotalRecords":' . $records_total . ',
	    "iTotalDisplayRecords": ' . $records_total . ',
	    "aaData":[';


	    foreach($recs as $rec) {
		$uploaded_on = $rec['uploaded_on'];
		$json .= '["<a class=\"clink\" href=\"#\" rel=\"'.$rec['save_id'].'\" >'.$uploaded_on->format('m-d-Y h:i:s').'</a>","'.$rec['year'].'","'.$rec['quarter'].'"],';
	    }


	    $json  = trim($json, ','). ']}';
	    return $json;

        }
    }

    public function getRowsBySaveId($saveId) {
        $q = $this->_em->createQuery("SELECT r from BL\Entity\VendorRoyaltyReportSave r where r.save_id='{$saveId}' ORDER BY r.client asc");
        return $q->getResult();
    }


    public function getRowsByVendorId($vendorId) {
        $q = $this->_em->createQuery("SELECT distinct r.save_id from BL\Entity\VendorRoyaltyReportSave r where r.vendor='{$vendorId}'");
        return $q->getResult();
    }

    public function deleteBySaveId($saveId) {
        $reportRows = $this->getRowsBySaveId($saveId);

	for($i=0; $i<count($reportRows); $i++) {
	    $this->_em->remove($reportRows[$i]);
	}
        $this->_em->flush();
	return true;
    }
}