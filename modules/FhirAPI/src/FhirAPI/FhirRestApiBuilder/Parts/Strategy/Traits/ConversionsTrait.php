<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits;

use function DeepCopy\deep_copy;

trait ConversionsTrait
{
    /*
     * Populates empty FHIR object with data from a "Fhir structured" array
     */
    public function arrayToFhirObject(&$obj, $arr)
    {
        foreach($arr as $key => $value) {
            if(is_array($value)) {
                if(is_numeric($key)) {
                    if($key > 0) {
                        $obj[$key] = deep_copy($obj[0]);
                    }
                    $this->arrayToFhirObject($obj[$key], $arr[$key]);
                }
                else {
                    $this->arrayToFhirObject($obj->$key, $arr[$key]);
                }
            }
            else {
                if(property_exists($obj,$key) && method_exists($obj->$key, "setValue")) {
                    $obj->$key->setValue($value);
                }else{
                    $methodName='set'.ucfirst($key);
                    if(method_exists($obj, $methodName)) {
                        $obj->$methodName($value);
                    }
                }
            }
        }
    }
}


