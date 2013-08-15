<?php

namespace BL\Entity;

/**
 *
 * @Table(name="client_profiles")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientProfileRepository")
 * @author Sukhon (Blueliner Marketing)
 */
class ClientProfile {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $greek_name;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $greek_approved_contact_person;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $greek_description;

    /**
     * @Column(type="decimal", length=6, scale=2, nullable=true)
     */
    private $greek_default_renewal_fee;

    /**
     * @Column(type="decimal", length=6, scale=2, nullable=true)
     */
    private $greek_default_late_fee;

    /**
     * @Column(name="annual_advance", type="decimal", length=6, scale=2, nullable=true)
     */
    private $annual_advance;

    /**
     * @ManyToOne(targetEntity="OrganizationType")
     * @JoinColumn(name="greek_org_type", referencedColumnName="id",nullable=true)
     */
    private $greek_org_type;

    /**
     * @Column(type="date", nullable=true)
     */
    private $greek_founding_year;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $symbol;

    /**
     * @Column(type="string",length=254, nullable=true)
     */
    private $founding_address;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $founding_address_line1;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $founding_address_line2;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $founding_city;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $founding_state;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $headquarters;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $headquarters_city;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $headquarters_state;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $greek_number_of_alumni;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $greek_number_of_undergrads;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $greek_number_of_alumni_chapters;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $greek_number_of_colg_chapters;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $greek_total_ug_chapters;

    /**
     * @Column(type="text", nullable=true)
     */
    private $greek_grant_of_license;

    /**
     * @Column(type="text", nullable=true)
     */
    private $greek_royalty_description;

    /**
     * @Column(type="text", nullable=true)
     */
    private $greek_recommendation_to_vendor;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $profile_status_update_time;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_type;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_logo;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_founding_loc;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_founding_year;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_num_coll_chap;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_num_alum_chap;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_num_undergrad;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_num_alum;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_image1;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_url1;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_image2;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_url2;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_image3;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_url3;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_image4;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_url4;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_image5;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_url5;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_image6;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $client_ad_url6;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $guid;

    /**
     * @Column(type="string",length=125, nullable=true)
     */
    private $org_founding_year;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $text_color;

    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $link_color;

    /**
     * @Column(type="decimal",length=4, nullable=true)
     */
    private $royalty_commission_per;

    /**
     * @Column(type="decimal",length=4, nullable=true)
     */
    private $royalty_commission_amt;



    /**
     * @Column(type="string",length=255, nullable=true)
     */
    private $left_side_bar_color;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $user_id;

    public function __get($property) {
        return $this->$property;
    }

    public function __construct() {
        $this->greek_founding_year = new \DateTime(date("Y-m-d"));
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
