<?php


namespace GenericTools\Model;

use GenericTools\Model\UtilsTraits\JoinBuilder;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class HealthcareServicesTable
{
    use baseTable;
    use JoinBuilder;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->joinTables();
    }

    private function joinTables()
    {
        $this->join =  $this->appendJoin(
            ["f"=>"facility"],
            new Expression(" hcs.providedBy = f.id"),
            ['title'=>'category_display'],
            Select::JOIN_LEFT
        );
        $this->join =  $this->appendJoin(
            ["lo_1"=>"list_options"],
            new Expression("lo_1.list_id = 'clinikal_service_categories' AND fhir_healthcare_services.category = lo_1.option_id"),
            ['name'=>'providedBy_display'],
            Select::JOIN_LEFT
        );
        $this->join =  $this->appendJoin(
            ["lo_2"=>"list_options"],
            new Expression("lo_2.list_id = 'clinikal_service_types' AND fhir_healthcare_services.category = lo_1.option_id"),
            ['name'=>'type_display'],
            Select::JOIN_LEFT
        );
    }

}
