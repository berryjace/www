<?php

namespace BL\Entity;

/**
 *
 * @Table(name="organization_types")
 * @Entity(repositoryClass="BL\Entity\Repository\OrganizationTypeRepository")
 * @author Rashed
 */
class OrganizationType {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     * */
    private $id;

    /**
     * @Column(type="string",length=254, nullable=true)
    */
    private $name;


    public function __construct() {
        // nothing
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}

