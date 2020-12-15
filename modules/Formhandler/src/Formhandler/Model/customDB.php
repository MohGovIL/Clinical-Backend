<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 8/16/16
 * Time: 1:46 PM
 */

namespace Formhandler\Model;

use Laminas\Exception;
use Laminas\Db\Sql\Sql;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\ValidatorChain;

class customDB implements InputFilterAwareInterface
{

    const LISTS_TABLE = 'list_options';
  //TODO make this dynamic
    public static $inputsValidations = array();

    /* This function gets a matrix of fields pulled from couchDb
     * search for the required field and update the inputsValidations
     * variable of the class accordingly
     */

    public function validationsInit ($fields_matrix,$fildes_names){
        $this::$inputsValidations=array();
        foreach ($fildes_names as $key => $field) {  //*Go on all fields of the form
            if($fields_matrix[$field]!=null){
                if (array_key_exists('required', $fields_matrix[$field]['attributes'])) {
                    array_push($this::$inputsValidations, array('name' => $field, 'required' => $fields_matrix[$field]['attributes']['required'] === 'true' ? true : false));

                }
            }

            if   ($fields_matrix[$field]['attributes']['validators']!=null){
                array_push($this::$inputsValidations,array('name' => $field,"dateFormNow"=>array(
                    'date' => array(
                        'pattern' => "(\d+(\.\d+)?)",
                        'message' => 'Only numbers'                    ))));

            }

        } // end foreach


    }

    public $sql;
    public function __construct($dbAdapter)
    {

        $this->sql = new Sql($dbAdapter);
    }

    public function getEncounterDate() {
        $sql = "SELECT date as edate FROM form_encounter WHERE encounter='".$_SESSION['encounter']."' AND pid='".$_SESSION['pid']."'LIMIT 1";
        $res = sqlStatement($sql);
        $row = sqlFetchArray($res);
        if($row) {
            $row = array_reverse($row);
        }
        return $row['edate'];
    }

    public function getCurrentLang()
    {
        $select = $this->sql->select();
        $select->from('lang_languages');
        $select->where(array('lang_id' => $_SESSION['language_choice']));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $row = $result->current();
        return $row;
    }

    public function getSqlReportTable($parms)
    {
        if(strpos($parms['form'],"form_")!=0 )
            $parms['form']="form_".$parms['form'];
        // prevent sql injection - make sure that 'form' parameters is name of table in DB.
        $sql = "SELECT TABLE_NAME FROM information_schema.tables where table_schema='" . $GLOBALS['dbase'] . "' and TABLE_NAME LIKE 'form_%'";
        $res = sqlStatement($sql);
        $tables = array();
        while ($row = sqlFetchArray($res))
        {
            $tables[]=$row['TABLE_NAME'];
        }

        if(!in_array($parms['form'], $tables)){
            throw new \Exception('Invalid table name');
        }

        $sql = "SELECT * FROM " .$parms['form'] . " WHERE id=? AND pid = ? AND encounter = ?";
        $res = sqlStatement($sql, array($parms['id'],$parms['pid'], $parms['encounter']));
        $row = sqlFetchArray($res);
        if($row) {
            $row = array_reverse($row);
        }
        return $row;
    }

    public function getFirstFormNotDeleted($pid,$form_name)
    {

        $sql = "SELECT * FROM forms WHERE pid = ? AND formdir = ? AND deleted = ? ORDER BY date ASC LIMIT 1";
        $res = sqlStatement($sql, array($pid,$form_name,"0"));
        $row = sqlFetchArray($res);
        if($row) {
            $row = array_reverse($row);
            return $row;
        }
        else{
            return null;
        }
    }

