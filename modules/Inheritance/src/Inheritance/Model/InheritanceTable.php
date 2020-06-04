<?php

namespace Inheritance\Model;

use Laminas\Db\TableGateway\TableGateway;
use Inheritance\Model\ErrorException;

/**
 * Class PumpsTable
 * @package Inheritance\Model
 */
class InheritanceTable
{


    const HEBREW_LANG = 7;
    const ENGLISH_LANG = 1;

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
       /* $resultSet = $this->tableGateway->select();
        return $resultSet;*/

        $rsArray = array();
        $rs = $this->tableGateway->select();
        foreach ($rs as $r) {
            $rsArray[] = get_object_vars($r);
        }

        return $rsArray;
    }

    public function createTable($table){

        $ress = "SHOW CREATE TABLE {$table}";
        $statement = $this->tableGateway->getAdapter()->query($ress);

        $return = ErrorException::execute($statement);
        $row = null;
        if ($return) {
            $needle = 'InnoDB';
            foreach ($return as $row) {
                $row = substr($row['Create Table'], 0, mb_strpos($row['Create Table'], $needle) + mb_strlen($needle)) . ";";
                break;
            }
        }
        $sql = "DROP TABLE IF EXISTS " . $table . ";";
        $sql .= $row;

        return $sql;
    }


    public function getRatesTable($toAdapter){

        $statement = $this->tableGateway->getAdapter()->query("SELECT * FROM codes WHERE code = 9001");
        $res = ErrorException::execute($statement);

        $columns = null;
        $value = '';
        $i = 1;
        $c = 1;
        $pr_id = 0;
        $sql = "DELETE FROM codes WHERE code = 9001;";
        if ($res->count()) {

            foreach ($res as $row) {
                $content = null;

                foreach ($row as $values) {

                    if($i == 1){
                        $pr_id = $values; // Store code id for update prices table at column pr_id
                        $i = 2;
                    }

                    $content .= "'" . addslashes($values) . "',";
                }

                $value .= "(" . trim($content, ',') . "),";

                if ($c == 1) {
                    $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";
                }

                if ($c == $res->count()) {
                    $value = trim($value, ',');
                    $value = $value . ";";
                    $sql .= "INSERT INTO codes {$columns} VALUES {$value}";
                    $value = null;
                    break;
                }

                $c++;
            }
        }

        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
        $statement = $toAdapter->query($sql);
        $res = ErrorException::execute($statement);


        if($pr_id AND $res){
                $statement = $this->tableGateway->getAdapter()->query("SELECT * FROM prices WHERE pr_id = {$pr_id}");
                $res = ErrorException::execute($statement);

                $columns = null;
                $value = '';
                $c = 1;
                $sql = "DELETE FROM prices WHERE pr_id = {$pr_id};";
                if ($res->count()) {

                    foreach ($res as $row) {
                        $content = null;

                        foreach ($row as $values) {
                            $content .= "'" . addslashes($values) . "',";
                        }

                        $value .= "(" . trim($content, ',') . "),";

                        if ($c == 1) {
                            $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";
                        }

                        if ($c == $res->count()) {
                            $value = trim($value, ',');
                            $value = $value . ";";
                            $sql .= "INSERT INTO prices {$columns} VALUES {$value}";
                            $value = null;
                            break;
                        }

                        $c++;
                    }



                    $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
                    $statement = $toAdapter->query($sql);
                    ErrorException::execute($statement);
                }

        }

    }


    public function getTranslationsTable(){


        $HEBREW_LANG = self::HEBREW_LANG;
        $ENGLISH_LANG = self::ENGLISH_LANG;

        $statement = $this->tableGateway->getAdapter()->query("SELECT languages.lang_description, 
                                                               languages.lang_code, 
                                                               constants.constant_name, 
                                                               definitions.definition 
                                                               FROM lang_definitions AS definitions 
                                                               JOIN lang_constants AS constants 
                                                               ON definitions.cons_id = constants.cons_id 
                                                               JOIN lang_languages AS languages 
                                                               ON definitions.lang_id = languages.lang_id 
                                                               WHERE definitions.lang_id IN ($HEBREW_LANG,$ENGLISH_LANG)");


        $res = ErrorException::execute($statement);

        $MAX_PAKET = 10000;

        $columns = null;
        $value = '';
        $i = 0;
        $c = 1;

        $sql = "DROP TABLE IF EXISTS lang_custom;CREATE TABLE lang_custom ( lang_description varchar(100) NOT NULL DEFAULT '', lang_code char(2) NOT NULL DEFAULT '', constant_name mediumtext, definition mediumtext ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        if ($res->count()) {

            foreach ($res as $row) {

                $content = null;

                foreach ($row as $values) {
                    $content .= "'" . addslashes($values) . "',";
                }

                $value .= "(" . trim($content, ',') . "),";

                if ($i == 0) {
                    $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";
                }

                if ($i == $MAX_PAKET OR $c == $res->count()) {
                    $value = trim($value, ',');
                    $value = $value . ";";
                    $sql .= "INSERT INTO lang_custom {$columns} VALUES {$value}";
                    $value = null;
                    $i = 0;
                }

                $c++;
                $i++;
            }
        }
        $convert = "INSERT INTO lang_constants (constant_name) SELECT custom.constant_name as constant_name FROM lang_custom AS custom WHERE NOT EXISTS (SELECT cons_id FROM lang_constants AS constants WHERE constants.constant_name = custom.constant_name) GROUP BY custom.constant_name; INSERT INTO lang_definitions(cons_id, lang_id, definition) SELECT temp.cons_id,temp.lang_id,temp.definition FROM (SELECT constants.cons_id,languages.lang_id,custom.definition FROM lang_custom AS custom JOIN lang_constants AS constants ON constants.constant_name = custom.constant_name JOIN lang_languages AS languages ON languages.lang_code = custom.lang_code AND languages.lang_description = custom.lang_description) AS temp WHERE NOT EXISTS (SELECT def.cons_id FROM lang_definitions AS def WHERE def.cons_id = temp.cons_id AND def.lang_id = temp.lang_id); UPDATE lang_definitions as def JOIN lang_constants AS constants ON constants.cons_id = def.cons_id JOIN lang_languages AS languages ON languages.lang_id = def.lang_id JOIN lang_custom AS custom ON custom.lang_code = languages.lang_code AND custom.lang_description = languages.lang_description AND custom.constant_name = constants.constant_name SET def.cons_id = constants.cons_id, def.lang_id = languages.lang_id, def.definition = custom.definition WHERE custom.definition <> def.definition;";
        $sql .= $convert;
        $sql .= "DROP TABLE IF EXISTS lang_custom;CREATE TABLE lang_custom ( lang_description varchar(100) NOT NULL DEFAULT '', lang_code char(2) NOT NULL DEFAULT '', constant_name mediumtext, definition mediumtext ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);

        return $sql;


    }

    public function getIcdTable($sync_id,$toAdapter){

            $MAX_PAKET = 10000;

            $sync_id = (int)$sync_id;
            $statement = $this->tableGateway->getAdapter()->query("SELECT * FROM codes WHERE code_type = {$sync_id} AND inheritable='1' ");
            $res = ErrorException::execute($statement);

            $columns = null;
            $value = '';
            $i = 0;
            $c = 1;
            $sql = "DELETE FROM codes WHERE code_type = {$sync_id};";
            if ($res->count()) {

                foreach ($res as $row) {
                    $content = null;

                    foreach ($row as $key => $values) {
                        $content .= "'" . addslashes($values) . "',";
                    }

                    $value .= "(" . trim($content, ',') . "),";

                    if ($c == 1) {
                        $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";
                    }

                    if($i == $MAX_PAKET OR $c == $res->count()){

                        $value = trim($value, ',');
                        $value = $value . ";";
                        $sql .= "INSERT INTO codes {$columns} VALUES {$value}";
                        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
                        $statement = $toAdapter->query($sql);
                        ErrorException::execute($statement);

                        unset($statement);
                        $sql = null;
                        $value = null;
                        $i = 0;
                    }

                    $c++;
                    $i++;
                }
            }


    }

    public function inheritAdditionalTables(){

        $tables = $this->getAdditionalTables();

        $sql = '';
        foreach($tables as $table) {

            $statement = $this->tableGateway->getAdapter()->query("SELECT * FROM {$table['table_name']} ");
            $res = ErrorException::execute($statement);

            $columns = null;
            $value = '';
            $c = 1;
            $sql .= "DELETE FROM {$table['table_name']};";
            if ($res->count()) {

                foreach ($res as $row) {
                    $content = null;

                    foreach ($row as $key => $values) {
                        $content .= "'" . addslashes($values) . "',";
                    }

                    $value .= "(" . trim($content, ',') . "),";

                    if ($c == 1) {
                        $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";
                    }

                    if ($c == $res->count()) {
                        $value = trim($value, ',');
                        $value = $value . ";";
                        $sql .= "INSERT INTO {$table['table_name']} {$columns} VALUES {$value}";
                    }

                    $c++;
                }
            }
        }
        $sql = "SET FOREIGN_KEY_CHECKS = 0;" . \Inheritance\Plugin\Transactionwrapper::wrap($sql) . "SET FOREIGN_KEY_CHECKS = 1;";
        return $sql;
    }

    public function getAdditionalTables(){
        $statement = $this->tableGateway->getAdapter()->query("SELECT table_name FROM moh_inheritance_tables ORDER BY `order` ASC");
        $tables = ErrorException::execute($statement);
        return $tables;
    }

    public function copyPermissionTable($tables,$sqlTable,$toAdapter,$download = null){

        $sql = $sqlTable;
/*
        $statement = $this->tableGateway->getAdapter()->query("SELECT id, name FROM `gacl_aro_groups`");
        $res = ErrorException::execute($statement);
        if ($res->count()) {
            $groups_array = array();
            foreach ($res as $rows) {
                $groups_array[$rows['name']] =  $rows['id'];
            }
        }

        $statement = $toAdapter->query("SELECT DISTINCT groups.name as group_id,map.aro_id as aro_id 
                                        FROM `gacl_groups_aro_map` as map
                                        LEFT JOIN gacl_aro_groups as groups on map.group_id = groups.id");
        $res = ErrorException::execute($statement);

        $columns = null;
        $value = '';
        $c = 1;
        $sql .= "TRUNCATE TABLE gacl_groups_aro_map; ALTER TABLE gacl_groups_aro_map AUTO_INCREMENT = 1;";
        if ($res->count()) {

            foreach ($res as $row) {
                $content = null;

                foreach ($row as $key => $values) {
                    if($key == 'group_id'){
                        $content .= "'" . addslashes($groups_array[$values]) . "',";
                    }else{
                        $content .= "'" . addslashes($values) . "',";
                    }
                }

                $value .= "(" . trim($content, ',') . "),";

                if ($c == 1) {
                    $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";

                }

                if ($c == $res->count()) {
                    $value = trim($value, ',');
                    $value = $value . ";";
                    $sql .= "INSERT INTO gacl_groups_aro_map {$columns} VALUES {$value}";
                    $value = null;
                    break;
                }

                $c++;
            }
        }*/

        $moduleReplaceTable = $this->createIdReplaceArray($toAdapter);

        foreach ($tables as $table) {

            if(strpos($table, 'module') !== false){
                // create tmp table and update the ids according the module id in the clinic database
                switch ($table){
                    case 'module_acl_group_settings':
                    case 'module_acl_sections':
                        $mod_id_col_name = 'module_id';
                        break;
                    case 'modules_settings':
                        $mod_id_col_name = 'mod_id';
                        break;
                }
                $this->createTmpModuleTable($table, $mod_id_col_name, $moduleReplaceTable);
            }

            $statement = null; // clear old connection
            $res = null; // clear old connection

            if(strpos($table, 'module') === false) {
                $ress = "SELECT * FROM {$table}";
            } else {
                $ress = "SELECT * FROM tmp_{$table}";
            }

            $statement = $this->tableGateway->getAdapter()->query($ress);
            $res = ErrorException::execute($statement);

            $columns = null;
            $value = '';

            $c = 1;

            if ($res->count()) {

                foreach ($res as $row) {
                    $content = null;

                    foreach ($row as $values) {
                        $content .= "'" . addslashes($values) . "',";
                    }

                    $value .= "(" . trim($content, ',') . "),";

                    if ($c == 1) {
                        $columns = "(" . implode(", ", array_keys($row)) . ")" . "\n";
                    }

                    if ($c == $res->count()) {
                        $value = trim($value, ',');
                        $value = $value . ";";
                        $sql .= "INSERT INTO {$table} {$columns} VALUES {$value}";
                        $value = null;
                        break;
                    }

                    $c++;
                }
            }

            if(strpos($table, 'module') !== false){
                $statement = $this->tableGateway->getAdapter()->query("DROP TABLE tmp_{$table};");
                $res = ErrorException::execute($statement);

            }
        }


        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
        if($download){
            return $sql;
        }
        $statement = $toAdapter->query($sql);
        ErrorException::execute($statement);

    }

    /**
     * @param $toAdapter
     * @return array
     * create array that compares between module id in zero and clinic
     */
    private function createIdReplaceArray($toAdapter){

        $statement = $this->tableGateway->getAdapter()->query('SELECT mod_id, mod_name FROM modules');
        $res = ErrorException::execute($statement);
        $replaceArray = array();
        foreach ($res as $result){
            $replaceArray[$result['mod_name']] = array('zero_id' => $result['mod_id']);
        }


        $statement = $toAdapter->query('SELECT mod_id, mod_name FROM modules');
        $res = ErrorException::execute($statement);

        foreach ($res as $result){
            $replaceArray[$result['mod_name']]['clinic_id'] = $result['mod_id'];
        }

        return $replaceArray;

    }

    /**
     * @param $table
     * @param $mod_id_col_name
     * @param $replaceArray
     * create tmp table for module acl and update the module id there as clinic
     */
    private function createTmpModuleTable($table,$mod_id_col_name, $replaceArray){

        $sqlCase  = array();
        foreach ($replaceArray as $value){

            if($value['zero_id'] != $value['clinic_id']){
                $sqlCase[] = "when $mod_id_col_name = {$value['zero_id']} then {$value['clinic_id']}";
            }
        }

        if(!empty($sqlCase)){
            $updateSql = "CREATE TABLE tmp_$table LIKE $table; INSERT tmp_$table SELECT * FROM $table;";
            $updateSql .= "UPDATE tmp_$table SET $mod_id_col_name = (case ";
            $updateSql .= implode(' ', $sqlCase);
            $updateSql .= " else $mod_id_col_name end)";

        } else {

            $updateSql = "CREATE TABLE tmp_$table LIKE $table; INSERT tmp_$table SELECT * FROM $table;";
        }

        $statement = $this->tableGateway->getAdapter()->query($updateSql);
        $res = ErrorException::execute($statement);

    }



    public function getRule(){
        $statement = $this->tableGateway->getAdapter()->query("SELECT list_options.title,list_options.option_id
                                                               FROM list_options
                                                               INNER JOIN rule_action ON rule_action.id = list_options.option_id AND list_options.option_id in (SELECT rule_id FROM clinical_plans_rules WHERE plan_id = '1_plan')
                                                               WHERE list_options.list_id = 'clinical_rules' AND list_options.activity = 1 GROUP BY rule_action.id");

        $rows = ErrorException::execute($statement);


        if ($rows->count()) {
            return $rows;
        } else {
            return array();
        }
    }

    public function copyRulesTable($tables,$sync_id,$toAdapter){

        $sync_id = "'".implode("','",$sync_id)."'";

        if(empty($sync_id)){
            return false;
        }

        // unique prefix per DB (prevent conflicts of rules)
        $prefix_replace = $GLOBALS['dbase'];

        /* remove all common rules from 'rule_action', 'rule_filter', 'rule_reminder','rule_target' before zero inherits them */
        $sql = "SELECT id FROM `rule_action` WHERE id IN({$sync_id}) GROUP BY id";
        $statement = $this->tableGateway->getAdapter()->query($sql);
        $resRules = ErrorException::execute($statement);

        $sql='';
        foreach($resRules as $row) {
            foreach($row as $values){
                $values = preg_replace('/^rule_(\d+)$/', "rule_{$prefix_replace}_$1", $values);
                $content = "'".addslashes($values)."'";
            }

            $sql .= 'DELETE FROM `rule_action` WHERE id = ' . $content . ';DELETE FROM `rule_filter` WHERE id = ' . $content . ';DELETE FROM `rule_reminder` WHERE id = ' . $content . ';DELETE FROM `rule_target` WHERE id = ' . $content . ';';
        }

        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
        $statement = $toAdapter->query($sql);
        ErrorException::execute($statement);

        /* End remove all common rules from 'rule_action' */

        $sql = '';
        foreach ($tables as $table) {
            $statement = null; // clear old connection
            $res = null; // clear old connection


            if($table == 'list_options'){
                //$sql .= "DELETE FROM {$table}  WHERE list_id = 'clinical_rules' AND option_id LIKE '%_{$prefix}_%';"; // this is work, it empty all rules
                $ress = "SELECT list_options.*
                         FROM list_options
                         INNER JOIN rule_action ON rule_action.item = list_options.option_id
                         WHERE list_id = 'rule_action' AND activity = 1
                         UNION SELECT * FROM list_options WHERE list_id = 'clinical_rules' AND option_id in ({$sync_id});";
            }else if ($table == 'rule_action_item'){
                $ress = "SELECT * FROM {$table} WHERE 1";

            }else if ($table == 'clinical_plans_rules') {
                //$sql .= "DELETE FROM {$table}  WHERE rule_id LIKE '%_{$prefix}_%';"; // this is work, it empty all rules
                $ress = "SELECT * FROM {$table} WHERE rule_id in ({$sync_id})";
            } else {
              //  $sql .= "DELETE FROM {$table}  WHERE id LIKE '%_{$prefix}_%';"; // this is work, it empty all rules
                $ress = "SELECT * FROM {$table} WHERE id in ({$sync_id})";
            }

            $statement = $this->tableGateway->getAdapter()->query($ress);
            $res = ErrorException::execute($statement);

            $columns = null;
            $value = '';

            $c = 1;

            if($res->count()){

                foreach($res as $row) {
                    $content = null;

                    foreach($row as $values){
                        $values = preg_replace('/^rule_(\d+)$/', "rule_{$prefix_replace}_$1", $values);
                        $content .= "'".addslashes($values)."',";
                    }

                    $value .= "(". trim($content,',')."),";

                    if($c == 1){
                        $columns = null;
                        foreach($row as $key => $values){
                            $columns .= "`".addslashes($key)."`,";
                        }

                        $columns = "(". trim($columns,',').")";
                    }

                    if($c == $res->count()){

                        $value = trim($value,',');
                        $value = $value . ";";
                        $sql .= "REPLACE INTO {$table} {$columns} VALUES {$value}";

                        $value = null;
                        break;
                    }

                    $c++;
                }
            }

        }


        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
        $statement = $toAdapter->query($sql);
        ErrorException::execute($statement);

    }


    public function storeFileAtPullFilesDb($sync_id,$toAdapter){
        $sql = "INSERT INTO moh_pull_files (file_name, file,file_size_bytes,file_size_mb,file_size_kb) VALUES";
        foreach ($sync_id as $file) {
            $templatedir = $GLOBALS['OE_SITE_DIR'] . "/documents/doctemplates/" . $file;
            if (file_exists($templatedir)) {
                $fp = fopen($templatedir, 'r');
                $content = fread($fp, filesize($templatedir));
                $content = addslashes($content);
                $fileSize_bytes = filesize($templatedir);
                fclose($fp);

                $fileSize_mb = number_format($fileSize_bytes / 1048576, 6);
                $file_size_kb = number_format($fileSize_bytes / 1024, 6);
                $sql .= "('$file', '$content','$fileSize_bytes','$fileSize_mb','$file_size_kb'),";
            } else {
                $_SESSION['sql_errors_exception_code'][] = "file doesn't exist: " . $templatedir;
            }
        }


        $sql = trim($sql, ',') . ";";

        $sql = \Inheritance\Plugin\Transactionwrapper::wrap($sql);
        $statement = $toAdapter->query($sql);
        ErrorException::execute($statement);
    }

}
