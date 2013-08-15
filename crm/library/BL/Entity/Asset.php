<?php

namespace BL\Entity;

/**
 *
 * @Table(name="assets")
 * @Entity(repositoryClass="BL\Entity\Repository\AssetRepository")
 * @author Mahbub
 */
class Asset {

    /**
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $name;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $type;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $category;

    /**
     * @Column(type="smallint",nullable=true)
     */
    private $is_default;

    /**
     * @Column(type="smallint",nullable=true)
     */
    private $display_order;

    /**
     * @Column(type="integer",nullable=true)
     */
    private $category_id;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}