<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PermissionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends EntityRepository {

    /**
     * Function to get Vendor Payments
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getVendorPayments($params) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;
        $vendor_id = isset($params['vendor_id']) ? $params['vendor_id'] : false;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';

        $vendor_condition = (false !== $vendor_id) ? ' v.id=?1' : '';

        $search_clause = "";

        if (!empty($searchStr) AND $vendor_id) {
            $search_clause.=" WHERE v.check_num like '%$searchStr%' AND v.vendor={$vendor_id} ";
        } else if (!empty($searchStr) AND !$vendor_id) {
            $search_clause.=" WHERE v.check_num like '%$searchStr%'";
        }
        $search_clause.= ($search_clause == "" AND false !== $vendor_id) ? 'WHERE v.vendor=' . $vendor_id : '';

        $order_clause = !empty($sort_by) ? " ORDER BY $sort_by $sort_dir" : "";

        /**
         * We have to build the query here
         */
        $q = $this->_em->createQuery("
            SELECT v
            FROM BL\Entity\Payment v
            $search_clause
            $order_clause
       ");
//        $q->setResultCacheDriver(new \Doctrine\Common\Cache\ApcCache());
//        $q->useResultCache(true);

        if ($search_clause == "" AND false !== $vendor_id) {
            $q->setParameter(1, $vendor_id);
        }
        if (isset($params['show_total']) AND $params['show_total'] === true) {
            $paginator = new Paginator($q);
            return $paginator->count();
        } else {
            $records = $q->setFirstResult($currentPage)->setMaxResults($itemPerPage); // Step 2
            return $records;
        }
    }

    /**
     * Function to search vendors by payments information
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchVendorByPayments($search_params, $limit_fields=array()) {
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'v.organization_name', '2' => 'l.applied_date', '3' => 'l.status');

        $search_params['iSortCol_0'] = isset($search_params['iSortCol_0']) ? $search_params['iSortCol_0'] : 0;
        $params = array(
            'search' => isset($search_params['sSearch']) ? $search_params['sSearch'] : '',
            'current_page' => isset($search_params['iDisplayStart']) ? $search_params['iDisplayStart'] : 1,
            'draw_count' => isset($search_params['sEcho']) ? $search_params['sEcho'] : "",
            'per_page' => isset($search_params['iDisplayLength']) ? $search_params['iDisplayLength'] : 10,
            'sort_key' => isset($sorting_cols[$search_params['iSortCol_0']]) ? $sorting_cols[$search_params['iSortCol_0']] : '',
            'search_op' => isset($search_params['search_op']) ? $search_params['search_op'] : 'AND',
            'sort_dir' => isset($search_params['sSortDir_0']) ? $search_params['sSortDir_0'] : 'ASC',
        );

        $field_map = array(
            "vendor_name" => "v.organization_name",
            "payment_id" => "p.kp_payment",
            "payment_amount" => "p.amount_paid",
            "payment_qtr" => "p.payment_quarter",
            "payment_year" => "p.payment_year",
            "reference" => "p.check_num"
        );

        /*
         * ToDo
         * Parse Date : convert the format mm/dd/yy to Y-d-m
         */
        $search_clause = "WHERE v.account_type=" . ACC_TYPE_VENDOR;
        if (isset($search_params['date_from']) AND $search_params['date_from'] <> "") {
            $search_clause.=" AND p.record_date >= {$search_params['date_from']} AND p.record_date <= {$search_params['date_to']} ";
        }

        foreach ($field_map as $k => $val) {
            if (isset($search_params[$k]) AND $search_params[$k] <> "") {
                $search_clause.= $params['search_op'] . " " . $val . " like '%" . $search_params[$k] . "%' ";
            }
        }

//        $search_clause.= "GROUP BY v.organization_name ";
        $status_conditions = "";
        if (isset($search_params['vendor_status']) && ($search_params['vendor_status'] != 'all' )) {
            $status = explode(',', $search_params['vendor_status']);
            $status_conditions .= " AND (";
            $count = 1;
            foreach ($status as $s) {
                $status_conditions .= " v.user_status='" . $s . "'";
                if ($count < count($status)) {
                    $status_conditions .= " OR";
                }
                $count++;
            }
            $status_conditions .=") ";
        }
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $is_limited_fields = count($limit_fields);
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : "p";
        $q = $this->_em->createQuery("
            SELECT {$select_fields}
            FROM BL\Entity\Payment p
            LEFT JOIN p.vendor v
            $search_clause
            $status_conditions
            $order_clause
       ");

//        $q->setResultCacheDriver(new \Doctrine\Common\Cache\ApcCache());
//        $q->useResultCache(true);

        $paginator = new Paginator($q);
        $records_total = $paginator->count();
        $records = $q->setFirstResult($params['current_page'])->setMaxResults($params['per_page'])->getResult();


        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
        if ($is_limited_fields) {
            return $records;    // Calling from export
        }
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
            $json .= '["<a href=\"javascript:;\" class=\"vendor_link\" rel=\"m:payments,v:' . $v->vendor->id . ',c:' . $v->id . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->vendor->organization_name)) . '</a>",
              "' . (($v->record_date->format("Y") > 1970) ? $v->record_date->format("M d, Y H:i A") : "N/A") . '","' . (!is_null($v->vendor->user_status) ? $v->vendor->user_status : "-") . '"]';
        }
        $json .= ']}';
        return $json;
    }

    /**
     * Function to get Client Payments by Quarter and year
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getClientPaymentReports($params) {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping;
        $rsm->addScalarResult('organization_name', 'organization_name');
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('check_num', 'check_num');
        $rsm->addScalarResult('total', 'total');
        $rsm->addScalarResult('amount_remaining', 'amount_remaining');
        $rsm->addScalarResult('AMC', 'AMC');
        $rsm->addScalarResult('sharing', 'sharing');

	$condition = '';
	if(isset($params['quarter'])) {
	    $condition = ' and p.payment_quarter=' . $params['quarter'] ;
	}



        $query = '
            SELECT u.organization_name,date_format(p.record_date,"%m/%d/%Y") as `date`,p.check_num,p.amount_paid as total, p.amount_remaining, pl.percent_amc*p.amount_remaining as AMC, pl.sharing, pl.percent_amc
            FROM payments p
            LEFT join payment_lineitems as pl on p.kp_payment=pl.payment_id
            LEFT join users as u on p.vendor_id=u.id
            where pl.client_id=' . $params['id'] . '
            AND p.payment_year="' . $params['fiscal_year'] . '"
	    '.$condition.'
            ORDER BY u.organization_name
            ';
        $query = $this->_em->createNativeQuery($query, $rsm);
        return $query->getResult();
    }
    public function getPaymentYear() {
        $q = $this->_em->createQuery("SELECT r FROM BL\Entity\Payment r group by r.payment_year ORDER BY r.payment_year desc");
        return $q->getResult();
    }
}
