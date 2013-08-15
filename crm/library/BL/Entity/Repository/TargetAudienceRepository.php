<?php

namespace BL\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ProductRepository
 *
 * @author medhad
 */
class TargetAudienceRepository extends EntityRepository{

    /**
     * Function to get ALl target audience with status to show in the vendor licensing proposed use
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */

    public function findAll(){
        $q = $this->_em->createQuery("SELECT a FROM BL\Entity\TargetAudience a");
        return $q->getResult();
    }

}
?>
