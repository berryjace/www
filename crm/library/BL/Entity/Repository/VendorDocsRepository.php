<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class VendorDocsRepository extends EntityRepository {

    /**
     * Function to Get Docs for Vendors
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getDocs($params) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;
        $vendor_id = isset($params['vendor_id']) ? $params['vendor_id'] : false;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';

        $search_clause = 'WHERE vd.vendor_id=' . $vendor_id;
        $search_clause.=!empty($searchStr) ? " AND vd.doc_name like '%$searchStr%' AND vd.vendor_id= ".$vendor_id : "";

        $order_clause = !empty($sort_by) ? " ORDER BY $sort_by $sort_dir" : "";
        
        $q = $this->_em->createQuery("
            SELECT vd
            FROM BL\Entity\VendorDocs vd
            $search_clause 
            $order_clause
       ");

        if (isset($params['show_total']) AND $params['show_total'] === true) {
            $paginator = new Paginator($q);
            return $paginator->count();
        } else {
            $records = $q->setFirstResult($currentPage)->setMaxResults($itemPerPage); // Step 2
            return $records;
        }
    }

    /**
     * Function to Search vendors by docs
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchVendorByDocs($search_params, $limit_fields=array()) {
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
            "file_name" => "vd.doc_name"
        );

        /*
         * ToDo
         * Parse Date : convert the format mm/dd/yy to Y-d-m
         */
        $search_clause = "WHERE v.account_type=".ACC_TYPE_VENDOR;
        if (isset($search_params['date_from']) AND $search_params['date_from'] <> "") {
            $search_clause.=" AND vd.update_date >= {$search_params['date_from']} AND vd.update_date <= {$search_params['date_to']} ";
        }

        foreach ($field_map as $k => $val) {
            if (isset($search_params[$k]) AND $search_params[$k] <> "") {
                $search_clause.= $params['search_op'] . " " . $val . " like '%" . $search_params[$k] . "%' ";
            }
        }

        $status_conditions = "";
        if (isset($search_params['vendor_status']) && ($search_params['vendor_status'] != 'all' )) {
            $status = explode(',', $search_params['vendor_status']);
            $count = 1;
            $status_conditions = " AND v.user_status IN ( ";
            foreach ($status as $s) {
                $status_conditions .= "'" . $s . "' ";
                if ($count < count($status)) {
                    $status_conditions .= " , ";
                }
                $count++;
            }
            $status_conditions .= " ) GROUP BY v.organization_name ";
        }        
        
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $is_limited_fields = count($limit_fields);
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : "vd";
        $q = $this->_em->createQuery("
            SELECT {$select_fields}
            FROM BL\Entity\VendorDocs vd
            LEFT JOIN vd.vendor_id v
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
        if($is_limited_fields){
            return $records;    // Calling from export
        }
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
        if (!is_null($v->update_date)) {
            $created_at = ( (int) $v->update_date->format("Y") > 0 ? $v->update_date->format("M d, Y H:i A") : 'N/A');
        } else {
            $created_at = 'N/A';
        } 
            $json .= '["<a href=\"javascript:;\" class=\"vendor_link\" rel=\"m:docs,v:' . $v->vendor_id->id . ',c:' . $v->id . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->vendor_id->organization_name)) . '</a>",
              "' . $created_at . '","' . (!is_null($v->vendor_id->user_status) ? $v->vendor_id->user_status : "-") . '"]';
        }
        $json .= ']}';
        return $json;
    }
    
}