<?php

namespace BL\Entity\Repository;

use DoctrineExtensions\Versionable\Exception;

use Doctrine\ORM\EntityRepository;

/**
 * Description of VendorRoyaltyReportSubmissionsRepository
 *
 * @author Mahbub
 */
class VendorRoyaltyReportSubmissionsRepository extends EntityRepository {

    /**
     * Function to get all royalty report by fiscal year
     * @author Masud
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

		$vendor_id = isset($params['vendor_id']) ? $params['vendor_id'] : '';

        $conditions = "WHERE v.organization_name IS NOT NULL AND v.account_type=" . ACC_TYPE_VENDOR;
	$conditions .=" and r.royalty_after_adv is not null";
        $search_clause = !empty($searchStr) ? " AND v.organization_name like '$searchStr%'" : "";

		$conditions .= !empty($vendor_id) ? ' and v.id='.$vendor_id : '';

        $order_clause = !empty($sort_by) ? "ORDER BY $sort_by $sort_dir" : "";
        //$gourp_clause = "GROUP BY r.vendor, r.year, r.quarter ";
        $gourp_clause = "GROUP BY r.submission_hash ";

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
            FROM BL\Entity\VendorRoyaltyReportSubmissions r
            LEFT JOIN r.vendor v
            $conditions
            $search_clause
            $gourp_clause
            $order_clause
       ");

       //echo $q->getSQL();
        if (isset($params['show_total']) AND $params['show_total'] === true) {
            return count($q->getResult());
//            return \DoctrineExtensions\Paginate\Paginate::getTotalQueryResults($q);
        } else {
            $records = $q->setFirstResult($currentPage)->setMaxResults($itemPerPage); // Step 2
            //Zend_Debug::dump($records->getArrayResult());
            return $records;
        }
    }


    public function getRoyaltyReportByVendor($vendorId = '')
    {
    	if (empty($vendorId)) {
    		throw new Exception('VENDOR_NOT_PROVIDED: Please provide the Vendor Id.');
    	}

    	$q = $this->_em->createQuery("
    			SELECT r
    			FROM BL\Entity\VendorRoyaltyReportSubmissions r
    			LEFT JOIN r.vendor v
    			WHERE v.id=$vendorId
			AND r.royalty_after_adv is not null
    			GROUP By r.submission_hash
    			");
    	return $q->getResult();

    }

    /**
     * Function to get Vendors' pending reports
     * @author Mahbub
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
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getReportHistory($vendor_id, $year, $quarter=NULL, $submission_type=NULL, $submission_hash=null) {
        $condition = isset($quarter) ? " AND r.quarter={$quarter}" : "";
        $condition .= isset($submission_type) ? " AND r.submission_type='{$submission_type}'" : "";
	if(!empty($vendor_id)) {
            $q = $this->_em->createQuery("SELECT r FROM BL\Entity\VendorRoyaltyReportSubmissions r WHERE r.vendor={$vendor_id} AND r.year='{$year}' $condition GROUP BY r.submission_type, r.quarter");
	}
	if (!empty($submission_hash)) {
	    $q = $this->_em->createQuery("SELECT r FROM BL\Entity\VendorRoyaltyReportSubmissions r WHERE  r.submission_hash='{$submission_hash}' $condition GROUP BY r.submission_hash");
	}
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
        $q = $this->_em->createQuery("SELECT r.year FROM BL\Entity\VendorRoyaltyReportSubmissions r GROUP BY r.year ORDER BY r.year DESC");
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

        $conditions = "WHERE v.organization_name !='' AND v.account_type=" . ACC_TYPE_VENDOR;
        $search_clause = !empty($searchStr) ? " AND v.organization_name like '$searchStr%'" : "";
        $order_clause = !empty($sort_by) ? "ORDER BY $sort_by $sort_dir" : "";

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
            FROM BL\Entity\VendorRoyaltyReportSubmissions r
            LEFT JOIN r.vendor v
            $conditions
            $search_clause
            $order_clause
       ");

        if (isset($params['show_total']) AND $params['show_total'] === true) {
            return \DoctrineExtensions\Paginate\Paginate::getTotalQueryResults($q);
        } else {
            $records = $q->setFirstResult($currentPage)->setMaxResults($itemPerPage); // Step 2
            return $records;
        }
    }
    public function deleteBySubmissionHash($hash)
    {
    	$q = $this->_em->createQuery("
    			DELETE BL\Entity\VendorRoyaltyReportSubmissions r
    			WHERE r.submission_hash = '{$hash}'
    			");
    	$records = $q->execute();
    	return $records;
    }

}
