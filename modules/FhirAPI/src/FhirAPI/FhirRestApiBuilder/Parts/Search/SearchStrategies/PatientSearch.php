<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\PatientsTable;
use OpenEMR\FHIR\R4\FHIRResourceContainer;

class PatientSearch extends BaseSearch
{

    public $paramsToDB = array();
    public $MAIN_TABLE = 'patient_data';
    public function search()
    {

        $FHIRBundle = $this->fhirObj->createSearchBundle();
        $params = $this->paramsFromBody;
        $stringArr = array();



        if ( !empty($params['ARGUMENTS']) || !empty($params['PARAMETERS_FOR_SEARCH_RESULT']) || !empty($params['PARAMETERS_FOR_ALL_RESOURCES'])) {

        //    $FHIRBundle = $this->fhirObj->createErrorBundle($FHIRBundle, $params);
        }


        if (isset($this->searchParams['name'])) {
            foreach ($this->searchParams['name'] as $key => $element) {
                $stringArr[] = $element['value'];
            }
            $patientDataFromDb = $this->searchThisTable->searchPatientByName($stringArr);
        } else {

            $this->paramHandler('_id','pid');
            $this->paramHandler('identifier','ss');
            $this->paramHandler('organization','mh_insurance_organiz');
            $this->paramHandler('mobile','phone_cell');
            $patientDataFromDb = $this->searchThisTable->buildGenericSelect($this->paramsToDB,implode(",",$this->orderParams));

        }

        foreach ($patientDataFromDb as $key => $patient) {
            $this->fhirObj->initFhirObject();
            $FHIRResourceContainer = new FHIRResourceContainer($this->fhirObj->DBToFhir($patient));
            $FHIRBundle = $this->fhirObj->addResourceToBundle($FHIRBundle, $FHIRResourceContainer, 'match');
            $this->fhirObj->setFHIR();
        }

        return $FHIRBundle;
    }



}
