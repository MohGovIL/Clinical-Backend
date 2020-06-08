<?php
/**
 * Date: 07/06/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR MedicationStatement
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MedicationStatement;

use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsOpenEmrTable;
use GenericTools\Model\ListsTable;
use Interop\Container\ContainerInterface;

/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRMedicationStatement;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;

use OpenEMR\FHIR\R4\FHIRElement\FHIRMedicationStatusCodes;
use function DeepCopy\deep_copy;

class FhirMedicationStatementMapping extends FhirBaseMapping  implements MappingData
{

    const OUTCOME_LIST ='outcome';
    const OCCURRENCE_LIST ='occurrence';


    private $adapter = null;
    private $container = null;
    private $FHIRMedicationStatement = null;
    private $outcomeTypes= array();
    private $occurrenceTypes= array();


    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRMedicationStatement = new FHIRMedicationStatement;

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
            $this->FHIRMedicationStatement = new FHIRMedicationStatement;
            return $this->FHIRMedicationStatement;
        }
        try{
            $this->FHIRMedicationStatement = new FHIRMedicationStatement($fhir);
            return $this->FHIRMedicationStatement;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * return fhir object
     */
    public function getFHIR()
    {
        return $this->FHIRMedicationStatement;
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
     * convert FHIRMedicationStatement to db array
     *
     * @param FHIRMedicationStatement
     *
     * @return array | void;
     */
    public function fhirToDb($FHIRMedicationStatement)
    {
        $dbMedicationStatement = array();

        $dbMedicationStatement['id']=$FHIRMedicationStatement->getId()->getValue();

        $outcomeList=array_flip($this->getOutcomeTypes());
        $outcomeCoding= $FHIRMedicationStatement->getStatus();
        $outcome= $outcomeCoding->getValue();
        $outcomeId=$outcomeList[$outcome];

        if(!is_null($outcome) && is_null($outcomeId)){
             ErrorCodes::http_response_code('400','outcome error');
             return;
        }else{
            $medicationStatementDataFromDb['outcome']=$outcomeId;
        }

        $categoryCoding= $FHIRMedicationStatement->getCategory()->getCoding()[0];
        $medicationStatementDataFromDb['list_option_id']=$categoryCoding->getCode()->getValue();

        if(!is_null($medicationStatementDataFromDb['list_option_id'])){
            $type=$categoryCoding->getSystem()->getValue();
            $medicationStatementDataFromDb['type']=substr($type, strrpos($type, '/') + 1);
        }else{
            $medicationStatementDataFromDb['type']=null;
        }
        $medicationStatementDataFromDb['title']= $FHIRMedicationStatement->getCategory()->getText()->getValue();

        $period=$FHIRMedicationStatement->getEffectivePeriod();

        $medicationStatementDataFromDb['begdate'] = $period->getStart();

        $medicationStatementDataFromDb['enddate'] = $period->getEnd();

        $medicationStatementDataFromDb['date'] =  $FHIRMedicationStatement->getDateAsserted();

        $code= $FHIRMedicationStatement->getMedicationCodeableConcept()->getCoding()[0];
        $medicationCode = $code->getCode()->getValue();
        $medicationSystem =  $code->getSystem()->getValue();
        if(!is_null($medicationCode) && !is_null($medicationSystem)){
            $medicationStatementDataFromDb['diagnosis']=$medicationSystem.":".$medicationCode;
        }else{
            $medicationStatementDataFromDb['diagnosis']=null;
        }

        $userRef =  $FHIRMedicationStatement->getInformationSource()->getReference()->getValue();
        if(!is_null($userRef) && $userRef!==""){
            $medicationStatementDataFromDb['user'] = substr($userRef, strrpos($userRef, '/') + 1);
        }

        $medicationStatementDataFromDb['comments'] = $FHIRMedicationStatement->getNote()[0]->getText();

        return $dbMedicationStatement;
    }

    /**
     * create FHIRMedicationStatement
     *
     * @param  string
     * @return FHIRMedicationStatement
     * @throws
     */
    public function DBToFhir(...$params)
    {
        $medicationStatementDataFromDb = $params[0];

        $FHIRMedicationStatement =$this->FHIRMedicationStatement;
        $FHIRMedicationStatement->getId()->setValue($medicationStatementDataFromDb['id']);

        if(!is_null($medicationStatementDataFromDb['outcome']) && $medicationStatementDataFromDb['outcome'] !=="" ){
            $outcomeList=$this->getOutcomeTypes();
            $outcome=$outcomeList[$medicationStatementDataFromDb['outcome']];
            $outcomeCoding= $FHIRMedicationStatement->getStatus();
            $outcomeCoding->setId($medicationStatementDataFromDb['outcome']);
            $outcomeCoding->setValue($outcome);
        }

        $categoryCoding= $FHIRMedicationStatement->getCategory()->getCoding()[0];

        if(!is_null($medicationStatementDataFromDb['list_option_id'])){
            $categoryCoding->getCode()->setValue($medicationStatementDataFromDb['list_option_id']);
            $categoryCoding->getSystem()->setValue("clinikal/medicationStatement/category/".$medicationStatementDataFromDb['type']);
        }

        if(!is_null($medicationStatementDataFromDb['title'])){
            $FHIRMedicationStatement->getCategory()->getText()->setValue($medicationStatementDataFromDb['title']);
        }

        $period=$FHIRMedicationStatement->getEffectivePeriod();

        if(!is_null($medicationStatementDataFromDb['begdate'])){
            $date=$this->createFHIRDateTime($medicationStatementDataFromDb['begdate']);
            $period->setStart($date);
        }

        if(!is_null($medicationStatementDataFromDb['enddate'])){
            $date=$this->createFHIRDateTime($medicationStatementDataFromDb['enddate']);
            $period->setEnd($date);
        }

        if(!is_null($medicationStatementDataFromDb['date'])){
            $date=$this->createFHIRDateTime($medicationStatementDataFromDb['date']);
            $FHIRMedicationStatement->setDateAsserted($date);
        }

        $codeFromDb=explode(":",$medicationStatementDataFromDb['diagnosis']);

        if(count($codeFromDb)>1){
            $code= $FHIRMedicationStatement->getMedicationCodeableConcept()->getCoding()[0];
            $code->getCode()->setValue($codeFromDb[1]);
            $code->getSystem()->setValue(self::LIST_SYSTEM_LINK.$codeFromDb[0]);
        }

        if(!empty($medicationStatementDataFromDb['pid'])){
            $FHIRMedicationStatement->getSubject()->getReference()->setValue("Patient/".$medicationStatementDataFromDb['pid']) ;
        }

        if(!empty($medicationStatementDataFromDb['user'])){
            $FHIRMedicationStatement->getInformationSource()->getReference()->setValue("Practitioner/".$medicationStatementDataFromDb['user']);
        }

        $FHIRMedicationStatement->getNote()[0]->setText($medicationStatementDataFromDb['comments']);

        $this->FHIRMedicationStatement=$FHIRMedicationStatement;

        return $FHIRMedicationStatement;
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

    public function validateDb($data){
        $flag =true;
        return $flag;
    }

    public function initFhirObject(){

        $FHIRMedicationStatement = new FHIRMedicationStatement();
        $FhirId = $this->createFHIRId(null);
        $FHIRMedicationStatement->setId($FhirId);

        $FHIRMedicationStatement->setStatus($this->createFHIRMedicationStatusCodes());

        $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
        $FHIRMedicationStatement->setCategory(deep_copy($FHIRCodeableConcept));

        $Period= $this->createFHIRPeriod(array());
        $FHIRMedicationStatement->setEffectivePeriod($Period);

        $FHIRDateTime=  $this->createFHIRDateTime(null);
        $FHIRMedicationStatement->setDateAsserted($FHIRDateTime);

        $FHIRMedicationStatement->setMedicationCodeableConcept(deep_copy($FHIRCodeableConcept));

        $FHIRAnnotation= $this->createFHIRAnnotation(array());
        $FHIRMedicationStatement->addNote($FHIRAnnotation);

        $FHIRReference=$this->createFHIRReference(array("reference"=>null));

        $FHIRMedicationStatement->setSubject(deep_copy($FHIRReference));

        $FHIRMedicationStatement->setInformationSource(deep_copy($FHIRReference));

        $this->FHIRMedicationStatement=$FHIRMedicationStatement;

        return $FHIRMedicationStatement;

    }

    public function parsedJsonToFHIR($data)

    {
        $FHIRMedicationStatement =$this->FHIRMedicationStatement;


        $this->FHIRMedicationStatement=$FHIRMedicationStatement;

        return $FHIRMedicationStatement;
    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        //$FHIRAppointment = $this->parsedJsonToFHIR($data);
        $this->arrayToFhirObject($this->FHIRMedicationStatement,$data);
        $dBdata = $this->fhirToDb($this->FHIRMedicationStatement);
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
     * create FHIRMedicationStatusCodes
     *
     * @param string
     * @param string
     *
     * @return FHIRMedicationStatusCodes | null
     */
    public function createFHIRMedicationStatusCodes($id=null,$value=null){

        $FHIRMedicationStatusCodes= new FHIRMedicationStatusCodes;
        $FHIRMedicationStatusCodes->setId($id);
        $FHIRMedicationStatusCodes->setValue($value);
        return $FHIRMedicationStatusCodes;

    }



}








