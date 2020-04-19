<?php

namespace GenericTools\Model;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

class PostcalendarEventsTable
{

    use baseTable;
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function safeUpdateApt(array $data)
    {
        $db=$this->tableGateway;
        $adapter=$db->adapter;
        $con=$adapter->getDriver()->getConnection();
        $con->beginTransaction();
        try {

            $where = new Where();
            $primaryKeyName="pc_eid";
            $primaryKeyVal=$data['openemr_postcalendar_events'][$primaryKeyName];
            $where->equalTo($primaryKeyName,$primaryKeyVal );

            $update =  $this->tableGateway->getSql()->update();
            $update->where($where);
            $update->set($data['openemr_postcalendar_events']);
            $rs = $this->tableGateway->updateWith($update);

            $codeReason=$data['event_codeReason_map'];
            foreach ($codeReason as $index =>$info){
                $codeReason[$index]['event_id']=$primaryKeyVal;
            }
            $tableGateway = new TableGateway('event_codeReason_map',
                $this->tableGateway->getAdapter(),null,
                $this->tableGateway->getResultSetPrototype()
            );
            $postcalendarEventsTable =  new EventCodeReasonMapTable($tableGateway);
            $postcalendarEventsTable->insertValueSets($codeReason);
            $insertedRecord=$this->getNoneRecurrent($primaryKeyVal);

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


    public function safeInsertApt(array $data)
    {
        $db=$this->tableGateway;
        $adapter=$db->adapter;
        $con=$adapter->getDriver()->getConnection();
        $con->beginTransaction();
        try {
            $insertRez=$db->insert($data['openemr_postcalendar_events']);
            $eid=$db->getLastInsertValue();

            $codeReason=$data['event_codeReason_map'];
            foreach ($codeReason as $index =>$data){
                $codeReason[$index]['event_id']=$eid;
            }
            $tableGateway = new TableGateway('event_codeReason_map',
                                            $this->tableGateway->getAdapter(),null,
                                            $this->tableGateway->getResultSetPrototype()
                                            );
            $postcalendarEventsTable =  new EventCodeReasonMapTable($tableGateway);
            $postcalendarEventsTable->insertValueSets($codeReason);
            $insertedRecord=$this->getNoneRecurrent($eid);
            $con->commit();
            return $insertedRecord;
        }catch( Exception $e ) {
            $con->rollBack();
            // todo restore auto increment
            return $e->getMessage();
        }
    }





    public function getNoneRecurrent($eid = null, $params = array(), $fromDate = null, $toDate = null, $sort=array())
    {
        $fieldsArray = array();
        $sql  = " SELECT e.pc_eventDate, e.pc_endDate, e.pc_startTime, e.pc_endTime, e.pc_duration, e.pc_recurrtype, e.pc_recurrspec,";        $sql .= " e.pc_recurrfreq, e.pc_catid, e.pc_eid, e.pc_gid, e.pc_title, e.pc_hometext, e.pc_apptstatus,";
        $sql .= " e.pc_facility,e.pc_pid,";
        $sql .= " e.pc_priority, e.pc_service_type, e.pc_healthcare_service_id, ";
       // $sql .= " u.fname AS ufname,u.mname AS umname,u.lname AS ulname,u.id AS uprovider_id,";
        $sql .= " lo.title as service_title,lo.seq as service_seq,";
        $sql .= " CONCAT( '{\"items\":[', if(lo2.option_id is not null, GROUP_CONCAT( JSON_OBJECT('id', lo2.option_id) ), '') , ']}') AS reason_ids,";
        $sql .= " CONCAT( '{\"items\":[', if(lo2.title is not null, GROUP_CONCAT( JSON_OBJECT('title', lo2.title) ), '') , ']}') AS reason_titles,";
        $sql .= " CONCAT( '{\"items\":[', if(lo2.seq is not null, GROUP_CONCAT( JSON_OBJECT('seq', lo2.seq) ), '') , ']}') AS reason_sequences";
        $sql .= " FROM openemr_postcalendar_events AS e";
        //$sql .= " LEFT OUTER JOIN users AS u ";
        //$sql .= " ON u.id = e.pc_aid ";
        $sql .= " LEFT JOIN list_options AS lo ";
        $sql .= " ON lo.option_id = e.pc_service_type ";
        $sql .= " LEFT JOIN event_codeReason_map AS cr ";
        $sql .= " ON e.pc_eid = cr.event_id ";
        $sql .= " LEFT JOIN list_options AS lo2 ";
        $sql .= " ON lo2.option_id = cr.option_id ";
        $sql .= " WHERE e.pc_recurrtype = '0' ";
        $sql .= " AND (lo.list_id = 'clinikal_service_types' OR  lo.list_id IS NULL)"   ;
        $sql .= " AND (lo2.list_id = 'clinikal_reason_codes' OR  lo2.list_id IS NULL)";

        if (!is_null($eid)) {
            $sql .= " AND e.pc_eid = ? ";
            $fieldsArray[] = $eid;
        } else {
            if (!is_null($fromDate)) {
                $sql .= " AND e.pc_eventDate >= ? ";
                $fieldsArray[] = $fromDate;
            }
            if (!is_null($toDate)) {
                $sql .= " AND e.pc_eventDate <= ?";
                $fieldsArray[] = $toDate;
            }

            foreach ($params as $fieldName => $value) {
                switch ($fieldName) {
                    case "pc_healthcare_service_id":
                    case "pc_apptstatus":
                    case "pc_service_type":
                    case "pc_eid":
                    case "pc_pid":
                        if(is_array($value)){
                            $counter=0;
                            foreach ($value as $index => $val){
                                $counter++;
                                $fieldsArray[]=$val;

                            }
                            $inQuery = implode(',', array_fill(0, $counter, '?'));
                            $sql .= " AND e.".$fieldName." IN(".$inQuery.") ";
                        }else{
                            $fieldsArray[] = $value;
                            $sql .= " AND e.".$fieldName." = ? ";
                        }

                        break;
                }
            }
        }
        $sql .= " GROUP BY e.pc_eid ";

        if(!empty($sort)){
            $sql .= " ORDER BY ".implode($sort,',');
        }else{
            $sql .= " ORDER BY pc_eventDate";
        }

        if(!empty($params['_count'])){
            $sql .= " LIMIT ".$params['_count'];
        }


        $statement = $this->tableGateway->adapter->createStatement($sql, $fieldsArray);
        $resultObj = $statement->execute();
        $resultArr = array();
        foreach ($resultObj as $row) {
            $resultArr[] = $row;
        }
        return $resultArr;

    }
}
