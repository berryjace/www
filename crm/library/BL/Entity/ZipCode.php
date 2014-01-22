<?php

namespace BL\Entity;

/**
 *
 * @Table(name="zip_codes")
 * @Entity(repositoryClass="BL\Entity\Repository\ZipCodeRepository")
 * @author Rasidul
 */
class ZipCode {

    /**
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $zip;
    
    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $state;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $latitude;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $longitude;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $city;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $full_state;

    

    public function __construct() {
        
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }
}