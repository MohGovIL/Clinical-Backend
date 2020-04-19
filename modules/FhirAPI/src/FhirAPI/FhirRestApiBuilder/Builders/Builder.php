<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir REST BUILDER ABSTRACT
 */

namespace FhirAPI\FhirRestApiBuilder\Builders;


use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Context;
//use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Organization\Encounter;
use FhirAPI\Service\FhirRequestParamsHandler;
use GenericTools\Model\LogServiceTable;
use GenericTools\Service\AclCheckExtendedService;
use Interop\Container\ContainerInterface;

Abstract class Builder
{
    const ROUTES = "routes";
    const FUNCTION = "function";
    const SEARCH = "search";
    const PARAMS = "params";

    private static $functionType;
    private $apiVersion = "";
    private static $type  = "";
    private static $part = null;
    private static $container = null;
    private $searchParams;

    public static function setContainer($container){
        if(is_null(self::$container))
            self::$container = $container;
    }
    public static  function getContainer(){
        return self::$container ;
    }
    public function __construct($apiVersion)
    {
        $this->setApiVersion($apiVersion);
    }

    /**
     * @return null
     */
    public static function getPart()
    {
        return self::$part;
    }

    /**
     * @param null $part
     */
    public static function setPart(&$part): void
    {
        self::$part = $part;
    }

    /**
     * @param string $type
     */
    public static function setType(string $type): void
    {
        self::$type = $type;
    }

    /**
     * @return string
     */
    public static function getType(): string
    {
        return self::$type;
    }

    public function setApiVersion($apiVersion){
        $this->apiVersion=$apiVersion;
    }

    public function getApiVersion($apiVersion){
        return $this->apiVersion;
    }

    public function addFunctionalityToRoutingMapping()
    {
        Restful::setPart(self::$type,"",self::FUNCTION,"read","read");
        Restful::setPart(self::$type,"",self::FUNCTION,"readOp","readOp");
         Restful::setPart(self::$type,"",self::FUNCTION,"vread","vread");
         Restful::setPart(self::$type,"",self::FUNCTION,"update","update");
         Restful::setPart(self::$type,"",self::FUNCTION,"patch","patch");
         Restful::setPart(self::$type,"",self::FUNCTION,"delete","delete");
         Restful::setPart(self::$type,"",self::FUNCTION,"create","create");
         Restful::setPart(self::$type,"",self::FUNCTION,"search","search");
         Restful::setPart(self::$type,"",self::FUNCTION,"history","history");

        /*("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"read","read");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"vread","vread");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"update","update");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"patch","patch");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"delete","delete");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"create","create");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"search","search");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"history","history");
        */
        /*("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"capabilities","capabilities");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"transactions","transactions");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"historyYype","historyType");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"wholeSearch","whileSearch");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"history","history");
        ("FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".self::$type."\\".self::$type)::setPart(self::$type,"",self::FUNCTION,"history","history");*/

    }

    public function getRestfulApi(): Restful
    {
        return self::$part;
        // TODO: Implement getRestfulApi() method.
    }


    public static function doRoutingFunction($params){
        if(self::$part == null) {
            $fhirResourse = $params['FHIRResource'];
            $implementThisInStrategy = "FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\\".$fhirResourse."\\".$fhirResourse;
            self::$part = new $implementThisInStrategy();
        }
        $context = new Context(new self::$part);
        $context->setTypeOfRestCall($params['functionType']);
        $context->setParamsFromUrl($params['paramsFromUrl']);
        $context->setBodyParams($params['paramsFromBody']);
        $context->setStrategyName($fhirResourse);
        $context->setContainer($params['container']);
        return $context->doSomeBusinessLogic();
    }


    public static function extractPid($functionType, $FHIRResource,$json){

        $pid=null;
        $patientSTR="Patient";

        // check if the element is from type FHIR Patient if so the patient id is in the url on certain actions
        if($FHIRResource===$patientSTR){
            if(in_array($functionType,array("read","update","patch","delete"))){
                $url=$_GET['_REWRITE_COMMAND'];
                $pid=substr($url,strpos($url,$patientSTR)+strlen($patientSTR)+1);
            }

        }else{
            preg_match('/Patient\/([0-9]+)/', $json, $matches);
            if(is_array($matches) && count($matches)===2){
                $pid=$matches[1];
            }

        }

        return $pid;
    }

    public static function logRequest($functionType, $FHIRResource, $container)
    {

        $saveToLogFlag=true;
        $event=$FHIRResource."-";

        switch ($functionType) {
            case "create":
                $event.="insert";
                break;
            case "update":
                $event.="replace";
                break;
            case "patch":
                $event.="update";
                break;
            case "delete":
                $event.="delete";
                break;
            default:
                $saveToLogFlag=false;

        }

        if($saveToLogFlag){

            $LogService= $container->get(LogServiceTable::class);
            $AclCheckExtendedService= $container->get(AclCheckExtendedService::class);

            $date=date('Y-m-d H:i:s');
            $user= $AclCheckExtendedService->getAuthUser();
            $groupname=$AclCheckExtendedService->getSiteId();
            $user_notes=$functionType."::".$_GET['_REWRITE_COMMAND'];
            $comments=file_get_contents('php://input'); // json request

            $patient_id=self::extractPid($functionType, $FHIRResource, $comments);

            //This will strip all whitespaces
            $comments=json_encode(json_decode($comments));

            $log_from="clinikal";
            $category="fhir-api";

            //$success="";

            $params=array(
                "date"=>$date,
                "event"=>$event,
                "user"=>$user,
                "groupname"=>$groupname,
                "comments"=>$comments,
                "user_notes"=>$user_notes,
                "patient_id"=>$patient_id,
                //"success"=>$success,
                "log_from"=>$log_from,
                "category"=>$category
            );

            $LogService->safeInsert($params,'id');
        }

    }


    public static function registerRoute($key,$functionType){

        $FHIRResource = self::$type;
        $container = self::getContainer();
     //   Registry::setNewRouting($key , function($params) use ($key, $functionType) {

          //(self::$type)::setPart($functionType,self::ROUTES, $key ,function($params,$functionType){ return self::doRoutingFunction($params,self::$type,$functionType);});
         Restful::setPart(
            self::$type,$functionType,
            self::ROUTES, $key , function (...$paramsFromUrl) use ($functionType, $FHIRResource, $container) {


             $FhirRequestParamsHandler = $container->get(FhirRequestParamsHandler::class);
             $paramsFromBody = $FhirRequestParamsHandler->getRequestParams();

             self::logRequest($functionType, $FHIRResource, $container);

             $response = self::doRoutingFunction([
                     'paramsFromUrl' => $paramsFromUrl,
                     'paramsFromBody' => $paramsFromBody,
                     'FHIRResource' => $FHIRResource,
                     'functionType' => $functionType,
                     'container' => $container
                 ]);

             //convert fhir to array
             $arr = json_decode(json_encode($response), true);

             // must have anonymous function because we are inside static function
             $myArrayFilter = function ($mainArr) use (&$myArrayFilter) {
                 if (empty($mainArr)) {
                     return array();
                 } else {
                     foreach ($mainArr as $index => $subArr) {
                         if (is_array($subArr)) {
                             $mainArr[$index] = $myArrayFilter($subArr);
                             if (empty($mainArr[$index])) {
                                 unset ($mainArr[$index]);
                             }
                         } else {
                             if ($subArr === null || $subArr === "") {
                                 unset ($mainArr[$index]);
                             }
                         }
                     }
                 }
                 return $mainArr;
             };
             //clean response array
             $result = $myArrayFilter($arr);

             return $result;
         });
    }

    /*
 * 3.1.0.17 Summary
These tables present a summary of the interactions described here. Note that all requests may include an optional Accept header to indicate the format used for the response (this is even true for DELETE since an OperationOutcome may be returned).

Interaction	Path	Request
Verb	Content-Type	Body	Prefer	Conditional
read	/[type]/[id]	GET	N/A	N/A	N/A	O: ETag, If-Modified-Since, If-None-Match
vread	/[type]/[id]/_history/[vid]	GET	N/A	N/A	N/A	N/A
update	/[type]/[id]	PUT	R	Resource	O	O: If-Match
patch	/[type]/[id]	PATCH	R (may be a patch type)	Patch	O	O: If-Match
delete	/[type]/[id]	DELETE	N/A	N/A	N/A	N/A
create	/[type]	POST	R	Resource	O	O: If-None-Exist
search	/[type]?	GET	N/A	N/A	N/A	N/A
/[type]/_search?	POST	application/x-www-form-urlencoded	form data	N/A	N/A
search-all	?	GET	N/A	N/A	N/A	N/A
capabilities	/metadata	GET	N/A	N/A	N/A	N/A
transaction	/	POST	R	Bundle	O	N/A
history	/[type]/[id]/_history	GET	N/A	N/A	N/A	N/A
history-type	/[type]/_history	GET	N/A	N/A	N/A	N/A
history-all	/_history	GET	N/A	N/A	N/A	N/A
(operation)	/$[name], /[type]/$[name] or /[type]/[id]/$[name]	POST	R	Parameters	N/A	N/A
GET	N/A	N/A	N/A	N/A
POST	application/x-www-form-urlencoded	form data	N/A	N/A
  */
    public function addRoutes()
    {  // Implement registerRoutes() method.
        $this->registerRoute("GET /fhir/". $this->apiVersion . "/".self::$type."/:id","read" );
        // When read has an operation (like $expand)
        $this->registerRoute("GET /fhir/". $this->apiVersion . "/".self::$type."/:id/:operation","readOp" );
        $this->registerRoute("GET /fhir/". $this->apiVersion . "/".self::$type,"search");

        $this->registerRoute("GET /fhir/". $this->apiVersion . "/".self::$type."/:id/_history/:vid","vread" );
        $this->registerRoute("PUT /fhir/". $this->apiVersion . "/".self::$type."/:id","update" );
        $this->registerRoute("PATCH /fhir/". $this->apiVersion . "/".self::$type."/:id","patch" );
        $this->registerRoute("DELETE /fhir/". $this->apiVersion . "/".self::$type."/:id","delete" );
        $this->registerRoute("POST /fhir/". $this->apiVersion . "/".self::$type,"create" );

        //$this->registerRoute(" ","search-all");
        //$this->registerRoute("GET /fhir/". $this->API_VERSION . "/".self::$type."/metadata","capabilities");
        //$this->registerRoute("POST /","transaction");
        $this->registerRoute("GET /fhir/". $this->apiVersion . "/".self::$type."/:id/_history","history" );
        $this->registerRoute("GET /fhir/". $this->apiVersion . "/".self::$type."/_history","history-type" );
        //$this->registerRoute("GET /fhir/". $this->API_VERSION . "/_history","history-all" );

    }

    public function addErrorCodes(){
        Restful::setErrorCodes();
    }

    public function addSearchParams()
    {
        if(self::$type) {
             Restful::setPart(self::$type,"",self::SEARCH,self::PARAMS,$this->searchParams);
        }
    }

    public function setSearchParams($params)
    {
        $this->searchParams=$params;
    }



}
