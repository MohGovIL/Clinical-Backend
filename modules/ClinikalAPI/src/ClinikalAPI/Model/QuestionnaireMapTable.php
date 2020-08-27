<?php

/**
 * Date: 25/09/2020
 *  @author Dror Golan <drorgo@matrix.co.il>
 */


namespace ClinikalAPI\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class QuestionnaireMapTable
{
    public function __construct(\Laminas\Db\TableGateway\TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getLastQuestionnaireAnswer($encounter_id=null,$question_id=null){

        if ($encounter_id==null || $question_id==null) //primary keys cannot be null
            return null;

        $data = $this->getQuestionnaireAnswer($encounter_id,$question_id);

        return $data[0];
    }
    public function getQuestionnaireAnswer($encounter_id=null,$question_id=null){

        if ($encounter_id==null || $question_id==null) //primary keys cannot be null
           return null;

        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();
        $where = new \Laminas\Db\Sql\Where();



            $where->equalTo("encounter",$encounter_id)->AND->
            equalTo("question_id",explode(",",$question_id));


        $select->where($where);
        $select->order('id DESC');
        $debug = $select->getSqlString();
        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }
        return $rsArray;
    }
}
