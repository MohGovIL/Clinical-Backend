<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Questionnaire
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest;

use GenericTools\Model\ListsTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRServiceRequest;
use OpenEMR\FHIR\R4\FHIRElement\FHIRRequestIntent;
use OpenEMR\FHIR\R4\FHIRElement\FHIRRequestStatus;
use function DeepCopy\deep_copy;
use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;



/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaire;
use OpenEMR\FHIR\R4\FHIRElement\FHIRPublicationStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class FhirServiceRequestMapping extends FhirBaseMapping implements MappingData
{

    private $adapter = null;
    private $container = null;
    private $FHIRServiceRequest = null;


    private $xRayList = array();
    private $medicineList = array();
    private $codeList = array();
    private $categoryList = array();

    CONST ORDER_DETAIL_SYSTEM_XRAY = "details_x_ray";
    CONST ORDER_DETAIL_SYSTEM_MEDICINE = "details_providing_medicine";
    CONST CODE_SYSTEM = "tests_and_treatments";
    CONST CATEGORY_SYSTEM = "service_types";

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRServiceRequest = new FHIRServiceRequest;

        $ListsTable = $this->container->get(ListsTable::class);

        $listXRay = $ListsTable->getListNormalized(self::ORDER_DETAIL_SYSTEM_XRAY);
        $this->setXRayList($listXRay);

        $listMedicine = $ListsTable->getListNormalized(self::ORDER_DETAIL_SYSTEM_MEDICINE);
        $this->setMedicineList($listMedicine);

        $listCode = $ListsTable->getListNormalized(self::CODE_SYSTEM);
        $this->setCodeList($listCode);

        $listCategory = $ListsTable->getListNormalized(self::CATEGORY_SYSTEM);
        $this->setCategoryList($listCategory);
    }

    /*****************************************get lists*******************************************************/

    public function setXRayList($types)
    {
        $this->xRayList=$types;
        return $this->xRayList;
    }

    public function getXRayList()
    {
        return $this->xRayList;
    }

    public function setMedicineList($types)
    {
        $this->medicineList=$types;
        return $this->medicineList;
    }

    public function getMedicineList()
    {
        return $this->medicineList;
    }

    public function setCodeList($types)
    {
        $this->codeList=$types;
        return $this->codeList;
    }

    public function getCodeList()
    {
        return $this->codeList;
    }


    public function setCategoryList($types)
    {
        $this->categoryList=$types;
        return $this->categoryList;
    }

    public function getCategoryList()
    {
        return $this->loincCodes;
    }


    /********************************************************************************************************/


    public function fhirToDb($FHIRServiceRequest)
    {

        $serviceRequestDb= array();
        $serviceRequestDb['id'] = $FHIRServiceRequest->getId()->getValue();

        $categoryCoding=$FHIRServiceRequest->getCategory()[0];
        if(is_object($categoryCoding)){
            $serviceRequestCoding=$categoryCoding->getCoding()[0];
            if(is_object($serviceRequestCoding)){
                $serviceRequestDb['category']=$serviceRequestCoding->getCode()->getValue();
            }
        }

        $codeCode=$FHIRServiceRequest->getCode();
        if(is_object($codeCode)){
            $codeCoding=$codeCode->getCoding()[0];
            if(is_object($codeCoding)){
                $serviceRequestDb['instruction_code']=$codeCoding->getCode()->getValue();
            }
        }

        $reasonCode=$FHIRServiceRequest->getReasonCode()[0];
        if(is_object($reasonCode)){
            $reasonCoding=$reasonCode->getCoding()[0];
            if(is_object($reasonCoding)){
                $serviceRequestDb['reason_code']=$reasonCoding->getCode()->getValue();
            }
        }

        $orderDetailnCode=$FHIRServiceRequest->getOrderDetail()[0];
        if(is_object($orderDetailnCode)){
            $orderDetailCoding=$orderDetailnCode->getCoding()[0];
            if(is_object($orderDetailCoding)){
                $serviceRequestDb['order_detail_code'] = $orderDetailCoding->getCode()->getValue();
                $orderDetailRef=  $orderDetailCoding->getSystem()->getValue();;
                $orderDetailRef=substr($orderDetailRef, strrpos($orderDetailRef, '/') + 1);
                $serviceRequestDb['order_detail_system'] =$orderDetailRef;
            }
        }


        $encRef=$FHIRServiceRequest->getEncounter()->getReference()->getValue() ;
        if(!empty($encRef)){
            $encRef=substr($encRef, strrpos($encRef, '/') + 1);
        }
        $serviceRequestDb['encounter']=$encRef;

        $patientRef=$FHIRServiceRequest->getSubject()->getReference()->getValue();
        if(!empty($patientRef)){
            $patientRef=substr($patientRef, strrpos($patientRef, '/') + 1);
        }
        $serviceRequestDb['patient']=$patientRef;

        $refReasonReference=$FHIRServiceRequest->getReasonReference()[0]->getReference()->getValue();
        if(!empty($refReasonReference)){
            $refReasonReference=substr($refReasonReference, strrpos($refReasonReference, '/') + 1);
        }
        $serviceRequestDb['reason_reference_doc_id']=$refReasonReference;

        $refPerformer=$FHIRServiceRequest->getPerformer()[0]->getReference()->getValue();
        if(!empty($refPerformer)){
            $refPerformer=substr($refPerformer, strrpos($refPerformer, '/') + 1);
        }
        $serviceRequestDb['performer']=$refPerformer;

        $refReference=$FHIRServiceRequest->getRequester()->getReference()->getValue();
        if(!empty($refReference)){
            $refReference=substr($refReference, strrpos($refReference, '/') + 1);
        }
        $serviceRequestDb['requester']=$refReference;

        $serviceRequestDb['patient_instruction']=$FHIRServiceRequest->getPatientInstruction()->getValue();

        $authoredOn =$FHIRServiceRequest->getAuthoredOn()->getValue();
        $authoredOn = $this->convertToDateTime($authoredOn);
        $serviceRequestDb['authored_on']=$authoredOn;

        $occurrenceDateTime = $FHIRServiceRequest->getOccurrenceDateTime()->getValue();
        $occurrenceDateTime = $this->convertToDateTime($occurrenceDateTime);
        $serviceRequestDb['occurrence_datetime']=$occurrenceDateTime;

        $serviceRequestDb['status']=$FHIRServiceRequest->getStatus()->getValue();
        $serviceRequestDb['intent']= $FHIRServiceRequest->getIntent()->getValue();
        $serviceRequestDb['note']=$FHIRServiceRequest->getNote()[0]->getText()->getValue();

        return $serviceRequestDb;
    }


    public function initFhirObject()
    {
        $FHIRServiceRequest = new FHIRServiceRequest;

        $FhirId = $this->createFHIRId(null);
        $FHIRCodeableConcept = $this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
        $FHIRReference = $this->createFHIRReference(array("reference"=>null));
        $FHIRString = $this->createFHIRString(null);
        $FHIRDateTime =  $this->createFHIRDateTime(null);
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

        $this->FHIRServiceRequest=$FHIRServiceRequest;
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
        $ServiceRequestFromDb=$parmas[0];

        if (!is_array($ServiceRequestFromDb) || count($ServiceRequestFromDb) < 1) {
            return null;
        }
        $FHIRServiceRequest=$this->FHIRServiceRequest;


        $FHIRServiceRequest->getId()->setValue($ServiceRequestFromDb['id']);


        if(!is_null($ServiceRequestFromDb['category'])){

            $categoryCoding=$FHIRServiceRequest->getCategory()[0];
            $serviceRequestCoding=$categoryCoding->getCoding()[0];
            $categoryList=$this->getCategoryList();

            $categoryCoding->getText()->setValue($categoryList[$ServiceRequestFromDb['category']]);
            $serviceRequestCoding->getCode()->setValue($ServiceRequestFromDb['category']);
            $serviceRequestCoding->getSystem()->setValue(self::LIST_SYSTEM_LINK.self::CATEGORY_SYSTEM);
        }


        $refEncounter= self::ENCOUNTER_URI.$ServiceRequestFromDb['encounter'];
        $FHIRServiceRequest->getEncounter()->getReference()->setValue($refEncounter);

        $FHIRServiceRequest->getReasonCode()[0]->getCoding()[0]->setCode($ServiceRequestFromDb['reason_code']);

        $refSubject= self::PATIENT_URI.$ServiceRequestFromDb['patient'];
        $FHIRServiceRequest->getSubject()->getReference()->setValue($refSubject);

        if(!is_null($ServiceRequestFromDb['instruction_code'])) {
            $codeCode=$FHIRServiceRequest->getCode();
            $codeCoding = $codeCode->getCoding()[0];
            $codeList=$this->getCodeList();
            $codeCode->getText()->setValue($codeList[$ServiceRequestFromDb['instruction_code']]);
            $codeCoding->getCode()->setValue($ServiceRequestFromDb['instruction_code']);
            $codeCoding->getSystem()->setValue(self::LIST_SYSTEM_LINK.self::CODE_SYSTEM);
        }

        $orderDetail=$FHIRServiceRequest->getOrderDetail()[0]->getCoding()[0];

        $orderDetail->setCode($ServiceRequestFromDb['order_detail_code']);
        $orderDetail->getSystem()->setValue(self::LIST_SYSTEM_LINK.$ServiceRequestFromDb['order_detail_system']);

        $FHIRServiceRequest->getPatientInstruction()->setValue($ServiceRequestFromDb['patient_instruction']);

        $refReference = self::PRACTITIONER_URI. $ServiceRequestFromDb['requester'];
        $FHIRServiceRequest->getRequester()->getReference()->setValue($refReference);

        $authoredOn = $this->createFHIRDateTime(null,null,$ServiceRequestFromDb['authored_on']);
        $FHIRServiceRequest->setAuthoredOn($authoredOn);

        $FHIRServiceRequest->getStatus()->setValue($ServiceRequestFromDb['status']);

        $FHIRServiceRequest->getIntent()->setValue($ServiceRequestFromDb['intent']);

        $FHIRServiceRequest->getNote()[0]->setText($ServiceRequestFromDb['note']);

        $refPerformer=self::PRACTITIONER_URI. $ServiceRequestFromDb['performer'];
        $FHIRServiceRequest->getPerformer()[0]->getReference()->setValue($refPerformer);

        $occurrenceDateTime = $this->createFHIRDateTime(null,null,$ServiceRequestFromDb['occurrence_datetime']);
        $FHIRServiceRequest->setOccurrenceDateTime($occurrenceDateTime);

        $refReasonReference=self::DOCUMENT_REFERENCE_URI. $ServiceRequestFromDb['reason_reference_doc_id'];
        $FHIRServiceRequest->getReasonReference()[0]->getReference()->setValue($refReasonReference);

        return $FHIRServiceRequest;


    }

    public function validateDb($data)
    {
        return true;
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
        $this->arrayToFhirObject($this->FHIRServiceRequest,$data);
        $dBdata = $this->fhirToDb($this->FHIRServiceRequest);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        /*
        $relatedPersonTable = $this->container->get(RelatedPersonTable::class);
        $primaryKey='id';
        unset($data['related_person']['id']);
        $updated=$relatedPersonTable->safeUpdate($data['related_person'],array($primaryKey=>$id));

        if(!is_array($updated)){
            return ErrorCodes::http_response_code('400','Error inserting to db');
        }

        $this->initFhirObject();
        return $this->DBToFhir($updated);
        */
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




}
