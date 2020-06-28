<?php


namespace FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies;

use GenericTools\Model\UtilsTraits\JoinBuilder;
use GenericTools\Service\CouchdbService;
use GenericTools\Service\S3Service;
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
            if($GLOBALS['use_s3']) {
                $s3Service = new S3Service($this->getContainer());
                $s3Service->connect();
            }
            else {
                $couchdbService = new CouchdbService($this->container);
                $couchdbService->connect();
            }
        }

        foreach ($documentsDataFromDb as $key => $document) {
            $fullUrl = $document['url'];

            // get the displayable file name (in case url is a full path or has the unix ts prefixed to it)
            $creationDateUnixTs = strtotime($document['date']);
            $document['url'] = ltrim(basename($document['url']), $creationDateUnixTs . "_");

            if($s3Service) {
                $data = $s3Service->fetchObject($fullUrl);
                $encData = base64_encode($data);
                $document['fileData'] = $encData;
            }
            elseif($couchdbService) {
                $document['fileData'] = $couchdbService->fetchDoc($document['couchDocId'], false);
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
