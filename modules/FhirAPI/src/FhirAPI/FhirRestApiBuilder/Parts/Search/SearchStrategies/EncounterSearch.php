<?php
/**
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * Encounter search - strategy
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use FhirAPI\Service\FhirRequestParamsHandler;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIREncounter;
use OpenEMR\FHIR\R4\FHIRElement\FHIRString;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use Zend\Db\Sql\Expression;


class EncounterSearch extends BaseSearch
{
    public function runMysqlQuery(){

        if(isset($this->searchParams['form_encounter.date'])) {
            //value is only date not datetime
            if (strlen($this->searchParams['form_encounter.date'][0]['value']) === 10 && $this->searchParams['form_encounter.date'][0]['operator'] === 'eq') {
                $dayDate = $this->searchParams['form_encounter.date'][0]['value'];
                $this->searchParams['form_encounter.date'][0] = [
                    'value' => $dayDate . ' 00:00:00|' .$dayDate . ' 23:59:59',
                    //between operator
                    'operator' => 'be',
                    'modifier' => null
                ];

            }
        }

        $dataFromDb = $this->searchThisTable->buildGenericSelect($this->searchParams,implode(",",$this->orderParams),$this->specialParams);
        foreach ($dataFromDb  as $key => $data) {

            $this->fhirObj->initFhirObject();
            $element=$this->fhirObj->DBToFhir($data);
            $FHIRResourceContainer = new FHIRResourceContainer($element);
            $this->FHIRBundle = $this->fhirObj->addResourceToBundle($this->FHIRBundle, $FHIRResourceContainer, 'match');
            // $this->FHIRBundle->deletePatient();
        }
    }





}