    public function getLastFormNotDeleted($pid,$form_name,$id= "0",$date="0")
    {
        if ($date=="0" || $id== "0"){
            $date=date('Y-m-d');

            $sql = "SELECT * FROM forms WHERE pid = ? AND formdir = ? AND deleted = ? AND DATE(date) <= ? ORDER BY date DESC LIMIT 1";
            $res = sqlStatement($sql, array($pid,$form_name,"0",$date));
            $row = sqlFetchArray($res);

            if($row) {
                $row = array_reverse($row);
                return $row;
            }
            else{
                return null;
            }

        }

        $sql = "SELECT * FROM forms WHERE pid = ? AND formdir = ? AND deleted = ? AND form_id < ? AND DATE(date) = ? ORDER BY date DESC LIMIT 1";
        $res = sqlStatement($sql, array($pid,$form_name,"0",$id,$date));
        $row = sqlFetchArray($res);
        if($row) {
            $row = array_reverse($row);
            return $row;
        }
        else{

            $sql = "SELECT * FROM forms WHERE pid = ? AND formdir = ? AND deleted = ? AND DATE(date) < ? ORDER BY date DESC LIMIT 1";
            $res = sqlStatement($sql, array($pid,$form_name,"0",$date));
            $row = sqlFetchArray($res);

            if($row) {
                $row = array_reverse($row);
                return $row;
            }
            else{
                 return null;
            }
       }
    }

