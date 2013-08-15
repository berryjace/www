<?php

namespace BL\Entity;

/**
 * VendorDocs
 *
 * @Table(name="vendor_docs")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorDocsRepository")
 * @author Sukhon
 */
class VendorDocs {

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=4)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $doc_name
     *
     * @Column(name="doc_name", type="string", length=255 , nullable=true)
     */
    private $doc_name;

    /**
     * @var string $doc_type
     *
     * @Column(name="doc_type", type="string", length=255 , nullable=true)
     */
    private $doc_type;
    
    /**
     * @var string $doc_url
     *
     * @Column(name="doc_url", type="string", length=255 , nullable=true)
     */
    private $doc_url;
    /**
     * @var string $doc_time
     *
     * @Column(name="doc_time", type="datetime" , nullable=true)
     */
    private $doc_time;

    /**
     * @var string $create_date
     *
     * @Column(name="create_date", type="datetime" ,nullable=true)
     */
    private $create_date;

    /**
     * @var string $update_date
     *
     * @Column(name="update_date", type="datetime" , nullable=true)
     */
    private $update_date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor_id;

    public function __construct() {
        //$this->Vendor = new \Doctrine\Common\Collections\ArrayCollection();
        $this->create_date = new \DateTime(date("Y-m-d H:i:s"));
        $this->update_date = new \DateTime(date("Y-m-d H:i:s"));
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

}
