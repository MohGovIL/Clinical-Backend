<?php

/**
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */

namespace FhirAPI\Model;

use Laminas\Db\TableGateway\TableGateway;
use GenericTools;
use GenericTools\Model\UtilsTraits\JoinBuilder;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

class FhirQuestionnaireTable
{
    protected $tableGateway;

    use GenericTools\Model\baseTable;
    use JoinBuilder;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->join = $this->joinTables();
    }


    private function joinTables()
    {

        $joinFieldsArr=  [

            'linkIds'=>new Expression('GROUP_CONCAT(qs.qid)'),
            'form_tables'=>new Expression('GROUP_CONCAT(qs.form_table)'),
            'column_types'=>new Expression('GROUP_CONCAT(qs.column_type)'),
            'questions'=>new Expression('GROUP_CONCAT(qs.question)'),
        ];

        $this->appendJoin(
            ["qs"=>"questionnaires_schemas"],
            new Expression("qs.form_name=fhir_questionnaire.directory"),
            $joinFieldsArr,
            Select::JOIN_LEFT
        );

        $this->addGroupForJoin('fhir_questionnaire.directory');

        return $this->getJoins();
    }
}
