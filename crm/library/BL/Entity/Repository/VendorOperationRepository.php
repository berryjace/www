<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of VendorOperationRepository
 *
 * @author Masud
 */
class VendorOperationRepository extends EntityRepository {

    public function getVendorOperations() {
        
    }

    /**
     * Function to Get Vendor Operation Row. FindOneBy can't popuplate form because it returns and Entity. So Array Hydration is needed
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getVendorOperation($vendor_id) {
        $q = $this->_em->createQuery("
            SELECT v
            FROM BL\Entity\VendorOperation v
            WHERE v.id={$vendor_id}
       ");
        return $q->getArrayResult();
    }

    /**
     * Function to Search vendors by operations information
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchVendorByOperations($search_params, $limit_fields=array()) {
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'v.organization_name', '2' => 'v.created_at', '3' => 'v.user_status');
        $search_params['iSortCol_0'] = isset($search_params['iSortCol_0']) ? $search_params['iSortCol_0'] : 0;
        $params = array(
            'search' => isset($search_params['sSearch']) ? $search_params['iDisplayStart'] : '',
            'current_page' => isset($search_params['iDisplayStart']) ? $search_params['iDisplayStart'] : 1,
            'draw_count' => isset($search_params['sEcho']) ? $search_params['sEcho'] : "",
            'per_page' => isset($search_params['iDisplayLength']) ? $search_params['iDisplayLength'] : 10,
            'sort_key' => isset($sorting_cols[$search_params['iSortCol_0']]) ? $sorting_cols[$search_params['iSortCol_0']] : '',
            'search_op' => isset($search_params['search_op']) ? $search_params['search_op'] : 'AND',
            'sort_dir' => isset($search_params['sSortDir_0']) ? $search_params['sSortDir_0'] : 'ASC',
        );

        $field_map = array(
            "vendor_name" => "v.organization_name",
            'insurance_contact' => "vo.insurance_contact",
            'insurance_company' => "vo.insurance_company",
            'vendor_products' => "vo.vendor_products",
            'insurance_phone' => "vo.insurance_phone",
            'vendor_royalty_structure' => "vo.vendor_royalty_structure",
            'vendor_recommendation_to_client' => "vo.vendor_recommendation_to_client",
            'default_note_to_vendor' => "vo.default_note_to_vendor",
            'vendor_type' => "vo.vendor_type",
            'have_late_fee' => "vo.have_late_fee",
            'operation_vendor_status' => "v.user_status"
        );

        /*
         * ToDo
         * Parse Date : convert the format mm/dd/yy to Y-d-m
         */
        $search_clause = "WHERE v.account_type=" . ACC_TYPE_VENDOR;
        if (isset($search_params['date_from']) AND $search_params['date_from'] <> "") {
            $search_clause.=" AND vo.note_time >= {$search_params['date_from']} AND vo.note_time <= {$search_params['date_to']} ";
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
            $status_conditions .=") ";
        }

        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";
        $is_limited_fields = count($limit_fields);
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : "vo";

        $q = $this->_em->createQuery("
            SELECT {$select_fields}
            FROM BL\Entity\VendorOperation vo
            LEFT JOIN vo.user_id v
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
        $AllRecords = array();
        if ($is_limited_fields) {
            foreach ($records as $rec) {
                if (array_search('v.id', $limit_fields)) {
                    $UserArray = array('user_first_name' => '', 'user_last_name' => '', 'user_title' => '', 'user_address' => '', 'user_city' => '', 'user_state' => '', 'user_zip' => '', 'user_phone' => '', 'user_mobile' => '', 'user_fax' => '');
                    $query = $this->_em->createQuery("SELECT 
                                                    uc.first_name AS user_first_name,
                                                    uc.last_name AS user_last_name,
                                                    uc.title AS user_title,
                                                    uc.address_line1 AS user_address,
                                                    uc.city AS user_city,
                                                    uc.state AS user_state,
                                                    uc.zipcode AS user_zip,
                                                    uc.phone AS user_phone,
                                                    uc.mobile AS user_mobile,
                                                    uc.fax AS user_fax
                                                    FROM BL\Entity\UserContact uc
                                                    WHERE uc.user_id = :id");
                    $query->setParameter('id', $rec['id']);
                    $UserContactList = $query->getResult();
                    foreach ($UserContactList as $User) {
                        foreach ($User as $key => $value) {
                            $UserArray[$key] .= (($UserArray[$key] == '') ? $value : '; ' . $value);
                        }
                    }
                    array_push($AllRecords, array_merge($rec, $UserArray));
                } else {
                    array_push($AllRecords, $rec);
                }
            }
            return $AllRecords;    // Calling from export
        }
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }
            $json .= '["<a href=\"javascript:;\" class=\"vendor_link\" rel=\"m:operations,v:' . $v->user_id->id . ',c:' . $v->id . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->user_id->organization_name)) . '</a>",
              "' . (!is_null($v->user_id->created_at) ? $v->user_id->created_at->format("M d, Y H:i A") : "N/A") . '","' . (!is_null($v->user_id->user_status) ? $v->user_id->user_status : "-") . '"]';
        }
        $json .= ']}';
        return $json;
    }

}
