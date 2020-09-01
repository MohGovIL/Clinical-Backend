<?php
/**
 * @author Dror Golan drorgo@matrix.co.il
 * base class for base search mechanism
 * this is part of a strategy element
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;


use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;

abstract class BaseSearch implements SearchInt
{
    protected const SEARCH = 'search';
    const PART = "PART";
    const EXACT = "EXACT";
    const SPECIFIC = "SPECIFIC";
    const EQUALITY = "EQUALITY";
    const _SORT = "_sort";
    const _COUNT= "_count";
    const _INCLUDE = "_include";
    const _SUMMARY = "_summary";
    const VALUE = "value";
    const OPERATOR = "operator";
    const LEFTJOIN = "JOIN_LEFT";
    const RIGHTJOIN = "RIGHT_LEFT";
    const JOININNER = "INNER_JOIN";
    const COLLECT_FHIR_OBJECT = "collect_fhir_object";
    const SUBJECT = "Subject";
    const COUNT = "Count";
    const FHIR_ELM_PATH="FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\";

    protected $_search;
    protected $type;
    protected $FHIRBundle;
    protected $searchThisTable;
    /**
     * @var array
     */
    protected $stringArr;
    /**
     * @var string
     */
    protected $searchType;
    /**
     * @var array
     */
    protected $searchParams;
    protected $fhirObj;
    protected $paramsAvaliable;
    protected $builderType;
    protected $paramsFromBody;
    protected $paramsFromURL;
    protected $buildThisSearch;
    protected $sortParams;
    protected $specialParams =[];
    protected $includeParams = [];
    protected $container;
    protected $join = null;
    /**
     * @var array
     */
    protected $summaryParams;
    protected $orderParams;
    /**
     * @var FhirBaseMapping
     */
    private $fhirBaseMapping;

    public function __construct($searchParams)
    {
        if (!is_null($searchParams)) {
            $this->fhirObj = $searchParams['fhirObj'];
            $this->FHIRBundle = $this->fhirObj->createSearchBundle();
            $this->searchThisTable = $searchParams['tableToSearchOnOrm'];
            $this->paramsAvaliable = isset($searchParams['paramsToSearch']) ? $searchParams['paramsToSearch']['search'] : null;
            $this->stringArr = array();
            $this->searchType = '';
            $this->searchParams = array();
            $this->paramsFromBody = $searchParams['paramsFromBody'];
            $this->paramsFromURL = $searchParams['paramsFromURL'];
            $this->buildThisSearch = $searchParams['buildThisSearch'];
            $this->container = $searchParams['container'];
            $this->join = $searchParams['join'];
            $this->categorizeParams();
            $this->buildSortParams();
        }
    }

    public function categorizeParams()
    {
        $params = $this->paramsFromBody;

        if (!empty($params['ARGUMENTS'])) {
            $searchArguments = $params['ARGUMENTS'];
            foreach ($searchArguments as $type => $valArr) {
                $this->searchParams[$type] = $valArr;
                unset($params['ARGUMENTS'][$type]);
            }
        }

        if (!empty($params['PARAMETERS_FOR_ALL_RESOURCES'])) {

            $searchArguments = $params['PARAMETERS_FOR_ALL_RESOURCES'];
            foreach ($searchArguments as $type => $valArr) {

                if (strpos($valArr[0], ",") !== false) {   // support arg=x,y,z

                        foreach (explode(",", $valArr[0]) as $k => $v) {
                            $this->searchParams[$type][] = array('value'=> $v);
                        }
                } else{
                    if(is_array($valArr[0])){   // special case
                        $this->searchParams[$type] = $valArr[0][''];
                    }else{
                        if(is_array($valArr)){   // support arg=x&arg=y
                            foreach ($valArr as $i => $j) {
                                $this->searchParams[$type][] = array('value'=> $j);
                            }
                        }else{ // support single value
                            $this->searchParams[$type] = $valArr[0];
                        }

                    }
                }



                unset($params['PARAMETERS_FOR_ALL_RESOURCES'][$type]);
            }
        }

        if (!empty($params['PARAMETERS_FOR_SEARCH_RESULT'])) {

            $searchArguments = $params['PARAMETERS_FOR_SEARCH_RESULT'];
            foreach ($searchArguments as $type => $valArr) {

                if ($type == self::_INCLUDE) {
                    if (strpos($valArr[0], ",") !== false) {
                        foreach (explode(",", $valArr[0]) as $k => $v) {
                            $this->includeParams[] = explode(",", $v);
                        }
                    } else {
                        if (is_array($valArr)) {
                            foreach ($valArr as $k => $v) {
                                $this->includeParams[] = [$v];
                            }
                        } else {
                            $this->includeParams[] = $valArr[0];
                        }
                    }

                }

                if ($type == self::_SUMMARY) {
                    if (strpos($valArr[0], ",") !== false) {
                        foreach (explode(",", $valArr[0]) as $k => $v) {
                            $this->summaryParams[] = explode(",", $v);
                        }
                    } else {
                        if (is_array($valArr)) {
                            foreach ($valArr as $k => $v) {
                                $this->summaryParams[] = [$v];
                            }
                        } else {
                            $this->summaryParams[] = $valArr[0];
                        }
                    }

                }

                if ($type == self::_COUNT) {
                    $this->specialParams[$type] = $valArr;
                }

                if ($type == self::_SORT) {
                    $this->sortParams[$type] = $valArr;
                }

                unset($params['PARAMETERS_FOR_SEARCH_RESULT'][$type]);
            }
        }
    }

    /*-----------*/
    # Restructing of parameters
    /* ------------ */

    public function addSortParams($fhir,$db)
    {
        $sortParam=$this->paramsAvaliable[self::_SORT];
        $fhirPlaceCount= substr_count($sortParam['fhir_place'],",");
        $openemrColumnCount=   substr_count($sortParam['openemr_column'],",");

        /*
         *  if both number of commas is zero add names without comma
         *  if number of commas are equal add names with comma
         *  if number of commas are not equal - add nothing - this should not happen
         */

        if( !empty($sortParam['fhir_place']) &&  $fhirPlaceCount === $openemrColumnCount) {
            $fhir=",".$fhir;
            $db=",".$db;

        }elseif(!($openemrColumnCount===0 && $fhirPlaceCount===0)) {
                $fhir="";
                $db="";
            }

        $this->paramsAvaliable[self::_SORT]['fhir_place'].=$fhir;
        $this->paramsAvaliable[self::_SORT]['openemr_column'].=$db;

    }

    public function buildSortParams()
    {
        $sortMapping = array_combine(explode(",", $this->paramsAvaliable[self::_SORT]['fhir_place']), explode(",", $this->paramsAvaliable[self::_SORT]['openemr_column']));
        if (!$sortMapping) {
            ErrorCodes::http_response_code(403, $this->FHIRBundle);
        }

        if (is_array($this->sortParams[self::_SORT])) {
            foreach ($this->sortParams[self::_SORT] as $key => $value) {
                $this->orderParams[$key] = $sortMapping[trim($value[self::VALUE])] . " " . $value[self::OPERATOR];
            }
        } else {
            $this->orderParams[self::_SORT] = $sortMapping[trim($this->sortParams[self::_SORT])];
        }
    }

    public function paramHandler($fhirSearchParam, $tableParams, $dbTable = null,$conversion=array())
    {
        if (is_null($dbTable)) {
            $dbTable = $this->MAIN_TABLE;
        }
        if (isset($this->searchParams[$fhirSearchParam])) {

            if(!empty($conversion)){
                $originalVal=$this->searchParams[$fhirSearchParam][0]['value'];
                if(!empty($conversion[$originalVal])){
                    $this->searchParams[$fhirSearchParam][0]['value']=$conversion[$originalVal];
                }
            }

            $this->paramsToDB[$dbTable . '.' . $tableParams] = $this->searchParams[$fhirSearchParam];
        }
    }

    public function search()
    {
        $params = $this->paramsFromBody;
        // if no get/post params return all
        if (empty($params['ARGUMENTS']) && empty($params['PARAMETERS_FOR_ALL_RESOURCES']) && empty($params['PARAMETERS_FOR_SEARCH_RESULT'])) {
            return $this->basicSearch();
        }

        return $this->runExtendedParmSearch();
    }

    public function basicSearch()
    {
        $dataFromDb = $this->searchThisTable->buildGenericSelect($this->searchParams, implode(",", $this->orderParams), array());
        foreach ($dataFromDb as $key => $value) {
            $this->fhirObj->initFhirObject();
            $FHIRResourceContainer = new FHIRResourceContainer($this->fhirObj->DBToFhir($value));
            $this->FHIRBundle = $this->fhirObj->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, 'match');
        }
        return $this->FHIRBundle;

    }

    public function runExtendedParmSearch()
    {
        if (!$this->checkIfParamsAllowed()) {
            $this->FHIRBundle = $this->fhirObj->createErrorBundle($this->FHIRBundle, $this->paramsFromBody);
            return ErrorCodes::http_response_code(403, $this->FHIRBundle);
        }
        $this->convertParamsToDbParams();

        if ($this->checkForSpecificSearch()) {
            return $this->runSpecificSearch();
        } else {
            return $this->runSimpleParamsSearch();
        }

    }

    public function checkIfParamsAllowed()
    {
        if (!$this->checkIfOrderIsAllowed()) {
            return false;
        }
        if (!$this->checkIfIncludeIsAllowed()) {
            return false;

        }
        if (!$this->checkIfSummaryIsAllowed()) {
            return false;
        }
        return count(array_intersect(array_keys($this->searchParams), array_keys($this->paramsAvaliable))) == count($this->searchParams);
    }

    public function checkIfOrderIsAllowed()
    {
        if (sizeOf($this->orderParams['_sort']) > 0) {
            return count(array_intersect(explode(",", implode(",", $this->orderParams)), explode(",", $this->paramsAvaliable[self::_SORT]['openemr_column']))) == count($this->orderParams);
        }
        return true;
    }

    public function checkIfIncludeIsAllowed()
    {

        if (sizeOf($this->includeParams) == 0) {
            return true;
        }
        foreach ($this->includeParams as $key => $value) {
            $allowed = false;
            if (count(array_intersect($value, explode(",", $this->paramsAvaliable[self::_INCLUDE]['fhir_place']))) == count($value)) {
                $allowed = true;
            }
        }
        return $allowed;
    }

    public function checkIfSummaryIsAllowed()
    {

        if (sizeOf($this->summaryParams) == 0) {
            return true;
        }
        foreach ($this->summaryParams as $key => $value) {
            $allowed = false;
            if (count(array_intersect($value, explode(",", $this->paramsAvaliable[self::_SUMMARY]['fhir_place']))) == count($value)) {
                $allowed = true;
            }
        }
        return $allowed;
    }

    /* ----------- */
    # Validate structure pf parameters
    /* ----------- */

    public function convertParamsToDbParams()
    {
        $searchParams = [];
        if (!empty($this->searchParams)) {
            foreach ($this->searchParams as $key => $value) {
                $saveThisKey = $key;
                $dataOnThisParam = $this->paramsAvaliable[$key];
                if (is_array($value) && sizeof($value) > 1) {
                    foreach ($value as $key => $value) {
                        if ($dataOnThisParam["search_type_method"] == self::PART) {
                            $value[self::VALUE] = "%" . $value[self::VALUE] . "%";
                            $searchParams[$dataOnThisParam['openemr_column']][] = $value;
                        } else {
                            $searchParams[$dataOnThisParam['openemr_column']][] = $value;
                        }
                    }
                } else {
                    if ($dataOnThisParam["search_type_method"] == self::PART) {
                        $value[0][self::VALUE] = "%" . $value[0][self::VALUE] . "%";
                        $searchParams[$dataOnThisParam['openemr_column']] = $value;
                    } else {
                        $searchParams[$dataOnThisParam['openemr_column']] = $value;
                    }
                }


            }
        }

        $this->searchParams = $searchParams;
    }

    public function checkForSpecificSearch()
    {
        foreach ($this->searchParams as $key => $value) {

            if ($this->paramsAvaliable[$key]['specific'] == SELF::SPECIFIC) {
                return true;
            }
        }
        return false;

    }

    public function runSimpleParamsSearch()
    {

        $collectFhirObjectsAvaliable = explode(",", $this->paramsAvaliable[self::_INCLUDE]['openemr_table']);
        $collectFhirObjectsTypesAvaliable = explode(",", $this->paramsAvaliable[self::_INCLUDE]['search_type_method']);
        $collectFhirObjectsSpecificFuncAvaliable = explode(",", $this->paramsAvaliable[self::_INCLUDE]['specific']);
        $collectFhirIncludesFromSearchAvaliable = explode(",", $this->paramsAvaliable[self::_INCLUDE]['fhir_place']);
        $collectFhirSummaryObjectsSpecificFuncAvaliable = explode(",", $this->paramsAvaliable[self::_SUMMARY]['specific']);
        $collectFhirSummaryObjectsSpecificFuncAvaliable = explode(",", $this->paramsAvaliable[self::_SUMMARY]['specific']);

        //$collectFhirSummayFromSearchAvaliable = explode(",", $this->paramsAvaliable[self::_SUMMARY]['fhir_place']);

        //FIX DATE TIME ISSUE
        //CHECK IF THIS FIELD IS A DATE FIELD

        /* foreach($this->searchParams['date'] as $key=>$value){
             $value[self::VALUE] = DateToYYYYMMDD($value[self::VALUE]);
             $this->searchParams['date'][$key] = $value;
         }*/

        if (in_array($this->summaryParams[0][0], $collectFhirSummaryObjectsSpecificFuncAvaliable)) {
            foreach ($collectFhirSummaryObjectsSpecificFuncAvaliable as $k => $v) {
                if ($v == strToLower(self::COUNT)) {

                    $this->runMysqlQuery();
                    $response = $this->FHIRBundle->getEntry()[0];
                    $total = $this->FHIRBundle->getTotal() ? $this->FHIRBundle->getTotal() : 0;
                    $this->FHIRBundle = new FHIRBundle();
                    return $this->FHIRBundle->setTotal($total);

                }
            }

        }

        if (!is_null($this->includeParams) && sizeOf($this->includeParams) > 0) { // check for include properties
            if ($this->paramsAvaliable[self::_INCLUDE]['search_type_method'] == self::COLLECT_FHIR_OBJECT) {
                if (sizeof($collectFhirObjectsAvaliable) != sizeOf($collectFhirIncludesFromSearchAvaliable) && sizeof($collectFhirObjectsAvaliable) != sizeof($collectFhirObjectsTypesAvaliable) && sizeof($collectFhirObjectsSpecificFuncAvaliable) != sizeof($collectFhirIncludesFromSearchAvaliable)) {
                    return ErrorCodes::http_response_code(403, $this->FHIRBundle);
                } else {
                    $this->runMysqlQuery();
                    $total = $this->FHIRBundle->getTotal();
                }
                $entries = $this->FHIRBundle->getEntry();

                foreach ($this->includeParams as $searchKey => $searchType) {
                    $objectsToCollect = [];
                    foreach ($entries as $key => $value) {
                        if ($value->getResource() == "Encounter") {
                            foreach ($collectFhirObjectsAvaliable as $element => $val) {
                                $FHIRElement = $collectFhirObjectsAvaliable[$element];
                                if (strpos($searchType[0], strtolower($FHIRElement)) !== false) {
                                    $cleanThisFromObjects = $FHIRElement . "/";
                                    $func = "get" . $collectFhirObjectsSpecificFuncAvaliable[$element];
                                    $objRef = str_replace($cleanThisFromObjects, "", $value->getResource()->$func()->getReference()->getValue());
                                    if (!in_array($objRef, $objectsToCollect)) {
                                        $objectsToCollect[] = $objRef;
                                    }
                                }
                            }
                        }
                    }

                    $strategy = "FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\$FHIRElement\\$FHIRElement";
                    foreach ($objectsToCollect as $k => $v) {

                        $params = ['paramsFromUrl' => [$v], "", "", "", 'container' => $this->container];
                        $obj = new $strategy($params);
                        $FhirObject = $obj->read($v);

                        //add this fhir object to the bundle
                        $FHIRResourceContainer = new FHIRResourceContainer($FhirObject);
                        $this->FHIRBundle = $this->fhirObj->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, '');
                    }
                }
            }
            //$this->FHIRBundle->setTotal($total);
        } else {
            $this->runMysqlQuery();
        }


        return $this->FHIRBundle;
    }

    public function runMysqlQuery()
    {
        $dataFromDb = $this->searchThisTable->buildGenericSelect($this->searchParams, implode(",", $this->orderParams), array());
        $includeFlag= !empty($this->includeParams);
        $tempIncludeParams=array();
        foreach ($dataFromDb as $key => $data) {
            if($includeFlag){  // if true collect the id for the include for later use
                foreach ($this->includeParams as $index => $include){
                    if(!empty($include[1]) && !empty($data[$include[1]]) ){
                        $this->includeParams[$index][3]=$data[$include[1]];

                        if(empty($tempIncludeParams[$include[0]])){
                            $tempIncludeParams[$include[0]][0]= $this->includeParams[$index]; //new fhir include object
                        }else{
                            $tempIncludeParams[$include[0]][0][3] .=",". $data[$include[1]];  //add fhir include object
                        }
                    }
                }
            }
            $this->fhirObj->initFhirObject();
            $FHIRResourceContainer = new FHIRResourceContainer($this->fhirObj->DBToFhir($data));
            $this->FHIRBundle = $this->fhirObj->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, 'match');
        }
        $this->includeParams=$tempIncludeParams;
        if($includeFlag){  //perform search() for each include element
            foreach ($this->includeParams as $index => $include){
                $FHIRElm=$include[0][2];
                $idStringList= $include[0][3];
                if(!empty($FHIRElm) && !empty($idStringList)){
                    $class = self::FHIR_ELM_PATH.$FHIRElm."\\".$FHIRElm;
                    if(class_exists($class)){
                        $paramsFromBody=array_keys(array_flip(explode(',',$idStringList))); // remove duplicate values
                        $initials=array();
                        $initials['paramsFromUrl']=array();
                        $initials['paramsFromBody']['PARAMETERS_FOR_ALL_RESOURCES']=array('_id'=>$paramsFromBody);
                        $initials['container']=$this->container;
                        $FHIRElmClass=new $class($initials);
                        $searchRez=$FHIRElmClass->search();
                        $entries = $searchRez->getEntry();
                        foreach ($entries as $key => $value) {
                            $FHIRElmOBJ=$value->getResource();
                            if(is_object($FHIRElmOBJ) && $FHIRElmOBJ->get_fhirElementName()===$FHIRElm){
                                $FHIRResourceContainer = new FHIRResourceContainer($FHIRElmOBJ);
                                $this->FHIRBundle = $this->fhirObj->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, 'include');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $fhirIncludeParam string include search param
     * @param $IncludeParamMap string db name of include field
     * @param $fhirType  string  FHIR object name
     */

    public function includeParamHandler($fhirIncludeParam,$IncludeParamMap,$fhirType )
    {
        if (is_array($this->includeParams)){
            foreach ($this->includeParams as $index => $data){
                if (is_array($data)){
                        $includeName = $data[0];
                        if ($fhirIncludeParam===$includeName){
                            $this->includeParams[$index][1]=$IncludeParamMap;
                            $this->includeParams[$index][2]=$fhirType;
                        }
                }
            }
        }
    }


    public function addSortDate($fieldPath)
    {
        if(isset($this->searchParams[$fieldPath])) {
            //value is only date not datetime
            if (strlen($this->searchParams[$fieldPath][0]['value']) === 10 ){

                $operator= $this->searchParams[$fieldPath][0]['operator'];
                if ($operator === 'eq') {
                    $dayDate = $this->searchParams[$fieldPath][0]['value'];
                    $this->searchParams[$fieldPath][0] = [
                        'value' => $dayDate . ' 00:00:00|' .$dayDate . ' 23:59:59',
                        //between operator
                        'operator' => 'be',
                        'modifier' => null
                    ];
                }

                elseif ($operator === 'le') {
                    $dayDate = $this->searchParams[$fieldPath][0]['value'];
                    $this->searchParams[$fieldPath][0] = [
                        'value' => $dayDate . ' 23:59:59',
                        //between operator
                        'operator' => 'le',
                        'modifier' => null
                    ];
                }
            }
        }
    }





}
