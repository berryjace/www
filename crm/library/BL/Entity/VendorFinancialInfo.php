<?php



//use Doctrine\ORM\Mapping as ORM;
namespace BL\Entity;

/**
 * LicenseFinStatement
 *
 * @Table(name="vendor_financial_infos")
 * @Entity(repositoryClass="BL\Entity\Repository\VendorFinancialInfoRepository")
 * @author Tanzim
 */
class VendorFinancialInfo
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
     * @var string $has_account_in_good_standing
     *
     * @Column(name="has_account_in_good_standing", type="string", nullable=true)
     */
    private $has_account_in_good_standing;

    /**
     * @var string $has_closed_financial_statement
     *
     * @Column(name="has_closed_financial_statement", type="string", nullable=true)
     */
    private $has_closed_financial_statement;

    /**
     * @var string $has_chart_of_capital_assets
     *
     * @Column(name="has_chart_of_capital_assets", type="string", nullable=true)
     */
    private $has_chart_of_capital_assets;

    /**
     * @var integer $full_time_employee_num
     *
     * @Column(name="full_time_employee_num", type="integer", length=4)
     */
    private $full_time_employee_num;

    /**
     * @var integer $years_in_business
     *
     * @Column(name="years_in_business", type="integer", length=4)
     */
    private $years_in_business;

    /**
     * @var string $business_failure_in_5_years
     *
     * @Column(name="business_failure_in_5_years", type="string")
     */
    private $business_failure_in_5_years;

    /**
     * @var string $any_person_bankrupt
     *
     * @Column(name="any_person_bankrupt", type="string", nullable=true)
     */
    private $any_person_bankrupt;

    /**
     * @var string $government_investigation
     *
     * @Column(name="government_investigation", type="string", nullable=true)
     */
    private $government_investigation;

    /**
     * @var string $contract_terminated_in_last_2_years
     *
     * @Column(name="contract_terminated_in_last_2_years", type="string", nullable=true)
     */
    private $contract_terminated_in_last_2_years;

    /**
     * @var string $litigation_against_the_officers
     *
     * @Column(name="litigation_against_the_officers", type="string", nullable=true)
     */
    private $litigation_against_the_officers;

    /**
     * @var string $any_collections_by_debt_collection_agency
     *
     * @Column(name="any_collections_by_debt_collection_agency", type="string", nullable=true)
     */
    private $any_collections_by_debt_collection_agency;

    /**
     * @var text $additional_explanation
     *
     * @Column(name="additional_explanation", type="text", nullable=true)
     */
    private $additional_explanation;
    
    /**
     * @var text $statement
     *
     * @Column(name="statement", type="text", nullable=true)
     */
    private $statement;

    /**
     * @var string $statement_type
     *
     * @Column(name="statement_type", type="string")
     */
    private $statement_type;

    

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private $vendor_id;

    

    
    public function __construct()
    {        
        $this->vendor_id = new \Doctrine\Common\Collections\ArrayCollection();        
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}