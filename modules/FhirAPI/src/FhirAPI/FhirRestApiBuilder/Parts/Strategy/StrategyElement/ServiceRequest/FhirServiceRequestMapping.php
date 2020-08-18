<?php
/**
 * Date: 24/03/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Questionnaire
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ValueSet\ValueSet;
use FhirAPI\Model\FhirServiceRequestTable;
use GenericTools\Model\ListsTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRServiceRequest;
use OpenEMR\FHIR\R4\FHIRElement\FHIRRequestIntent;
use OpenEMR\FHIR\R4\FHIRElement\FHIRRequestStatus;
use function DeepCopy\deep_copy;
use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIRElementValidation;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaire;
use OpenEMR\FHIR\R4\FHIRElement\FHIRPublicationStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class FhirServiceRequestMapping extends FhirBaseMapping implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIRServiceRequest = null;
    private $FhirValueSet = null;

    const CODE_SYSTEM = "tests_and_treatments";
    const CATEGORY_SYSTEM = "service_types";
    const EMPTY_TIME = "0000-00-00 00:00:00";

    use FHIRElementValidation;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRServiceRequest = new FHIRServiceRequest;

        $valueSetParams = array('paramsFromUrl' => array(), 'paramsFromBody' => array(), 'container' => $this->container);
        $this->FhirValueSet = new ValueSet($valueSetParams);
    }


    public function fhirToDb($FHIRServiceRequest)
    {

        $serviceRequestDb = array();
        $serviceRequestDb['id'] = $FHIRServiceRequest->getId()->getValue();

        $categoryCoding = $FHIRServiceRequest->getCategory()[0];
        if (is_object($categoryCoding)) {
            $serviceRequestCoding = $categoryCoding->getCoding()[0];
            if (is_object($serviceRequestCoding)) {
                $serviceRequestDb['category'] = $serviceRequestCoding->getCode()->getValue();
            }
        }

        $codeCode = $FHIRServiceRequest->getCode();
        if (is_object($codeCode)) {
            $codeCoding = $codeCode->getCoding()[0];
            if (is_object($codeCoding)) {
                $serviceRequestDb['instruction_code'] = $codeCoding->getCode()->getValue();
            }
        }

        $reasonCode = $FHIRServiceRequest->getReasonCode()[0];
        if (is_object($reasonCode)) {
            $reasonCoding = $reasonCode->getCoding()[0];
            if (is_object($reasonCoding)) {
                $serviceRequestDb['reason_code'] = $reasonCoding->getCode()->getValue();
            }
        }

        $serviceRequestDb['order_detail_code'] = null;
        $serviceRequestDb['order_detail_system'] = null;

        $orderDetailnCode = $FHIRServiceRequest->getOrderDetail()[0];
        if (is_object($orderDetailnCode)) {
            $orderDetailCoding = $orderDetailnCode->getCoding()[0];
            if (is_object($orderDetailCoding)) {
                $serviceRequestDb['order_detail_code'] = $orderDetailCoding->getCode()->getValue();
                $orderDetailRef = $orderDetailCoding->getSystem()->getValue();;
                $orderDetailRef = substr($orderDetailRef, strrpos($orderDetailRef, '/') + 1);
                $serviceRequestDb['order_detail_system'] = $orderDetailRef;
            }
        }


        $encRef = $FHIRServiceRequest->getEncounter()->getReference()->getValue();
        if (!empty($encRef)) {
            $encRef = substr($encRef, strrpos($encRef, '/') + 1);
        }
        $serviceRequestDb['encounter'] = $encRef;

        $patientRef = $FHIRServiceRequest->getSubject()->getReference()->getValue();
        if (!empty($patientRef)) {
            $patientRef = substr($patientRef, strrpos($patientRef, '/') + 1);
        }
        $serviceRequestDb['patient'] = $patientRef;

        $refReasonReference = $FHIRServiceRequest->getReasonReference()[0]->getReference()->getValue();
        if (!empty($refReasonReference)) {
            $refReasonReference = substr($refReasonReference, strrpos($refReasonReference, '/') + 1);
        }
        $serviceRequestDb['reason_reference_doc_id'] = $refReasonReference;

        $refPerformer = $FHIRServiceRequest->getPerformer()[0]->getReference()->getValue();
        if (!empty($refPerformer)) {
            $refPerformer = substr($refPerformer, strrpos($refPerformer, '/') + 1);
        }
        $serviceRequestDb['performer'] = $refPerformer;

        $refReference = $FHIRServiceRequest->getRequester()->getReference()->getValue();
        if (!empty($refReference)) {
            $refReference = substr($refReference, strrpos($refReference, '/') + 1);
        }
        $serviceRequestDb['requester'] = $refReference;

        $serviceRequestDb['patient_instruction'] = $FHIRServiceRequest->getPatientInstruction()->getValue();

        $authoredOn = $FHIRServiceRequest->getAuthoredOn()->getValue();
        $authoredOn = $this->convertToDateTime($authoredOn);
        $serviceRequestDb['authored_on'] = $authoredOn;

        $occurrenceDateTime = $FHIRServiceRequest->getOccurrenceDateTime()->getValue();
        $occurrenceDateTime = $this->convertToDateTime($occurrenceDateTime);
        $serviceRequestDb['occurrence_datetime'] = $occurrenceDateTime;

        $serviceRequestDb['status'] = $FHIRServiceRequest->getStatus()->getValue();
        $serviceRequestDb['intent'] = $FHIRServiceRequest->getIntent()->getValue();
        $serviceRequestDb['note'] = $FHIRServiceRequest->getNote()[0]->getText()->getValue();

        return $serviceRequestDb;
    }

    public function initFhirObject()
    {
        $FHIRServiceRequest = new FHIRServiceRequest;

        $FhirId = $this->createFHIRId(null);
        $FHIRCodeableConcept = $this->createFHIRCodeableConcept(array("code" => null, "text" => "", "system" => ""));
        $FHIRReference = $this->createFHIRReference(array("reference" => null));
        $FHIRString = $this->createFHIRString(null);
        $FHIRDateTime = $this->createFHIRDateTime(null);
        $FHIRRequestStatus = $this->createFHIRRequestStatus(null);
        $FHIRRequestIntent = $this->createFHIRRequestIntent(null);
        $FHIRAnnotation = $this->createFHIRAnnotation(array());

        $FHIRServiceRequest->setId($FhirId);
        $FHIRServiceRequest->addCategory(deep_copy($FHIRCodeableConcept));
        $FHIRServiceRequest->setEncounter(deep_copy($FHIRReference));
        $FHIRServiceRequest->addReasonCode(deep_copy($FHIRCodeableConcept));
        $FHIRServiceRequest->setSubject(deep_copy($FHIRReference));
        $FHIRServiceRequest->setCode(deep_copy($FHIRCodeableConcept));
        $FHIRServiceRequest->addOrderDetail(deep_copy($FHIRCodeableConcept));
        $FHIRServiceRequest->setPatientInstruction(deep_copy($FHIRString));
        $FHIRServiceRequest->setRequester(deep_copy($FHIRReference));
        $FHIRServiceRequest->setAuthoredOn(deep_copy($FHIRDateTime));
        $FHIRServiceRequest->setStatus($FHIRRequestStatus);
        $FHIRServiceRequest->setIntent($FHIRRequestIntent);
        $FHIRServiceRequest->addNote($FHIRAnnotation);
        $FHIRServiceRequest->addPerformer(deep_copy($FHIRReference));
        $FHIRServiceRequest->setOccurrenceDateTime(deep_copy($FHIRDateTime));
        $FHIRServiceRequest->addReasonReference(deep_copy($FHIRReference));

        $this->FHIRServiceRequest = $FHIRServiceRequest;
        return $FHIRServiceRequest;
    }

    /**
     * create FHIRServiceRequest
     *
     * @param array
     * @param bool
     *
     * @return FHIRServiceRequest | FHIRBundle | null
     * @throws
     */
    public function DBToFhir(...$parmas)
    {
        $ServiceRequestFromDb = $parmas[0];

        if (!is_array($ServiceRequestFromDb) || count($ServiceRequestFromDb) < 1) {
            return null;
        }
        $FHIRServiceRequest = $this->FHIRServiceRequest;

        $FHIRServiceRequest->getId()->setValue($ServiceRequestFromDb['id']);

        if (!is_null($ServiceRequestFromDb['category'])) {

            $categoryCoding = $FHIRServiceRequest->getCategory()[0];
            $serviceRequestCoding = $categoryCoding->getCoding()[0];

            $title=$this->getValueSetTitle(self::CATEGORY_SYSTEM,$ServiceRequestFromDb['category']);
            $categoryCoding->getText()->setValue($title);
            $serviceRequestCoding->getCode()->setValue($ServiceRequestFromDb['category']);
            $serviceRequestCoding->getSystem()->setValue(self::LIST_SYSTEM_LINK . self::CATEGORY_SYSTEM);
        }

        if(!is_null($ServiceRequestFromDb['encounter'])){
            $refEncounter = self::ENCOUNTER_URI . $ServiceRequestFromDb['encounter'];
            $FHIRServiceRequest->getEncounter()->getReference()->setValue($refEncounter);
        }

        $FHIRServiceRequest->getReasonCode()[0]->getCoding()[0]->setCode($ServiceRequestFromDb['reason_code']);

        if(!is_null($ServiceRequestFromDb['patient'])){
            $refSubject = self::PATIENT_URI . $ServiceRequestFromDb['patient'];
            $FHIRServiceRequest->getSubject()->getReference()->setValue($refSubject);
        }


        if (!is_null($ServiceRequestFromDb['instruction_code'])) {
            $codeCode = $FHIRServiceRequest->getCode();
            $codeCoding = $codeCode->getCoding()[0];

            $title=$this->getValueSetTitle(self::CODE_SYSTEM,$ServiceRequestFromDb['instruction_code']);
            $codeCode->getText()->setValue($title);
            $codeCoding->getCode()->setValue($ServiceRequestFromDb['instruction_code']);
            $codeCoding->getSystem()->setValue(self::LIST_SYSTEM_LINK . self::CODE_SYSTEM);
        }

        $orderDetailCoding =$FHIRServiceRequest->getOrderDetail()[0];

        if(!is_null($ServiceRequestFromDb['order_detail_code'])  && !is_null($ServiceRequestFromDb['order_detail_system']) ){
            $orderDetail = $orderDetailCoding->getCoding()[0];
            $title=$this->getValueSetTitle($ServiceRequestFromDb['order_detail_system'],$ServiceRequestFromDb['order_detail_code']);
            $orderDetailCoding->getText()->setValue($title);
            $orderDetail->setCode($ServiceRequestFromDb['order_detail_code']);
            $orderDetail->getSystem()->setValue(self::LIST_SYSTEM_LINK . $ServiceRequestFromDb['order_detail_system']);
        }

        $FHIRServiceRequest->getPatientInstruction()->setValue($ServiceRequestFromDb['patient_instruction']);

        if(!is_null($ServiceRequestFromDb['requester'])){
            $refReference = self::PRACTITIONER_URI . $ServiceRequestFromDb['requester'];
            $FHIRServiceRequest->getRequester()->getReference()->setValue($refReference);
        }

        if(!is_null($ServiceRequestFromDb['performer'])){
            $refPerformer = self::PRACTITIONER_URI . $ServiceRequestFromDb['performer'];
            $FHIRServiceRequest->getPerformer()[0]->getReference()->setValue($refPerformer);
        }

        if(!is_null($ServiceRequestFromDb['reason_reference_doc_id'])){
            $refReasonReference = self::DOCUMENT_REFERENCE_URI . $ServiceRequestFromDb['reason_reference_doc_id'];
            $FHIRServiceRequest->getReasonReference()[0]->getReference()->setValue($refReasonReference);
        }

        if($ServiceRequestFromDb['occurrence_datetime']!==self::EMPTY_TIME && !is_null($ServiceRequestFromDb['occurrence_datetime'])){
            $occurrenceDateTime = $this->createFHIRDateTime(null, null, $ServiceRequestFromDb['occurrence_datetime']);
            $FHIRServiceRequest->setOccurrenceDateTime($occurrenceDateTime);
        }

        if($ServiceRequestFromDb['authored_on']!==self::EMPTY_TIME && !is_null($ServiceRequestFromDb['occurrence_datetime']) ){
            $authoredOn = $this->createFHIRDateTime(null, null, $ServiceRequestFromDb['authored_on']);
            $FHIRServiceRequest->setAuthoredOn($authoredOn);
        }


        $FHIRServiceRequest->getStatus()->setValue($ServiceRequestFromDb['status']);
        $FHIRServiceRequest->getIntent()->setValue($ServiceRequestFromDb['intent']);
        $FHIRServiceRequest->getNote()[0]->setText($ServiceRequestFromDb['note']);

        return $FHIRServiceRequest;

    }


    public function parsedJsonToDb($parsedData)
    {
        return array();

    }

    public function parsedJsonToFHIR($parsedData)
    {

        return array();

    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        $this->arrayToFhirObject($this->FHIRServiceRequest, $data);
        $dBdata = $this->fhirToDb($this->FHIRServiceRequest);
        return $dBdata;
    }

    public function updateDbData($data, $id)
    {
        $FhirServiceRequestTable = $this->container->get(FhirServiceRequestTable::class);

        $serviceRequestDataFromDb = $FhirServiceRequestTable->buildGenericSelect(["id"=>$id]);
        $allData=array('new'=>$data,'old'=>$serviceRequestDataFromDb);
        //$mainTable=$formEncounterTable->getTableName();
        $isValid=$this->validateDb($allData,null);

        if ($isValid) {
            $primaryKey = 'id';
            $primaryKeyValue = $id;
            unset($data[$primaryKey]);
            $rez = $FhirServiceRequestTable->safeUpdate($data, array($primaryKey => $primaryKeyValue));
            if (is_array($rez)) {
                $this->initFhirObject();
                $patient = $this->DBToFhir($rez);
                return $patient;
            } else { //insert failed
                ErrorCodes::http_response_code('500', 'insert object failed :' . $rez);
            }
        } else { // object is not valid
            ErrorCodes::http_response_code('406', 'object is not valid');
        }
        //this never happens since ErrorCodes call to exit()
        return false;
    }

    /**
     * @param $value string
     * @return FHIRRequestStatus
     */
    private function createFHIRRequestStatus($value)
    {
        $FHIRRequestStatus = new FHIRRequestStatus;
        $FHIRRequestStatus->setValue($value);
        return $FHIRRequestStatus;
    }

    /**
     * @param $value string
     * @return FHIRRequestIntent
     */
    private function createFHIRRequestIntent($value)
    {
        $FHIRRequestIntent = new FHIRRequestIntent;
        $FHIRRequestIntent->setValue($value);
        return $FHIRRequestIntent;
    }


    private function getValueSetTitle($listName,$value,$operations='$expand')
    {
        $this->FhirValueSet->setOperations(array($operations));
        $this->FhirValueSet->setParamsFromUrl(array($listName));
        $this->FhirValueSet->setParamsFromBody(array('PARAMETERS_FOR_SEARCH_RESULT'=>array ('filter' => array (0 => array ('value' => $value, 'operator' => '=',),),)));
        $ValueSetRez = $this->FhirValueSet->read();
        $expansion=$ValueSetRez->getExpansion();
        //$display=$expansion['contains'][0]['display']->getValue();
        return $expansion->getContains()[0]->getDisplay()->getValue();
    }



}
