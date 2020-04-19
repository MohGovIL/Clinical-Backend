<?php


namespace GenericTools\Model;

use Zend\Db\TableGateway\TableGateway;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class QuestionnaireResponseTable
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

        $joinFieldsArr=  [
            'questionnaire_id'=>'id'
        ];

        $this->appendJoin(
            ["reg"=>"registry"],
            new Expression("reg.directory=questionnaire_response.form_name"),
            $joinFieldsArr,
            Select::JOIN_LEFT
        );

        $this->addGroupForJoin('questionnaire_response.id');

        return $this->getJoins();
    }

}
