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
        $this->paramHandler('category','category_id','cat_to_doc');

        $documentsDataFromDb = $this->searchThisTable->buildGenericSelect($this->paramsToDB);

        if($summary !== "true") {
            //create service objects
            if($GLOBALS['clinikal_storage_method'] == S3Service::STORAGE_METHOD_CODE) {
                $s3Service = new S3Service($this->container);
                $s3Service->connect();
            }
            elseif ($GLOBALS['clinikal_storage_method'] == CouchdbService::STORAGE_METHOD_CODE) {
                $couchdbService = new CouchdbService($this->container);
                $couchdbService->connect();
            }
        }

        foreach ($documentsDataFromDb as $key => $document) {
            $fullUrl = $document['url'];

            // get the displayable file name (in case url is a full path or has the unix ts prefixed to it)
            $creationDateUnixTs = strtotime($document['date']);
            $document['url'] = ltrim(basename($document['url']), $creationDateUnixTs . "_");

            // only try to fetch if the global storage method matches this storage method used to store this object
            if($s3Service && $document['storageMethod'] == S3Service::STORAGE_METHOD_CODE) {
                $data = $s3Service->fetchObject($fullUrl);
                $encData = base64_encode($data);
                $document['fileData'] = $encData;
            }
            elseif($couchdbService && $document['storageMethod'] == CouchdbService::STORAGE_METHOD_CODE) {
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
