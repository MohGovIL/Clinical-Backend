<?php
/**
 * Date: 21/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class MAPPING FOR ORGANIZATION
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Appointment;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\EventCodeReasonMapTable;
use Interop\Container\ContainerInterface;

use GenericTools\Model\PostcalendarCategoriesTable;
use GenericTools\Model\PostcalendarEventsTable;
use GenericTools\Model\ListsTable;

/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRAppointment;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAppointmentStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResource\FHIRAppointment\FHIRAppointmentParticipant;

class FhirAppointmentMapping extends FhirBaseMapping implements MappingData
{
    CONST APPT_STATUS_LIST = 'clinikal_app_statuses';

    private $adapter = null;
    private $container = null;
    private $FHIRAppointment = null;
    private $postCalendarCategoriesList = null;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Zend\Db\Adapter\Adapter');
        $this->FHIRAppointment = new FHIRAppointment;
        $categoriesListTable = $this->container->get(PostcalendarCategoriesTable::class);
        $this->postCalendarCategoriesList = $categoriesListTable->fetchAll(array(), true);
    }

    public function fhirToDb($FhirObject)
    {
        $dbData=array();
        $allData=array();
        $codeReason=array();

        $tempObject=$FhirObject->getId();
        $dbData['pc_eid']= (is_object($tempObject)) ? $tempObject->getValue() : null;

        $tempObject=$FhirObject->getStatus();
        $dbData['pc_apptstatus']=(is_object($tempObject)) ? $tempObject->getValue() : null;

        $tempObject=$FhirObject->getServiceType()[0];
        $tempObject=(is_object($tempObject)) ? $tempObject->getCoding()[0] : null;
        $dbData['pc_service_type']=(is_object($tempObject)) ? $tempObject->getCode() : null;

        $tempObject=$FhirObject->getDescription();
        $dbData['pc_title']=(is_object($tempObject)) ? $tempObject->getValue() : null;

        $tempObject=$FhirObject->getCreated();
        $dbData['pc_time']=(is_object($tempObject)) ? $tempObject->getValue() : null;

        $tempObject=$FhirObject->getComment();
        $dbData['pc_hometext']=(is_object($tempObject)) ? $tempObject->getValue() : null;


        $tempObject=$FhirObject->getStart();
        $startDate=(is_object($tempObject)) ? $tempObject->getValue() : null;
        $onlyDate=(!is_null($startDate)) ? substr($startDate,0,10) : null;
        $time=(!is_null($startDate)) ? substr($startDate,11,8) : null;
        $dbData['pc_eventDate']=$onlyDate;
        $dbData['pc_startTime']=$time;


        $tempObject=$FhirObject->getEnd();
        $endDate=(is_object($tempObject)) ? $tempObject->getValue() : null;
        $onlyDate=(!is_null($onlyDate)) ? $onlyDate : null;substr($endDate,0,10);
        $time=(!is_null($onlyDate)) ? $onlyDate : null;substr($endDate,11,8);
        $dbData['pc_endTime']=$onlyDate;
        $dbData['pc_endDate']=$time;


        $tempObject=$FhirObject->getMinutesDuration();
        $dbData['pc_duration']=(is_object($tempObject)) ? $tempObject->getValue()->getValue()*60 : null;

        $tempObject=$FhirObject->getPriority();
        $dbData['pc_priority']=(is_object($tempObject)) ? $tempObject->getValue() : null;

        $tempObject=$FhirObject->getParticipant();

        foreach($tempObject as $index =>$data){
            $actor=$data->getActor();
            $reference=$actor->getReference();

            if (strpos($reference, 'Patient/') !== false ) {
                $dbData['pc_pid']=(!empty($reference)) ? substr($reference,8,20) : null;

            }elseif (strpos($reference, 'HealthcareService/') !== false) {
                $dbData['pc_healthcare_service_id']=(!empty($reference)) ? substr($reference,18,20) : null;
            }
        }

        $reasonCodeArr=$FhirObject->getReasonCode();

        foreach($reasonCodeArr as $idx =>$data){
            $code=$data->getCoding()[0]->getCode()->getValue();
            $codeReason[]=array("event_id"=>$dbData['pc_eid'],"option_id"=>$code);
        }


        $allData['openemr_postcalendar_events']=$dbData;
        $allData['event_codeReason_map']=$codeReason;

        return $allData;
    }

    /**
     * check if appointment data is valid
     *
     * @param array
     * @return array
     */
    public function convertFieldsToDB($data)
    {
        $dbData = array();
        $mapper = 'RETURN_DB_NAME';

        foreach ($data as $fieldName => $value) {

            switch ($fieldName) {
                case 'id':
                    if ($value !== $mapper) {
                        $dbData['pc_eid'] = $value;
                    } else {
                        return array($fieldName => 'pc_eid');
                    }
                    break;
                case 'status':
                    if ($value !== $mapper) {
                        $dbData['pc_apptstatus'] = $value;
                    } else {
                        return array($fieldName => 'pc_apptstatus');
                    }
                    break;
                case 'description':
                    if ($value !== $mapper) {
                        $dbData['pc_title'] = $value;
                    } else {
                        return array($fieldName => 'pc_title');
                    }
                    break;
                case 'created':
                    if ($value !== $mapper) {
                        $dbData['pc_time'] = $value;
                    } else {
                        return array($fieldName => 'pc_time');
                    }
                    break;
                case 'comment':
                    if ($value !== $mapper) {
                        $dbData['pc_hometext'] = $value;
                    } else {
                        return array($fieldName => 'pc_hometext');
                    }
                    break;
                case 'date':
                case 'start':
                    if ($value !== $mapper) {
                        $dbData['pc_eventDate'] = $value;
                        $dbData['pc_startTime'] = $value;
                    } else {
                        return array($fieldName => 'pc_eventDate');
                    }
                    break;
                case 'end':
                    if ($value !== $mapper) {
                        $dbData['pc_endTime'] = $value;
                        $dbData['pc_eventDate'] = $value;
                    } else {
                        return array($fieldName => 'pc_eventDate');
                    }
                    break;
                case 'requestedPeriod.start':
                    if ($value !== $mapper) {
                        $dbData['pc_eventDate'] = $value;
                    } else {
                        return array($fieldName => 'pc_eventDate');
                    }
                    break;
                case 'requestedPeriod.end':
                    //$dbData['pc_endDate'] = $value;
                    break;
                case 'minutesDuration':
                    if ($value !== $mapper) {
                        $dbData['pc_duration'] = $value;
                    } else {
                        return array($fieldName => 'pc_duration');
                    }
                    break;

                case 'service-type':
                case 'serviceType':
                case 'serviceType.coding.code':
                    if ($value !== $mapper) {
                        $dbData['pc_service_type'] = $value;
                    } else {
                        return array($fieldName => 'pc_service_type');
                    }
                    break;
                case 'participant.actor':
                    if ($value !== $mapper) {
                        $dbData['pc_healthcare_service_id'] = $value;
                    } else {
                        return array($fieldName => 'pc_healthcare_service_id');
                    }
                    break;
                case 'priority':
                    if ($value !== $mapper) {
                        $dbData['pc_priority'] = $value;
                    } else {
                        return array($fieldName => 'pc_priority');
                    }
                    break;
            }
        }

        return $dbData;
    }

    /**
     * create FHIRAppointment
     *
     * @param array
     * @param bool
     *
     * @return FHIRAppointment | FHIRBundle | null
     * @throws
     */
    public function DBToFhir(...$parmas)
    {
        $appointment=$parmas[0];
        $isSingle=$parmas[1];

        if (!is_array($appointment) || count($appointment) < 1) {
            return null;
        }
        $FHIRAppointment=$this->FHIRAppointment;

        $id = $this->createFHIRId($appointment['pc_eid']);
        $FHIRAppointment->setId($id);
        $FHIRAppointmentStatus = new FHIRAppointmentStatus();
        $FHIRAppointmentStatus->setValue($appointment['pc_apptstatus']);
        $FHIRAppointment->setStatus($FHIRAppointmentStatus);


        if(!is_null($appointment['pc_service_type'])){

            $serviceType=$FHIRAppointment->getServiceType()[0];
            $serviceType->setText($appointment['service_title']);
            $coding=$serviceType->getCoding()[0];
            $coding->setCode($appointment['pc_service_type']);
        }

        $FHIRUnsignedInt=$this->createFHIRUnsignedInt($appointment['pc_priority']);
        $FHIRAppointment->setPriority($FHIRUnsignedInt);

        $FHIRString = $this->createFHIRString($appointment['pc_title']);
        $FHIRAppointment->setDescription($FHIRString);

        $FHIRDateTime = $this->createFHIRDateTime($appointment['pc_time']);
        $FHIRAppointment->setCreated($FHIRDateTime);

        $FHIRString = $this->createFHIRString($appointment['pc_hometext']);
        $FHIRAppointment->setComment($FHIRString);

        $FHIRInstant = $this->createFHIRInstant($appointment['pc_eventDate'], $appointment['pc_startTime']);
        $FHIRAppointment->setStart($FHIRInstant);
        $FHIRInstant = $this->createFHIRInstant($appointment['pc_eventDate'], $appointment['pc_endTime']);
        $FHIRAppointment->setEnd($FHIRInstant);

        $FHIRPositiveInt = $this->createFHIRPositiveInt($appointment['pc_duration'] / 60);
        $FHIRAppointment->setMinutesDuration($FHIRPositiveInt);


        // add reasons

        $reasonIds=json_decode($appointment['reason_ids'],true)['items'];
        $reasonTitles=json_decode($appointment['reason_titles'],true)['items'];

        if(!empty($reasonIds)){
            $reasonCode=$FHIRAppointment->getReasonCode()[0];
            $reasonCode->setText($reasonTitles[0]['title']);
            $coding=$reasonCode->getCoding()[0];
            $coding->getCode()->setValue($reasonIds[0]['id']);
            unset($reasonIds[0]);
            unset($reasonTitles[0]);
        }

        foreach($reasonIds as $index => $val){
            $reason=array('code'=>$val['id'],'text'=>$reasonTitles[$index]['title']);
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept($reason);
            $FHIRAppointment->addReasonCode($FHIRCodeableConcept);
        }

        if(!is_null($appointment['pc_pid'])){
            //add patient as participant
            $uri = 'Patient/' . $appointment['pc_pid'];
            $uriString =$this->createFHIRString($uri);
            $FHIRAppointment->getParticipant()[0]->getActor()->setReference($uriString);
        }

        if(!is_null($appointment['pc_healthcare_service_id'])){
            //add health care service as participant
            $uri = 'HealthcareService/' . $appointment['pc_healthcare_service_id'];
            $uriString =$this->createFHIRString($uri);
            $FHIRAppointment->getParticipant()[1]->getActor()->setReference($uriString);
        }

        if ($isSingle) {
            return $FHIRAppointment;
        } else {
            //toDo if recurrent set identifier and add extension
            return new FHIRBundle();
        }

    }

    /**
     * create FHIRAppointmentParticipant element
     *
     * @param array
     *
     * @return FHIRAppointmentParticipant
     */
    public function createFHIRAppointmentParticipant($participantArr)
    {

        if (empty($participantArr)) {
            return null;
        }

        $FHIRAppointmentParticipant = new FHIRAppointmentParticipant;


        if (key_exists('reference', $participantArr) && !empty($participantArr['reference'])) {
            $FHIRReference = $this->createFHIRReference($participantArr['reference']);
            $FHIRAppointmentParticipant->setActor($FHIRReference);
        }

        //$FHIRAppointmentParticipant->setStatus();
        //$FHIRAppointmentParticipant->setRequired();
        //$FHIRAppointmentParticipant->setPeriod();
        //$FHIRAppointmentParticipant->addType();

        return $FHIRAppointmentParticipant;
    }

    /**
     * check if appointment data is valid
     *
     * @param array
     * @return bool
     */
    public function validateDb($data)
    {

        $statuses = $this->getApptStatuses();
        $statusIds = array_keys($statuses);
        $postTable=$data['openemr_postcalendar_events'];
        foreach ($data as $fieldName => $value) {

            switch ($fieldName) {
                case 'pc_apptstatus':
                    if (!in_array($value, $statusIds)) {
                        return false;
                    }
                    break;
                default:
                    return true;
            }

        }

        return true;
    }

    /**
     * return Appointment status list
     *
     * @return array
     * @throws
     */
    public function getApptStatuses()
    {
        $ListsTable = $this->container->get(ListsTable::class);
        $list = $ListsTable->getListNormalized(self::APPT_STATUS_LIST);

        return $list;
    }

    public function parsedJsonToDb($parsedData)
    {
        return $parsedData;

    }

    public function parsedJsonToFHIR($parsedData)
    {
        $appointment=$parsedData;

        if (!is_array($appointment) || count($appointment) < 1) {
            return null;
        }

        $FHIRAppointment=$this->FHIRAppointment;

        //bring max from db
        $id = $this->createFHIRId($appointment['id']);
        $FHIRAppointment->setId($id);
        $FHIRAppointmentStatus = new FHIRAppointmentStatus();
        $FHIRAppointmentStatus->setValue($appointment['status']);
        $FHIRAppointment->setStatus($FHIRAppointmentStatus);

        if(!empty($appointment['serviceType'])){
            $code=$appointment['serviceType'][0]['coding'][0]['code'];
            $title=$appointment['serviceType'][0]['text'];
            $FHIRAppointment->serviceType[0]->setText($title);
            $FHIRAppointment->serviceType[0]->coding[0]->setCode($code);
        }

        $FHIRUnsignedInt=$this->createFHIRUnsignedInt($appointment['priority']);
        $FHIRAppointment->setPriority($FHIRUnsignedInt);

        $FHIRString = $this->createFHIRString($appointment['description']);
        $FHIRAppointment->setDescription($FHIRString);

        $FHIRDateTime = $this->createFHIRDateTime($appointment['created']);
        $FHIRAppointment->setCreated($FHIRDateTime);

        $FHIRString = $this->createFHIRString($appointment['comment']);
        $FHIRAppointment->setComment($FHIRString);

        $FHIRInstant = $this->createFHIRInstant(substr($appointment['start'],0,10), substr($appointment['start'],11,8));
        $FHIRAppointment->setStart($FHIRInstant);
        $FHIRInstant = $this->createFHIRInstant(substr($appointment['end'],0,10), substr($appointment['end'],11,8));
        $FHIRAppointment->setEnd($FHIRInstant);

        $FHIRPositiveInt = $this->createFHIRPositiveInt($appointment['minutesDuration'] );
        $FHIRAppointment->setMinutesDuration($FHIRPositiveInt);

        $reasonTitles=array_map(function ($a) { return $a['text']; } ,$appointment['reasonCode']);
        $reasonIds=array_map(function ($a) { return $a['coding'][0]['code']; } ,$appointment['reasonCode']);

        if(!empty($reasonIds)){
            $text=$FHIRAppointment->reasonCode[0]->getText();
            $text->setValue($reasonTitles[0]);
            $FHIRAppointment->reasonCode[0]->setText($text);
            $code=$FHIRAppointment->reasonCode[0]->getCoding()[0]->getCode();
            $code->setValue($reasonIds[0]);
            $FHIRAppointment->reasonCode[0]->coding[0]->setCode($code);
            unset($reasonTitles[0]);
            unset($reasonIds[0]);
        }

        foreach($reasonIds as $index => $val){
            $reason=array('code'=>$val,'text'=>$reasonTitles[$index]);
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept($reason);
            $FHIRAppointment->addReasonCode($FHIRCodeableConcept);
        }

        $participantsArr=$appointment['participant'];

        foreach ($participantsArr as $index =>$participant){
            $reference=$participant['actor']['reference'];
            if (strpos($reference, 'Patient/') !== false ) {
                $uri=$reference;
                $FHIRAppointment->participant[0]->actor->setReference($uri);

            }elseif (strpos($reference, 'HealthcareService/') !== false) {
                $uri=$reference;
                $FHIRAppointment->participant[1]->actor->setReference($uri);
            }
        }

        return $FHIRAppointment;

    }

    public function initFhirObject()
    {
        $FHIRAppointment = new FHIRAppointment;

        $id = $this->createFHIRId(null);
        $FHIRAppointment->setId($id);
        $FHIRAppointmentStatus = new FHIRAppointmentStatus();
        $FHIRAppointmentStatus->setValue(null);
        $FHIRAppointment->setStatus($FHIRAppointmentStatus);

        $codeArr = array('code' =>null , 'text' => null);
        $FHIRCodeableConcept = $this->createFHIRCodeableConcept($codeArr);
        $FHIRAppointment->addServiceType($FHIRCodeableConcept);

        $FHIRUnsignedInt=$this->createFHIRUnsignedInt(null);
        $FHIRAppointment->setPriority($FHIRUnsignedInt);

        $FHIRString = $this->createFHIRString(null);
        $FHIRAppointment->setDescription($FHIRString);

        $FHIRDateTime = $this->createFHIRDateTime(null);
        $FHIRAppointment->setCreated($FHIRDateTime);

        $FHIRString = $this->createFHIRString(null);
        $FHIRAppointment->setComment($FHIRString);

        $FHIRInstant = $this->createFHIRInstant(null,null);
        $FHIRAppointment->setStart($FHIRInstant);
        $FHIRInstant = $this->createFHIRInstant(null,null);
        $FHIRAppointment->setEnd($FHIRInstant);

        $FHIRPositiveInt = $this->createFHIRPositiveInt(null);
        $FHIRAppointment->setMinutesDuration($FHIRPositiveInt);

        $reason=array('code'=>null,'text'=>null);
        $FHIRCodeableConcept=$this->createFHIRCodeableConcept($reason);
        $FHIRAppointment->addReasonCode($FHIRCodeableConcept);

        $participantArr = array('reference' => array('reference' => null));
        $FHIRAppointmentParticipant = $this->createFHIRAppointmentParticipant($participantArr);
        $FHIRAppointment->addParticipant($FHIRAppointmentParticipant);

        $participantArr = array('reference' => array('reference' => null));
        $FHIRAppointmentParticipant = $this->createFHIRAppointmentParticipant($participantArr);
        $FHIRAppointment->addParticipant($FHIRAppointmentParticipant);

        $this->FHIRAppointment=$FHIRAppointment;

        return $FHIRAppointment;

    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        $FHIRAppointment = $this->parsedJsonToFHIR($data);
        $dBdata = $this->fhirToDb($FHIRAppointment);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        $postcalendarEventsTable = $this->container->get(PostcalendarEventsTable::class);
        $data['openemr_postcalendar_events']['pc_eid']=$id;
        $eventCodeReasonMapTable = $this->container->get(EventCodeReasonMapTable::class);
        $eventCodeReasonMapTable->deleteValueSetsById($id);
        $updated=$postcalendarEventsTable->safeUpdateApt($data);
        $this->initFhirObject();
        return $this->DBToFhir($updated[0], true);
    }


}
