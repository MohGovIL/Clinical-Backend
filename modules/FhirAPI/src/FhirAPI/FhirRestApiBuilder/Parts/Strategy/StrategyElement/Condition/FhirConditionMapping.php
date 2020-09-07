<?php
/**
 * Date: 21/01/20
 * @author  eyal wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Condition
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Condition;

use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\IssueEncounterTable;
use GenericTools\Model\ValueSetsTable;
use OpenEMR\FHIR\R4\FHIRResource\FHIRCondition\FHIRConditionEvidence;
use OpenEMR\FHIR\R4\FHIRResource\FHIRCondition\FHIRConditionStage;
use GenericTools\Model\ListsOpenEmrTable;
use GenericTools\Model\ListsTable;
use Interop\Container\ContainerInterface;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIRElementValidation;

/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRCondition;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;

use function DeepCopy\deep_copy;

class FhirConditionMapping extends FhirBaseMapping  implements MappingData
{

    const OUTCOME_LIST ='outcome';
    const OCCURRENCE_LIST ='occurrence';
    const RESOLVED =array('inactive','remission','resolve');

    private $adapter = null;
    private $container = null;
    private $FHIRCondition = null;
    private $outcomeTypes= array();
    private $occurrenceTypes= array();

    use FHIRElementValidation;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRCondition = new FHIRCondition;

        $ListsTable = $this->container->get(ListsTable::class);

        $listOutcome = $ListsTable->getListNormalized(self::OUTCOME_LIST);
        $this->setOutcomeTypes($listOutcome);

        $listOccurrence = $ListsTable->getListNormalized(self::OCCURRENCE_LIST);
        $this->setOccurrenceTypes($listOccurrence);
    }


    /**
     * set fhir object
     */
    public function setFHIR($fhir=null)
    {
        if(is_null($fhir)){
            $this->FHIRCondition = new FHIRCondition;
            return $this->FHIRCondition;
        }
        try{
            $this->FHIRCondition = new FHIRCondition($fhir);
            return $this->FHIRCondition;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * return fhir object
     */
    public function getFHIR()
    {
        return $this->FHIRCondition;
    }

    public function setOutcomeTypes($types)
    {
        $this->outcomeTypes=$types;
        return $this->outcomeTypes;
    }

    public function getOutcomeTypes()
    {
        return $this->outcomeTypes;
    }

    public function setOccurrenceTypes($types)
    {
        $this->occurrenceTypes=$types;
        return $this->occurrenceTypes;
    }

    public function getOccurrenceTypes()
    {
        return $this->occurrenceTypes;
    }


    /**
     * convert FHIRCondition to db array
     *
     * @param FHIRCondition
     *
     * @return array;
     */
    public function fhirToDb($FHIRCondition)
    {
        $dbCondition = array();

        $dbCondition['id']=$FHIRCondition->getId()->getValue();


        $dbCondition['outcome']=$FHIRCondition->getClinicalStatus()->getCoding()[0]->getCode()->getValue();


        $categoryCoding= $FHIRCondition->getCategory()[0]->getCoding()[0];

        $dbCondition['list_option_id']=$categoryCoding->getCode()->getValue();

        $typeRef=$categoryCoding->getSystem()->getValue();
        $dbCondition['type']=substr($typeRef, strrpos($typeRef, '/') + 1);

        $stage=$FHIRCondition->getStage()[0];

        $dbCondition['title']=$stage->getSummary()->getText()->getValue();

        $dbCondition['begdate']=$FHIRCondition->getOnsetDateTime()->getValue();

        $dbCondition['date']=$FHIRCondition->getRecordedDate()->getValue();

        $dbCondition['enddate']=$FHIRCondition->getAbatementDateTime()->getValue();

        $dbCondition['occurrence']=$stage->getType()->getCoding()[0]->getCode()->getValue();

        $code= $FHIRCondition->getCode()->getCoding()[0];

        $codFromDb=array();

        $valueSet=$code->getSystem()->getValue();
        $valueSet=substr($valueSet, strrpos($valueSet, '/') + 1);
        $dbCondition['diagnosis_valueset']=$valueSet;

        $valueSetsTable = $this->container->get(ValueSetsTable::class);
        $codeType=$valueSetsTable->getCodeTypeByValueSet($valueSet);

        $codFromDb[0]=$codeType;
        $codFromDb[1]=$code->getCode()->getValue();

        if(!empty($codFromDb[0]) && !empty($codFromDb[1])){
            $codForDb=implode(":",$codFromDb);
        }else{
            $codForDb=null;
        }
        $dbCondition['diagnosis']=$codForDb;

        $comments = $FHIRCondition->getNote()[0]->getText();
        if(gettype($comments) === "object"){
            $comments = $comments->getValue();
        }
        $dbCondition['comments']=$comments;

        $pidRef=$FHIRCondition->getSubject()->getReference()->getValue() ;
        if(!empty($pidRef)){
            $pidRef=substr($pidRef, strrpos($pidRef, '/') + 1);
        }
        $dbCondition['pid']=$pidRef;

        $userRef=$FHIRCondition->getRecorder()->getReference()->getValue() ;
        if(!empty($userRef)){
            $userRef=substr($userRef, strrpos($userRef, '/') + 1);
        }
        $dbCondition['user']=$userRef;

        $evidenceCode=$FHIRCondition->getEvidence()[0]->getCode()[0]->getCoding()[0];

        $dbCondition['reaction']= $evidenceCode->getCode()->getValue();

        $encounterRef=$FHIRCondition->getEncounter()->getReference()->getValue() ;
        if(!empty($encounterRef)){
            $encounterRef=substr($encounterRef, strrpos($encounterRef, '/') + 1);
        }


        $issueEncounter=array(
                                'list_id'=>null,
                                'pid'=>$dbCondition['pid'],
                                'encounter'=>$encounterRef,
                                'resolved'=>( in_array( $dbCondition['outcome'],self::RESOLVED) ) ? 1 : 0,
                              );

        $dbConditionAll=array('lists'=>$dbCondition,'issue_encounter'=>$issueEncounter);

        return $dbConditionAll;
    }

    /**
     * create FHIRCondition
     *
     * @param  string
     * @return FHIRCondition
     * @throws
     */
    public function DBToFhir(...$params)
    {
        $conditionDataFromDb = $params[0];

        $FHIRCondition =$this->FHIRCondition;
        $FHIRCondition->getId()->setValue($conditionDataFromDb['id']);

        if(!is_null($conditionDataFromDb['outcome']) && $conditionDataFromDb['outcome'] !=="" ){
            $outcomeList=$this->getOutcomeTypes();
            $outcome=$outcomeList[$conditionDataFromDb['outcome']];
            $outcomeCoding= $FHIRCondition->getClinicalStatus()->getCoding()[0];

            $outcomeCoding->getCode()->setValue($conditionDataFromDb['outcome']);
            $outcomeCoding->getSystem()->setValue(self::LIST_SYSTEM_LINK.'outcome');
            $FHIRCondition->getClinicalStatus()->setText($outcome);
        }


        $categoryCoding= $FHIRCondition->getCategory()[0]->getCoding()[0];

        $categoryCoding->getCode()->setValue($conditionDataFromDb['list_option_id']);
        $categoryCoding->getSystem()->setValue("http://clinikal/condition/category/".$conditionDataFromDb['type']);

        $stage=$FHIRCondition->getStage()[0];

        $stage->getSummary()->getText()->setValue($conditionDataFromDb['title']);

        $onsetDateTime= $this->createFHIRDateTime($conditionDataFromDb['begdate']);
        $FHIRCondition->getOnsetDateTime()->setValue($onsetDateTime);

        $recordedDate= $this->createFHIRDateTime(null,null,$conditionDataFromDb['date']);
        $FHIRCondition->getRecordedDate()->setValue($recordedDate);

        $abatementDateTime=$this->createFHIRDateTime($conditionDataFromDb['enddate']);
        $FHIRCondition->getAbatementDateTime()->setValue($abatementDateTime);

        $stageCoding=$stage->getType()->getCoding()[0];

        if(!is_null($conditionDataFromDb['occurrence']) && $conditionDataFromDb['occurrence'] !=="" ){
            $occurrenceList=$this->getOccurrenceTypes();
            $occurrence=$occurrenceList[$conditionDataFromDb['occurrence']];
            $stageCoding->setCode($conditionDataFromDb['occurrence']);
            $stageCoding->getSystem()->setValue(self::LIST_SYSTEM_LINK.'occurrence');
            $stage->getType()->setText($occurrence);
        }

        $code= $FHIRCondition->getCode()->getCoding()[0];

        $codeFromDb=explode(":",$conditionDataFromDb['diagnosis']);

        if(count($codeFromDb)>1){
            $code->getCode()->setValue($codeFromDb[1]);
            $code->getSystem()->setValue(self::LIST_SYSTEM_LINK.$conditionDataFromDb['diagnosis_valueset']);

            $CodesTable =$this->container->get('ImportData\Model\CodesTable');
            $title=$CodesTable->getCodeTitle($codeFromDb[1],$codeFromDb[0]);
            $FHIRCondition->getCode()->getText()->setValue($title);
        }

        $FHIRCondition->getNote()[0]->setText($conditionDataFromDb['comments']);

        if(!empty($conditionDataFromDb['pid'])){
            $FHIRCondition->getSubject()->getReference()->setValue(self::PATIENT_URI.$conditionDataFromDb['pid']) ;
        }

        if(!empty($conditionDataFromDb['user'])){
            $FHIRCondition->getRecorder()->getReference()->setValue(self::PRACTITIONER_URI.$conditionDataFromDb['user']);
        }

        $evidenceCode=$FHIRCondition->getEvidence()[0]->getCode()[0]->getCoding()[0];


        if(!empty($conditionDataFromDb['reaction'])){
            $evidenceCode->getCode()->setValue($conditionDataFromDb['reaction']);
            $evidenceCode->getSystem()->setValue(self::LIST_SYSTEM_LINK.'reaction');
        }

        if(!empty($conditionDataFromDb['encounter'])){
            $FHIRCondition->getEncounter()->getReference()->setValue(self::ENCOUNTER_URI.$conditionDataFromDb['encounter']);
        }


        $this->FHIRCondition=$FHIRCondition;

        return $FHIRCondition;
    }


    public function parsedJsonToDb($parsedData)
    {
        $dbPatient = array();
        if($parsedData['resourceType']!=="Patient"){
            return $dbPatient;
        }

        $dbPatient['pid'] = (is_null($parsedData['id'])) ? null : ucfirst($parsedData['id']);
        $dbPatient['ss'] = (empty($parsedData['identifier'])) ? null :$parsedData['identifier'][0]['value'];
        $dbPatient['sex'] = (is_null($parsedData['gender'])) ? null : ucfirst($parsedData['gender']);
        $dbPatient['DOB'] = (is_null($parsedData['birthDate'])) ? null :$parsedData['birthDate'];
        $dbPatient['deceased_date'] = (is_null($parsedData['deceasedDateTime'])) ? null : substr($parsedData['deceasedDateTime'],0,10);

        $patientName = $parsedData['name'][0];
        $dbPatient['lname'] = (is_null($patientName['family'])) ? null : $patientName['family'];

        $dbPatient['fname'] = (is_null($patientName['given'][0])) ? null : $patientName['given'][0];
        unset($patientName['given'][0]);
        $dbPatient['mname'] = (empty($patientName['given'])) ? null : implode(" ",$patientName['given']);

        $mainAddress = $parsedData['address'][0];

        if(!empty($mainAddress['line'])) {
            $addressType = $mainAddress['type'];

            if ($addressType === "postal" || $addressType === "both") {
                $dbPatient['street'] =$mainAddress['line'][0];
                $dbPatient['mh_house_no'] =$mainAddress['line'][1];
                if($addressType === "both"){
                    $dbPatient['mh_pobox'] =$mainAddress['line'][2];
                }
            } elseif ($addressType === "physical") {
                $dbPatient['mh_pobox'] =$mainAddress['line'][0];
            }
        }
        $dbPatient['postal_code'] = (is_null($mainAddress['postalCode'])) ? null : $mainAddress['postalCode'];
        $dbPatient['city'] = (is_null($mainAddress['city'])) ? null : $mainAddress['city'];
        $dbPatient['country_code'] = (is_null($mainAddress['country'])) ? null : $mainAddress['country'];

        $telecom = $parsedData['telecom'];

        if (!is_null($telecom) && is_array($telecom)) {

            foreach ($telecom as $index => $element) {

                $systemVal = $element['system'];
                $typeVal = $element['use'];

                if ($systemVal === "phone" && $typeVal === "home") {
                    $dbPatient['phone_home'] = $element['value'];
                    continue;
                }
                if ($systemVal === "phone" && $typeVal === "mobile") {
                    $dbPatient['phone_cell'] = $element['value'];
                    continue;
                }
                if ($systemVal === "email") {
                    $dbPatient['email'] = $element['value'];
                    continue;
                }
            }

        } else {
            $dbPatient['email'] = null;
            $dbPatient['phone_home'] = null;
            $dbPatient['phone_cell'] = null;
        }


        return $dbPatient;
    }

    public function initFhirObject(){

        $FHIRCondition = new FHIRCondition();
        $FhirId = $this->createFHIRId(null);
        $FHIRCondition->setId($FhirId);

        $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));

        $FHIRCondition->setClinicalStatus(deep_copy($FHIRCodeableConcept));

        $FHIRCondition->addCategory($FHIRCodeableConcept);

        $FHIRConditionStage=$this->createFHIRConditionStage(array());
        $FHIRCondition->addStage($FHIRConditionStage);

        $FHIRDateTime=  $this->createFHIRDateTime(null);
        $FHIRCondition->setOnsetDateTime(deep_copy($FHIRDateTime));

        $FHIRCondition->setRecordedDate(deep_copy($FHIRDateTime));

        $FHIRCondition->setAbatementDateTime(deep_copy($FHIRDateTime));

        $FHIRCondition->setCode(deep_copy($FHIRCodeableConcept));

        $FHIRAnnotation = $this->createFHIRAnnotation(array());
        $FHIRCondition->addNote($FHIRAnnotation);

        $FHIRReference=$this->createFHIRReference(array("reference"=>null));
        $FHIRCondition->setSubject($FHIRReference);

        $FHIRCondition->setRecorder(deep_copy($FHIRReference));

        $FHIRConditionEvidence =$this->createFHIRConditionEvidence(array());
        $FHIRCondition->addEvidence($FHIRConditionEvidence);

        $FHIRCondition->setEncounter(deep_copy($FHIRReference));

        $this->FHIRCondition=$FHIRCondition;

        return $FHIRCondition;

    }

    public function parsedJsonToFHIR($data)

    {
        $FHIRCondition =$this->FHIRCondition;


        $this->FHIRCondition=$FHIRCondition;

        return $FHIRCondition;
    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        //$FHIRAppointment = $this->parsedJsonToFHIR($data);
        $this->arrayToFhirObject($this->FHIRCondition,$data);
        $dBdata = $this->fhirToDb($this->FHIRCondition);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        $listsOpenEmrTable = $this->container->get(ListsOpenEmrTable::class);
        /*********************************** validate *******************************/
        $conditionDataFromDb = $listsOpenEmrTable->buildGenericSelect(["id"=>$id]);
        $allData=array('new'=>$data,'old'=>$conditionDataFromDb);
        $mainTable=$listsOpenEmrTable->getTableName();
        $isValid=$this->validateDb($allData,$mainTable);
        /***************************************************************************/

        if($isValid){
            $primaryKey='id';
            $primaryKeyValue=$id;
            unset($data['lists'][$primaryKey]);
            $rez=$listsOpenEmrTable->safeUpdate($data['lists'],array($primaryKey=>$primaryKeyValue));
            if(is_array($rez)){

                /**********************************************/
                $issueEncounterTable = $this->container->get(IssueEncounterTable::class);
                $data['issue_encounter']['list_id']= $rez['id'];
                $idArr= array(
                    "list_id"=>$data['issue_encounter']['list_id']
                );
                /***********************************************/

                if(!empty($data['issue_encounter']['encounter'])){

                    $exist =$issueEncounterTable->getDataByParams($idArr);
                    if(empty($exist)){
                        $rez2=$issueEncounterTable->insert($data['issue_encounter']);
                    }else{
                        unset($data['issue_encounter']['list_id']);
                        $rez2=$issueEncounterTable->updateData($idArr,$data['issue_encounter']);
                    }

                    if(!$rez2){
                        //todo : call delete by $dbData['lists']['id']
                        ErrorCodes::http_response_code('500','insert encounter info failed :'.$rez);
                    }else{
                        $rez['encounter'] =  $data['issue_encounter']['encounter'];
                    }
                }else{  // if no encounter delete issue encounter records
                    $rez2=$issueEncounterTable->deleteDataByParams($idArr);
                }

                $this->initFhirObject();
                return $this->DBToFhir($rez);
                
            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','failed validation');
        }
        //this never happens since ErrorCodes call to exit()
        return false;
    }

    /**
     * create FHIRConditionStage
     *
     * @param array
     *
     * @return FHIRConditionStage | null
     */
    public function createFHIRConditionStage(array $stageArr)
    {
        $FHIRConditionStage = new FHIRConditionStage;

        if (key_exists('summary', $stageArr)) {

            $FHIRConditionStage->setSummary($stageArr['summary']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
            $FHIRConditionStage->setSummary($FHIRCodeableConcept);
        }

        if (key_exists('assessment', $stageArr)) {
            $FHIRConditionStage->addAssessment($stageArr['assessment']);

        }else{
            $FHIRReference = $this->createFHIRReference(null);
            $FHIRConditionStage->addAssessment($FHIRReference);
        }

        if (key_exists('type', $stageArr)) {
            $FHIRConditionStage->setType($stageArr['type']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
            $FHIRConditionStage->setType($FHIRCodeableConcept);
        }

        return $FHIRConditionStage;
    }

    /**
     * create FHIRConditionEvidence
     *
     * @param array
     *
     * @return FHIRConditionEvidence | null
     */
    public function createFHIRConditionEvidence(array $conditionEvidenceArr)
    {
        $FHIRConditionEvidence = new FHIRConditionEvidence;

        if (key_exists('code', $conditionEvidenceArr)) {

            $FHIRConditionEvidence->addCode($conditionEvidenceArr['code']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
            $FHIRConditionEvidence->addCode($FHIRCodeableConcept);
        }

        if (key_exists('detail', $conditionEvidenceArr)) {
            $FHIRConditionEvidence->addDetail($conditionEvidenceArr['detail']);

        }else{
            $FHIRReference = $this->createFHIRReference(null);
            $FHIRConditionEvidence->addDetail($FHIRReference);
        }

        return $FHIRConditionEvidence;
    }

}








