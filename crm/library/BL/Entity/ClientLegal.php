<?php
namespace BL\Entity;

/**
 *
 * @Table(name="client_legals")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientLegalRepository")
 * @author Sukhon (Blueliner Marketing)
 */
class ClientLegal {

    /**
     *
     * @var integer $id
     * @Column(name="id", type="integer",nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $choice_of_law;

    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $licensor_title;

    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $legal_name;

    /**
     * @Column(type="string",length=200,nullable=true)
     */
    private $legal_firm;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $legal_address_line1;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $legal_address_line2;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $legal_city;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $legal_state;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $legal_zipcode;

    /**
     * @Column(type="string",length=254,nullable=true)
     */
    private $legal_phone;

    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
     */
    private $user_id;

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
