<?php

namespace Inheritance\Model;

use Zend\Db\TableGateway\TableGateway;
use Inheritance\Model\ErrorException;

/**
 * Class PumpsTable
 * @package Inheritance\Model
 */
class ListsTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    public function fetchAll()
    {
        /* $resultSet = $this->tableGateway->select();
         return $resultSet;*/

        $rsArray=array();
        $rs= $this->tableGateway->select();
        foreach($rs as $r) {
            $rsArray[]=get_object_vars($r);
        }

        return $rsArray;
    }



    public function getInheritance($id)
    {
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row;
    }



    public function saveInheritance(Inheritance $Inheritance)
    {
        $data = array(
            'from_date' => $Inheritance->from_date,
            'command'  => $Inheritance->command,
            'drug'  => $Inheritance->drug,
            'daily_dosage'  => $Inheritance->daily_dosage,
            'units'  => $Inheritance->units,
            'part_1'  => $Inheritance->part_1,
            'part_2'  => $Inheritance->part_2,
            'user_name' => $Inheritance->user_name,
            'notes' => $Inheritance->notes,
        );

        $id = (int)$Inheritance->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
            $Inheritance->id = $id;
        } else {
            if ($this->getInheritance($Inheritance->id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Inheritance id does not exist');
            }
        }
        return (array) $Inheritance;
    }

    public function deleteInheritance($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }


    public function getParentList(){
        $lang_id = empty($_SESSION['language_choice']) ? '1' : $_SESSION['language_choice'];
        $sql = "SELECT lo.option_id, 
                IF(LENGTH(ld.definition),ld.definition,lo.title) AS title 
                FROM list_options AS lo 
                LEFT JOIN lang_constants AS lc ON lc.constant_name = lo.title 
                LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND 
                ld.lang_id = '$lang_id' 
                WHERE lo.list_id = 'lists' AND (ld.definition LIKE 'מב\"ר%' OR  ld.definition LIKE 'מב&quot;ר%')
                ORDER BY IF(LENGTH(ld.definition),ld.definition,lo.title), lo.seq";

        $statement = $this->tableGateway->adapter->createStatement($sql);
        $return = ErrorException::execute($statement);
        return $return;
    }

    public function get_list_option_permission($clinic_id){
        $listOptionPermissionTable = new TableGateway('moh_list_option_permission', $this->tableGateway->getAdapter());

        $ress = $listOptionPermissionTable->select(array('networking_clinic_id' => $clinic_id,'edit' => 1));
        $array = array();

        foreach($ress as $row) {
            $row = get_object_vars($row);
            $array[] = $row['list_option_id'];
        }

        return $array;
    }

    public function createTable($table){
        $ress = "SHOW CREATE TABLE {$table}";
        $statement = $this->tableGateway->adapter->createStatement($ress);
        $return = ErrorException::execute($statement);
        $row = null;
        if($return){
            foreach($return as $row){
                $row = $row['Create Table'];
                break;
            }
        }
        $sql = "DROP TABLE IF EXISTS " . $table . ";";
        $sql .= $row;

        return $sql;
    }




    public function copyListOptionsTable($sync_id = array(),$toAdapter){

        $MAX_PAKET = 10000;

        $table = 'list_options';

        $tableAdapter = new TableGateway($table, $this->tableGateway->getAdapter());

        $list_id_where = null;
        foreach($sync_id as $sync_row){
            $list_id_where .= "'";
            $list_id_where .= $sync_row;
            $list_id_where .= "',";
        }

        $list_id_where = trim($list_id_where,',');

        $ress = "SELECT * FROM {$table} WHERE list_id in({$list_id_where}) OR (list_id = 'lists' AND option_id in({$list_id_where}))";

        $statement = $tableAdapter->adapter->createStatement($ress);
        $res = ErrorException::execute($statement);

        $updateTable = null;

        foreach($sync_id as $value){
            $updateTable .= "DELETE FROM {$table} WHERE list_id = 'lists' AND option_id = '{$value}';";
            $updateTable .= "DELETE FROM {$table} WHERE list_id = '{$value}';";
        }


        $sql = $updateTable;

        $columns = null;
        $value = '';

        $i = 0;
        $c = 1;

        foreach($res as $row) {
            $content = null;

            foreach($row as $values){
                $content .= "'".addslashes($values)."',";
            }


            $value .= "(". trim($content,',')."),";

            if($i == 0){
                $columns = "(".implode(", ",array_keys($row)).")"."\n";
            }

            if($i == $MAX_PAKET OR $c == $res->count()){

                $value = trim($value,',');
                $value = $value . ";";
                $tableAdapterAnother = new TableGateway($table, $toAdapter);
                $sql .= "INSERT INTO {$table} {$columns} VALUES {$value}";
                $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
                $statement = $tableAdapterAnother->adapter->createStatement($sql);
                ErrorException::execute($statement);
                unset($statement);
                $sql = null;
                $value = null;
                $i = 0;

            }

            $c++;
            $i++;
        }

        // Here we push lang of list option to another adapter
        $this->copyListOptionsLang($sync_id,$toAdapter);
    }

    private function copyListOptionsLang($sync_id = array(),$toAdapter){

        $table = 'list_options';

        $tableAdapter = new TableGateway($table, $this->tableGateway->getAdapter());

        $list_id_where = null;
        foreach($sync_id as $sync_row){
            $list_id_where .= "'";
            $list_id_where .= $sync_row;
            $list_id_where .= "',";
        }

        $list_id_where = trim($list_id_where,',');

        $ress = "SELECT title FROM list_options as lo WHERE (list_id in({$list_id_where}) OR (list_id = 'lists' AND option_id in({$list_id_where}))) AND (title NOT in( SELECT constant_name FROM lang_constants))";

        $tableAdapterAnother = new TableGateway($table, $toAdapter);
        $statement = $tableAdapterAnother->adapter->createStatement($ress);
        $res = ErrorException::execute($statement);
        $rowCount = (empty($res)) ? 0 : $res->count();

        if($rowCount){
            $list_title_where = null;
            foreach($res as $row){
                $list_title_where .= "'";
                $list_title_where .= $row['title'];
                $list_title_where .= "',";
            }
            $list_title_where = trim($list_title_where,',');

            $res = null;
            $ress = "SELECT distinct constant_name FROM lang_constants WHERE constant_name in({$list_title_where}) ";
            $statement = $tableAdapter->adapter->createStatement($ress);
            $res = ErrorException::execute($statement);


            $sql = null;
            $columns = null;
            $value = '';

            $c = 1;
            $rowCount = (empty($res)) ? 0 : $res->count();
            if($rowCount){

                foreach($res as $row) {
                    $content = null;

                    foreach($row as $values){
                        $content .= "'".addslashes($values)."',";
                    }

                    $value .= "(". trim($content,',')."),";

                    if($c == 1){
                        $columns = "(".implode(", ",array_keys($row)).")"."\n";
                    }

                    if($c == $rowCount){

                        $value = trim($value,',');
                        $value = $value . ";";
                        $sql .= "INSERT INTO lang_constants {$columns} VALUES {$value}";
                        $value = null;
                        break;
                    }

                    $c++;
                }

                $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
                $statement = $toAdapter->query($sql);
                ErrorException::execute($statement);
            }


        if($rowCount){
            $res = null;
            $ress = "SELECT distinct cons_id,constant_name FROM lang_constants WHERE constant_name in({$list_title_where}) ";
            $statement = $tableAdapterAnother->adapter->createStatement($ress);
            $res = ErrorException::execute($statement);
            $another_lang_constants = array();

            foreach($res as $row){
                $another_lang_constants[$row['constant_name']] = $row['cons_id'];
            }

            $res = null;
            $ress = "SELECT lang_id,lang_code  FROM lang_languages";
            $statement = $tableAdapterAnother->adapter->createStatement($ress);
            $res = ErrorException::execute($statement);
            $another_lang_languages = array();
            foreach($res as $row){
                $another_lang_languages[$row['lang_code']] = $row['lang_id'];
            }

            $res = null;
            $ress = "SELECT ld.def_id,lc.constant_name as cons_id,lang.lang_code as lang_id,ld.definition
                     FROM `lang_definitions` ld
                     inner join lang_constants lc on lc.cons_id = ld.cons_id
                     inner join lang_languages lang on lang.lang_id = ld.lang_id  
                     WHERE lc.constant_name in({$list_title_where})";
            $statement = $tableAdapter->adapter->createStatement($ress);
            $res = ErrorException::execute($statement);

            $sql = null;
            $columns = null;
            $value = '';

            $c = 1;

            $rowCount = (empty($res)) ? 0 : $res->count();
            if($rowCount){

                foreach($res as $row) {
                    $content = null;

                    foreach($row as $key => $values){
                        if($key == 'cons_id'){
                            $content .= "'".addslashes($another_lang_constants[$values])."',";
                        }else if($key == 'lang_id'){
                            $content .= "'".addslashes($another_lang_languages[$values])."',";
                        }else{
                        $content .= "'".addslashes($values)."',";
                        }
                    }

                    $value .= "(". trim($content,',')."),";

                    if($c == 1){
                        $columns = "(".implode(", ",array_keys($row)).")"."\n";
                    }

                    if($c == $rowCount){

                        $value = trim($value,',');
                        $value = $value . ";";
                        $sql .= "INSERT INTO lang_definitions {$columns} VALUES {$value}";
                        $value = null;
                        break;
                    }

                    $c++;
                }
            }

              $statement = $toAdapter->query($sql);
              ErrorException::execute($statement);


        }

        }

    }


    public function storeListOptionPermission($clinic_id,$list_id = array()){

        $listOptionPermissionTable = new TableGateway('moh_list_option_permission', $this->tableGateway->getAdapter());

        $listOptionPermissionTable->update(array('edit' => 0),array('networking_clinic_id' => $clinic_id));

        if(!empty($clinic_id)){

            foreach ($list_id as $id){

                $res = $listOptionPermissionTable->select(array('list_option_id' => $id,'networking_clinic_id' => $clinic_id));
                $row = $res->current();

                if(empty($row)){
                    $listOptionPermissionTable->insert(array(
                        'list_option_id' => $id,
                        'networking_clinic_id' => $clinic_id,
                        'edit' => 1,
                    ));
                }else{
                    $listOptionPermissionTable->update(array('edit' => 1),array('list_option_id' => $id,'networking_clinic_id' => $clinic_id));
                }
            }
        }

    }

}