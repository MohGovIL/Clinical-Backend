<?php
/**
 * Date: 21/01/20
 * @author  eyal wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Condition
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MedicationRequest;

use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsOpenEmrTable;
use GenericTools\Model\ListsTable;
use Interop\Container\ContainerInterface;

/*include FHIR*/

use Laminas\Form\Annotation\Instance;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRMedicationRequest;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;

use OpenEMR\FHIR\R4\FHIRElement\FHIRMedicationrequestStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIRDosage;
use OpenEMR\FHIR\R4\FHIRResource\FHIRDosage\FHIRDosageDoseAndRate;
use OpenEMR\FHIR\R4\FHIRResource\FHIRMedicationRequest\FHIRMedicationRequestSubstitution;
use phpDocumentor\Reflection\Types\Object_;
use function DeepCopy\deep_copy;

class FhirMedicationRequestMapping extends FhirBaseMapping  implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIRMedicationRequest = null;

    private $route_list=array();
    private $interval_list=array();
    private $form_list=array();
    private $site_list=array();
    private $status_list=array();
    CONST DRUG_SYSTEM="http://clinikal/valueset/drugs";

    CONST DRUG_ROUTE_LIST = "drug_route";
    CONST DRUG_INTERVAL_LIST = "drug_interval";
    CONST DRUG_FORM_LIST = "drug_form";
    CONST DRUG_SITE_LIST = "drug_site";
    CONST STATUS_LIST = "medicationrequest_status";
    CONST DOSAGE_UNIT_SYSTEM = "http://clinikal/valueset/units";




    public function __construct(ContainerInterface $container)
    {

        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRMedicationRequest = new FHIRMedicationRequest;

        $ListsTable = $this->container->get(ListsTable::class);


        $listForm = $ListsTable->getList(self::DRUG_FORM_LIST);
        $this->setFormList($listForm);

        $listInterval = $ListsTable->getList(self::DRUG_INTERVAL_LIST);
        $this->setIntervalList($listInterval);


        $listRoute = $ListsTable->getList(self::DRUG_ROUTE_LIST);
        $this->setRouteList($listRoute);

        $listSite = $ListsTable->getList(self::DRUG_SITE_LIST);
        $this->setSiteList($listSite);

        $listStatus = $ListsTable->getList(self::STATUS_LIST);
        $this->setStatusList($listStatus);

    }


    /**
     * set fhir object
     */
    public function setFHIR($fhir=null)
    {
        if(is_null($fhir)){
            $this->FHIRMedicationRequest = new FHIRMedicationRequest;
            return $this->FHIRMedicationRequest;
        }
        try{
            $this->FHIRMedicationRequest = new FHIRMedicationRequest($fhir);
            return $this->FHIRMedicationRequest;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * return fhir object
     */
    public function getFHIR()
    {
        return $this->FHIRMedicationRequest;
    }

    public function setRouteList($list)
    {
        foreach($list as $code =>$dataArr){
            $this->route_list[$code]=$dataArr['title'];
        }
        return $this->route_list;
    }

    public function getRouteList()
    {
        return $this->route_list;
    }

    public function setIntervalList($list)
    {
        foreach($list as $code =>$dataArr){
            $this->interval_list[$code]=$dataArr['title'];
        }
        return $this->interval_list;
    }

    public function getIntervalList()
    {
        return $this->interval_list;
    }

    public function setFormList($list)
    {
        foreach($list as $code =>$dataArr){
            $this->form_list[$code]=$dataArr['title'];
        }
        return $this->form_list;
    }

    public function getFormList()
    {
        return $this->form_list;
    }

    public function setSiteList($list)
    {
        foreach($list as $code =>$dataArr){
            $this->site_list[$code]=$dataArr['title'];
        }
        return $this->site_list;
    }

    public function getSiteList()
    {
        return $this->site_list;
    }

    public function setStatusList($list)
    {
        foreach($list as $code =>$dataArr){
            $this->status_list[$code]=$dataArr['title'];
        }
        return $this->status_list;
    }

    public function getStatusList()
    {
        return $this->status_list;
    }


    /**
     * convert FHIRMedicationRequest to db array
     *
     * @param FHIRMedicationRequest
     *
     * @return array;
     */
    public function fhirToDb($FHIRMedicationRequest)
    {
        $dbObservation = array();

        $dbObservation['id']=$FHIRMedicationRequest->getId()->getValue();

        $FHIRdate= $FHIRMedicationRequest->getIssued()->getValue();
        $dbObservation['date']= $this->convertToDateTime($FHIRdate);

        $pidRef=$FHIRMedicationRequest->getSubject()->getReference()->getValue();
        if (strpos($pidRef, self::PATIENT_URI) !== false ) {
            $dbObservation['pid']= (!empty($pidRef)) ? substr($pidRef,strlen(self::PATIENT_URI),20) : null;
        }else{
            $dbObservation['pid']=null;
        }

        $userRef=$FHIRMedicationRequest->getPerformer()[0]->getReference()->getValue();
        if (strpos($userRef, self::PRACTITIONER_URI) !== false ) {
            $dbObservation['user']= (!empty($userRef)) ? substr($userRef,strlen(self::PRACTITIONER_URI),20) : null;
        }else{
            $dbObservation['user']=null;
        }

        $eidRef=$FHIRMedicationRequest->getEncounter()->getReference()->getValue();
        if (strpos($eidRef, self::ENCOUNTER_URI) !== false ) {
            $dbObservation['eid']= (!empty($eidRef)) ? substr($eidRef,strlen(self::ENCOUNTER_URI),20) : null;
        }else{
            $dbObservation['eid'] = null;
        }

        $dbObservation['activity'] =  $FHIRMedicationRequest->getStatus()->getValue();
        $dbObservation['note'] = $FHIRMedicationRequest->getNote()[0]->getText()->getValue();
        $dbObservation['category'] = $FHIRMedicationRequest->getCategory()[0]->getText()->getValue();

        $components=$FHIRMedicationRequest->getComponent();

        $LonicToDbMappig=$this->getLonicToDbMappig();

        foreach($components as $index => $comp){

            $code=$comp->getValueCodeableConcept()->getCoding()[0];
            $codeVal=$code->getCode()->getValue();
            if(!is_null($codeVal)){
                $system=$code->getSystem()->getValue();
                $lonicCode=substr($system, strrpos($system, '/') + 1);
                $dbObservation[$LonicToDbMappig[$lonicCode]]=$codeVal;
            }

            $Quantity=$comp->getValueQuantity()->getValue();
            $QuantityVal=$Quantity->getValue();
            if(!is_null($QuantityVal)){
                $lonicCode=$comp->getValueQuantity()->getCode()->getValue();
                $dbObservation[$LonicToDbMappig[$lonicCode]]=$QuantityVal;
            }
        }

        return $dbObservation;
    }

    /**
     * create FHIRMedicationRequest
     *
     * @param  string
     * @return array
     * @throws
     */


    public function parsedJsonToDb($parsedData)
    {
        $dbObservation = array();
        return $dbObservation;
    }

    public function validateDb($data){
        $flag =true;
        return $flag;
    }

    public function initFhirObject(){

        $FHIRMedicationRequest = new FHIRMedicationRequest();
        $FhirId = $this->createFHIRId(null);
        $FHIRMedicationRequest->setId($FhirId);

        $FHIRReference=  $this->createFHIRReference(["reference" => null]);

        $FHIRMedicationRequest->setSubject(deep_copy($FHIRReference));
        $FHIRMedicationRequest->setEncounter(deep_copy($FHIRReference));
        $FHIRMedicationRequest->setRecorder(deep_copy($FHIRReference));

        $FHIRMedicationrequestStatus=$this->createFHIRMedicationRequestStatus();
        $FHIRMedicationRequest->setStatus($FHIRMedicationrequestStatus);

        $FHIRDateTime=$this->createFHIRDateTime(null);
        $FHIRMedicationRequest->setAuthoredOn($FHIRDateTime);

        $note=$this->createFHIRAnnotation(array());
        $FHIRMedicationRequest->addNote($note);

        $FHIRMedicationRequestSubstitution=$this->createFHIRMedicationRequestSubstitution(array());
        $FHIRMedicationRequest->setSubstitution($FHIRMedicationRequestSubstitution);

        $FHIRDosage= $this->createFHIRDosage(array());
        $FHIRMedicationRequest->addDosageInstruction($FHIRDosage);


        $this->FHIRMedicationRequest=$FHIRMedicationRequest;
        return $FHIRMedicationRequest;

    }

    public function DBToFhir(...$params)
    {
        $medicationRequestDataFromDb = $params[0];
        $FHIRMedicationRequest =$this->FHIRMedicationRequest;

        if(!empty($medicationRequestDataFromDb)){

            $FHIRMedicationRequest->getId()->setValue($medicationRequestDataFromDb['id']);

            if(!is_null($medicationRequestDataFromDb['patient_id'])){
                $patientRef=self::PATIENT_URI . $medicationRequestDataFromDb['patient_id'];
                $FHIRMedicationRequest->getSubject()->getReference()->setValue($patientRef);
            }

            if(!is_null($medicationRequestDataFromDb['encounter'])){
                $encRef=self::ENCOUNTER_URI . $medicationRequestDataFromDb['encounter'];
                $FHIRMedicationRequest->getEncounter()->getReference()->setValue($encRef);
            }

            if(!is_null($medicationRequestDataFromDb['provider_id'])){
                $recorderRef=self::PRACTITIONER_URI . $medicationRequestDataFromDb['provider_id'];
                $FHIRMedicationRequest->getRecorder()->getReference()->setValue($recorderRef);
            }

            if(!is_null($medicationRequestDataFromDb['datetime'])){
                $authoredOnDate=$this->createFHIRDateTime(null,null,$medicationRequestDataFromDb['datetime '],false);
                $FHIRMedicationRequest->getAuthoredOn()->setValue($authoredOnDate);
            }

            $FHIRMedicationRequest->getNote()[0]->getText()->setValue($medicationRequestDataFromDb['note']);

            $bool=($medicationRequestDataFromDb['substitute']==1) ? true : false;
            $FHIRBoolean=$this->createFHIRBoolean($bool);
            $FHIRMedicationRequest->getSubstitution()->getAllowedBoolean()->setValue($FHIRBoolean);
        }

        $drugCode=$medicationRequestDataFromDb['drug_id'];
        $drugDisplay=$medicationRequestDataFromDb['drug'];
        $drugSystem=self::DRUG_SYSTEM;

        $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>$drugCode,"display"=>$drugDisplay,"system"=>$drugSystem));
        $FHIRMedicationRequest->setMedicationCodeableConcept($FHIRCodeableConcept);

        $substituteDbVal=$medicationRequestDataFromDb['substitute'];
          if(!is_null($substituteDbVal)) {
              $bool= (in_array($substituteDbVal,array(1,true,"1")) ) ? true : false;
              $FHIRBoolean = $this->createFHIRBoolean($bool);
              $FHIRMedicationRequest->getSubstitution()->setAllowedBoolean($FHIRBoolean);
          }

        if(!is_null($medicationRequestDataFromDb['active'])) {
                $statusList = $this->getStatusList();
                $fhirStatus=$statusList[$medicationRequestDataFromDb['active']];
                $FHIRMedicationRequest->getStatus()->setValue($fhirStatus);
        }



        //*************************************************************************************************************

        $dosageInstruction=$FHIRMedicationRequest->getDosageInstruction()[0];
        $timing=$dosageInstruction->getTiming();

        $boundsPeriod=$timing->getRepeat()->getBoundsPeriod();

        if(!is_null($medicationRequestDataFromDb['dosage'])){
            $FHIRDecimal=$this->createFHIRDecimal($medicationRequestDataFromDb['dosage']);
            $dosageInstruction->getDoseAndRate()[0]->getDoseQuantity()->setValue($FHIRDecimal);
        }

        $maxDosePerAdministration=$dosageInstruction->getMaxDosePerAdministration();

        if(!is_null($medicationRequestDataFromDb['quantity'])){
            $FHIRDecimal=$this->createFHIRDecimal($medicationRequestDataFromDb['quantity']);
            $maxDosePerAdministration->setValue($FHIRDecimal);
        }

        if(!is_null($medicationRequestDataFromDb['size'])){
            $FHIRCode=$this->createFHIRCode($medicationRequestDataFromDb['size']);
            $maxDosePerAdministration->setCode($FHIRCode);
        }

        if(!is_null($medicationRequestDataFromDb['unit'])){
            $FHIRString=$this->createFHIRString($medicationRequestDataFromDb['unit']);
            $maxDosePerAdministration->setUnit($FHIRString);
            $maxDosePerAdministration->setSystem(self::DOSAGE_UNIT_SYSTEM);

        }

        if(!is_null($medicationRequestDataFromDb['end_date'])){
            $end= $this->createFHIRDateTime($medicationRequestDataFromDb['end_date'],null,null,false);
            $boundsPeriod->getEnd()->setValue($end);
        }

        if(!is_null($medicationRequestDataFromDb['start_date'])){
            $start= $this->createFHIRDateTime($medicationRequestDataFromDb['start_date'],null,null,false);
            $boundsPeriod->getStart()->setValue($start);
        }


        $siteCode=$medicationRequestDataFromDb['site'];
        if(!is_null($siteCode)){
            $formList=$this->getSiteList();
            $methodText=$formList[$siteCode];
            $methodSystem=self::LIST_SYSTEM_LINK.self::DRUG_SITE_LIST;
            $method=$this->createFHIRCodeableConcept(array("code"=>$siteCode,"text"=>$methodText,"system"=>$methodSystem));
            $dosageInstruction->setSite($method);
        }

        $formCode=$medicationRequestDataFromDb['form'];
        if(!is_null($formCode)){
            $formList=$this->getFormList();
            $methodText=$formList[$formCode];
            $methodSystem=self::LIST_SYSTEM_LINK.self::DRUG_FORM_LIST;
            $method=$this->createFHIRCodeableConcept(array("code"=>$formCode,"text"=>$methodText,"system"=>$methodSystem));
            $dosageInstruction->setMethod($method);
        }

        $routeCode=$medicationRequestDataFromDb['route'];
        if(!is_null($routeCode)){
            $routeList=$this->getRouteList();
            $methodText=$routeList[$routeCode];
            $methodSystem=self::LIST_SYSTEM_LINK.self::DRUG_ROUTE_LIST;
            $route=$this->createFHIRCodeableConcept(array("code"=>$routeCode,"text"=>$methodText,"system"=>$methodSystem));
            $dosageInstruction->setRoute($route);
        }

        $intervalCode=$medicationRequestDataFromDb['interval'];
        if(!is_null($intervalCode)){

            $intervalList=$this->getIntervalList();
            $methodText=$intervalList[$intervalCode];
            $methodSystem=self::LIST_SYSTEM_LINK.self::DRUG_INTERVAL_LIST;
            $interval=$this->createFHIRCodeableConcept(array("code"=>$intervalCode,"text"=>$methodText,"system"=>$methodSystem));
            $timing->setCode($interval);
        }

        //*************************************************************************************************************

        $this->FHIRMedicationRequest=$FHIRMedicationRequest;

        return $FHIRMedicationRequest;
    }

    public function parsedJsonToFHIR($data)

    {
        $FHIRMedicationRequest =$this->FHIRMedicationRequest;

        $this->FHIRMedicationRequest=$FHIRMedicationRequest;

        return $FHIRMedicationRequest;
    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        $this->arrayToFhirObject($this->FHIRMedicationRequest,$data);
        $dBdata = $this->fhirToDb($this->FHIRMedicationRequest);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        $listsOpenEmrTable = $this->container->get(ListsOpenEmrTable::class);
        $flag=$this->validateDb($data);
        if($flag){
            $primaryKey='id';
            $primaryKeyValue=$id;
            unset($data[$primaryKey]);
            $rez=$listsOpenEmrTable->safeUpdate($data,array($primaryKey=>$primaryKeyValue));
            if(is_array($rez)){
                $this->initFhirObject();
                $patient=$this->DBToFhir($rez);
                return $patient;
            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','object is not valid');
        }
        //this never happens since ErrorCodes call to exit()
        return false;
    }


    /**
     * create FHIRMedicationRequestStatus
     *
     * @param string
     *
     * @return FHIRMedicationRequestStatus | null
     */
    public function createFHIRMedicationRequestStatus($code=null){
        $FHIRMedicationRequestStatus= new FHIRMedicationRequestStatus;
        if(!is_null($code)) {
            $codeVal=$this->createFHIRCode($code)->getValue();
            $FHIRMedicationRequestStatus->setValue($codeVal);
        }

        return $FHIRMedicationRequestStatus;

    }


    /**
     * @param $data
     * @return FHIRDosage
     */
    public function createFHIRDosage($data){

        $FHIRDosage= new FHIRDosage;
        $FHIRCodeableConcept= $this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));

        $dataNotEmpty=!(empty($data));

        if($dataNotEmpty && isset($data['dosagedoseandrate']) && $this->checkFHIRType($data['dosagedoseandrate'],'FHIRDosageDoseAndRate')){
            $FHIRDosage->addDoseAndRate($data['dosagedoseandrate']);
        }else{
            $FHIRDosageDoseAndRate=$this->createFHIRDosageDoseAndRate(array());
            $FHIRDosage->addDoseAndRate($FHIRDosageDoseAndRate);
        }


        if($dataNotEmpty && isset($data['timing']) && $this->checkFHIRType($data['timing'],'timing')){
            $FHIRDosage->setTiming($data['timing']);
        }else{
            $FHIRTiming=$this->createFHIRTiming(array());
            $FHIRDosage->setTiming($FHIRTiming);
        }

        if($dataNotEmpty && isset($data['maxdoseperadministration']) && $this->checkFHIRType($data['maxdoseperadministration'],'FHIRQuantity')){
            $FHIRDosage->setMaxDosePerAdministration($data['maxdoseperadministration']);
        }else{
            $FHIRQuantity=$this->createFHIRQuantity(array());
            $FHIRDosage->setMaxDosePerAdministration($FHIRQuantity);
        }

        if($dataNotEmpty && isset($data['site']) && $this->checkFHIRType($data['site'],'FHIRCodeableConcept')){
            $FHIRDosage->setSite($data['site']);
        }else{
            $FHIRDosage->setSite(deep_copy($FHIRCodeableConcept));
        }

        if($dataNotEmpty && isset($data['method']) && $this->checkFHIRType($data['method'],'FHIRCodeableConcept')){
            $FHIRDosage->setMethod($data['method']);
        }else{
            $FHIRDosage->setMethod(deep_copy($FHIRCodeableConcept));
        }

        if($dataNotEmpty && isset($data['rote']) && $this->checkFHIRType($data['route'],'FHIRCodeableConcept')){
            $FHIRDosage->setRoute($data['rote']);
        }else{
            $FHIRDosage->setRoute(deep_copy($FHIRCodeableConcept));
        }

        return $FHIRDosage;
    }

    /**
     * @param $data
     * @return FHIRDosageDoseAndRate
     */
    public function createFHIRDosageDoseAndRate($data){

        $FHIRDosageDoseAndRate= new FHIRDosageDoseAndRate;

        if(is_array($data['quantity'])){
            $FHIRQuantity=$this->createFHIRQuantity($data['quantity']);
        }else{
            $FHIRQuantity=$this->createFHIRQuantity(array());
        }
        $FHIRDosageDoseAndRate->setDoseQuantity($FHIRQuantity);


        if(is_array($data['quantity'])){
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept($data['type']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
        }

        $FHIRDosageDoseAndRate->setType($FHIRCodeableConcept);

        return $FHIRDosageDoseAndRate;

    }


    /**
     * create FHIRMedicationRequestSubstitution
     *
     * @param $data
     *
     * @return FHIRMedicationRequestSubstitution | null
     */
    public function createFHIRMedicationRequestSubstitution( array $data)
    {
        $FHIRMedicationRequestSubstitution= new FHIRMedicationRequestSubstitution;

         if(!empty($data['allowed'])){
             $FHIRBoolean=$this->createFHIRBoolean($data['allowed']);
         }else{
             $FHIRBoolean=$this->createFHIRBoolean(null);
         }
        $FHIRMedicationRequestSubstitution->setAllowedBoolean($FHIRBoolean);

        if(!empty($data['allowed'])){
            $allowed=$this->createFHIRCodeableConcept($data['allowed']);
        }else{
            $allowed=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
        }
        $FHIRMedicationRequestSubstitution->setAllowedCodeableConcept($allowed);

        if(!empty($data['reason'])){
            $reason=$this->createFHIRCodeableConcept($data['reason']);
        }else{
            $reason=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
        }
        $FHIRMedicationRequestSubstitution->setReason($reason);

        return $FHIRMedicationRequestSubstitution;

    }

}







