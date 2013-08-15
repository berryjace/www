<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * Event
 *
 * @Table(name="client_reports")
 * @Entity(repositoryClass="BL\Entity\Repository\ClientReport")
 * @author Tanzim
 */
class ClientReport
{
    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer", length=11)
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $client_id
     *
     * @Column(name="client_id", type="integer", length=11)
     */
    private $client_id;

    /**
     * @var string $report
     *
     * @Column(name="report", type="string")
     */
    private $report;

    /**
     * @var datetime $cdate
     *
     * @Column(name="cdate", type="datetime")
     */
    private $cdate;
    
    /**
     * @var integer $quarter
     *
     * @Column(name="quarter", type="integer", length=11)
     */
    private $quarter;

    /**
     * @var string $fiscal_year
     *
     * @Column(name="fiscal_year", type="string")
     */
    private $fiscal_year;
    
     /**
     * @var datetime $mdate
     *
     * @Column(name="mdate", type="datetime")
     */
    private $mdate;

    public function __construct()
    {
         $this->cdate = new \DateTime(date("Y-m-d H:i:s"));
    }
    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property,$value)
    {
        $this->$property = $value;
    }
}