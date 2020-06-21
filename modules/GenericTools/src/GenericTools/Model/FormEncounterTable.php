<?php

namespace GenericTools\Model;

use GenericTools\Model\UtilsTraits\JoinBuilder;
use GenericTools\Traits\magicMethods;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGateway;

class FormEncounterTable
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

        $this->appendJoin(
            ["lr"=>"list_options"],
            new Expression("lr.option_id=service_type AND lr.list_id='clinikal_service_types'"),
            ['service_type_title'=>'title','service_type_seq'=>'seq'],
            Select::JOIN_LEFT
        );

        $this->appendJoin(
            ["erm"=>"encounter_reasoncode_map"],
            "erm.eid=form_encounter.id",
            ['reason_code_encounter_type'=>'eid',
             'reason_code'=>new Expression('GROUP_CONCAT(distinct(reason_code))')],
            Select::JOIN_LEFT
        );

        $this->appendJoin(
            ["lrc"=>"list_options"],
            new Expression("lrc.option_id=erm.reason_code AND lrc.list_id='clinikal_reason_codes'"),
            ['reason_code_title'=>new Expression('GROUP_CONCAT(distinct(lrc.title))'),
             'reason_code_encounter_seq'=>new Expression('GROUP_CONCAT(distinct(lrc.seq))')],
            Select::JOIN_LEFT
        );

        $this->addGroupForJoin('form_encounter.id');

        return $this->getJoins();
    }

    /**
     * @return array -   all of the facilities from facility table
     */
    public function fetchAll()
    {
        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=(array)$r;
        }
        return $rsArray;
    }
    public function fetchAllOrderBy($order)
    {
        $rsArray=array();
        $select = new Select();
        $select->columns(array("*"));
        $select->from($this->tableGateway->getTable());

        $select->order($order);
        $rs= $this->tableGateway->selectWith($select);
        foreach($rs as $r) {
            $rsArray[]=(array)$r;
        }
        return $rsArray;
    }


    public function safeInsertEncounter(array $data)
    {
        $db=$this->tableGateway;
        $adapter=$db->adapter;
        $con=$adapter->getDriver()->getConnection();
        $con->beginTransaction();
        try {
            $insertRez=$db->insert($data['form_encounter']);
            $eid=$db->getLastInsertValue();

            $codeReason=$data['encounter_reasoncode_map'];
            foreach ($codeReason as $index =>$data){
                $codeReason[$index]['eid']=$eid;
            }
            $tableGateway = new TableGateway('encounter_reasoncode_map',
                $this->tableGateway->getAdapter(),null,
                $this->tableGateway->getResultSetPrototype()
            );
            $postcalendarEventsTable =  new EncounterReasonCodeMapTable($tableGateway);
            $postcalendarEventsTable->insertValueSets($codeReason);

            $insertedRecord=$this->buildGenericSelect(array("id"=>$eid));


            $con->commit();
            return $insertedRecord;
        }catch( Exception $e ) {
            $con->rollBack();
            // todo restore auto increment
            return $e->getMessage();
        }
    }


    public function safeUpdateEncounter(array $data)
    {
        $db=$this->tableGateway;
        $adapter=$db->adapter;
        $con=$adapter->getDriver()->getConnection();
        $con->beginTransaction();
        try {

            $where = new Where();
            $primaryKeyName="id";
            $primaryKeyVal=$data['form_encounter'][$primaryKeyName];
            $where->equalTo($primaryKeyName,$primaryKeyVal );

            $update =  $this->tableGateway->getSql()->update();
            $update->where($where);
            $update->set($data['form_encounter']);
            $rs = $this->tableGateway->updateWith($update);

            $codeReason=$data['encounter_reasoncode_map'];
            foreach ($codeReason as $index =>$info){
                $codeReason[$index]['eid']=$primaryKeyVal;
            }
            $tableGateway = new TableGateway('encounter_reasoncode_map',
                $this->tableGateway->getAdapter(),null,
                $this->tableGateway->getResultSetPrototype()
            );
            $EncounterReasonCodeMapTable=  new EncounterReasonCodeMapTable($tableGateway);
            $EncounterReasonCodeMapTable->insertValueSets($codeReason);
            $insertedRecord=$this->buildGenericSelect(array("id"=>$primaryKeyVal));

            $con->commit();
            return $insertedRecord;
        }catch( Exception $e ) {
            $con->rollBack();
            // todo restore auto increment
            //$sql = "ALTER TABLE " . $table ."AUTO_INCREMENT=";
            //$statement = $adapter->createStatement($sql, array());
            //$resultObj = $statement->execute();
            return $e->getMessage();
        }
    }

    /**
     *  Return status,secondary status, last status update date
     * @param integer
     * @return array
    */
    public function getStatusStateByEid($eid)
    {
        $sql="SELECT status,secondary_status,status_update_date FROM " . $this->tableGateway->table . " WHERE id = ?";

        $statement = $this->tableGateway->adapter->createStatement($sql, array($eid));
        $return = $statement->execute();

        $results = array();
        foreach ($return as $row) {
            $results[] = $row;
        }

        return $results[0];
    }

}
