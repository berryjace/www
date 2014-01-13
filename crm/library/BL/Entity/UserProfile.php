<?php

namespace BL\Entity;

/**
 *
 * @Table(name="users_profile")
 * @Entity(repositoryClass="BL\Entity\Repository\UserProfileRepository")
 * @author Sukhon
 */
class UserProfile {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     * */
    private $id;

    /**
     * @Column(name="user_code", type="string",length=54,nullable=true)
     */
    private $user_code;
    /**
     * @Column(name="organization_name", type="string",length=254,nullable=true)
     */
    private $organization_name;
    /**
     * @Column(name="first_name", type="string",length=254,nullable=true)
     */
    private $first_name;

    /**
     * @Column(name="last_name", type="string",length=254,nullable=true)
     */
    private $last_name;

    /**
     * @Column(name="username", type="string",length=254,nullable=true)
     */
    private $username;

    /**
     * @Column(name="address_line1", type="string",length=254,nullable=true)
     */
    private $address_line1;

    /**
     * @Column(name="address_line2", type="string",length=254,nullable=true)
     */
    private $address_line2;

    /**
     * @Column(name="city", type="string",length=254,nullable=true)
     */
    private $city;

    /**
     * @Column(name="state", type="string",length=254,nullable=true)
     */
    private $state;

    /**
     * @Column(name="zipcode", type="string",length=254,nullable=true)
     */
    private $zipcode;

    /**
     * @Column(name="email", type="string",length=254,nullable=true)
     */
    private $email;

    /**
     * @Column(name="company_email", type="string",length=100,nullable=true)
     */
    private $company_email;

    /**
     * @Column(name="phone", type="string",length=254,nullable=true)
     */
    private $phone;

    /**
     *@Column(name="phone2", type="string", length=254, nullable=true)    
     */
    private $phone2;
    
    /**
     * @Column(name="fax", type="string",length=254,nullable=true)
     */
    private $fax;
    
    /**
     * @Column(name="website", type="string",length=254,nullable=true)
     */
    private $website;

    /**
     * @Column(name="active_date", type="datetime", nullable=true)
     */
    private $active_date;
   
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;
    
    /**
     * @Column(name="agreement_notification_email", type="string", nullable=true)
     */
    private $agreement_notification_email;
    
    public function __construct() {
        $this->active_date = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}

