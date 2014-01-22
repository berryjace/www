<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * LicenseStatement
 *
 * @Table(name="license_statements")
 * @Entity(repositoryClass="BL\Entity\Repository\LicenseStatementRepository")
 * @author Tanzim
 */
class LicenseStatement
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
     * @Column(name="user_id", type="integer", length=8)
     */
    private $user_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $User;

    public function __construct()
    {
        $this->User = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}