<?php
/**
 * @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * FHIR ADDRESS trait
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits;


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
                return self::blockedIfValue($validator,$data);
                break;
            case 'required':
                return self::checkRequired($validator, $data, $mainTable);
                break;
            case 'valueset':
                return self::checkIfInList($validator, $data, $mainTable);
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
    public function blockedIfValue($validator,$data)
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
}
