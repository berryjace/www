<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class InvoiceRepository extends EntityRepository {

    /**
     * Function to Get Invoice for Vendor
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getInvoice($params) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;
        $vendor_id = isset($params['vendor_id']) ? $params['vendor_id'] : false;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';

        $search_clause = !empty($searchStr) ? " WHERE inv.id like '%$searchStr%' AND inv.vendor_id={$vendor_id} " : "";
        $search_clause.= ($search_clause == "" AND false !== $vendor_id) ? 'WHERE inv.vendor_id=' . $vendor_id : '';
        $search_clause.= ' GROUP BY inv.id ';

        $order_clause = !empty($sort_by) ? " ORDER BY $sort_by $sort_dir" : 'inv.id desc';
        $limit = " LIMIT " . $currentPage . ", " . $itemPerPage;

	//echo $order_clause;

        /**
         * We have to build the query here
         */
        $sql = "SELECT
                inv.id,
                inv.invoice_number,
                inv.fiscal_year,
                inv.quarter,
                inv.invoice_date,
                inv.invoice_type,
                inv.invoice_status,
                inv.payment_status,
                inv.amount_due,
                SUM(li.amount_paid) AS amount_paid
                FROM invoice AS inv
                LEFT JOIN invoice_lineitems AS li
                ON li.invoice_id = inv.id " . $search_clause . $order_clause . $limit; //ON li.invoice_number_li = inv.invoice_number " . $search_clause . $order_clause . $limit;

        $count_sql = "SELECT
                inv.id
                FROM invoice AS inv
                LEFT JOIN invoice_lineitems AS li
                ON (li.invoice_id = inv.id) " . $search_clause . $order_clause;  //ON (li.invoice_number_li = inv.invoice_number) " . $search_clause . $order_clause;
//        echo $sql;
//        die('-----------------------');

        $con = $this->_em->getConnection();
        $st = $con->executeQuery($sql);
        $result = $st->fetchAll();

        $st = $con->executeQuery($count_sql);
        $total_row = $st->fetchAll();

//        $paginator = \Zend_Paginator::factory($result);
//        $records_total = $paginator->count();
//        $paginator->setItemCountPerPage($itemPerPage);
//        $paginator->setCurrentPageNumber($currentPage);

        return array('records' => $result, 'total_records' => count($total_row));
    }

    /**
     * Function to search vendor by invoice information
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchVendorByInvoice($search_params, $limit_fields=array()) {
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'v.organization_name', '2' => 'v.applied_date', '3' => 'v.status');

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
            "invoice_type" => "i.invoice_type",
            "invoice_term" => "i.invoice_term",
            "address" => "i.address",
            "city" => "i.city",
            "state" => "i.state",
            "zip" => "i.zip",
            "invoice_number" => "i.invoice_number",
            "invoice_date" => "i.invoice_date",
            "email" => "i.email",
            "phone_1" => "i.phone1",
            "phone_2" => "i.phone2",
            "fax" => "i.fax"
        );

        /*
         * ToDo
         * Parse Date : convert the format mm/dd/yy to Y-d-m
         */
        $search_clause = "WHERE v.account_type=" . ACC_TYPE_VENDOR;
        if (isset($search_params['date_from']) AND $search_params['date_from'] <> "") {
            $search_clause.=" AND i.created_at >= {$search_params['date_from']} AND i.created_at <= {$search_params['date_to']} ";
        }

        foreach ($field_map as $k => $val) {
            if (isset($search_params[$k]) AND $search_params[$k] <> "") {
                $search_clause.= $params['search_op'] . " " . $val . " like '%" . $search_params[$k] . "%' ";
            }
        }

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
            $status_conditions .=") GROUP BY v.organization_name ";
        }
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $is_limited_fields = count($limit_fields);
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : "i";
        $q = $this->_em->createQuery("
            SELECT {$select_fields}
            FROM BL\Entity\Invoice i
            LEFT JOIN i.vendor_id v
            $search_clause
            $status_conditions
            $order_clause
       ");

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

            if (!is_null($v->created_at)) {
                $created_at = ( (int) $v->created_at->format("Y") > 0 ? $v->created_at->format("M d, Y H:i A") : 'N/A');
            } else {
                $created_at = 'N/A';
            }


            $json .= '["<a href=\"javascript:;\" class=\"vendor_link\" rel=\"m:invoices,v:' . $v->vendor_id->id . ',c:' . $v->id . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->vendor_id->organization_name)) . '</a>",
              "' . $created_at . '","' . (!is_null($v->vendor_id->user_status) ? $v->vendor_id->user_status : "-") . '"]';
        }
        $json .= ']}';
        return $json;
    }

    /**
     * Function to search vendor by invoice information
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String (JSON)
     */
    public function searchVendorInvoiceByParams($search_params) {
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'v.user_status', '2' => 'i.invoice_number', '3' => 'i.invoice_date', '4'=>'i.amount_due', '5'=>'i.amount_paid');

        $params = array(
            'search' => isset($search_params['search']) ? $search_params['search'] : '',
            'current_page' => isset($search_params['page_start']) ? $search_params['page_start'] : 1,
            'draw_count' => isset($search_params['draw_count']) ? $search_params['draw_count'] : "",
            'per_page' => isset($search_params['per_page']) ? $search_params['per_page'] : 10,
            'fiscal_year' => isset($search_params['fiscal_year']) ? $search_params['fiscal_year'] : '',
            'quarter' => isset($search_params['quarter']) ? $search_params['quarter'] : '',
            'invoice_status' => isset($search_params['invoice_status']) ? $search_params['invoice_status'] : '',
            'payment_status' => isset($search_params['payment_status']) ? $search_params['payment_status'] : '',
            'invoice_type' => isset($search_params['invoice_type']) ? $search_params['invoice_type'] : '',
            'vendor_status' => isset($search_params['vendor_status']) ? $search_params['vendor_status'] : '',
            'sort_key' => isset($sorting_cols[$search_params['sort_key']]) ? $sorting_cols[$search_params['sort_key']] : $sorting_cols[0],
            'search_op' => isset($search_params['search_op']) ? $search_params['search_op'] : 'AND',
            'sort_dir' => isset($search_params['sort_dir']) ? $search_params['sort_dir'] : 'ASC',
        );
        
            $search_clause = "WHERE v.account_type= " . ACC_TYPE_VENDOR;
        
        $search_clause.= ($params['fiscal_year'] <> "" && $params['fiscal_year'] !="All") ? " AND i.fiscal_year='" . $params['fiscal_year'] . "' " : '';
        $search_clause.= ($params['quarter'] <> "") ? " AND i.quarter='" . $params['quarter'] . "' " : '';
        $search_clause.= ($params['invoice_type'] <> "" && $params['invoice_type'] !="All") ? " AND i.invoice_type='" . $params['invoice_type'] . "'" : '';
        $search_clause.= ($params['invoice_status'] <> "" && $params['invoice_status'] !="All") ? " AND i.invoice_status='" . $params['invoice_status'] . "' " : '';
        $search_clause.= ($params['payment_status'] <> "" && $params['payment_status'] !="All") ? " AND i.payment_status='" . $params['payment_status'] . "' " : '';
        $search_clause.= ($params['vendor_status'] <> "" && $params['vendor_status'] !="All") ? " AND v.user_status='" . $params['vendor_status'] . "' " : '';
        
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";
        //GROUP BY v.id
        $q = $this->_em->createQuery("
            SELECT partial i.{id, invoice_date, invoice_number,invoice_status, invoice_type, amount_due, amount_paid, payment_status}, partial v.{id, organization_name, user_status}
            FROM BL\Entity\Invoice i
            LEFT JOIN i.vendor_id v
            $search_clause
            $order_clause
       ");

//        echo $q->getSQL();
//        die('-----');

        if (isset($search_params['show_total']) AND $search_params['show_total'] === true) {
            $paginator = new Paginator($q);
            return $paginator->count();
        } else {
            $records = $q->setFirstResult($params['current_page'])->setMaxResults($params['per_page']);
            return $records;
        }
    }

    /**
     * Function to get invoices for data migration
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getInvoicesDt($offset, $limit) {
        /*$sql = "
            SELECT DISTINCT (vendor_id)
            FROM invoice
            LIMIT $offset, $limit
            ";
        */
        $sql = "
            SELECT id, vendor_id
            FROM invoice
            ORDER BY id ASC
            LIMIT $offset, $limit
            ";
        $con = $this->_em->getConnection();
        $st = $con->executeQuery($sql);
        $result = $st->fetchAll();
        return $result;
    }

}
