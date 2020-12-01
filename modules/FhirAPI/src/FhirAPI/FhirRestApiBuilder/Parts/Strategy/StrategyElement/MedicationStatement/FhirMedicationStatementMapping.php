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
use GenericTools\Model\ValueSetsTable;
use ImportData\Model\CodesTable;
use Interop\Container\ContainerInterface;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRMedicationStatement;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIRElementValidation;
use GenericTools\Model\IssueEncounterTable;
use OpenEMR\FHIR\R4\FHIRElement\FHIRMedicationStatusCodes;
use function DeepCopy\deep_copy;

class FhirMedicationStatementMapping extends FhirBaseMapping  implements MappingData
{

    const OUTCOME_LIST ='outcome';
    const OCCURRENCE_LIST ='occurrence';
    const MED_CATEGORY="medication";
    const RESOLVED =array('completed' ,'entered-in-error' ,'stopped' , 'on-hold' );

    private $adapter = null;
    private $container = null;
    private $FHIRMedicationStatement = null;
    private $outcomeTypes= array();
    private $occurrenceTypes= array();

    use FHIRElementValidation;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRMedicationStatement = new FHIRMedicationStatement;

        $ListsTable = $this->container->get(ListsTable::class);

        $listOutcome = $ListsTable->getListNormalized(self::OUTCOME_LIST,null, null, null, false); // not translated
        $this->setOutcomeTypes($listOutcome);
        $listOccurrence = $ListsTable->getListNormalized(self::OCCURRENCE_LIST, null, null, null, false); // not translated

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
        $medicationStatementDataFromDb = array();

        $medicationStatementDataFromDb['id'] = $FHIRMedicationStatement->getId()->getValue();

        $outcomeList = array_flip($this->getOutcomeTypes());
        $outcomeCoding = $FHIRMedicationStatement->getStatus();
        $outcome = $outcomeCoding->getValue();
        $outcomeId = $outcomeList[$outcome];

        if (!is_null($outcome) && is_null($outcomeId)) {
            ErrorCodes::http_response_code('400', 'outcome error');
            return;
        } else {
            $medicationStatementDataFromDb['outcome'] = $outcomeId;
        }

        $categoryCoding = $FHIRMedicationStatement->getCategory()->getCoding()[0];
        $medicationStatementDataFromDb['list_option_id'] = $categoryCoding->getCode()->getValue();

        $medicationStatementDataFromDb['type'] = self::MED_CATEGORY;

        $medicationStatementDataFromDb['title'] = $FHIRMedicationStatement->getCategory()->getText()->getValue();

        $period = $FHIRMedicationStatement->getEffectivePeriod();

        $medicationStatementDataFromDb['begdate'] = $period->getStart()->getValue();;

        $medicationStatementDataFromDb['enddate'] = $period->getEnd()->getValue();;

        $medicationStatementDataFromDb['date'] = $FHIRMedicationStatement->getDateAsserted()->getValue();;

        $code = $FHIRMedicationStatement->getMedicationCodeableConcept()->getCoding()[0];


        $medicationCode = $code->getCode()->getValue();
        $medicationSystem = $code->getSystem()->getValue();
        $medicationSystem = substr($medicationSystem, strrpos($medicationSystem, '/') + 1);
        $medicationStatementDataFromDb['diagnosis_valueset']=$medicationSystem;

        $valueSetsTable = $this->container->get(ValueSetsTable::class);
        $codeType=$valueSetsTable->getCodeTypeByValueSet($medicationSystem);

        if (!is_null($medicationCode) && !is_null($medicationSystem)) {
            $medicationStatementDataFromDb['diagnosis'] = $codeType . ":" . $medicationCode;
        } else {
            $medicationStatementDataFromDb['diagnosis'] = null;
        }

        $pidRef = $FHIRMedicationStatement->getSubject()->getReference()->getValue();
        if (!is_null($pidRef) && $pidRef !== "") {
            $medicationStatementDataFromDb['pid'] = substr($pidRef, strrpos($pidRef, '/') + 1);
        }

        $userRef = $FHIRMedicationStatement->getInformationSource()->getReference()->getValue();
        if (!is_null($userRef) && $userRef !== "") {
            $medicationStatementDataFromDb['user'] = substr($userRef, strrpos($userRef, '/') + 1);
        }

        $comments =  $FHIRMedicationStatement->getNote()[0]->getText();
        if(gettype($comments) === "object"){
            $comments = $comments->getValue();
        }
        $medicationStatementDataFromDb['comments']=$comments;

        $encounterRef=$FHIRMedicationStatement->getContext()->getReference()->getValue() ;
        if(!empty($encounterRef)){
            $encounterRef=substr($encounterRef, strrpos($encounterRef, '/') + 1);
        }

