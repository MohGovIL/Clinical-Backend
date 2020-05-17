<?php
/**
 * User: Eyal Wolanowski eyalvo@matrix.co.il
 * Date: 17/05/20
 * Time: 16:58
 */
namespace ClinikalAPI\Model;

use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use GenericTools\Model\baseTable;

class ClinikalPatientTrackingChangesTable
{


    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function replaceInto($params){

        // since replace into not part of any ANSI SQL a check need to be done first
        if($params['facility_id']==="ALL"){
            $update =  $this->tableGateway->getSql()->update();
            $data=array("update_date"=>$params['update_date']);
            $update->set($data);
            $this->tableGateway->updateWith($update);
        }else{
            $id=array("facility_id"=>$params['facility_id']);
            $exist=$this->getDataByParams($id);
            if(!empty($exist)){
                $this->updateData($id,$params);
            }else{
                $this->insert($params);
            }
        }
        return true;
    }

    public function getLastUpdateDate($facilityId){

        $rsArray = array();

        if($facilityId==="ALL"){
            $db=$this->tableGateway;
            $table=$db->getTable();
            $sql = "SELECT MAX(update_date) as update_date FROM " . $table;
            $statement = $db->adapter->createStatement($sql, array());
            $resultObj = $statement->execute();
            foreach ($resultObj as $row) {
                $rsArray[] = $row;
            }
        }else{
            $select = $this->tableGateway->getSql()->select();
            $where = new Where();
            $where->AND->equalTo('facility_id', $facilityId);
            $select->where($where);
            $rs = $this->tableGateway->selectWith($select);
            foreach ($rs as $r) {
                $rsArray[] = get_object_vars($r);
            }
        }
        return $rsArray[0]['update_date'];
    }
}

