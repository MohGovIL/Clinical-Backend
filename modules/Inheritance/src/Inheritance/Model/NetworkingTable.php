<?php

namespace Inheritance\Model;

use Laminas\Db\TableGateway\TableGateway;
use Inheritance\Model\ErrorException;
use Interop\Container\ContainerInterface;

/**
 * Class PumpsTable
 * @package Inheritance\Model
 */
class NetworkingTable
{

    private $tableGateway;
    protected $success_sons_connection = array();
    protected $fail_sons_connection = array();
    protected $error_code;
    public function __construct(TableGateway $tableGateway, ContainerInterface $container)
    {
        $this->tableGateway = $tableGateway;
        $this->error_code = substr(md5(microtime(true)), 0, 6);
        $this->container = $container;
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

    /**
     * @throws \Exception
     */
    public function save(Networking $networking)
    {
        $data = get_object_vars($networking);

        $id = (int)$networking->id;
        if ($id == 0) {
            unset($data['id']);
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
            $networking->id = $id;
        } else {
            $update  = $this->tableGateway->update($data, array('id' => $id));
        }

        return (array) $networking;
    }

    public function updateParent($child, $newParent)
    {
        $this->tableGateway->update(array('parent' => $newParent), array('id' => $child));
    }

    public function getSingle($id)
    {
        /* $resultSet = $this->tableGateway->select();
         return $resultSet;*/

        $rsArray = array();
        $rs = $this->tableGateway->select(array('id' => $id));
        $row = $rs->current();
        return $row;
    }

    public function remove($id){
        $rs = $this->tableGateway->delete(array('id' => $id));
        return $rs;
    }


    public function sync_edit_list_permission($edit_id,$sync_id,$clinic_id,$getListsTable){

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);

        if ($rows) {

            foreach ($rows as $row) {

                $adapter = $this->zend_connection_adapter($row);
                if ($adapter == 'errors') {
                    continue;
                }

                $getListsTable->copyListOptionsTable($sync_id, $adapter);

                $listOptionsTable = new TableGateway('list_options', $adapter);

                $listOptionsTable->update(array('edit_options' => 0), array('list_id' => 'lists'));


                foreach ($edit_id as $id) {
                    $listOptionsTable->update(array('edit_options' => 1), array('list_id' => 'lists', 'option_id' => $id));
                }

            }
        }
    }

    public function download_permissions($getInheritanceTable){
        return $this->sync_permissions($_SESSION['my_client']->id,$getInheritanceTable,1);
    }
    public function sync_permissions($clinic_id,$getInheritanceTable,$download = false){

        if($download){
            $rows = $this->get_adapter_sons_by_clinic_id(0,$download);
        }else{
            $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        }

        if ($rows) {

            $tables = array(
                'gacl_acl',
                'gacl_acl_sections',
                'gacl_acl_seq',
                'gacl_aco',
                'gacl_aco_map',
                'gacl_aco_sections',
                'gacl_aco_sections_seq',
                'gacl_aco_seq',
                'gacl_aro_groups',
                'gacl_aro_groups_id_seq',
                'gacl_aro_groups_map',
                'gacl_aro_map',
                'gacl_aro_sections',
                'gacl_aro_sections_seq',
                'gacl_axo',
                'gacl_axo_groups',
                'gacl_axo_groups_map',
                'gacl_axo_map',
                'gacl_axo_sections',
                'gacl_groups_axo_map',
                'gacl_phpgacl',
                'registry' // forms permission
            );


            $sqlTable = null;
            foreach ($tables as $table) {

                $sqlTable .= $getInheritanceTable->createTable($table);
            }

            foreach ($rows as $row) {
                $adapter = $this->zend_connection_adapter($row);
                if ($adapter == 'errors') {
                    continue;
                }

                if($download) {
                    return $getInheritanceTable->copyPermissionTable($tables, $sqlTable, $adapter, $download);
                }
                else{
                    $getInheritanceTable->copyPermissionTable($tables, $sqlTable, $adapter,$download);
                }
            }
        }
    }

    public function sync_icd($clinic_id, $sync_id, $getInheritanceTable){

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        if ($rows) {
            foreach ($rows as $row) {

                $adapter = $this->zend_connection_adapter($row);

                if ($adapter == 'errors') {
                    continue;
                }

                $getInheritanceTable->getIcdTable($sync_id, $adapter);
            }
        }
    }

