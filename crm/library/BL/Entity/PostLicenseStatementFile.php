<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * PostLicenseStatementFile
 *
 * @Table(name="post_license_statement_files")
 * @Entity(repositoryClass="BL\Entity\Repository\PostLicenseStatementFileRepository")
 * @author Tanzim
 */
class PostLicenseStatementFile
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
     * @var string $title
     *
     * @Column(name="title", type="string", length=50)
     */
    private $title;

    /**
     * @var string $file_url
     *
     * @Column(name="file_url", type="string", length=200)
     */
    private $file_url;

    /**
     * @var string $file_extension
     *
     * @Column(name="file_extension", type="string", length=20)
     */
    private $file_extension;

    /**
     * @var string $user_type
     *
     * @Column(name="user_type", type="string")
     */
    private $user_type;

    /**
     * @Column(name="post_license_statement_id", type="integer", length=8)
     */
    private $post_license_statement_id;

    /**
     * @ManyToOne(targetEntity="PostLicenseStatement")
     * @JoinColumns({
     *   @JoinColumn(name="post_license_statement_id", referencedColumnName="id")
     * })
     */
    private $PostLicenseStatement;

    public function __construct()
    {
        $this->PostLicenseStatement = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}