        $issueEncounter=array(
            'list_id'=>null,
            'pid'=>$medicationStatementDataFromDb['pid'],
            'encounter'=>$encounterRef,
            'resolved'=>( in_array( $medicationStatementDataFromDb['outcome'],self::RESOLVED) ) ? 1 : 0,
        );

        $medicationStatementAll=array('lists'=>$medicationStatementDataFromDb,'issue_encounter'=>$issueEncounter);

        return $medicationStatementAll;
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

        $FHIRMedicationStatement = $this->FHIRMedicationStatement;
        $FHIRMedicationStatement->getId()->setValue($medicationStatementDataFromDb['id']);

        if (!is_null($medicationStatementDataFromDb['outcome']) && $medicationStatementDataFromDb['outcome'] !== "") {
            $outcomeList = $this->getOutcomeTypes();
            $outcome = $outcomeList[$medicationStatementDataFromDb['outcome']];
            $outcomeCoding = $FHIRMedicationStatement->getStatus();
            $outcomeCoding->setId($medicationStatementDataFromDb['outcome']);
            $outcomeCoding->setValue($outcome);
        }

        $categoryCoding = $FHIRMedicationStatement->getCategory()->getCoding()[0];

        if (!is_null($medicationStatementDataFromDb['list_option_id'])) {
            $categoryCoding->getCode()->setValue($medicationStatementDataFromDb['list_option_id']);
            $categoryCoding->getSystem()->setValue("clinikal/medicationStatement/category/" . $medicationStatementDataFromDb['type']);
        }

        if (!is_null($medicationStatementDataFromDb['title'])) {
            $FHIRMedicationStatement->getCategory()->getText()->setValue($medicationStatementDataFromDb['title']);
        }

        $period = $FHIRMedicationStatement->getEffectivePeriod();

        if (!is_null($medicationStatementDataFromDb['begdate'])) {
            $date = $this->createFHIRDateTime($medicationStatementDataFromDb['begdate'],null,null,false);
            $period->setStart($date);
        }

        if (!is_null($medicationStatementDataFromDb['enddate'])) {
            $date = $this->createFHIRDateTime($medicationStatementDataFromDb['enddate'],null,null,false);
            $period->setEnd($date);
        }

        if (!is_null($medicationStatementDataFromDb['date'])) {
            $date = $this->createFHIRDateTime(null, null,$medicationStatementDataFromDb['date']);
            $FHIRMedicationStatement->setDateAsserted($date);
        }

        $codeFromDb = explode(":", $medicationStatementDataFromDb['diagnosis']);

        if (count($codeFromDb) > 1) {
            $code = $FHIRMedicationStatement->getMedicationCodeableConcept()->getCoding()[0];
            $code->getCode()->setValue($codeFromDb[1]);
            $code->getSystem()->setValue(self::LIST_SYSTEM_LINK . $medicationStatementDataFromDb['diagnosis_valueset']);

            $CodesTable =$this->container->get('ImportData\Model\CodesTable');
            $title=$CodesTable->getCodeTitle($codeFromDb[1],$codeFromDb[0]);
            $FHIRMedicationStatement->getMedicationCodeableConcept()->getText()->setValue($title);
        }

        if (!empty($medicationStatementDataFromDb['pid'])) {
            $FHIRMedicationStatement->getSubject()->getReference()->setValue("Patient/" . $medicationStatementDataFromDb['pid']);
        }

        if (!empty($medicationStatementDataFromDb['user'])) {
            $FHIRMedicationStatement->getInformationSource()->getReference()->setValue("Practitioner/" . $medicationStatementDataFromDb['user']);
        }

        $FHIRMedicationStatement->getNote()[0]->setText($medicationStatementDataFromDb['comments']);

        if(!empty($medicationStatementDataFromDb['encounter'])){
            $FHIRMedicationStatement->getContext()->getReference()->setValue(self::ENCOUNTER_URI.$medicationStatementDataFromDb['encounter']);
        }

        $this->FHIRMedicationStatement = $FHIRMedicationStatement;

        return $FHIRMedicationStatement;
    }

    public function parsedJsonToDb($parsedData)
    {
        $dbMedicationStatement = array();
        return $dbMedicationStatement;
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

        $FHIRMedicationStatement->setContext(deep_copy($FHIRReference));

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
        /*********************************** validate *******************************/
        $encounterDataFromDb = $listsOpenEmrTable->buildGenericSelect(["id"=>$id]);
        $allData=array('new'=>$data,'old'=>$encounterDataFromDb);
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








