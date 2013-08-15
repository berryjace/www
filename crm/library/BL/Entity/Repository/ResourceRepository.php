<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ResourceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResourceRepository extends EntityRepository {

    /**
     * Function to get all resource for a Role
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Array
     */
    public function get_all_resources($role) {
        $q = $this->_em->createQuery("SELECT p,r,rs FROM BL\Entity\Permission p LEFT JOIN p.resource_id rs LEFT JOIN p.roles r where r.id='" . $role . "'");
        $u = $q->getArrayResult();

        $res = array();
        $i = 0;
        foreach ($u as $k => $v) {
            $res[$i++] = $v['resource_id']['resource'];
        }
        $this->_em->clear();
        return \array_unique($res);
    }

    /**
     * Function to get all permissions for a role
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Array
     */
    public function get_all_permissions($role) {
        $q = $this->_em->createQuery("SELECT p,r,rs FROM BL\Entity\Permission p LEFT JOIN p.resource_id rs LEFT JOIN p.roles r where r.id='" . $role . "'");
        $u = $q->getArrayResult();

        $res = array();
        $i = 0;
        foreach ($u as $k => $v) {
            $res[$i]['permission'] = $v['permission'];
            $res[$i]['resource'] = $v['resource_id']['resource'];
            $i++;
        }
        $this->_em->clear();
        return $res;
    }

    /**
     * Function to get all resource list
     * @author Mamun
     * @copyright Blueliner Marketing
     * @version 0.1
     * @return Array Result
     */
    public function get_all_resource_listing($params=array()) {
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 0;
        if (($params ["limit"] * $params ["page"]) >= 0 and $params ["limit"] > 0) {
            $q = $this->_em->createQuery('SELECT p,r,rs FROM BL\Entity\Permission p LEFT JOIN p.resource_id rs LEFT JOIN p.roles r order by ' . $params ["sidx"] . ' ' . $params ["sortd"]);
            $q->setFirstResult(($params['page'] - 1)*$params['limit']);
            $q->setMaxResults($params['limit']);
        } else {
            $q = $this->_em->createQuery('SELECT p,r,rs FROM BL\Entity\Permission p LEFT JOIN p.resource_id rs LEFT JOIN p.roles r order by rs.id');
        }

        return $q->getArrayResult();
    }


}