<?php

namespace GenericTools\Model;

class FormLogTables
{
    private $adapter;

    public function __construct($dbAdapter)
    {
        $this->adapter = $dbAdapter;
    }

    // Fetch updates info from log tables.
    // You can fetch using formId and dates, or using the specific update row id.
    public function getFormLogs($formId, $fromDate, $toDate, $rowId=null)
    {
        $whereParams = array();
        if($formId){
            $where = "mfu.`form_id` = ? ";
            array_push($whereParams, $formId);
        }
        else{
           $where = "mfu.`id` = ? ";
           array_push($whereParams, $rowId);
        }

        if($fromDate){
            $where .= " and mfu.`update_datetime` >= ? ";
            array_push($whereParams, $fromDate);
        }
        if($toDate){
            $where .= " and mfu.`update_datetime` <= ? ";
            array_push($whereParams, $toDate);
        }

        $sql =
            "select mfu.`id`, mft.`tab_name`, mff.`component_id`, mfc.`component_name`, mff.`field_name`, " .
                "lo.`title`, mfu.`reason_text`, mfu.`update_datetime`, mfu.`updated_by`, " .
                "mfuf.`before_update`, mfuf.`after_update`, mff.`list_id`" .
            "from `moh_form_updates` as mfu " .
            "join `moh_form_updated_fields` as mfuf on  mfu.`id` = mfuf.`update_id` " .
            "join `moh_form_fields` as mff on mfuf.`field_id` = mff.`id` " .
            "join `moh_form_components` as mfc on mff.`component_id` = mfc.`id` " .
            "join `moh_form_tabs` as mft on mfc.`tab_id` = mft.`id` " .
            "join `list_options` as lo on lo.`list_id` = 'moh_rab_update_reasons' and mfu.`reason_type` = lo.`option_id` " .
            "where " . $where . "" .
            "order by mfu.`update_datetime` desc; ";
        $statement = $this->adapter->createStatement($sql, $whereParams);
        $return = $statement->execute();
        $results = array();

        // create array for view
        foreach ($return as $row) {
            $updateId = $row['id'];
            $results[$updateId]['tab_name'] = $row['tab_name'];
            $results[$updateId]['reason_type'] = $row['title'];
            $results[$updateId]['reason_text'] = $row['reason_text'];
            $results[$updateId]['update_datetime'] = $row['update_datetime'];
            $results[$updateId]['updated_by'] = $row['updated_by'];
            $results[$updateId]['components'][$row['component_id']]['name'] = $row['component_name'];
            $results[$updateId]['components'][$row['component_id']]['fields'][] = array(
                'field_name' => $row['field_name'],
                'before_update' => $row['before_update'],
                'after_update' => $row['after_update'],
                'list_id' => $row['list_id']
            );
        }
        return (isset($results)) ? $results : array();

    }

    // save update instance to moh_form_updates table
    public function saveUpdateReason($id,$reasonType,$reasonText,$updateDatetime,$updatedBy)
    {
        $sql = "INSERT INTO moh_form_updates (form_id,reason_type,reason_text,update_datetime,updated_by) VALUES (?,?,?,?,?)";
        $statement = $this->adapter->createStatement($sql, array($id,$reasonType,$reasonText,$updateDatetime,$updatedBy));
        $return = $statement->execute();
        $row = $return->getGeneratedValue();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row;

    }

    // save updated fields to moh_form_updated_fields table
    public function insertRecord($updateId,$fieldId,$beforeUpdate,$afterUpdate)
    {
        $sql = "INSERT INTO moh_form_updated_fields (update_id,field_id,before_update,after_update) VALUES (?,?,?,?)";
        $statement = $this->adapter->createStatement($sql, array($updateId,$fieldId,$beforeUpdate,$afterUpdate));
        $return = $statement->execute();
        $row = $return->getGeneratedValue();
        if (!$row) {
            return false;
            //throw new \Exception("Could not find row $serial_no");
        }
        return $row;

    }

    public function getFieldsInfo($fieldsArray)
    {
        $bindString = "(" . implode(",", array_map(function(){return '?';}, $fieldsArray)) . ")";
        $sql = "select id, field_name, component_id, list_id from moh_form_fields where id in " . $bindString . ";";
        $statement = $this->adapter->createStatement($sql, $fieldsArray);
        $resultObj = $statement->execute();
        $resultArr = array();
        foreach ($resultObj as $row) {
           $resultArr[$row['id']] = $row;
        }
        return $resultArr;
    }






}
