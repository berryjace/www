<?php

namespace BL\Entity;

/**
 * 
 * @Table(name="archives")
 * @Entity(repositoryClass="BL\Entity\Repository\ArchiveRepository")
 * @author Mahbub
 */
class Archive {

    /**
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=20,nullable=false)
     */
    private $module;

    /**
     * @Column(type="string", length=60,nullable=false)
     */
    private $resource;

    /**
     * @Column(type="text",nullable=true)
     */
    private $content;

    /**
     * @Column(type="datetime",nullable=false)
     */
    private $date_created;

    /**
     * @Column(type="datetime",nullable=false)
     */
    private $date_updated;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="added_by", referencedColumnName="id", nullable=false)
     */
    private $added_by;

    public function __construct() {
        $this->date_created = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}