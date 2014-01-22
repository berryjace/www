<?php

namespace BL\Entity;

/**
 *
 * @Table(name="states")
 * @Entity(repositoryClass="BL\Entity\Repository\StateRepository")
 * @author Rashed
 */
class State {

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

    /**
     * @Column(type="string",length=50,nullable=true)
     */
    private $abbrev;
    

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

