<?php
/**
 * @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * FHIR ADDRESS trait
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits;


use DateTime;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ValueSet\ValueSet;

trait FHIRElementValidation
{
    private $valueSet = null;
    private $safeTables = array(   // white list for pdo
                                'patient_data'=>'id',
                                'users'=>'id',
                                );

    /**
     * return initialized valueset class
     *
     * @return ValueSet
     */
    public function getValueSet()
    {
        if ($this->valueSet===null) {
            $this->valueSet = new ValueSet(array(
                'paramsFromUrl' => array(),
                'paramsFromBody' => array(),
                'container' => $this->container
            ));
        } else {
            $this->valueSet->setParamsFromUrl(array());
            $this->valueSet->setOperations(array());
        }

        return $this->valueSet;
    }


    /**
     * map and run a validation function
     *
     * @param $validator array
     * @param $data array
     * @param $mainTable string
     *
     * @return bool
     */
    public function validate($validator, $data, $mainTable = null)
    {
        switch ($validator['validation']) {
            case 'blockedIfValue':
                return self::blockedIfValue($validator,$data);
                break;
            case 'required':
                return self::checkRequired($validator, $data, $mainTable);
                break;
            case 'valueset':
                return self::checkIfInList($validator, $data, $mainTable);
                break;
            case 'valuesetNotRequired':
                return self::checkIfInList($validator, $data, $mainTable,true);
                break;
            case 'aptDateRangeCheck':
                return self::aptDateRangeCheck($data, $mainTable);
                break;
            case 'aptReasonCodes':
                return self::aptReasonCodes($validator, $data, $mainTable);
                break;
            case 'ifExist':
                return self::ifExist($validator, $data, $mainTable);
                break;
        }
        return true;
    }

    /**
     * check if value is not empty
     *
     * @param $validator array
     * @param $data array
     * @param $mainTable string
     *
     * @return bool
     */
    public function checkRequired($validator, $data, $mainTable)
    {
        if (is_array($data['new'])) {
            $requiredField = null;
            if (!is_null($mainTable)) {
                $requiredField = $data['new'][$mainTable][$validator['filed_name']];
            } else {
                $requiredField = $data['new'][$validator['filed_name']];
            }
            if ($requiredField !== "" && $requiredField !== null) {
                return true;
            }
        }
        error_log("Validation checkRequired failed on field {$validator['filed_name']}");
        return false;
    }

    /**
     * check if value is not empty
     *
     * @param $validator array
     * @param $data array
     * @param array multidimensional
     *
     * @return bool
     */
    public function ifExist($validator, $data, $mainTable)
    {
        if (is_array($data['new'])) {
            $existField = null;
            if (!is_null($mainTable)) {
                $existField = $data['new'][$mainTable][$validator['filed_name']];
            } else {
                $existField = $data['new'][$validator['filed_name']];
            }
            if ($existField !== "" && $existField !== null) {

                $count=$this->countDbRecords($validator['validation_param'],$this->safeTables[$validator['validation_param']],$existField);
                return ($count<=0) ? false : true ;
            }

        }
        error_log("Validation checkRequired failed on field {$validator['filed_name']}");
        return false;
    }

    /**
     * return false if trying to update when status is finished
     *
     * @param $data array
     *
     * @return bool
     */
    public function blockedIfValue($validator,$data)
    {
        if ($data['old'][0][$validator['filed_name']] === $validator['validation_param']) {
            error_log("Validation blockedIfValue failed on field {$validator['filed_name']}");
            return false;
        } else {
            return true;
        }
    }

    /**
     * check if value is in valueset
     *
     * @param $validator array
     * @param $data array
     * @param $mainTable array multidimensional
     *
     * @return bool
     */
    public function checkIfInList($validator, $data, $mainTable,$allowNull=false)
    {
        if (is_array($data['new'])) {
            $value = null;
            if (!is_null($mainTable)) {
                $value = $data['new'][$mainTable][$validator['filed_name']];
            } else {
                $value = $data['new'][$validator['filed_name']];
            }

            if($allowNull===true && empty($value)){
                return true;
            }

            $param = array(
                "0" => $validator['validation_param'],
                "1" => '$expand'
            );

            $valueSet=$this->getValueSet();
            $valueSet->setParamsFromUrl($param);
            $valueSet->setOperations($param);
            $list = $valueSet->read();
            $codes = self::getCodeArrayFromValueSet($list);

            if (in_array($value, $codes)) {
                return true;
            }
        }
        error_log("Validation checkIfInList failed on field {$validator['filed_name']}");
        return false;
    }

    /**
     * check date range
     *
     * @param $data array
     * @param array multidimensional
     *
     * @return bool
     */
    public function aptDateRangeCheck( $data, $mainTable)
    {
        $info=$data['new'][$mainTable];
        if(in_array(null,array($info['pc_eventDate'],$info['pc_startTime'],$info['pc_endDate'],$info['pc_endTime']))){
            error_log("Validation aptDateRangeCheck failed");
            return false;
        }
        $start= $info['pc_eventDate'] . ' ' . $info['pc_startTime'];
        $end= $info['pc_endDate'] . ' ' . $info['pc_endTime'];
        return $this->checkRange($start,$end);
    }

    /**
     * create array of codes from fhir valueset
     *
     * @param $validator array
     * @param $data array
     * @param array multidimensional
     *
     * @return bool
     */
    public function aptReasonCodes($validator, $data, $mainTable)
    {
        $validator['validation_param'].=$data['new'][$mainTable]['pc_service_type'];

        foreach($data['new']['event_codeReason_map'] as $index =>$reason){

            $check=array('new'=>array('event_codeReason_map'=>array('event_codeReason_map'=>$reason['option_id'])));
            if(!$this->checkIfInList($validator,$check, 'event_codeReason_map') ){
                error_log("Validation aptReasonCodes failed");
                return false;
            }
        }
        return true;
    }

    /**
     * create array of codes from fhir valueset
     *
     * @param $valueSet ValueSet
     *
     * @return array
     */
    private function getCodeArrayFromValueSet($valueSet)
    {
        $codes = array();
        if (method_exists($valueSet, 'get_fhirElementName') && $valueSet->get_fhirElementName() === "ValueSet") {
            $contains = $valueSet->getExpansion()->getContains();;
            foreach ($contains as $index => $codeInfo) {
                $codes[] = $codeInfo->getCode()->getValue();;
            }
        }
        return $codes;
    }

    /**
     * check if begin date smaller then end date
     *
     * @param $start string
     * @param $end string
     *
     * @return bool
     */
    private function checkRange($start,$end)
    {
        $begin = new DateTime($start);
        $finish = new DateTime($end);

        return ($begin < $finish) ;
    }

    private function countDbRecords($table,$field,$value)
    {
            //Table and Column names CANNOT be replaced by parameters in PDO
            if(in_array($table,array_keys($this->safeTables)) &&  in_array($field,$this->safeTables)){
                $sql = "SELECT COUNT(*) AS 'countMe' FROM ".$table." WHERE ".$field." = ?";
                $res = sqlStatement($sql, array($value));
                $row = sqlFetchArray($res);
                if($row) {
                    $row = array_reverse($row);
                    return $row['countMe'];
                }
                else{
                    return 0;
                }
            }else{
                return 0;
            }
      }
}
