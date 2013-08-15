<?php

namespace BL\Entity;

/**
 *
 * @Table(name="users")
 * @Entity(repositoryClass="BL\Entity\Repository\UserRepository")
 * @author Sukhon
 */
class User {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     * */
    private $id;

    /**
     * @Column(name="account_type", type="integer",length=1, nullable=true)
    */
    private $account_type;
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
     * @Column(name="password", type="string",length=254, nullable=true)
     */
    private $password;

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
     * @Column(name="title", type="string",length=254,nullable=true)
     */
    private $title;
    
    /**
     * @Column(name="website", type="string",length=254,nullable=true)
     */
    private $website;

    /**
     * @Column(name="secret_question", type="text",nullable=true)
     */
    private $secret_question;

    /**
     * @Column(name="secret_answer", type="text",nullable=true)
     */
    private $secret_answer;

    /**
     * @Column(name="links", type="string",length=254,nullable=true)
     */
    private $links;

    /**
     * @Column(name="activation_key", type="string",length=254, nullable=true)
     */
    private $activation_key;

    /**
     * @Column(name="reg_date", type="datetime", nullable=true)
     */
    private $reg_date;

    /**
     * @Column(name="approve_date", type="datetime", nullable=true)
     */
    private $approve_date;

    /**
     * @Column(name="decline_date", type="datetime", nullable=true)
     */
    private $decline_date;

    /**
     * @Column(name="reason_for_declining", type="text",nullable=true)
     */
    private $reason_for_declining;
    
    /**
     * @Column(name="user_status", type="string",length=254, nullable=true)
     */
    private $user_status;
    
    /**
     * @Column(name="reg_status", type="string",length=254, nullable=true)
     */
    private $reg_status;   

    /**
     * @Column(name="picture", type="string",length=254, nullable=true)
     */
    private $picture;

    /**
     * @Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;   
   
    /**
     * @ManyToMany(targetEntity="Role", cascade={"persist,remove"})
     * @JoinTable(name="users_roles",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")})
     */
    private $roles;
    
    /**
      @ManyToMany(targetEntity="Banner", mappedBy="clients")
     */
    private $client_banners;

    /**
     * @Column(name="latitude", type="string",length=254, nullable=true)
     */
    private $latitude;
    /**
     * @Column(name="longitude", type="string",length=254, nullable=true)
     */
    private $longitude;

    /**
     * @Column(name="last_login", type="datetime", nullable=true)
     */
    private $last_login;  
    
    public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reg_date = new \DateTime(date("Y-m-d H:i:s"));
        $this->created_at = new \DateTime(date("Y-m-d H:i:s"));
        $this->updated_at = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}

