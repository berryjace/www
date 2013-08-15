<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * PostLicenseStatement
 *
 * @Table(name="post_license_statements")
 * @Entity(repositoryClass="BL\Entity\Repository\PostLicenseStatementRepository")
 * @author Tanzim
 */
class PostLicenseStatement
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=8)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var text $statement
     *
     * @Column(name="statement", type="text")
     */
    private $statement;

    /**
     * @var string $statement_type
     *
     * @Column(name="statement_type", type="string")
     */
    private $statement_type;

    /**
     * @var integer $update_version
     *
     * @Column(name="update_version", type="integer", length=4)
     */
    private $update_version;

    /**
     * @var string $updated_by
     *
     * @Column(name="updated_by", type="string")
     */
    private $updated_by;

    /**
     * @Column(name="license_id", type="integer", length=8)
     */
    private $license_id;

    /**
     * @OneToOne(targetEntity="License", mappedBy="PostLicenseStatement")
     * @JoinColumns({
     *   @JoinColumn(name="license_id", referencedColumnName="id", unique=true)
     * })
     */
    private $License;

    public function __get($property){
        return $this->$property;
    }

    public function __set($property, $value){
        $this->$property = $value;
    }

}