    public function sync_rules($clinic_id, $sync_id, $getInheritanceTable){

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        if ($rows) {

            $tables = array(
                'list_options',
                'rule_action_item',
                'clinical_plans_rules',
                'rule_action',
                'rule_target',
                'rule_filter',
                'rule_reminder',
                'clinical_rules',
            );

            foreach ($rows as $row) {


                $adapter = $this->zend_connection_adapter($row);

                if ($adapter == 'errors') {
                    continue;
                }

                $getInheritanceTable->copyRulesTable($tables,$sync_id, $adapter);
            }
        }
    }

    public function sync_template($clinic_id, $sync_id, $getInheritanceTable){

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        if ($rows) {
            foreach ($rows as $row) {

                $adapter = $this->zend_connection_adapter($row);

                if ($adapter == 'errors') {
                    continue;
                }

                $getInheritanceTable->storeFileAtPullFilesDb($sync_id, $adapter);
            }
        }
    }

    public function sync_rates($clinic_id, $getInheritanceTable){

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        if ($rows) {
            foreach ($rows as $row) {

                $adapter = $this->zend_connection_adapter($row);

                if ($adapter == 'errors') {
                    continue;
                }

                $getInheritanceTable->getRatesTable($adapter);
            }
        }
    }

    public function sync_translations($clinic_id, $getInheritanceTable){

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        if ($rows) {

            $sql = $getInheritanceTable->getTranslationsTable();

            foreach ($rows as $row) {

                $adapter = $this->zend_connection_adapter($row);

                if ($adapter == 'errors') {
                    continue;
                }

                $statement = $adapter->query($sql);
                $res = ErrorException::execute($statement);
                unset($statement);
                unset($adapter);
            }
        }
    }

    public function sync_additionals($clinic_id, $getInheritanceTable){

        $SyncRez=array();

        $rows = $this->get_adapter_sons_by_clinic_id($clinic_id);
        if ($rows) {
            $sql = $getInheritanceTable->inheritAdditionalTables();

            foreach ($rows as $row) {

                $adapter = $this->zend_connection_adapter($row);

                if ($adapter == 'errors') {
                    continue;
                }

                $statement = $adapter->query($sql);
                $res = ErrorException::execute($statement);
                unset($statement);
                unset($adapter);

                if (class_exists('PostSync\Controller\PostSyncController')) {
                    $module= new \PostSync\Controller\PostSyncController($this->container);
                    $SyncRez=$module::Sync($row);
                }


            }
        }

        return $SyncRez;

    }

    private function get_adapter_sons_by_clinic_id($clinic_id,$download = false){

        $multipledbTable = new TableGateway('networking_db', $this->tableGateway->getAdapter());

        $clinicParentSonsIdImplode = implode(',',$this->get_clinic_parent_sons($clinic_id,$download));

        $ress = "SELECT * FROM " . $multipledbTable->table . " WHERE clinic_id in({$clinicParentSonsIdImplode})";

        $statement = $multipledbTable->adapter->createStatement($ress);
        $rows = ErrorException::execute($statement);

        $array = array();
        if(count($rows) AND count($clinic_id) > 0) {
            foreach ($rows as $row) {

                $config = [
                    'driver' => 'Pdo_Mysql', // for example
                    'username' => $row['username'],
                    'password' => my_decrypt($row['password']),
                    'database' => $row['dbname'],
                    'hostname' => $row['host'],
                    'port' => $row['port'],
                ];

                $row['networking_config'] = $config;
                $array[] = $row;
            }
        }

        return $array;
    }

    private function zend_connection_adapter($row){

        $adapter = new \Laminas\Db\Adapter\Adapter($row['networking_config']);
        try {
            $adapter->getDriver()->getConnection()->connect();
        } catch (\Exception $ex) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            $errorExceptionTable = new TableGateway('error_exception', $this->tableGateway->getAdapter());
            $errorExceptionTable->insert(array(
                'networking_clinic_id' => $row['clinic_id'],
                'error_code' => $this->error_code,
                'file' => $caller['file'],
                'line' => $caller['line'],
                'error_log' => $ex,
            ));
            $this->setFailSonsConnection($row['clinic_id']);

            return 'errors';
        }


        $this->setSsuccessSonsConnection($row['clinic_id']);

