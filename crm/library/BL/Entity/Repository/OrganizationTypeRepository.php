<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * List of State In USA
 *
 * @author Rashed
 */
class OrganizationTypeRepository extends EntityRepository {

    /**
     * Function to get all Design Tag
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function findAll() {
        $q = $this->_em->createQuery("SELECT ot.id, ot.name FROM BL\Entity\OrganizationType ot Order By ot.name ASC");
        return $q->getResult();
    }

}

?>
