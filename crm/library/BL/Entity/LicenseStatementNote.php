<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * LicenseStatementNote
 *
 * @Table(name="license_statement_notes")
 * @Entity(repositoryClass="BL\Entity\Repository\LicenseStatementNoteRepository")
 * @author Tanzim
 */
class LicenseStatementNote
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
     * @Column(name="note", type="text")
     */
    private $note;

    /**
     * @Column(name="user_type", type="string")
     */
    private $user_type;

    /**
     * @Column(name="license_statement_id", type="integer", length=8)
     */
    private $license_statement_id;

    /**
     * @Column(name="created_by", type="integer", length=8)
     */
    private $created_by;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $User;

    /**
     * @ManyToOne(targetEntity="LicenseStatement")
     * @JoinColumns({
     *   @JoinColumn(name="license_statement_id", referencedColumnName="id")
     * })
     */
    private $LicenseStatement;

    public function __construct()
    {
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
    $this->LicenseStatement = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}