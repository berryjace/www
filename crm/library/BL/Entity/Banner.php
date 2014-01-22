<?php

namespace BL\Entity;

/**
 *
 * @Table(name="banners")
 * @Entity(repositoryClass="BL\Entity\Repository\BannerRepository")
 * @author Mahbub
 */
class Banner {

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
    private $location;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $link;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $image;

    /**
     * @Column(type="boolean",nullable=true)
     */
    private $is_active;

    /**
     * @Column(type="datetime")
     */
    private $added_on;
    
    /**
     * @Column(type="datetime",nullable=true)
     */
    private $start_date;

    /**
     * @Column(type="datetime",nullable=true)
     */
    private $end_date;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="added_by", referencedColumnName="id", nullable=true)
     */
    private $added_by;    
    
    /**
     * @ManyToMany(targetEntity="User",inversedBy="client_banners",cascade={"persist,remove"})
     * @JoinTable(name="banners_clients",
     *      joinColumns={@JoinColumn(name="banner_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="client_id", referencedColumnName="id")})
     */
    private $clients;

    public function __construct() {
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->added_on = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}