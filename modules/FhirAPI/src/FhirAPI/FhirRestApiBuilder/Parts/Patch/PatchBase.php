<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 18/02/20
 * Time: 15:57
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Patch;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;

class PatchBase
{

    protected $mapping;
    protected $container;
    protected $paramsFromUrl;
    protected $paramsFromBody;
    protected $selfApiCalls;

    public function __construct($params = null)
    {
        if (!is_null($params)) {
            $this->initParams($params);
        }
    }

    public function getDbDataToUpdate()
    {
        $FHIRElement = $this->getElementFromDb();
        $FHIRElement= $this->processPatchRequest($FHIRElement);
        $dataToUpdate = $this->mapping->fhirToDb($FHIRElement);
        $valid = $this->mapping->validateDb($dataToUpdate);
        if ($valid) {
            return $dataToUpdate;
        } else {
            return array();
        }
    }

    private function initParams($initials){
        $this->paramsFromUrl=$initials['paramsFromUrl'];
        $this->paramsFromBody=$initials['paramsFromBody'];
        $this->container=$initials['container'];
        $this->mapping=$initials['mapping'];
        $this->selfApiCalls=$initials['selfApiCalls'];
    }


    /**
     * return FHIR object by ID sent in paramsFromUrl
     * @param object
     * @return object
     **/
    public function getElementFromDb()
    {
        return $this->selfApiCalls->read($this->paramsFromUrl);
    }

    /**
     * execute patch lines on FHIR object
     * @param object
     * @return object
     **/
    public function processPatchRequest($FHIRElement)
    {
        foreach ($this->paramsFromBody['ARGUMENTS'] as $path => $info) {
            if ($info[0]['operator'] === 'replace' && !empty($info[0]['value'])) {
                $FHIRElement = $this->patchReplaceHandler($FHIRElement, $path, $info[0]['value']);
                unset($this->paramsFromBody['ARGUMENTS'][$path]);
            }
        }
        return $FHIRElement;
    }

    /**
     * Replace value in FHIR object based on given path
     * @param object
     * @param string
     * @param string
     * @return object
     **/
    public function patchReplaceHandler($fhirElement,$path,$value)
    {
        $objectToChange=&$fhirElement;
        $pathArr=explode('.',$path);
        //$arrLength=count($pathArr)-1;
        foreach($pathArr as $key => $part){
            if(!is_numeric($part)){
                $objectToChange=   &$objectToChange->$part;
            }else{
                $objectToChange=&$objectToChange[$part];
            }
        }
        $objType=gettype($value);
        switch ($objType) {
            case "string":
                $objectToChange->setValue($value);
                break;
            case "array":
                foreach ($value as $index => $val){
                    if(is_numeric($index)){

                        $objectToChange[$index]->setValue($val);

                    }else{

                        if(gettype($objectToChange->$index)==="string"){
                            $objectToChange->$index=$val;
                        }else{
                            $objectToChange->$index->setValue($val);
                        }
                    }
                }
                break;
        }
        return $fhirElement;
    }

    /**
    *return error bundle based on paramsFromBody that was not processed
    **/
    public function createErrorBundle()
    {
        $paramsFromBody=$this->paramsFromBody;
        $FHIRBundle = new FHIRBundle;
        $errorBundle = $this->mapping->createErrorBundle($FHIRBundle, $paramsFromBody);
        return $errorBundle;

    }

    public function dataNotValidErrorBundle()
    {
        return $errorBundle = $this->mapping->createNotValidErrorBundle();
    }

    public function isRequestFullyProcessed(){
        $flag =TRUE;
        $flag= ($flag && empty($this->paramsFromBody['ARGUMENTS']));
        $flag= ($flag && empty($this->paramsFromBody['PARAMETERS_FOR_SEARCH_RESULT']));
        $flag= ($flag && empty($this->paramsFromBody['PARAMETERS_FOR_ALL_RESOURCES']));
        $flag= ($flag && empty($this->paramsFromBody['POST_PARSED_JSON']));
        if($flag){
            return TRUE;
        }else{
            return FALSE;
        }

    }


}
