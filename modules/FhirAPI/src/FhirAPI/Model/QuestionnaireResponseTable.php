<?php


namespace FhirAPI\Model;

use Laminas\Db\TableGateway\TableGateway;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use GenericTools;

class QuestionnaireResponseTable
{
    use GenericTools\Model\baseTable;
    use JoinBuilder;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->join = $this->joinTables();
    }


    private function joinTables()
    {

        $joinFieldsArr=  [
            'questionnaire_id'=>'id'
        ];

        $this->appendJoin(
            ["reg"=>"fhir_questionnaire"],
            new Expression("reg.directory=questionnaire_response.form_name"),
            $joinFieldsArr,
            Select::JOIN_LEFT
        );

        $this->addGroupForJoin('questionnaire_response.id');

        return $this->getJoins();
    }

}
