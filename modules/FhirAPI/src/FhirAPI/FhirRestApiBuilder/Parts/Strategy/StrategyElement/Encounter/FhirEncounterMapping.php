<?php
/**
 * Date: 21/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Encounter
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Encounter;



use function DeepCopy\deep_copy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;


use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\EncounterReasonCodeMapTable;
use GenericTools\Model\FormEncounterTable;
use Interop\Container\ContainerInterface;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIREncounter;
use OpenEMR\FHIR\R4\FHIRElement\FHIREncounterStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIREncounter\FHIREncounterParticipant;

use  FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\ConversionsTrait;


class FhirEncounterMapping extends FhirBaseMapping implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIREncounter = null;
    private $requestParams=array();

    CONST APT_REF='Appointment';
    CONST Practitioner_REF='Practitioner';
    CONST RP_REF= 'RelatedPerson';
    CONST PATIENT_REF= 'Patient';
    CONST ORG_REF= 'Organization';
    CONST RCD_URL = 'reasonCodesDetail';
    CONST AW_URL='arrivalWay';
    CONST SECONDARY_STATUS_URL='secondaryStatus';
    CONST STATUS_UPDATE_DATE_URL='statusUpdateDate';

    CONST EXTENSIONS_ENCOUNTER_URL='http://clinikal/extensions/encounter/';

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->setFHIREncounter(new FHIREncounter());
    }

    /**
     * convert FHIREncounter to db array
     *
     * @param FHIREncounter
     *
     * @return array;
     */
    public function fhirToDb($FHIREncounter)
    {
        $encounter=array();
        $encounter["status"]= $FHIREncounter->getStatus()->getValue();
        $encounter['id']= $FHIREncounter->getId()->getValue();

        $date=$FHIREncounter->getPeriod()->getStart()->getValue();
        $encounter['date']= $this->convertToDateTime($date);

        $reference=$FHIREncounter->getAppointment()[0]->getReference()->getValue();
        if (strpos($reference, self::APT_REF.'/') !== false ) {
            $encounter["eid"]= (!empty($reference)) ? substr($reference,strlen(self::APT_REF)+1,20) : null;

        }else{
            $encounter["eid"]=null;
        }

        $reference=$FHIREncounter->getServiceProvider()->getReference()->getValue();
        if (strpos($reference, self::ORG_REF.'/') !== false ) {
            $encounter["facility_id"]= (!empty($reference)) ? substr($reference,strlen(self::ORG_REF)+1,20) : null;
        }else{
            $encounter["facility_id"]=null;
        }

        $encounter["provider_id"]=null;
        $encounter["escort_id"]=null;

        $participants=$FHIREncounter->getParticipant();
        foreach($participants as $index =>$participant){
            $reference=$participant->getIndividual()->getReference()->getValue();

            if (strpos($reference, self::Practitioner_REF.'/') !== false ) {
                $encounter["provider_id"]= (!empty($reference)) ? substr($reference,strlen(self::Practitioner_REF)+1,20) : null;
            } elseif (strpos($reference, self::RP_REF.'/') !== false ) {
                $encounter["escort_id"]= (!empty($reference)) ? substr($reference,strlen(self::RP_REF)+1,20) : null;
            }

        }

        $reference= $FHIREncounter->getSubject()->getReference()->getValue();
        if (strpos($reference, self::PATIENT_REF.'/') !== false ) {
            $encounter["pid"]= (!empty($reference)) ? substr($reference,strlen(self::PATIENT_REF)+1,20) : null;
        }else{
            $encounter["pid"]=null;
        }

        $encounter["priority"]=$FHIREncounter->getPriority()->getCoding()[0]->getCode()->getValue();

        $encounter["service_type"]=$FHIREncounter->getServiceType()->getCoding()[0]->getCode()->getValue();

        $fhirReasonsCodes=$FHIREncounter->getReasonCode();
        $reasonsCodes=array();

        foreach($fhirReasonsCodes as $index => $reason){
            $eid=$encounter["eid"];
            $reasonsCode=$reason->getCoding()[0]->getCode()->getValue();
            $reasonsCodes[]=array("eid"=>$eid,"reason_code"=>$reasonsCode);
        }

        $Extensions= $FHIREncounter->getExtension();

        $date="";
        foreach($Extensions as $exIndex => $Extension){
            $url =   $Extension->getUrl();
            $extensionName=substr($url, strrpos($url, '/') + 1);
            switch ($extensionName) {
                case self::AW_URL:
                        $encounter["arrival_way"]=trim($Extensions[$exIndex]->getValueString());
                    break;
                case self::RCD_URL:
                        $encounter["reason_codes_details"]=trim($Extensions[$exIndex]->getValueString());
                    break;
                case self::SECONDARY_STATUS_URL:
                    $encounter["secondary_status"]=trim($Extensions[$exIndex]->getValueString());
                    break;
                case self::STATUS_UPDATE_DATE_URL:
                    $date=trim($Extensions[$exIndex]->getValueDateTime());
                    $date=$this->convertToDateTime($date);
                    $encounter["status_update_date"]=$date;
                    break;
            }
        }

        $encounterData=array('form_encounter'=>$encounter,'encounter_reasoncode_map'=>$reasonsCodes);

        return $encounterData;
    }



    /**
     * create FHIREncounter
     *
     * @param  string
     * @return FHIREncounter
     * @throws
     */
    public function DBToFhir(...$data)
    {
        $encounter = $data[0];
        $FHIREncounter = $this->FHIREncounter;

        $FHIREncounter->getStatus()->setValue($encounter["status"]);
        $FHIREncounter->getId()->setValue($encounter['id']);
        $date=$this->createFHIRDateTime(null,null,$encounter['date']);
        $FHIREncounter->getPeriod()->getStart()->setValue($date);

        if( isset($encounter["eid"]) && intval($encounter["eid"]) > 0 ) {
            $ref= self::APT_REF."/" . $encounter["eid"];
            $FHIREncounter->getAppointment()[0]->getReference()->setValue($ref);
        }

        if( isset($encounter["facility_id"]) && intval($encounter["facility_id"]) > 0 ) {
            $ref= self::ORG_REF."/" . $encounter["facility_id"];
            $FHIREncounter->getServiceProvider()->getReference()->setValue($ref);
        }

        /* add Participants*/
        if( isset($encounter["provider_id"]) && intval($encounter["provider_id"]) > 0 ) {
            $ref= self::Practitioner_REF."/" . $encounter["provider_id"];
            $FHIREncounter->getParticipant()[0]->getIndividual()->getReference()->setValue($ref);
        }

        if(isset($encounter["escort_id"]) && intval($encounter["escort_id"]) > 0) {
            $ref= self::RP_REF."/" . $encounter["escort_id"];
            $FHIREncounter->getParticipant()[1]->getIndividual()->getReference()->setValue($ref);
        }

        /************************************/

        if( isset($encounter["pid"]) && intval($encounter["pid"]) > 0 ) {
            $ref= self::PATIENT_REF."/" . $encounter["pid"];
            $FHIREncounter->getSubject()->getReference()->setValue($ref);
        }

        $FHIREncounter->getPriority()->getCoding()[0]->getCode()->setValue($encounter["priority"]);

        if(isset($encounter["service_type"]) && intval($encounter["service_type"]) > -1) {
            $FHIREncounter->getServiceType()->getCoding()[0]->getCode()->setValue($encounter["service_type"]);
            $FHIREncounter->getServiceType()->getText()->setValue($encounter["service_type_title"]);
        }


       if(isset($encounter["reason_code"])){

           $reasons_codes =explode(",",$encounter["reason_code"]);
           $reasons_codes_titles =explode(",",$encounter["reason_code_title"]);

           if(!empty($reasons_codes)){
               $reasonCode=$FHIREncounter->getReasonCode()[0];
               $reasonCode->setText($reasons_codes_titles[0]);
               $coding=$reasonCode->getCoding()[0];
               $coding->getCode()->setValue($reasons_codes[0]);
               unset($reasons_codes[0]);
               unset($reasons_codes_titles[0]);
           }

           foreach($reasons_codes as $index => $val){
               $reason=array('code'=>$val,'text'=>$reasons_codes_titles[$index]);
               $FHIRCodeableConcept=$this->createFHIRCodeableConcept($reason);
               $FHIREncounter->addReasonCode($FHIRCodeableConcept);
           }
       }

        $Extensions= $FHIREncounter->getExtension();

        foreach($Extensions as $exIndex => $Extension){
            $url =   $Extension->getUrl();
            $extensionName=substr($url, strrpos($url, '/') + 1);
            switch ($extensionName) {
                case self::AW_URL:
                    if(isset($encounter["arrival_way"]) && !is_null($encounter["arrival_way"])){
                        $Extensions[$exIndex]->setValueString($encounter["arrival_way"]);
                    }else{
                        unset($FHIREncounter->extension[$exIndex]);
                    }
                    break;
                case self::RCD_URL:
                    if(isset($encounter["reason_codes_details"]) && !is_null($encounter["reason_codes_details"])){
                        $Extensions[$exIndex]->setValueString($encounter["reason_codes_details"]);
                    }else{
                        unset($FHIREncounter->extension[$exIndex]);
                    }
                    break;

                case self::SECONDARY_STATUS_URL:
                    if(isset($encounter["secondary_status"]) && !is_null($encounter["secondary_status"])){
                        $Extensions[$exIndex]->setValueString($encounter["secondary_status"]);
                    }else{
                        unset($FHIREncounter->extension[$exIndex]);
                    }
                    break;

                case self::STATUS_UPDATE_DATE_URL:
                    if(isset($encounter["status_update_date"]) && !is_null($encounter["status_update_date"])){
                        $FHIRDateTime=$this->createFHIRDateTime(null,null,$encounter["status_update_date"]);
                        $Extensions[$exIndex]->setValueDateTime($FHIRDateTime);
                    }else{
                        unset($FHIREncounter->extension[$exIndex]);
                    }
                    break;
            }
        }

        $this->FHIREncounter=$FHIREncounter;

        return $FHIREncounter;
    }

    /**
     * @param FHIREncounter $FHIREncounter
     * @return FhirEncounterMapping
     */
    public function setFHIREncounter(FHIREncounter $FHIREncounter)
    {
        $this->FHIREncounter = $FHIREncounter;
        return $this;
    }



    public function DbFhir($dbArray)
    {
        // TODO: Implement DbFhir() method.
    }

    public function parsedJsonToDb($parsedData)
    {
        // TODO: Implement parsedJsonToDb() method.
    }

    public function validateDb($data)
    {
        // TODO: Implement validateDb() method.
        return true;
    }


    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        //$FHIRRelatedPerson = $this->parsedJsonToFHIR($data);
        $data=$this->manageExtensions($data,$this->FHIREncounter);
        $this->arrayToFhirObject($this->FHIREncounter,$data);
        $dBdata = $this->fhirToDb($this->FHIREncounter);
        return $dBdata;
    }

    public function initFhirObject()
    {
        $FHIREncounter = new FHIREncounter;
        $FHIREncounterStatus = new FHIREncounterStatus(['value' => null]);
        $FHIREncounter->setStatus($FHIREncounterStatus);

        $id = $this->createFHIRId(null);
        $FHIREncounter->setId($id);

        $FHIRPeriod = $this->createFHIRPeriod(['start' => null ]);
        $FHIREncounter->setPeriod($FHIRPeriod);

        $FHIRReferenceAppointment = $this->createFHIRReference(["reference" => null]);
        $FHIREncounter->addAppointment($FHIRReferenceAppointment);

        $FHIRReferenceServiceProvider = $this->createFHIRReference(["reference" => null]);
        $FHIREncounter->setServiceProvider($FHIRReferenceServiceProvider);

        /*add Participants*/
        $FHIRReferenceParticipantIndividual = $this->createFHIRReference(["reference" => null]);
        $FHIRParticipant = new FHIREncounterParticipant(["individual" => $FHIRReferenceParticipantIndividual]);
        $FHIREncounter->addParticipant($FHIRParticipant);

        $FHIREncounter->addParticipant(deep_copy($FHIRParticipant));
        /*****************/

        $FHIRReferenceSubject = $this->createFHIRReference(["reference" => null]);
        $FHIREncounter->setSubject($FHIRReferenceSubject);

        $FHIRCodingPriority = $this->createFHIRCodeableConcept( ["code"=>null]);
        $FHIREncounter->setPriority($FHIRCodingPriority);

        $FHIRCodeableConceptArr=array("code" => null, "text" => null);

        $FHIRServiceType = $this->createFHIRCodeableConcept($FHIRCodeableConceptArr);
        $FHIREncounter->setServiceType($FHIRServiceType);

        $reason=array('code'=>null,'text'=>null);
        $FHIRCodeableConcept=$this->createFHIRCodeableConcept($reason);
        $FHIREncounter->addReasonCode($FHIRCodeableConcept);

        $FHIRExtensionRSD= $this->createFHIRExtension(self::EXTENSIONS_ENCOUNTER_URL.self::RCD_URL,'string',null);
        $FHIREncounter->addExtension($FHIRExtensionRSD);

        $FHIRExtensionAW= $this->createFHIRExtension(self::EXTENSIONS_ENCOUNTER_URL.self::AW_URL,'string',null);
        $FHIREncounter->addExtension($FHIRExtensionAW);

        $FHIRExtensionAW= $this->createFHIRExtension(self::EXTENSIONS_ENCOUNTER_URL.self::SECONDARY_STATUS_URL,'string',null);
        $FHIREncounter->addExtension($FHIRExtensionAW);

        $FHIRExtensionAW= $this->createFHIRExtension(self::EXTENSIONS_ENCOUNTER_URL.self::STATUS_UPDATE_DATE_URL,'dateTime',null);
        $FHIREncounter->addExtension($FHIRExtensionAW);

        $this->FHIREncounter=$FHIREncounter;
        return $FHIREncounter;

    }


    public function updateDbData($data,$id)
    {
        $formEncounterTable = $this->container->get(FormEncounterTable::class);
        $data['form_encounter']['id']=$id;
        $encounterReasonCodeMapTable = $this->container->get(EncounterReasonCodeMapTable::class);
        $encounterReasonCodeMapTable->deleteValueSetsById($id);
        $updated=$formEncounterTable->safeUpdateEncounter($data);
        $this->initFhirObject();
        return $this->DBToFhir($updated[0], true);
    }
}





