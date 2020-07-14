<?php


namespace GenericTools\Model;

use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\TableGateway;

class HealthcareServicesTable
{
    use baseTable;
    use JoinBuilder;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->join = $this->joinTables();
    }

    private function joinTables()
    {
        $this->join =  $this->appendJoin(
            ["f"=>"facility"],
            new Expression("fhir_healthcare_services.providedBy = f.id"),
            ['providedBy_display'=>'name'],
            Select::JOIN_LEFT
        );
        $this->join =  $this->appendJoin(
            ["lo_1"=>"list_options"],
            new Expression("lo_1.list_id = 'clinikal_service_categories' AND fhir_healthcare_services.category = lo_1.option_id"),
            ['category_display'=>'title'],
            Select::JOIN_LEFT
        );
        $this->join =  $this->appendJoin(
            ["lo_2"=>"list_options"],
            new Expression("lo_2.list_id = 'clinikal_service_types' AND fhir_healthcare_services.type = lo_2.option_id"),
            ['type_display'=>'title'],
            Select::JOIN_LEFT
        );

        return $this->getJoins();
    }

}
