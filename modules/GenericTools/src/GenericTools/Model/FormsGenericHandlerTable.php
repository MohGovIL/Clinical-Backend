<?php

namespace GenericTools\Model;

use Laminas\Db\TableGateway\TableGateway;

class FormsGenericHandlerTable
{


    protected $adapter;

    public function __construct( $adapter)
    {
        $this->adapter = $adapter;
    }



    public function getFormAnswers($formQuestionMapping,$encounter,$form_id){

        $answers=array();

        foreach ($formQuestionMapping as $tableName => $qids){

            $fieldsArray=array();
            $fieldsArray[]= $encounter;
            $fieldsArray[]= $form_id;

            $bindString=   "(" . implode(",",array_map(function($arr){return "?";},$qids)) . ")" ;

            $sql = " SELECT * FROM ".$tableName;
            $sql .=" WHERE encounter = ? AND form_id = ? ";
            $sql .=" AND question_id in " . $bindString . ";";

            $fieldsArray=array_merge($fieldsArray,$qids);

            $statement = $this->adapter->createStatement($sql, $fieldsArray);
            $resultObj = $statement->execute();
            foreach ($resultObj as $row) {
                $answers[$row['question_id']] = $row['answer'];
            }

        }

        return $answers;
    }


    public function insertFormAnswers($tableName,$records){

        $fieldsArray=array();
        $bindString= "";

        foreach($records as $index => $record){

            $bindString.=" (";

            foreach($record as $inx => $val){

                $fieldsArray[]=$val;
                $bindString.="?,";
            }
            $bindString=rtrim($bindString, ",");
            $bindString.="),";

        }
        $bindString=rtrim($bindString, ",");

        $sql = " INSERT INTO ".$tableName. " (`encounter`,`form_id`,`question_id`,`answer`) VALUES ";
        $sql.= $bindString;
        $sql.= "ON DUPLICATE KEY UPDATE ";
        $sql.= "encounter=VALUES(encounter),form_id=VALUES(form_id),question_id=VALUES(question_id),answer=VALUES(answer);";

        $statement = $this->adapter->createStatement($sql, $fieldsArray);
        $resultObj = $statement->execute();

        return $resultObj->valid();

    }

}
