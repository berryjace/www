<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * List of State In USA
 *
 * @author Rashed
 */
class StateRepository extends EntityRepository {

    /**
     * Function to get all Design Tag
     * @author Rashed
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function findAll() {
        $q = $this->_em->createQuery("SELECT st.id, st.name, st.abbrev FROM BL\Entity\State st Order By st.name ASC");
        return $q->getResult();
    }


}

?>
