<?php

namespace BL\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ProductRepository
 *
 * @author medhad
 */
class ProductRepository extends EntityRepository{

    /**
     * Function to get ALl products with status to show in the vendor licensing proposed use
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */

    public function findAll(){
        $q = $this->_em->createQuery("SELECT p FROM BL\Entity\Product p");
        return $q->getResult();
    }

    public function findByCategory($category_id){
        $q = $this->_em->createQuery("SELECT p FROM BL\Entity\Product p WHERE p.product_category_id = ".$category_id);
        return $q->getResult();
    }

    public function  findByCategoryProductName($category_id, $product_name){
        $q = $this->_em->createQuery("SELECT p FROM BL\Entity\Product p WHERE p.product_category_id = ".$category_id ." and p.product_name = '".$product_name."'");
        return $q->getResult();
    }
    public function findByid($id){
        $q = $this->_em->createQuery("SELECT p.id,p.product_name FROM BL\Entity\Product p WHERE p.id =  ".$id);
        return $q->getResult();
    }	

}
?>
