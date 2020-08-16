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
                return self::blockStatusIfValue($validator,$data);
                break;
            case 'required':
                return self::checkRequired($validator, $data, $mainTable);
                break;
            case 'valueset':
                return self::checkIfInList($validator, $data, $mainTable);
                break;
            case 'aptDateRangeCheck':
                return self::aptDateRangeCheck($data, $mainTable);
                break;
            case 'aptReasonCodes':
                return self::aptReasonCodes($validator, $data, $mainTable);
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
        return false;
    }

    /**
     * return false if trying to update when status is finished
     *
     * @param $data array
     *
     * @return bool
     */
    public function blockStatusIfValue($validator,$data)
    {
        if ($data['old'][0]['status'] === $validator['validation_param']) {
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
     * @param $mainTable string
     *
     * @return bool
     */
    public function checkIfInList($validator, $data, $mainTable)
    {
        if (is_array($data['new'])) {
            $value = null;
            if (!is_null($mainTable)) {
                $value = $data['new'][$mainTable][$validator['filed_name']];
            } else {
                $value = $data['new'][$validator['filed_name']];
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




}
