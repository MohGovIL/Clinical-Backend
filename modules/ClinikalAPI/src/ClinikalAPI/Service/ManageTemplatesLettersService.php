<?php

/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 */

namespace ClinikalAPI\Service;

use ClinikalAPI\Model\ManageTemplatesLettersTable;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use ReflectionMethod;

trait ManageTemplatesLettersService
{
    /**
     * get letter template info
     * @param bool $fullInfo
     * @return array
     */
    public function getLetterList($fullInfo = false)
    {
        $letters = array();
        $FormContextMapTable = $this->container->get(ManageTemplatesLettersTable::class);
        $param = array("active" => "1");
        $dbData = $FormContextMapTable->getDataByParams($param);

        if ($fullInfo) { //for create letter
            foreach ($dbData as $index => $letter) {
                $letters[$letter['letter_name']] = array(
                    "letter_post_json" => json_decode($letter['letter_post_json']),
                    "letter_class" => $letter['letter_class'],
                    "letter_class_action" => $letter['letter_class_action'],
                );
            }
        } else { //for letter list
            foreach ($dbData as $index => $letter) {
                $letters[$letter['letter_name']] = array("letter_post_json" => json_decode($letter['letter_post_json']));
            }
        }
        return $letters;

    }

    /**
     * create letter
     * @param string $letter_name
     * @param string $fileData
     * @return array
     */
    public function createLetter($letter_name,$fileData)
    {
        $letterList = $this->getLetterList(true);
        if (empty($letterList[$letter_name])) {
            ErrorCodes::http_response_code('404', 'Letter not found');
            return array();
        } else {
            $className = $letterList[$letter_name]['letter_class'];
            $methodName = $letterList[$letter_name]['letter_class_action'];
            if (class_exists($className)) {
                $data=array();
                if(is_array($fileData)){
                    $data= $fileData;
                }
                $class = new $className($this->container,$data);
                //$info= $this->container->get('GenericTools\Model\FacilityTable');
                $methodName.="Action";
                if (method_exists($class,$methodName)) {
                    $reflection = new ReflectionMethod($class, $methodName);
                    if ($reflection->isPublic()) {
                        return $class->$methodName();
                    }else{
                        ErrorCodes::http_response_code('500', 'method is not public');
                    }
                }else{
                    ErrorCodes::http_response_code('500', 'method does not exist');
                }
            }else{
                ErrorCodes::http_response_code('500', 'class does not exist');
            }
            return array();
        }
    }
}


