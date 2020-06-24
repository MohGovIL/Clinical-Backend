<?php


namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use GenericTools\Model\UtilsTraits\JoinBuilder;
use GenericTools\Service\CouchdbService;
use OpenEMR\FHIR\R4\FHIRResourceContainer;

class DocumentReferenceSearch extends BaseSearch
{
    use JoinBuilder;
    public $paramsToDB = array();
    public $MAIN_TABLE = 'documents';
    public function search()
    {

        $FHIRBundle = $this->fhirObj->createSearchBundle();

        $summary = $this->summaryParams[0][0];

        $this->paramHandler('_id','id');
        $this->paramHandler('encounter','encounter_id');
        $this->paramHandler('patient','foreign_id');
        $documentsDataFromDb = $this->searchThisTable->buildGenericSelect($this->paramsToDB);

        if($summary !== "true") {
            //TODO if S3  init S3 .. else
            $couchdbService = new CouchdbService($this->container);
            $couchdbService->connect();
        }

        foreach ($documentsDataFromDb as $key => $document) {
            //TODO get basefile of $document['url'] into $document['title']
            //TODO if S3 ... elseif ...
            if($couchdbService) {
                $document['couchdbData'] = $couchdbService->fetchDoc($document['couchDocId'], false);
            }
            $this->fhirObj->initFhirObject();
            $FHIRResourceContainer = new FHIRResourceContainer($this->fhirObj->DBToFhir($document));
            $FHIRBundle = $this->fhirObj->addResourceToBundle($FHIRBundle, $FHIRResourceContainer, 'match');
            $this->fhirObj->setFHIR();
        }

        return $FHIRBundle;

    }

    private function getCouchdbData()
    {

    }
}
