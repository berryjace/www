<?php

//use Doctrine\ORM\Mapping as ORM;

namespace BL\Entity;

/**
 * License
 *
 * @Table(name="licenses")
 * @Entity(repositoryClass="BL\Entity\Repository\LicenseRepository")
 * @author Tanzim
 */
class License {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $status
     *
     * @Column(name="status", type="integer", length=2, nullable=true)
     */
    private $status;

    /**
     * @var string $vendor_signature
     *
     * @Column(name="vendor_signature", type="string", length=50, nullable=true)
     */
    private $vendor_signature;

    /**
     * @Column(name="vendor_title", type="string", length=50, nullable=true)
     */
    private $vendor_title;

    /**
     * @var string $client_signature
     *
     * @Column(name="client_signature", type="string", length=50, nullable=true)
     */
    private $client_signature;

    /**
     * @Column(name="client_title", type="string", length=50, nullable=true)
     */
    private $client_title;

    /**
     * @var text $reject_note_from_client
     *
     * @Column(name="reject_note_from_client", type="text", length=16777215, nullable=true)
     */
    private $reject_note_from_client;

    /**
     * @var string $payment_method
     *
     * @Column(name="payment_method", type="string", nullable=true)
     */
    private $payment_method;

    /**
     * @var string $payment_status
     *
     * @Column(name="payment_status", type="string", nullable=true)
     */
    private $payment_status;

    /**
     * @var string $sample_status
     *
     * @Column(name="sample_status", type="string", nullable=true)
     */
    private $sample_status;

    /**
     * @var text $product_desc
     *
     * @Column(name="product_desc", type="text", length=16777215, nullable=true)
     */
    private $product_desc;

    /**
     * @var string $supplier_name
     *
     * @Column(name="supplier_name", type="string", length=80, nullable=true)
     */
    private $supplier_name;

    /**
     * @var string $other_desc
     *
     * @Column(name="other_desc", type="string", length=50, nullable=true)
     */
    private $other_desc;

    /**
     * @var text $financial_statement
     *
     * @Column(name="financial_statement", type="text", nullable=true)
     */
    private $financial_statement;

    /**
     * @var text $agreement_statement
     *
     * @Column(name="agreement_statement", type="text", nullable=true)
     */
    private $agreement_statement;

    /**
     * @var text $statement
     *
     * @Column(name="statement", type="text", nullable=true)
     */
    private $statement;

    /**
     * @var datetime $applied_date
     *
     * @Column(name="applied_date", type="datetime", nullable=true)
     */
    private $applied_date;

    /**
     * @var datetime $vendor_sign_date
     *
     * @Column(name="vendor_sign_date", type="datetime", nullable=true)
     */
    private $vendor_sign_date;

    /**
     * @var datetime $client_sign_date
     *
     * @Column(name="client_sign_date", type="datetime", nullable=true)
     */
    private $client_sign_date;

    /**
     * @var datetime $admin_sign_date
     *
     * @Column(name="admin_sign_date", type="datetime", nullable=true)
     */
    private $admin_sign_date;

    /**
     * @var datetime $cancel_date
     *
     * @Column(name="cancel_date", type="datetime", nullable=true)
     */
    private $cancel_date;

    /**
     * @var string $vendor_name
     *
     * @Column(name="vendor_name", type="string", length=200, nullable=true)
     */
    private $vendor_name;

    /**
     * @var string $client_name
     *
     * @Column(name="client_name", type="string", length=200, nullable=true)
     */
    private $client_name;

    /**
     * @var string $sharing
     *
     * @Column(name="sharing", type="string", nullable=true)
     */
    private $sharing;

    /**
     * @var string $vendor_type
     *
     * @Column(name="vendor_type", type="string", length=5, nullable=true)
     */
    private $vendor_type;

    /**
     * @var string $royalty_structure
     *
     * @Column(name="royalty_structure", type="text", nullable=true)
     */
    private $royalty_structure;

    /**
     * @var decimal $royalty_commission
     *
     * @Column(name="royalty_commission", type="decimal", length=18, scale=2, nullable=true)
     */
    private $royalty_commission;

    /**
     * @var decimal $royalty_commission_type
     *
     * @Column(name="royalty_commission_type", type="string", length=1, nullable=false)
     */
    private $royalty_commission_type;

    /**
     * @var decimal $default_renewal_fee
     *
     * @Column(name="default_renewal_fee", type="decimal", length=18, scale=2, nullable=true)
     */
    private $default_renewal_fee;

    /**
     * @var decimal $annual_advance
     *
     * @Column(name="annual_advance", type="decimal", length=6, scale=2, nullable=true)
     */
    private $annual_advance;

    /**
     * @var text $royalty_description
     *
     * @Column(name="royalty_description", type="text", nullable=true)
     */
    private $royalty_description;

    /**
     * @var text $grant_of_license
     *
     * @Column(name="grant_of_license", type="text", nullable=true)
     */
    private $grant_of_license;

    /**
     * @var text $product_sample_link
     *
     * @Column(name="product_sample_link", type="text", nullable=true)
     */
    private $product_sample_link;

    /**
     * @var text $vendor_products
     *
     * @Column(name="vendor_products", type="text", nullable=true)
     */
    private $vendor_products;

    /**
     * @var text $recom_for_vendor
     *
     * @Column(name="recom_for_vendor", type="text", length=16777215, nullable=true)
     */
    private $recom_for_vendor;

    /**
     * @var text $target_audience_vendor
     *
     * @Column(name="target_audience_vendor", type="string", nullable=true)
     */
    private $target_audience_vendor;

    /**
     * @var text $recom_for_client
     *
     * @Column(name="recom_for_client", type="text", length=16777215, nullable=true)
     */
    private $recom_for_client;

    /**
     * @var text $license_specific_note
     *
     * @Column(name="license_specific_note", type="text", nullable=true)
     */
    private $license_specific_note;

    /**
     * @var string $license_agree_number
     *
     * @Column(name="license_agree_number", type="string", nullable=true)
     */
    private $license_agree_number;

    /**
     * @var string $admin_decline_reason
     * @Column(name="admin_decline_reason", type="string", length=1000, nullable=true)
     */
    private $admin_decline_reason;

    /**
     * @var string $vendor_decline_reason
     * @Column(name="vendor_decline_reason", type="string", length=1000, nullable=true)
     */
    private $vendor_decline_reason;

    /**
     * @var string $client_decline_reason
     * @Column(name="client_decline_reason", type="string", length=1000, nullable=true)
     */
    private $client_decline_reason;

    /**
     * @var string $save_status
     * @Column(name="save_status", type="string", length=10, nullable=true)
     */
    private $save_status;

    /**
     * @var string $grandfathered
     * @Column(name="grandfathered", type="string", length=1, nullable=false)
     */
    private $grandfathered;

    /**
     * @var string $file_path
     * @Column(name="file_path", type="string", length=200, nullable=true)
     */
    private $file_path;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    private $vendor_id;

    /**
     * @ManyToMany(targetEntity="TargetAudience", inversedBy="License")
     * @JoinTable(name="licenses_target_audiences",
     *   joinColumns={
     *     @JoinColumn(name="license_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @JoinColumn(name="target_audience_id", referencedColumnName="id")
     *   }
     * )
     */
    private $TargetAudience;

    public function __construct() {
        $this->TargetAudience = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __get($property) {
	if(isset($this->$property))
	        return $this->$property;
	else
		return null;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
