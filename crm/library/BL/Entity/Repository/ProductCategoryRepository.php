<?php

namespace BL\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ProductRepository
 *
 * @author medhad
 */
class ProductCategoryRepository extends EntityRepository{

    /**
     * Function to get ALl products category with status to show in the vendor licensing proposed use
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */

    public function findAll(){
        $q = $this->_em->createQuery("SELECT p FROM BL\Entity\ProductCategory p");
        return $q->getResult();
    }

}
?>
