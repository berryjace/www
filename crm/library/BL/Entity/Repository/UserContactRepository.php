<?php
namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
/**
 * Description of UserContactRepository
 *
 * @author Masud
 */
class UserContactRepository extends EntityRepository{


    public function getUserContacts(){

    }



    /**
     * Function to search vendors by contact information
     * @author Bal
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchVendorByContact($search_params, $limit_fields=array()) {
        $sorting_cols = array('0' => 'v.organization_name', '1' => 'v.organization_name', '2' => 'vc.create_date', '3' => 'v.user_status');
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
            "description" => "vc.note",
            "vendor_name" => "v.organization_name"
        );

        /*
         * ToDo
         * Parse Date : convert the format mm/dd/yy to Y-d-m
         */
        $search_clause = "WHERE v.account_type=" . ACC_TYPE_VENDOR;
        if (isset($search_params['date_from']) AND $search_params['date_from'] <> "") {
            $TempDate = explode('/', $search_params['date_from']);
            $DateFrom = $TempDate[2] . '-' . $TempDate[0] . '-' . $TempDate[1];
            $TempDate = explode('/', $search_params['date_to']);
            $DateTo = $TempDate[2] . '-' . $TempDate[0] . '-' . $TempDate[1];
            $search_clause.=" AND vc.note_time BETWEEN '{$DateFrom}' AND '{$DateTo}' ";
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
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : "vc";
        $q = $this->_em->createQuery("
            SELECT {$select_fields}
            FROM BL\Entity\UserContact vc
            LEFT JOIN vc.user_id v
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
            if (!is_null($v->note_time)) {
                $note_time = ( (int) $v->note_time->format("Y") > 0 ? $v->note_time->format("M d, Y H:i A") : 'N/A');
            } else {
                $note_time = 'N/A';
            }

            $json .= '["<a href=\"javascript:;\" class=\"vendor_link\" rel=\"m:correspondence,v:' . $v->vendor_id->id . ',c:' . $v->id . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->vendor_id->organization_name)) . '</a>",
              "' . $note_time . '",
              "' . (!is_null($v->vendor_id->user_status) ? $v->vendor_id->user_status : "-") . '"]';
        }
        $json .= ']}';
        return $json;
    }

}
?>
