<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 26/01/20
 * Time: 10:26
 */

namespace GenericTools\Model;
use Exception;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate\Predicate;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Insert;
trait baseTable
{
    private static  $AND = "AND";
    private static  $OR  = "or";
    private static  $OPERATOR  = "operator";
    private static  $MODIFIER  = "modifier";
    private static  $VALUE  = "value";
    private static  $SELECT  = "select";
    private static  $LEFTJOIN  = "JOIN_LEFT";
    private static  $RIGHTJOIN  = "JOIN_RIGHT";
    private static  $JOININNER  = "INNER_JOIN";
    private static  $ON  = "ON";
    private static  $GROUP  = "GROUP";
    private static  $EXACT  = "exact";
    private static  $COUNT= "_count";

    public $join = null;
    /**
     * @param array
     * @return array| /ArrayObject|null
     *
     */

    public function getDataByParams(array $FilterData)
    {
        $rsArray = array();
        if( count($FilterData) <= 0 )return $rsArray;

        $select = $this->tableGateway->getSql()->select();

        $where = new Where();
        foreach($FilterData as $field=>$value){
            if(strpos($value,"%")!== false){
                $where->AND->like($field, $value);
            }
            else {
                $where->AND->equalTo($field, $value);
            }
        }
        $select->where($where);
        $rs = $this->tableGateway->selectWith($select);
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }
        return $rsArray;
    }


    /**
     * @param array
     * @param array
     * @return  bool
     *
     */

    public function updateData(array $id, array $data)
    {

        if( count($id) <= 0  ||  count($data) <= 0) return false;

        $where = new Where();
        foreach($id as $field=>$value){
            $where->AND->equalTo($field, $value);
        }
        $update =  $this->tableGateway->getSql()->update();
        $update->where($where);
        $update->set($data);
        $rs = $this->tableGateway->updateWith($update);
        return true;

    }


    /**
     *
     * This function insert data (can be parietal) to db
     * It returns the entire row by the primaryKey field param
     * It uses transaction to make sure that the row returned is not modified before
     * the select is committed
     *
     * @param array
     * array with field name=> value
     * @param string
     * the primary key of the table
     * @param string
     * field name that gets tha max +1 value of its column
     * @return  string | array
     */

    public function safeInsert(array $data,$primaryKey,$maxField=null)
    {
        $db=$this->tableGateway;
        $table=$db->getTable();
        $adapter=$db->adapter;
        $con=$adapter->getDriver()->getConnection();
        $con->beginTransaction();
        try {
            if (!is_null($maxField)) {
                $sql = "SELECT MAX(" . $maxField . ") as max FROM " . $table;
                $statement = $adapter->createStatement($sql, array());
                $resultObj = $statement->execute();
                $flag = $resultObj->isQueryResult();

                if (!$flag) {
                    $adapter->getDriver()->getConnection()->commit();
                    return 'failed to get max';
                } else {
                    $max = $resultObj->current()['max'];
                    $data[$maxField] = $max+1;
                }
            };
            $insertRez=$db->insert($data);
            $id=$db->getLastInsertValue();
            $insertRez=$db->select(array($primaryKey=>$id));
            $insertedRecord=(array)$insertRez->current();
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
     *
     * This function update data
     * It returns the entire updated row by the primaryKey field param
     * It uses transaction to make sure that the row returned is not modified before
     * the select is committed
     *
     * @param array
     * array with field name=> value
     * @param string
     * the primary key of the table
     * @param string
     * field name that gets tha max +1 value of its column
     * @return  string | array
     */

    public function safeUpdate(array $data,array $primaryKey)
    {
        $db=$this->tableGateway;
        $adapter=$db->adapter;
        $con=$adapter->getDriver()->getConnection();
        $con->beginTransaction();
        try {

            if( count($primaryKey) != 1  ||  count($data) <= 0) die("called safeUpdate with bad params");

            $where = new Where();
            $primaryKeyName=key($primaryKey);
            $where->equalTo($primaryKeyName, $primaryKey[$primaryKeyName]);

            $update =  $this->tableGateway->getSql()->update();
            $update->where($where);
            $update->set($data);
            $rs = $this->tableGateway->updateWith($update);

            $insertRez=$db->select(array($primaryKeyName=>$primaryKey[$primaryKeyName]));

            $insertedRecord=(array)$insertRez->current();
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



    public function buildGenericSelect(array $FilterData = null,$order = null,$specialParams = array() )
    {
        $rsArray = array();
        $select = $this->tableGateway->getSql()->select();

        if (isset($this->join)){
            $join = $this->join;
        }else{
            $join=null;
        }

       // if( count($FilterData) <= 0 )return $rsArray;
        if(!empty($join) && is_array($join['join_with'])) {

            for($key = 0; $key < count($join['join_with']); $key++) {

                $joinType =  !empty($join['join_type'][$key]) ?  $join['join_type'][$key] : null;
                $joinSelectColumns =  !empty($join[self::$SELECT][$key]) ?  $join[self::$SELECT][$key] : null;
                $joinWith = !empty($join['join_with'][$key]) ? $join['join_with'][$key] : null;
                $joinOrder= !empty($join['join_order'][$key]) ? $join['join_order'][$key] : null;
                $joinON = !empty($join[self::$ON][$key]) ? $join[self::$ON][$key] : null;


                $select->join($joinWith, $joinON, /*Select::SQL_STAR*/ $joinSelectColumns, $joinType);
                if($joinOrder!="")
                {
                    $select->order($joinOrder);
                }
            }
        }

        if(!is_null($order) && $order!="") {
            $select->order(new Expression($order));
        }

        $where = new Where();
        foreach($FilterData as $field=>$value){


            $last = (is_null($value[0]['sqlOp'])) ? self::$AND : $value[0]['sqlOp'];
            $this->createQuery($value,$where,$field,$last,$FilterData);
        }
        $select->where($where);


        if(!is_null($join[self::$GROUP]) && $join[self::$GROUP] != "" ) {
            $select->group($join[self::$GROUP]);
        }

        if(!empty($specialParams)){
            if(!empty($specialParams[self::$COUNT])){
                $select->limit(intval($specialParams[self::$COUNT][0]));
            }

        }
        //print $select->getSqlString();die;
        $rs = $this->tableGateway->selectWith($select);
        $rsArray = array();
        foreach ($rs as $r) {
            if(is_null($r->id))continue;
            $rsArray[] = get_object_vars($r);
        }
        return $rsArray;
    }

    public function parsePredicateOr($value,&$where,$field){
        if(is_array($value)) {
            foreach ($value as $key => $val) {
                $this->buildOrAndPredicateWhereToWhere($val,$where,$field,self::$OR);
            }
        }
        else{
            $this->buildOrAndPredicateWhereToWhere($value,$where,$field,self::$OR);
        }
    }

    public function buildOrAndPredicateWhereToWhere($val,&$where,$field,$predicate)
    {
        //check modifiers
        if (isset($val[self::$MODIFIER]) && !is_null($val[self::$MODIFIER]) && $val[self::$MODIFIER] != self::$EXACT) {
            if ($val[self::$MODIFIER] == "not") {
                $where->$predicate->notEqualTo($field, trim($val[self::$VALUE]));
            }
            elseif ($val[self::$MODIFIER] == "contains") {
                $where->$predicate->like($field, '%'.trim($val[self::$VALUE]).'%');
            }

        } else {
            if(is_array($val)){

                $value = $val[self::$VALUE];
                $op = $val[self::$OPERATOR];
            }else{
                $value=$val;
            }

            if (strpos($value, "%") !== false) {
                $where->$predicate->like($field, trim($value));
            } elseIf (strpos($op, "ne") !== false) {
                $where->$predicate->notEqualeTo($field, trim($value));
            } elseIf (strpos($op, "eq") !== false) {
                //     $val = str_replace("=","",$val);
                $where->$predicate->equalTo($field, trim($value));
            } elseIf (strpos($op, "gt") !== false) {
                //   $val = str_replace(">","",$val);
                $where->$predicate->greaterThan($field, trim($value));
            } elseIf (strpos($op, "lt") !== false) {
                //   $val = str_replace("<","",$val);
                $where->$predicate->lessThan($field, trim($value));
            } elseIf (strpos($op, "ge") !== false) {
                //    $val = str_replace(">=","",$val);
                $where->$predicate->greaterThanOrEqualTo($field, trim($value));
            } elseIf (strpos($op, "le") !== false) {
                //  $val = str_replace("<=","",$val);
                $where->$predicate->lessThanOrEqualTo($field, trim($value));
            } elseIf (strpos($op, "be") !== false) {
                //  $val = str_replace("<=","",$val);
                $betArray = explode('|',$value);
                $where->$predicate->between($field, $betArray[0], $betArray[1]);
            }
            else {
                $where->$predicate->equalTo($field, trim($value));
            }
        }



    }


    public function parsePredicateAnd($value,&$where,$field){

        if(is_array($value)){
            if(is_array($value[self::$VALUE])) {
                foreach ($value[self::$VALUE] as $key => $val) {
                    $this->buildOrAndPredicateWhereToWhere($val,$where,$field,self::$AND);
                }
                return;
            }
        }
        $this->buildOrAndPredicateWhereToWhere($value,$where,$field,self::$AND);

    }

    public function checkIfIn($value){

        foreach($value as $key=>$val) {
            If (strpos($val['operator'], "ne") !== false /*|| strpos($val, "!=") !== false*/) {
                return false;
            } elseIf (strpos($val['operator'], "eq") !== false /*|| strpos($val['operator'], "=") !== false*/) {
                return false;
            } elseIf (strpos($val['operator'], "gt") !== false /*|| strpos($val['operator'], ">") !== false*/) {
                return false;
            } elseIf (strpos($val['operator'], "lt") !== false /*|| strpos($val['operator'], "<") !== false*/) {
                return false;
            } elseIf (strpos($val['operator'], "ge") !== false /*|| strpos($val['operator'], ">=") !== false*/) {
                return false;
            } elseIf (strpos($val['operator'], "le") !== false /*|| strpos($val['operator'], "<=") !== false*/) {
                return false;
            }
        }
        return true;
    }


    public function createQuery($value,&$where,$field,&$last,$FilterData)
    {
        if (is_array($value)) { //needs or
            if(sizeof($FilterData[$field]) > 1 && $this->checkIfIn($value)){
                foreach($value as $key=>$val){
                    $inThisValues[] = $val['value'];
                    //unset ($value[$key][self::$OPERATOR]);
                    //unset ($value[$key][self::$MODIFIER]);
                }
                $where->in($field,$inThisValues);
            }
            else {
                //foreach ($value as $key => $value) {
                if ($last == self::$AND) {
                    $this->parsePredicateAnd($value[0], $where, $field);
                    $last = self::$OR;
                } else {
                    $this->parsePredicateOr($value[0], $where, $field);
                    $last = self::$OR;

                }
            }
        }
        else {//needs and
            $this->parsePredicateAnd($value,$where,$field);
            $last = self::$AND;
        }

    }


    public function insert($data)
    {
        $result = $this->tableGateway->insert($data);
        return $result;
    }



    public function deleteDataByParams(array $FilterData)
    {
        $rsArray = array();
        if( count($FilterData) <= 0 )return $rsArray;

        $delete = $this->tableGateway->getSql()->delete();

        $where = new Where();
        foreach($FilterData as $field=>$value){
            if(strpos($value,"%")!== false){
                $where->AND->like($field, $value);
            }
            else {
                $where->AND->equalTo($field, $value);
            }
        }
        $delete->where($where);
        $rs = $this->tableGateway->deleteWith($delete);
        return $rs;
    }

}
