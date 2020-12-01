<?php
/**
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * Encounter search - strategy
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\Service\FhirBaseMapping;
use FhirAPI\Service\FhirRequestParamsHandler;
use GenericTools\Model\ListsTable;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIREncounter;
use OpenEMR\FHIR\R4\FHIRElement\FHIRString;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use Laminas\Db\Sql\Expression;


class EncounterSearch extends BaseSearch
{
    CONST STATUSES="clinikal_enc_statuses";
    CONST SECONDARY_STATUSES="clinikal_enc_secondary_statuses";

    public function runMysqlQuery(){

         $allStatuses= ($this->searchParams)['all_statuses'];
         if(!empty($allStatuses) && is_array($allStatuses)) {

             $ListsTable = $this->container->get(ListsTable::class);
             $listStatus = $ListsTable->getListNormalized(self::STATUSES);
             $listSecondaryStatus = $ListsTable->getListNormalized(self::SECONDARY_STATUSES);

             foreach ($allStatuses as $index => $tempStatus){
                $statVal=$tempStatus['value'];

                 if(key_exists($statVal,$listStatus)){
                     $this->searchParams['form_encounter.status']['nestGroup'] = 'status_group';
                     $this->searchParams['form_encounter.status'][] = [
                         'value' => $statVal,
                         'operator' => 'NULL',
                         'modifier' => 'exact',
                         'sqlOp'=>'OR'
                     ];
                 }elseif(key_exists($statVal,$listSecondaryStatus)){
                     $this->searchParams['form_encounter.secondary_status']['nestGroup'] = 'status_group';
                     $this->searchParams['form_encounter.secondary_status'][] = [
                         'value' =>$statVal,
                         'operator' => 'NULL',
                         'modifier' => 'exact',
                         'sqlOp'=>'OR',
                     ];
                 }
             }
             unset(($this->searchParams)['all_statuses']);
         }

        $this->addShortDate('form_encounter.date');
        $this->addShortDate('status_update_date');

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
