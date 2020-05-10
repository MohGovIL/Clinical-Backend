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


        if (isset($this->searchParams['identifier:of-type'])) {
            foreach ($this->searchParams['identifier:of-type'] as $key => $element) {
                $vals=$element['value'];
                $valArr=explode("|",$vals);

                if ($valArr[0]!==""){
                    $this->searchParams['system'][]=array('value' => $valArr[0], 'operator' => NULL);
                }
                if ($valArr[1]!==""){
                    $this->searchParams['identifier:code'][]=array('value' => $valArr[1], 'operator' => NULL);
                }
                if ($valArr[2]!==""){
                    $this->searchParams['identifier'][]=array('value' => $valArr[2], 'operator' => NULL);
                }
            }
            unset($this->searchParams['identifier:of-type']);

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
            $this->paramHandler('identifier:code','mh_type_id');
            //$this->paramHandler('system','');  not implemented yet
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
