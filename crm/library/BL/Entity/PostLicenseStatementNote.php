<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * PostLicenseStatementNote
 *
 * @Table(name="post_license_statement_notes")
 * @Entity(repositoryClass="BL\Entity\Repository\PostLicenseStatementNoteRepository")
 * @author Tanzim
 */
class PostLicenseStatementNote
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
     * @var text $note
     *
     * @Column(name="note", type="text")
     */
    private $note;

    /**
     * @var string $user_type
     *
     * @Column(name="user_type", type="string")
     */
    private $user_type;

    /**
     * @var string $specific_to
     *
     * @Column(name="specific_to", type="string")
     */
    private $specific_to;

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