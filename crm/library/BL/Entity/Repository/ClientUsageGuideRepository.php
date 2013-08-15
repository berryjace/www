<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Description of ClientUsageGuideRepository
 *
 * @author Masud
 */
class ClientUsageGuideRepository extends EntityRepository {

    /**
     * Function to get usage guide for client
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @param array $params
     * @return string 
     */
    public function getUsageGuides($params) {
        $condition = "WHERE cug.user_id=" . $params['user_id'] . " ";
        $search_clause = !empty($params['search']) ? "AND cug.guide_name like '%" . $params['search'] . "%'" : "";
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $q = $this->_em->createQuery("
            SELECT cug
            FROM BL\Entity\ClientUsageGuide cug            
            $condition
            $search_clause 
            $order_clause
       ");

        //print_r($q->getSQL());        
        $paginator = new Paginator($q);
        $records_total = $paginator->count();
        $records = $q->setFirstResult($params['current_page'])->setMaxResults($params['per_page'])->getResult();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $g) {
            $file_path = $params['base_url'] . '/assets/images/';
            $guide_icon = 'document.png';
            $action_link = '<a class="view_link" href="javascript:;" rel="' . $g->id . '">View</a>&nbsp;|&nbsp;
                            <a class="edit_link" href="javascript:;" rel="' . $g->id . '">Edit</a>&nbsp;|&nbsp;
                            <a class="delete_usageguide" href="javascript:;" title="' . $g->guide_name . '" rel="' . $g->id . '">Delete</a>';
            if (($g->guide_url != '') || ($g->guide_url != NULL)) {
                $guide_icon = $g->guide_type . ".png";
                $action_link .= '&nbsp;|&nbsp;<a class="download_link" href="javascript:;" rel="' . $g->guide_url . '">Download</a>';
            }

            $prec[] = array(
                '<input type="checkbox" class="checkbox" name="usageguide_name" rel="' . $g->id . '">',
                '<img src="' . $file_path . $guide_icon . '" class="usageguide_logo" >',
                $g->guide_name,
                substr($g->guide_content, 0, 50),
                $action_link
            );
        }
        $json .= \Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to get client usage guide for vendor
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @param array $params
     * @return string 
     */
    public function getUsageGuidesForVendor($params) {

        $condition = "WHERE cug.user_id=" . $params['user_id'] . " ";
        $search_clause = !empty($params['search']) ? "AND cug.guide_name like '%" . $params['search'] . "%'" : "";
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $q = $this->_em->createQuery("
            SELECT cug
            FROM BL\Entity\ClientUsageGuide cug            
            $condition
            $search_clause 
            $order_clause
       ");

        //print_r($q->getSQL());        
        $paginator = new Paginator($q);
        $records_total = $paginator->count();
        $records = $q->setFirstResult($params['current_page'])->setMaxResults($params['per_page'])->getResult();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $g) {
            $file_path = $params['base_url'] . '/assets/images/';
            $guide_icon = 'document.png';
            $action_link = '<a class="view_link" href="javascript:;" rel="' . $g->id . '" rev="' . $g->user_id->id . '">View</a>';
            if (($g->guide_url != '') || ($g->guide_url != NULL)) {
                $guide_icon = $g->guide_type . ".png";
                $action_link .= '&nbsp;|&nbsp;<a class="download_link" href="javascript:;" rel="' . $g->guide_url . '">Download</a>';
            }

            $prec[] = array(
                '<img src="' . $file_path . $guide_icon . '" class="usageguide_logo" >',
                $g->guide_name,
                substr($g->guide_content, 0, 50),
                $action_link
            );
        }
        $json .= \Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to get client usage guide for admin
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @param array $params
     * @return string 
     */
    public function getUsageGuidesForAdmin($params) {

        $condition = "WHERE cug.user_id=" . $params['user_id'] . " ";
        $search_clause = !empty($params['search']) ? "AND cug.guide_name like '%" . $params['search'] . "%'" : "";
        $order_clause = !empty($params['sort_key']) ? " ORDER BY " . $params['sort_key'] . " " . $params['sort_dir'] . "" : "";

        $q = $this->_em->createQuery("
            SELECT cug
            FROM BL\Entity\ClientUsageGuide cug            
            $condition
            $search_clause 
            $order_clause
       ");

        //print_r($q->getSQL());        
        $paginator = new Paginator($q);
        $records_total = $paginator->count();
        $records = $q->setFirstResult($params['current_page'])->setMaxResults($params['per_page'])->getResult();

        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
        $prec = array();
        foreach ($records as $g) {
            $file_path = $params['base_url'] . '/assets/images/';
            $guide_icon = 'document.png';
            $action_link = '<a class="view_link" href="javascript:;" rel="' . $g->id . '" rev="' . $g->user_id->id . '">View</a>';
            if (($g->guide_url != '') || ($g->guide_url != NULL)) {
                $guide_icon = $g->guide_type . ".png";
                $action_link .= '&nbsp;|&nbsp;<a class="download_link" href="javascript:;" rel="' . $g->guide_url . '">Download</a>';
            }
            $prec[] = array(
                '<img src="' . $file_path . $guide_icon . '" class="usageguide_logo" >',
                $g->guide_name,
                substr($g->guide_content, 0, 50),
                $action_link
            );
        }
        $json .= \Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

}

?>
