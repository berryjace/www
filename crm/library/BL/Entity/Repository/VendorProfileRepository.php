<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * VendorProfile
 *
 * @author Rashed
 */
class VendorProfileRepository extends EntityRepository {

    /**
     * Function to get vendor web profile
     * @author Rasidul
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param array $params
     * @return events
     */
    public function getWebProfiles($params) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';


        $search_clause = !empty($searchStr) ? " WHERE vp.organization_name like '$searchStr%'" : "";

        if (empty($search_clause)) {
            if ($params['targetPage'] != 'all') {
                $search_clause.=" WHERE vp.active=" . $params['targetPage'];
            }
        }

        $order_clause = !empty($sort_by) ? " ORDER BY $sort_by $sort_dir" : "";

        $q = $this->_em->createQuery("
            SELECT vp
            FROM BL\Entity\VendorProfile vp
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
     * Function to Search Vendors By Web Profile Information
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchVendorByWebProfile($search_params=array(), $limit_fields=array()) {
        $storefront_online = "";
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'v.organization_name', '2' => 'l.applied_date', '3' => 'l.status');
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
            "organization_name" => "v.organization_name",
            "address1" => "vp.address1",
            "address2" => "vp.address2",
            "city" => "vp.city",
            "state" => "vp.state",
            "zip" => "vp.zip",
            "email" => "vp.email",
            "phone1" => "vp.phone1",
            "phone2" => "vp.phone2",
            "fax" => "vp.fax",
            "web_page" => "vp.web_page",
            "product_offered" => "vp.product_offered",
            "company_description" => "vp.company_discripction"
        );

        $search_clause = "WHERE v.account_type=" . ACC_TYPE_VENDOR . " AND v.id = vp.user_id ";

        foreach ($field_map as $k => $val) {
            if (isset($search_params[$k]) AND $search_params[$k] <> "") {
                $search_clause.= $params['search_op'] . " " . $val . " like '%" . $search_params[$k] . "%' ";
            }
        }

        if (isset($search_params['storefont']) AND $search_params['storefont'] <> "") {
            $storefront_online.= $search_params['storefont'];
        }
        if (isset($search_params['online']) AND $search_params['online'] <> "") {
            $storefront_online.= (($storefront_online <> "") ? " , " . $search_params['online'] : $search_params['online']);
        }
        if (isset($search_params['wholesale']) AND $search_params['wholesale'] <> "") {
            $storefront_online.= (($storefront_online <> "") ? " , " . $search_params['wholesale'] : $search_params['wholesale']);
        }
        if ($storefront_online <> "") {
            $search_clause.= " AND vp.user_id IN ( SELECT vendor_id FROM vendor_services WHERE service_id IN ($storefront_online) GROUP BY vendor_id ) ";
        }

        $status_conditions = "";
        if (isset($search_params['vendor_status']) && ($search_params['vendor_status'] != 'all' )) {
            $status = explode(',', $search_params['vendor_status']);
            $status_conditions .= " AND v.user_status IN ( ";
            $count = 1;
            foreach ($status as $s) {
                $status_conditions .= "'" . $s . "'";
                if ($count < count($status)) {
                    $status_conditions .= " , ";
                }
                $count++;
            }
            $status_conditions .=" ) ";
        }

        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";
        $is_limited_fields = count($limit_fields);
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : " vp.id,vp.user_id,v.user_status,v.created_at,v.organization_name,vp.address1,vp.address2,vp.city,vp.state,vp.zip,vp.email,vp.phone1,vp.phone2,vp.fax,vp.web_page,vp.product_offered,vp.company_discripction ";
        $q = "SELECT " . $select_fields . " FROM vendor_profiles AS vp , users AS v " . $search_clause . $status_conditions . $order_clause;

        $con = $this->_em->getConnection();
        $result = $con->fetchAll($q);
        $paginator = \Zend_Paginator::factory($result);
        $records_total = $paginator->count();
        $paginator->setItemCountPerPage($params['per_page']);
        $paginator->setCurrentPageNumber($params['current_page']);

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
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

		    Zend_Debug::dump($UserArray);exit;

                    array_push($AllRecords, array_merge($rec, $UserArray));
                } else {
                    array_push($AllRecords, $rec);
                }
            }
            return $AllRecords;    // Calling from export
        }
        foreach ($result as $v) {
            $prec[] = array(
                '<a href=\"javascript:;\" class=\"vendor_link\" rel=\"m:web-profile,v:' . $v['user_id'] . ',c:' . $v['id'] . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v['organization_name'])) . '</a>',
                ((!is_null($v['created_at'])) ? date("M d, Y H:i A", strtotime($v['created_at'])) : "N/A" ),
                (!is_null($v['user_status']) ? $v['user_status'] : "-")
            );
        }
        $json .= \Zend_Json::encode($prec);
        $json .= '}';
        echo $json;
    }

}
