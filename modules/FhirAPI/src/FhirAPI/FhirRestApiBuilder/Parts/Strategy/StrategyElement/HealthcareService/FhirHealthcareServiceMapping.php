<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\HealthcareService;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRHealthcareService;
use OpenEMR\FHIR\R4\FHIRResource\FHIRHealthcareService\FHIRHealthcareServiceAvailableTime;
use OpenEMR\FHIR\R4\FHIRResource\FHIRHealthcareService\FHIRHealthcareServiceNotAvailable;

class FhirHealthcareServiceMapping extends FhirBaseMapping implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIRHealthcareService = null;
    private $urlParamToDbColumnMapping;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRHealthcareService = new FHIRHealthcareService;

        $this->setUrlParamToDbColumnMapping(
            array(
                "_id" => "id",
                "active" => "active",
                "service-type" => "type",
                "organization" => "providedBy",
                "name" => "name"
            )
        );

    }

    public function setNewFHIRHealthcareService()
    {
        $this->FHIRHealthcareService = new FHIRHealthcareService;
    }

    public function setUrlParamToDbColumnMapping(array $mapping)
    {
        $this->urlParamToDbColumnMapping = $mapping;
    }

    public function getUrlParamToDbColumnMapping()
    {
        return $this->urlParamToDbColumnMapping;
    }

    public function fhirToDb($healthcareService)
    {

    }

    public function DBToFhir(...$params)
    {
        $this->setNewFHIRHealthcareService(); //reset

        $healthcareServiceDataFromDb = $params[0];
        $FHIRId = $this->createFHIRId($healthcareServiceDataFromDb['id']);
        $this->FHIRHealthcareService->setId($FHIRId);

        $FHIRBoolean = $this->createFHIRBoolean($healthcareServiceDataFromDb['active']);
        $this->FHIRHealthcareService->setActive($FHIRBoolean);

        $reference = 'Organization/' . $healthcareServiceDataFromDb['providedBy'];
        $display = $healthcareServiceDataFromDb['providedBy_display'];
        $referenceArr = array('reference' => $reference, 'display' => $display);
        $FHIRReference = $this->createFHIRReference($referenceArr);
        $this->FHIRHealthcareService->setProvidedBy($FHIRReference);

        $code = array('code' => $healthcareServiceDataFromDb['category'], 'text' => $healthcareServiceDataFromDb['category_display']);
        $FHIRCodeableConcept = $this->createFHIRCodeableConcept($code);
        $this->FHIRHealthcareService->addCategory($FHIRCodeableConcept);

        $code = array('code' => $healthcareServiceDataFromDb['type'], 'text' => $healthcareServiceDataFromDb['type_display']);
        $FHIRCodeableConcept = $this->createFHIRCodeableConcept($code);
        $this->FHIRHealthcareService->addType($FHIRCodeableConcept);

        $FHIRString = $this->createFHIRString($healthcareServiceDataFromDb['name']);
        $this->FHIRHealthcareService->setName($FHIRString);

        $FHIRString = $this->createFHIRString($healthcareServiceDataFromDb['comment']);
        $this->FHIRHealthcareService->setComment($FHIRString);

        $FHIRMarkdown = $this->createFHIRMarkdown($healthcareServiceDataFromDb['extraDetails']);
        $this->FHIRHealthcareService->setExtraDetails($FHIRMarkdown);

        $availableTimeArray = json_decode($healthcareServiceDataFromDb['availableTime'], true);
        foreach ($availableTimeArray as $availableTime) {
            $FHIRHealthcareServiceAvailableTime = $this->createFHIRHealthcareServiceAvailableTime($availableTime);
            $this->FHIRHealthcareService->addAvailableTime($FHIRHealthcareServiceAvailableTime);
        }

        $notAvailableArray = json_decode($healthcareServiceDataFromDb['notAvailable'], true);
        foreach ($notAvailableArray as $notAvailable) {
            $FHIRHealthcareServiceNotAvailable = $this->createFHIRHealthcareServiceNotAvailable($notAvailable);
            $this->FHIRHealthcareService->addNotAvailable($FHIRHealthcareServiceNotAvailable);
        }

        $FHIRString = $this->createFHIRString($healthcareServiceDataFromDb['availabilityExceptions']);
        $this->FHIRHealthcareService->setAvailabilityExceptions($FHIRString);

        //object is cloned, otherwise it's passed by reference and overwritten next time this function is called
        return clone $this->FHIRHealthcareService;
    }

    private function createFHIRHealthcareServiceAvailableTime($availableTimeArray)
    {
        $FHIRHealthcareServiceAvailableTime = new FHIRHealthcareServiceAvailableTime();

        //days of week
        if (isset($availableTimeArray['daysOfWeek'])) {
            foreach ($availableTimeArray['daysOfWeek'] as $d) {
                $FHIRDaysOfWeek = $this->createFHIRDaysOfWeek($d);
                $FHIRHealthcareServiceAvailableTime->addDaysOfWeek($FHIRDaysOfWeek);
            }
        }
        //all day
        if (isset($availableTimeArray['allDay'])) {
            $FHIRBoolean = $this->createFHIRBoolean($availableTimeArray['allDay']);
            $FHIRHealthcareServiceAvailableTime->setAllDay($FHIRBoolean);
        }
        //start time
        if (isset($availableTimeArray['availableStartTime'])) {
            $FHIRTime = $this->createFHIRTime($availableTimeArray['availableStartTime']);
            $FHIRHealthcareServiceAvailableTime->setAvailableStartTime($FHIRTime);
        }
        //end time
        if (isset($availableTimeArray['availableEndTime'])) {
            $FHIRTime = $this->createFHIRTime($availableTimeArray['availableEndTime']);
            $FHIRHealthcareServiceAvailableTime->setAvailableEndTime($FHIRTime);
        }

        return $FHIRHealthcareServiceAvailableTime;

    }

    private function createFHIRHealthcareServiceNotAvailable($notAvailableArray)
    {
        $FHIRHealthcareServiceNotAvailable = new FHIRHealthcareServiceNotAvailable();

        //description
        if (isset($data['description'])) {
            $FHIRString = $this->createFHIRString($notAvailableArray['description']);
            $FHIRHealthcareServiceNotAvailable->setDescription($FHIRString);
        }
        //during
        if (isset($notAvailableArray['during'])) {
            $FHIRPeriod = $this->createFHIRPeriod($notAvailableArray['during']);
            $FHIRHealthcareServiceNotAvailable->setDuring($FHIRPeriod);
        }

        return $FHIRHealthcareServiceNotAvailable;
    }

    public function parsedJsonToDb($parsedData){} //convert array from request to db format

    public function validateDb($data){}


    public function initFhirObject()
    {
        // TODO: Implement initFhirObject() method.
        /*
        $FHIRHealthcareService = new FHIRHealthcareService;
        $this->FHIRHealthcareService=$FHIRHealthcareService;
        return $FHIRHealthcareService;
        */
    }


}
