<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ClientCorrespondenceRepository extends EntityRepository {

    /**
     * Function to Get Correspondence for Clients
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getCorrespondence($params) {
        $itemPerPage = isset($params['per_page']) ? $params['per_page'] : 10;
        $currentPage = isset($params['page_start']) ? $params['page_start'] : 1;
        $client_id = isset($params['client_id']) ? $params['client_id'] : false;

        $searchStr = isset($params['search']) ? $params['search'] : '';
        $sort_by = isset($params['sort_key']) ? $params['sort_key'] : '';
        $sort_dir = isset($params['sort_dir']) ? $params['sort_dir'] : '';

        $client_condition = (false !== $client_id) ? ' cc.id=?1' : '';

        $search_clause = !empty($searchStr) ? " WHERE cc.note like '%$searchStr%' AND cc.client_id={$client_id} " : "";
        $search_clause.= ($search_clause == "" AND false !== $client_id) ? 'WHERE cc.client_id=' . $client_id : '';

        $order_clause = !empty($sort_by) ? " ORDER BY $sort_by $sort_dir" : "";

        /**
         * We have to build the query here
         */
        $q = $this->_em->createQuery("
            SELECT cc
            FROM BL\Entity\ClientCorrespondence cc
            $search_clause 
            $order_clause
       ");
        
//        $q->setResultCacheDriver(new \Doctrine\Common\Cache\ApcCache());
//        $q->useResultCache(true);
        
        if ($search_clause == "" AND false !== $client_id) {
            $q->setParameter(1, $client_id);
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
     * Function to search client by correspondence information
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function searchClientByCorrespondence($search_params, $limit_fields=array()) {
        $sorting_cols = array('0' => 'c.organization_name', '1' => 'c.organization_name', '2' => 'l.applied_date', '3' => 'l.status');

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
            "client_name" => "c.organization_name",
            "description" => "cc.note",
            "subject" => "cc.subject"            
        );

        /*
         * ToDo
         * Parse Date : convert the format mm/dd/yy to Y-d-m
         */
        $search_clause = "WHERE c.account_type=".ACC_TYPE_CLIENT;
        if (isset($search_params['date_from']) AND $search_params['date_from'] <> "") {
            $search_clause.=" AND cc.note_time >= {$search_params['date_from']} AND cc.note_time <= {$search_params['date_to']} ";
        }

        foreach ($field_map as $k => $val) {
            if (isset($search_params[$k]) AND $search_params[$k] <> "") {
                $search_clause.= $params['search_op'] . " " . $val . " like '%" . $search_params[$k] . "%' ";                
            }
        }

        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $is_limited_fields = count($limit_fields);
        $select_fields = $is_limited_fields ? implode(",", $limit_fields) : "cc";
        $q = $this->_em->createQuery("
            SELECT {$select_fields}
            FROM BL\Entity\ClientCorrespondence cc
            LEFT JOIN cc.client_id c
            $search_clause 
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

            if (!is_null($v->note_time)) {
                $created_at = ( (int) $v->note_time->format("Y") > 0 ? $v->note_time->format("M d, Y H:i A") : 'N/A');
            } else {
                $created_at = 'N/A';
            }


            $json .= '["<a href=\"javascript:;\" class=\"client_link\" rel=\"m:correspondence,v:' . $v->client_id->id . ',c:' . $v->id . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v->client_id->organization_name)) . '</a>",
              "' . $created_at . '","' . (!is_null($v->client_id->user_status) ? $v->client_id->user_status : "-") . '"]';
        }
        $json .= ']}';
        return $json;
    }

}