<?php

namespace BL\Entity;

/**
 *
 * @Table(name="white_label")
 * @Entity(repositoryClass="BL\Entity\Repository\WhiteLabelRepository")
 * @author Mahbub
 */
class WhiteLabel {

    /**
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $header_name;
    
    /**
     * @Column(type="text",nullable=true)
     */
    private $url;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $header_file;

    /**
     * @Column(type="string",length=7,nullable=true)
     */
    private $bg_color;

    /**
     * @Column(type="string",length=7,nullable=true)
     */
    private $button_color;

    /**
     * @Column(type="string",length=7,nullable=true)
     */
    private $font_color;

    /**
     * @Column(type="string",length=7,nullable=true)
     */
    private $footer_color;

    /**
     * @Column(type="datetime")
     */
    private $last_modified;

    /**
     * @OneToOne(targetEntity="User")
     */
    private $client;
    /**
     * @Column(type="text",nullable=true)
     */
    private $header_link;

    public function __construct() {
        $this->last_modified = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }
}