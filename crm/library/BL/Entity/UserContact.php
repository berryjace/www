<?php

namespace BL\Entity;

/**
 *
 * @Table(name="user_contacts")
 * @Entity(repositoryClass="BL\Entity\Repository\UserContactRepository")
 * @author Sukhon (Blueliner Marketing)
 */
class UserContact {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @Column(type="string", length=16, nullable=true)
     */
    private $sal;

    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $first_name;

    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $last_name;

    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $title;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $address_line1;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $city;
    
    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $email;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $state;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $zipcode;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $phone;

    /**
     * @Column(type="string",length=20,nullable=true)
     */
    private $phone_ext;

    /**
     * @Column(type="string",length=254,nullable=true)
    */
    private $mobile;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $fax;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $contact_type;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *  @JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
     * })
     */
    private $user_id;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