    public function getPaymentsTicketsOfTheForm($list)
    {

    $sql = "SELECT billing_type,paid,billing_date,pay_date FROM `moh_vac_patient_billings` WHERE pid =? AND form_name=? AND form_id=? ;";

    $res = sqlStatement($sql,$list);

    while ($row = sqlFetchArray($res))
    {
        $rows[$row['billing_type']]=["paid"=>$row['paid'],"billing_data"=>$row['billing_date'],"pay_date"=>$row['pay_date']];
    }
    return $rows;
    }
    /**return List data
     * @param $list
     * @return array
     */
    public function getListParams($list){

        $sql = "SELECT lo.* ," .
            "IF(LENGTH(ld.definition),ld.definition,lo.title) AS trans_title " .
            "FROM list_options as lo " .
            "LEFT JOIN lang_constants AS lc ON lc.constant_name = lo.title " .
            "LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND " .
            "ld.lang_id = ? " .
            "WHERE lo.activity=1 AND lo.list_id = ? ".
            "ORDER BY lo.seq, trans_title";
        $res = sqlStatement($sql, array($_SESSION['language_choice'],$list));

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }
        return $rows;
    }

    /**return Column data
     * @param $list
     * @return array
     */
    public function getColumnDataWhere($table,$columns,$where,$order_by){

        $where = str_replace("*","'",$where);
        $columns= str_replace("*","`",$columns);

        $sql ="SELECT ". $columns ." FROM ". $table . ($where ? " WHERE ". $where ." ":" ");

        $sql .= $order_by? $order_by : "";

        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }
        return $rows;
    }
    /**return Column data
     * @param $list
     * @return array
     */
    public function getColumnBlobData($table,$column,$pid=null,$id=null,$encounter=null){

        $sql = " SELECT `". $column."` FROM ".$table;
        if($pid || $id || $encounter){
            $sql.=" WHERE ";

            if($pid ){
                    $sql.= "pid = $pid";
            }
            if( $id ){

                if($pid)
                    $sql.= " AND ";

                $sql.= "id = $id";
            }
            if( $encounter){
                if($pid || $id)
                    $sql.= " AND ";

                $sql.= "encounter = $encounter";
            }


        }

        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }
        return $rows[0][$column];
    }

    /**
     * @param $list
     * @param null $option_id
     * @param null $value_default
     * @return mixed
     */
    public function getListParamsByOptionIdEx($list, $option_id = null, $option_default=false, $option_field=null){

        $sql = "SELECT * FROM list_options WHERE activity=1 and list_id = ? ".($option_id > 0 ? " and option_id= ?" : "")." ORDER BY seq";
        $res = sqlStatement($sql, ($option_id > 0 ? array($list, $option_id) : array($list) ));
        while ($row = sqlFetchArray($res)) {
            $rows[$row['option_id']]=$row;
        }
        if( $option_id == null) {
            $option_id = 0;
            $rows[$option_id][$option_field] = '-';
        }
        return ($option_default ? ($option_field ? $rows[$option_id][$option_field] : $rows[$option_id]) : $rows);
    }

    /**return List data
     * @param $list
     * @return array
     */
    public function getListParamsByOptionId($list){

        $sql = "SELECT * FROM list_options WHERE activity=1 and list_id = ? ORDER BY seq";
        $res = sqlStatement($sql, array($list));
        while ($row = sqlFetchArray($res)) {
            $rows[$row['option_id']]=$row;
        }
        return $rows;
    }

    public function selectDataFromDB($table,$fields,$id = null,$where = null,$valueFromList=null){

        $sql = "SELECT $fields FROM $table";

        if(!is_null($id)){
            $sql.=" WHERE id=". $id;
        }
        if(!is_null($where)){
            $sql.= (!is_null($id)) ? " AND" : " WHERE";
            $sql.=" " . $where;
        }

        //if is form table get only active forms
        if (strpos($table, 'form_') === 0) {

            if (!is_null($id) || !is_null($where)) {
                $sql .= " AND id IN (SELECT form_id FROM forms WHERE ";
                if (!is_null($where)) {
                    $sql .= $where . " AND deleted='0'";
                } else {
                    $sql .= " deleted='0' ";
                }

                $sql .= " )";

            } else {
                $sql .= " WHERE id IN (SELECT form_id FROM forms WHERE deleted='0')";
            }

        }





            $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            if(!is_null($valueFromList)){

                foreach ($row as $key => $val){
                    $row[$key] = xlt($this->getValueFromList($val,$valueFromList));
                }

            }
            $rows[]=$row;
        }

        return $rows;
    }
    public function selectDataFromForms($tableName,$encounter,$id){

        $sql = "SELECT * FROM $tableName";
        if(!is_null($encounter) && trim($encounter)!='') {
            $sql .= " WHERE encounter=" . $encounter . " AND id=" . $id;
        }
        else{
            $sql .= " WHERE  id=" . $id;
        }
        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }

        return $rows;
    }
    public function selectMaxDataFromDB($table,$fields,$id){

        $sql = "SELECT $fields FROM $table";


            $sql.=" WHERE pid=$id AND id  in (SELECT MAX(id) FROM $table WHERE pid=$id)";



        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }

        return $rows;
    }

    public function selectPatientDataFromDB($table,$fields,$pid = null){

        if(!$pid)
            return null;
        $sql = "SELECT $fields FROM $table";

        if(!is_null($pid)){
            $sql.=" WHERE pid=". $pid;
        }


        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }

        return $rows;
    }

    public function selectFromDB($table,$fields,$id = null){

        $sql = "SELECT $fields FROM $table";

        if(!is_null($id)){
            $sql.=" WHERE ". $id;
        }


        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }

        return $rows[0];
    }

    public function selectFromDBWhere($table,$fields,$where){

        $sql = "SELECT $fields FROM $table";

        if(!is_null($where)){
            $sql.=" WHERE ". $where;
        }


        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }

        return $rows[0];
    }

    public function selectExposureFromDB($occupation){


        $sql = " SELECT B.title   FROM  list_options B   WHERE B.list_id = 'moh_vaccination_plan'  AND FIND_IN_SET (B.option_id , (SELECT A.codes FROM list_options A WHERE A.list_id = 'moh_occupations' AND A.option_id = ? ))";
        $res = sqlStatement($sql, $occupation);

        $result = array();
        while ($row = sqlFetchArray($res)){
            $result[] = xl($row['title']);
        }

        if(is_null($result)){
            return "";
        }

        return $result;
    }


    public function getValueFromList($id, $list,$field='title'){

        $sql = "SELECT ".$field." FROM " . self::LISTS_TABLE . " WHERE list_id = '" . $list . "' AND option_id = '" .  $id . "'";

        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }
        return $rows[0]['title'];
    }


    public function getValueFromTable($field,$table,$whereField,$whereValue){

        $sql = "SELECT ".$field." FROM " . $table . " WHERE ".$whereField." = '" . $whereValue ."'";

        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }
        return $rows[0][$field];
    }


    public function selectPatientDataFromDBMaxDate($table,$fields,$pid = null,$form_id){

        if(!$pid)
            return null;



        $sql = "SELECT a.$fields,b.deleted FROM $table a LEFT JOIN forms b ON (a.encounter = b.encounter AND a.pid = b.pid AND a.id = b.form_id) ";

        if(!is_null($pid)){
            if($form_id<=0){//this is a new form

                $sql.=" WHERE a.pid=". $pid ." AND  a.id = (SELECT max(k.id) FROM $table  k LEFT JOIN forms f ON (k.encounter = f.encounter AND k.pid = f.pid AND k.id = f.form_id)  WHERE k.pid=".$pid." AND  f.deleted = 0 ) ";
            }
            else{
                $sql.=" WHERE a.pid=". $pid ." AND  a.id = (SELECT max(k.id) FROM $table  k LEFT JOIN forms f ON (k.encounter = f.encounter AND k.pid = f.pid AND k.id = f.form_id)  WHERE k.pid=".$pid." AND (k.id<".$form_id." ) AND f.deleted=0) ";
            }

        }


        $res = sqlStatement($sql);

        while ($row = sqlFetchArray($res))
        {
            $rows[]=$row;
        }

        return $rows;
    }


    public function saveForm($tableParm,$tableName,$formName){

        //must be after the Redirect
        //save the form data into the form table
        $tableParm['groupname']=$tableParm['authProvider'];
        unset ($tableParm['authProvider']);
        $tableParm['user']=$tableParm['authUser'];
        unset ($tableParm['authUser']);
        unset ($tableParm['form_name']);
        //$tableParm['form_name']=$tableName;
        $encounter=$tableParm['encounter'];
        $existVitalsForm = false;

        if($tableName=="form_vitals")
        {
            $existVitalsForm = $this->isExistVitalsForm($tableParm['pid'], $tableParm['encounter']);
            unset ($tableParm['encounter']);
            $formName = 'Vitals';
            $tableParm['activity'] = 1;
        }
        $table_name_params=explode(",",$tableName);
        if(sizeof($table_name_params)>1 || $existVitalsForm)
        {
            if($table_name_params[0]=="form_vitals")
            {
                unset ($tableParm['encounter']);
            }

            return $this->updateForm($tableParm,$tableName,$formName, $tableParm['pid']);
        }


        $result =$this->customInsert($tableParm,trim($tableName));
        $newId=  $result->getGeneratedValue();

        //register the form report into forms table
        $reportTableName="forms";
       /* if($tableName=="form_vitals") {
            $reportKeys = array('activity', 'date', 'encounter', 'form_name', 'form_id', 'pid', 'user', 'groupname', 'authorized', 'deleted', 'formdir');
            $reportValues = array("1", date("Y-m-d H:i:s"), $encounter, "Vitals", $newId, $tableParm['pid'], $tableParm['user'], $tableParm['groupname'], $tableParm['authorized'], '0', $tableName);
        } else {

        }*/

        $reportKeys=array ('date', 'encounter', 'form_name', 'form_id', 'pid', 'user', 'groupname', 'authorized', 'deleted', 'formdir') ;
        $tableName=$this->str_replace_first("form_","",$tableName);
        $reportValues=array(date("Y-m-d H:i:s"), $encounter, $formName,$newId,  $tableParm['pid'], $tableParm['user'], $tableParm['groupname'],  $tableParm['authorized'], '0', $tableName);
        $reportParm=array_combine($reportKeys,$reportValues);
        $this->customInsert($reportParm,$reportTableName);
        return $newId;

    }
    /*
     * updating a givien row in sql table
     */

    private function str_replace_first($from, $to, $content)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }

    public function updateForm($tableParm,$tableName,$formName,$id){

        $form_id=$id;
        //must be after the Redirect
        //save the form data into the form table
        if(isset($tableParm['authProvider']))
        $tableParm['groupname']=$tableParm['authProvider'];
        unset ($tableParm['authProvider']);
       /* if(isset($tableParm['authUser']))
        $tableParm['user']=$tableParm['authUser'];*/
        unset ($tableParm['authUser']);
        unset ($tableParm['form_name']);

        //save to other table change the id
        $table_name_params=explode(",",$tableName);
        if(sizeof($table_name_params)>1)
        {

            if($table_name_params[1]=='$patient_id_DB')
            {

                $table_name_params[1]='id';
                $id = $_SESSION['pid'];
                $tableName = $table_name_params[0];

            }
            else {

                $id = $tableParm[$table_name_params[1]];
                $tableName = $table_name_params[0];
            }

                unset ($tableParm['pid']);
                unset ($tableParm["encounter"]);
                unset ($tableParm["authProvider"]);
                unset ($tableParm["authUser"]);
                unset ($tableParm["authorized"]);
                unset ($tableParm["groupname"]);
                unset ($tableParm["user"]);
        }

        if($tableName=='form_vitals')
        {
            $select = $this->sql->select();
            $select->from($tableName );

            $form_vitals['pid']=$tableParm['pid'];
            //$form_vitals['id']=$form_id;

            $select->where( $form_vitals );

            $statement  =$this->sql->prepareStatementForSqlObject( $select );
            $result    = $statement->execute();
            $row = $result->current();

            unset ($tableParm["encounter"]);
            if(!$row)
            {
                $this->customInsert($tableParm,$tableName);
            }
        }
        if($row) {
            $id = (int)$row['id'];
        }
        $date=$this->getFormDate($id,$tableName);
        $tableParm['date']=$date;
        $update = $this->sql->update();
        $update->table($tableName );
        $update->set( $tableParm );
        if(sizeof($table_name_params)>1) {
            $update->where(array($table_name_params[1] => (int)$id));
        }
        else{
            $update->where(array('id' => (int)$id));
        }
        $statement  =$this->sql->prepareStatementForSqlObject( $update );
        $result    = $statement->execute();
        return  $result;
       // exit;
    }

    /*
     * insert a row to a sql table
     */
    public function customInsert($tableParm,$tableName){
        $record = $this->sql->insert($tableName);
        $record->values($tableParm);//On use
        $statement = $this->sql->prepareStatementForSqlObject($record);
        $result = $statement->execute();
        return  $result;
       // exit;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            foreach(self::$inputsValidations as $input) {
                $inputFilter->add($input);
            }

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function getFormDate($id,$tableName){
        $select = $this->sql->select();
        $select->from($tableName);
        $select->where(array('id' => $id));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        return $row['date'];
    }

    public function getUserName($id){
        $tableName="users";
        $select = $this->sql->select();
        $select->from($tableName);
        $select->where(array('id' => $id));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        return $row['username'];
    }

    public function getUserFnameLname($id){
        $tableName="users";
        $select = $this->sql->select();
        $select->from($tableName);
        $select->where(array('id' => $id));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        return $row['fname']. " ".$row['lname'];
    }

    private function isExistVitalsForm($pid,$encounterId){
        $select = $this->sql->select();
        $select->from('forms');
        $select->where(array('pid' => $pid, 'encounter' => $encounterId, 'formdir' => 'vitals','deleted' => '0'));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        return empty($row) ? false : true;
    }

    public function getFormsAclFromRegistry($form_name){
        $select = $this->sql->select();
        $select->from('registry');
        $select->where(array('directory' => $form_name));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        return $row['aco_spec'];
    }


    public function getCurrentUserGender()
    {
        $select = $this->sql->select();
        $select->from('patient_data');
        $select->where(array('pid' => $_SESSION['pid']));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $row = $result->current();
        return strtolower($row['sex']);
    }

    public function getCurrentUserFullName()
    {
        $select = $this->sql->select();
        $select->from('patient_data');
        $select->where(array('pid' => $_SESSION['pid']));
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $row = $result->current();
        return $row['fname'].' '.$row['lname'];
    }



    public function getCurrentEncounterDate()
    {
        $encounterId=$_SESSION["encounter"];
        $sql="SELECT date FROM form_encounter WHERE encounter=?";
        $res = sqlStatement($sql, array($encounterId));
        $row = sqlFetchArray($res);

        return substr($row['date'],0,10);
    }


}
