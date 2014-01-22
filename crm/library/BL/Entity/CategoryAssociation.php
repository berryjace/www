<?php

namespace BL\Entity;

/**
 *
 * @Table(name="category_association")
 * @Entity(repositoryClass="BL\Entity\Repository\CategoryAssociationRepository")
 * @author Rashed
 */
class CategoryAssociation {

    /**
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     * */
    private $id;

    /**
     * @Column(name="client_id", type="integer",length=11,nullable=true)
     */
    private $client_id;

    /**
     * @Column(name="vendor_id", type="integer",length=11,nullable=true)
     */
    private $vendor_id;

    /**
     * @Column(name="product_categories_id", type="integer",length=11,nullable=true)
     */
    private $product_categories_id;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $url;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}

