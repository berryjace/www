<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of DesignTagRepository
 *
 * @author Tanzim
 */
class DesignTagRepository extends EntityRepository {

    /**
     * Function to get all Design Tag
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
//    public function findAll() {
//        $q = $this->_em->createQuery("SELECT dt FROM BL\Entity\DesignTag dt");
//        return $q->getResult();
//    }
//
//    public function findByTagName($tag_name) {
//        $q = $this->_em->createQuery("SELECT dt FROM BL\Entity\DesignTag dt WHERE dt.tag_name = " . $tag_name);
//        return $q->getResult();
//    }
//
//    public function findByID($id) {
//        $q = $this->_em->createQuery("SELECT dt FROM BL\Entity\DesignTag dt WHERE dt.id = " . $id);
//        return $q->getResult();
//    }

}

?>