        return $adapter;

    }


    public function get_clinic_parent_sons($clinic_id,$download = false){

        if($download){
            $sql = "parent = {$clinic_id}";
        }else{
            $sql = "parent = {$clinic_id} AND id != {$clinic_id}";
        }

        $ress = "SELECT id,type FROM " . $this->tableGateway->table . " WHERE {$sql}  AND valid = 1 ORDER BY parent";
        $statement = $this->tableGateway->adapter->createStatement($ress);
        $return = ErrorException::execute($statement);
        $array = array();
        foreach ($return as $row) {
            $array[] = $row['id'];
        }

        if(count($array) > 0 AND !in_array($clinic_id,$array)){
            return $array;
        }else{
            return array();
        }
    }


    function get_main_parent($parent){
        while($parent > 0){
            $rowset = $this->tableGateway->select(array('id' => $parent, 'valid' => 1));
            $row = $rowset->current();
            $parent = $row->parent;
        }

        return $row;
    }

    public function get_tree()
    {
        $this->setMyClientId();

        if ($_SESSION['my_client']->parent != 0) {
            $row = $this->get_main_parent($_SESSION['my_client']->parent);
            $html = '<div class="hv-item"><div class="hv-item-parent"> <p class="simple-card">' . $row->clinic_name . '</p></div><div class="hv-item-children one-child">';
        }

        $html .= $this->create_tree($_SESSION['my_client']->id);

        if ($_SESSION['my_client']->parent != 0) {
            $html .= '</div></div>';
        }

        $html = substr(trim($html), 0, -6);

        return $html;
    }

    public function create_tree($parent = 0, $flag = 1)
    {

        if ($flag AND $parent != 0) {
            $sql_parent = 'id = ' . $parent;
        } else {
            if (!$parent) {
                $parent = 0;
            }
            $sql_parent = 'parent = ' . $parent;
        }

        $ress = "SELECT id,parent,clinic_name,type FROM " . $this->tableGateway->table . " WHERE {$sql_parent} AND valid = 1";
        $statement = $this->tableGateway->adapter->createStatement($ress);
        $return = $statement->execute();

        $count = count($return);
        $i = 1;
        foreach ($return as $row) {

            if ($row['type']) {
                $html .= '<div class="hv-item-child"><p class="simple-card" data-type="' . $row['type'] . '" data-id="' . $row['id'] . '">' . $row['clinic_name'] . '</p>';
                if ($i == $count) {
                    $html .= '</div>';

                }
            } else {
                if ($parent) {
                    if ($row['parent'] == 0) {
                        $center = 'center';
                    }

                    $html .= '<div class="hv-item-child ' . $center . '">';
                }

                $rowset = $this->tableGateway->select(array('parent' => $row["id"], 'valid' => 1));
                $ifparents = $rowset->count();

                if ($ifparents == 1) {
                    $oneParent = 'one-child';
                }

                if ($flag) {
                    $my_clinic = 'my_clinic';
                }

                $html .= '<div class="hv-item"><div class="hv-item-parent ' . $my_clinic . '"> <p class="simple-card"  data-type="' . $row['type'] . '" data-id="' . $row['id'] . '">' . $row['clinic_name'] . '</p></div><div class="hv-item-children ' . $oneParent . '">';
            }

            if ($row['type'] == 0) {
                $html .= $this->create_tree($row["id"], 0);
                $html .= '</div>';

            }
            $html .= '</div>';
            $i++;
        }

        return $html;
    }

    public function newGetAllZeroTrees()
    {
        $this->setMyClientId();

        $allTrees = $this->newGetChildren(0);
        $this->newBuildAllZeroTrees($allTrees, 0);

        $generalDbTree = array(
            'chart' => array(
                'container' => '#custom-colored',
                'nodeAlign' => 'BOTTOM',
                'rootOrientation' => 'WEST',
                'connectors' => array(
                    'type' => 'step',
                    'style' => array(
                        'stroke' => 'black',
                        'arrow-end' => 'open-wide-long',
                    ),
                ),
                'node' => array(
                    'HTMLclass' => 'nodeExample1'
                )
            ),
            'nodeStructure' => array(
                'text' => array(
                    'name' => 'GeneralDB',
                    'data-id' => 'generaldb',
                    'data-drag' => 'false'
                ),
                'connectors' => array(
                    'type' => 'step',
                    'style' => array(
                        'stroke' => 'black',
                        'arrow-end' => 'none',
                        'stroke-dasharray' => '. '
                    ),
                ),
                'children' => array()
            )
        );
        foreach($allTrees as $tree) {
            $generalDbTree['nodeStructure']['children'][] = $tree;
        }

        return $generalDbTree;
    }

    private function newBuildAllZeroTrees(&$parents, $index)
    {
        if($index == count($parents)) {
            return;
        }
        $children = $this->newGetChildren($parents[$index]['text']['data-id']);
        if(!empty($children)) {
            $this->newBuildAllZeroTrees($children, 0);
            $parents[$index]['childrenDropLevel'] = 2;
        }
        else{
            $parents[$index]['text']['data-drag'] = true; // only leafs can be transferred
        }
        $parents[$index]['children'] = $children;
        $this->newBuildAllZeroTrees($parents, $index+1);

    }

    private function newGetChildren($id)
    {
        $childrenArray = array();

        $rowSet = $this->tableGateway->select(array('parent' => $id, 'valid' => 1));

        foreach ($rowSet as $row) {
            $childrenArray[] = array(
                'text' => array(
                    'name' => $row->clinic_name,
                    'title' => ($row->version) ? $row->version: '?',
                    'data-id' => $row->id,
                    'data-drag' => 'false'
                ),
                'children' => array()
            );
        }

        return $childrenArray;
    }

    public function getMultiSelectTree()
    {
        $ress = "SELECT id,parent,clinic_name,type FROM " . $this->tableGateway->table . " WHERE type = 1 AND valid = 1 ORDER BY parent";
        $statement = $this->tableGateway->adapter->createStatement($ress);
        $return = $statement->execute();
        $array = array();
        foreach ($return as $row) {
            $row['patch'] = $this->getMultiSelectTreePatch($row['id']);
            $array[] = $row;
        }

        return $array;
    }

    private function getMultiSelectTreePatch($idChild){
        $rowset = $this->tableGateway->select(array('id' => $idChild));
        $row = $rowset->current();
        $arr = array($row->clinic_name);
        $arr2 = array_reverse($this->createMultiSelectTreePatch($row->parent));
        $arr_marge = array_merge($arr2, $arr);
        $html = implode('/', $arr_marge);

        return $html;
    }

    private function createMultiSelectTreePatch($parent = 0,&$array = array()){
        $parent = (int)$parent;
        $rowset = $this->tableGateway->select(array('id' => $parent));
        $row = $rowset->current();

        if ($parent == 0) {
            return $array;
        } else {
            $array[] = $row->clinic_name;
            return $this->createMultiSelectTreePatch($row->parent, $array);
        }
    }

    private function setSsuccessSonsConnection($clinic_id){

        $rowset = $this->tableGateway->select(array('id' => $clinic_id));

        $row = $rowset->current();
        $this->success_sons_connection[$clinic_id] = $row->clinic_name;
    }

    public function getSsuccessSonsConnection(){
        if ($this->success_sons_connection) {
            return $this->success_sons_connection;
        } else {
            return array();
        }
    }

    private function setFailSonsConnection($clinic_id){

        $rowset = $this->tableGateway->select(array('id' => $clinic_id));

        $row = $rowset->current();
        $this->fails_sons_connection[$clinic_id] = $row->clinic_name;
    }

    public function getFailSonsConnection(){
        if ($this->fails_sons_connection) {
            return $this->fails_sons_connection;
        } else {
            return array();
        }
    }

    public function getErrorCode(){
        return $this->error_code;
    }

    public function setMyClientId()
    {

        if ($_SESSION['my_client']->id == '') {
            unset($_SESSION['my_client']);
        }

        if (!isset($_SESSION['my_client'])) {
            $multipledbTable = new TableGateway('networking_db', $this->tableGateway->getAdapter());
            $rowset = $multipledbTable->select(array('dbname' => $GLOBALS['dbase'], 'host' => $GLOBALS['host']));
            $row = $rowset->current();

            if (!$row) {
                echo "<h1>Networking connection not found</h1>";
                exit();
            }

            $rowset = $this->tableGateway->select(array('id' => $row->clinic_id, 'valid' => 1));
            $rowNetworking = $rowset->current();

            $_SESSION['my_client'] = (object)array('id' => $row->clinic_id, 'parent' => $rowNetworking->parent, 'clinic_name' => $rowNetworking->clinic_name);

        }

    }

}
