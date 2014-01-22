<?php

namespace BL\Entity;

/**
 *
 * @Table(name="roles")
 * @Entity(repositoryClass="BL\Entity\Repository\RoleRepository")
 * @author noman
 */
class Role {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     * */
    private $id;
    /**
     * @Column(type="string",length=100,nullable=true)
     */
    private $role_name;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

    public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

}