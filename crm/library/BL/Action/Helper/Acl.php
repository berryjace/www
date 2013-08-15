<?php

/**
 * ACL Helper Class
 * @author Noman
 * @copyright Blueliner Marketing
 * @version 0.1
 * @access public
 */
class BL_Action_Helper_Acl extends Zend_Acl {

    /**
     * __construct
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function __construct($role) {
        $this->loadRoles();

        $inhRole = $role;
        if (!empty($inhRole)) {
            $this->loadResources($inhRole);
            $this->loadPermissions($inhRole);
        }
    }

    /**
     * Function to load all Roles from DB table
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     */
    public function loadRoles() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();

        $allRoles = $em->getRepository("BL\Entity\Role")->get_all_roles();
        $em->clear();
        foreach ($allRoles as $role) {
            $this->addRole(new Zend_Acl_Role($role));
        }
        return true;
    }

    /**
     * Function to Load all the resources for the specified role
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access private
     */
    public function loadResources($role) {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $allResources = $em->getRepository("BL\Entity\Resource")->get_all_resources($role);
        //Zend_Debug::dump($allResources);
        foreach ($allResources as $res) {
            if (!$this->has($res)) {
                $this->addResource(new Zend_Acl_Resource($res));
            }
        }
        return true;
    }

    /**
     * Load all the permission for the specified role
     *
     * @param Zend_Db_Adapter $db
     * @param integer $role
     * @return boolean
     */
    public function loadPermissions($role) {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $em = $this->doctrineContainer->getEntityManager();
        $allPermissions = $em->getRepository("BL\Entity\Resource")->get_all_permissions($role);

        foreach ($allPermissions as $res) {
            if ($res['permission'] == 'yes') {
                $this->allow($role, $res['resource']);
                //echo 'all-'.$role.$res['resource'].'<br/>';
            } else {
                $this->deny($role, $res['resource']);
                //echo 'deny-'.$role.$res['resource'].'<br/>';
            }
        }
        return true;
    }

}
