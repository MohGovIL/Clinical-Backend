<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\DocumentReference;

use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use GenericTools\Model\DocumentsTable;
use GenericTools\Model\DocumentsCategoriesTable;
use GenericTools\Service\CouchdbService;
use GenericTools\Service\S3Service;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;

class DocumentReference extends Restful implements  Strategy
{

    const COUCH_STORAGE = 1;
    const S3_STORAGE = 10;

    public function __construct($params=null)
    {
        if(!is_null($params))
        {
            $this->initParams($params);
        }
    }

    private function initParams($initials){
        $this->setOperations($initials['paramsFromUrl']);
        $this->setParamsFromUrl($initials['paramsFromUrl']);
        $this->setParamsFromBody($initials['paramsFromBody']);
        $this->setContainer($initials['container']);
        $this->setMapping($initials['container']);
    }

    public function doAlgorithm($arrParams)
    {
        $this->initParams($arrParams);

        $this->functionName = $arrParams['type'];
        $function = Restful::$data[$arrParams['strategyName']][self::$function][$this->functionName];
        return $this->$function();
    }

    public function setMapping($container)
    {
        $this->mapping = new FhirDocumentReferenceMapping($container);
    }

    public function read()
    {
        //init empty object
        $this->mapping->initFhirObject();

        //get data from mariadb
        $documentsTable = $this->container->get(DocumentsTable::class);
        $params = array('documents.id' => $this->paramsFromUrl[0]);
        $documentsDataFromDb = $documentsTable->buildGenericSelect($params);
        if(empty($documentsDataFromDb))
        {
            //not found
            return self::$errorCodes::http_response_code(204);
        }
        $documentsDataFromDb = $documentsDataFromDb[0];

        $fullUrl = $documentsDataFromDb['url'];

        // get the displayable file name (in case url is a full path or has the unix ts prefixed to it)
        $creationDateUnixTs = strtotime($documentsDataFromDb['date']);
        $documentsDataFromDb['url'] = ltrim(basename($documentsDataFromDb['url']), $creationDateUnixTs . "_");

        // only try to fetch if the global storage method matches this storage method used to store this object
        if ($GLOBALS['clinikal_storage_method'] == $documentsDataFromDb['storageMethod']) {

            if ($documentsDataFromDb['storageMethod'] == S3Service::STORAGE_METHOD_CODE) {
                //get file from S3
                $s3Service = new S3Service($this->getContainer());
                $s3Service->connect();
                $data = $s3Service->fetchObject($fullUrl);
                $encData = base64_encode($data);
                $documentsDataFromDb['fileData'] = $encData;
            } elseif ($documentsDataFromDb['storageMethod'] == CouchdbService::STORAGE_METHOD_CODE) {
                //get document from couchdb
                $couchdbService = new CouchdbService($this->getContainer());
                $couchdbService->connect();
                $documentsDataFromDb['fileData'] = $couchdbService->fetchDoc($documentsDataFromDb['couchDocId'], false);
            }
        }
        //convert data (fill empty object)
        $documentsObj = $this->mapping->DBToFhir($documentsDataFromDb);

        $this->mapping->setFHIR();

        return $documentsObj;
    }

    public function search()
    {

        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(DocumentsTable::class),
            'fhirObj'=>new FhirDocumentReferenceMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'DocumentReferenceSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();
    }

    public function create()
    {

        //todo :what happens if insert db success and couch fails and vice versa

        $documentsTable = $this->container->get(DocumentsTable::class);
        $documentsCategoriesTable = $this->container->get(DocumentsCategoriesTable::class);

        $this->mapping->initFhirObject();

        $json = $this->paramsFromBody['POST_PARSED_JSON'];
        $json['id'] = $documentsTable->lastId() + 1;;
        $fhirDocumentReference = $this->mapping->parsedJsonToFhir($json);
        $dbStructuredData = $this->mapping->fhirToDb($fhirDocumentReference);

        $creationDate = date("Y-m-d H:i:s");
        $dbStructuredData['documents']['date'] = $creationDate;

        // upload to storage
        $result = $this->uploadToStorage($dbStructuredData);
        if ($result == false) {
            return self::$errorCodes::http_response_code(500);
        }
        if($dbStructuredData["documents"]["storagemethod"] == self::S3_STORAGE) {
            $dbStructuredData['documents']['url'] = $result['url'];
        }
        elseif($dbStructuredData["documents"]["storagemethod"] == self::COUCH_STORAGE) {
            $dbStructuredData['documents']['couch_docid'] = $result['id'];
            $dbStructuredData['documents']['couch_revid'] = $result['rev'];
        }

        // save to documents table
        $inserted = $documentsTable->insert($dbStructuredData['documents']);
        if( $inserted == false ) {
            return self::$errorCodes::http_response_code(500);
        }

        // save to categories_to_documents table
        $inserted = $documentsCategoriesTable->insert($dbStructuredData['categories_to_documents']);
        if( $inserted == false ) {
            return self::$errorCodes::http_response_code(500);
        }

        return $fhirDocumentReference;
    }


    public function delete()
    {
        //init empty object
        $this->mapping->initFhirObject();

        //get data from mariadb
        $documentsTable = $this->container->get(DocumentsTable::class);
        $documentsCategoriesTable = $this->container->get(DocumentsCategoriesTable::class);
        $docId=$this->paramsFromUrl[0];
        $params = array('documents.id' => $docId);
        $documentsDataFromDb = $documentsTable->buildGenericSelect($params);
        if(empty($documentsDataFromDb))
        {
            $moreInfo="failed to retrieve from db";
            $explanation="document was not found";
            return $this->mapping->createDeleteFailRespond($docId,$explanation,$moreInfo);
        }
        $documentsDataFromDb = $documentsDataFromDb[0];
        $mysqlID=$documentsDataFromDb['id'];

        // only try to delete if the global storage method matches this storage method used to store this object
        if($GLOBALS['clinikal_storage_method'] == $documentsDataFromDb['storageMethod']) {
            if ($documentsDataFromDb['storageMethod'] == S3Service::STORAGE_METHOD_CODE) {
                $s3Service = new S3Service($this->getContainer());
                $s3Service->connect();
                $rez = $s3Service->deleteObject($documentsDataFromDb['url']);
            } elseif ($documentsDataFromDb['storageMethod'] == CouchdbService::STORAGE_METHOD_CODE) {
                $couchDocId = $documentsDataFromDb['couchDocId'];
                $couchRevId = $documentsDataFromDb['couchRevId'];
                $couchdbService = new CouchdbService($this->getContainer());
                $couchdbService->connect();
                $rez = $couchdbService->deleteDocument($couchDocId, $couchRevId);
            }
        }
        if($rez!==true){
            $explanation="failed to delete from storage";
            return $this->mapping->createDeleteFailRespond($docId,$explanation,$rez);
        }else{

            $deleteDoc=$documentsTable->deleteDataByParams(array("id"=>$mysqlID));
            $deleteCat=$documentsCategoriesTable->deleteDataByParams(array("document_id"=>$mysqlID));
            if($deleteDoc===1 && $deleteCat===1){
                return $this->mapping->createDeleteSuccessRespond();
            }else{
                $explanation="failed to delete from db ";
                return $this->mapping->createDeleteFailRespond($docId,$explanation);
            }

        }

    }


    public function update()
    {
        $dbData = $this->mapping->getDbDataFromRequest($this->paramsFromBody['POST_PARSED_JSON']);
        $docId =$this->paramsFromUrl[0];
        $result = $this->updateDbData($dbData,$docId);
        if($result) {
            return $this->mapping->parsedJsonToFHIR($this->paramsFromBody['POST_PARSED_JSON']);
        }
        else {
            return self::$errorCodes::http_response_code(500);
        }
    }


    private function updateDbData($data, $id)
    {
        $documentsTable = $this->container->get(DocumentsTable::class);
        $documentsCategoriesTable = $this->container->get(DocumentsCategoriesTable::class);

        $params = array('documents.id' => $id);
        $documentsDataFromDb = $documentsTable->buildGenericSelect($params)[0];

        /*
        Only try to update if the storage method of the new object matches this storage method used to store old object
        (this means that it also matches the global storage method because it's used to set the method in the new object)
        */
        if ($data['documents']["storagemethod"] == $documentsDataFromDb['storageMethod']) {
            $creationDate = date("Y-m-d H:i:s");
            $data['documents']['date'] = $creationDate;
            $updateArray = array();
            if ($data['documents']["storagemethod"] == self::COUCH_STORAGE) {
                $updateArray['id'] = $documentsDataFromDb['couchDocId'];
                $updateArray['rev'] = $documentsDataFromDb['couchRevId'];
            }
            /*
            For couchdb this an actual update is performed.
            For S3 it is a delete and insert (can't rename an S3 object so can't just update).
            */
            $uploadResult = $this->uploadToStorage($data, $updateArray);
            if ($data['documents']["storagemethod"] == self::S3_STORAGE) {
                $s3Service = new S3Service($this->getContainer());
                $s3Service->connect();
                $deleteResult = $s3Service->deleteObject($documentsDataFromDb['url']);
                if (!$deleteResult) {
                    return false;
                }
                $data['documents']['url'] = $uploadResult['url'];
            } elseif ($data['documents']["storagemethod"] == self::COUCH_STORAGE) {
                $data['documents']['couch_docid'] = $uploadResult['id'];
                $data['documents']['couch_revid'] = $uploadResult['rev'];
            }
        }

        //update mariadb
        $docsResult = $documentsTable->safeUpdate($data['documents'], array('id' => $id));
        if (!$docsResult) {
            return false;
        }
        $catResult = $documentsCategoriesTable->safeUpdate($data['categories_to_documents'], array('document_id' => $id));
        if (!$catResult) {
            return false;
        }

        return true;
    }


    // Uploads to the storage engine/service in use (e.g. couchdb or S3).
    // In case of an update, can pass required params to $updateArr.
    // Note that an S3 update is actually a delete + insert.
    private function uploadToStorage($arr, $updateArr = array())
    {
        if ($GLOBALS['clinikal_storage_method'] == S3Service::STORAGE_METHOD_CODE) {
            // save to S3
            $creationDateUnixTs = strtotime($arr['documents']['date']);
            // create full url for s3
            $url = $this->createS3Url(
                $GLOBALS['s3_bucket_name'],
                $GLOBALS['s3_path'],
                $arr['documents']['url'],
                $creationDateUnixTs
            );
            $s3Service = new S3Service($this->getContainer());
            $s3Service->connect();
            $decoData = base64_decode($arr['storage']['data']);
            $result['success'] = $s3Service->saveObject($url, $decoData);
            if ($result != false) {
                $result['url'] = $url;
            }
        }
        elseif ($GLOBALS['clinikal_storage_method'] == CouchdbService::STORAGE_METHOD_CODE) {
            // save to couchdb
            $couchdbService = new CouchdbService($this->getContainer());
            $couchdbService->connect();
            if(empty($updateArr)) {
                $result = $couchdbService->putDoc($arr['storage']['data'], $updateArr['id'], $updateArr['rev'], false);
            }
            else {
                $result = $couchdbService->saveDoc($arr['storage']['data'], false);
            }
        }

        return $result;
    }


    private function createS3Url($bucket, $path, $filename, $unixtime)
    {
        $separator = "_";
        return "s3://${bucket}/${path}/${unixtime}${separator}${filename}";
    }


    private function parseS3Url($url)
    {
        $url = ltrim($url, "s3://");
        $arr = explode("/", $url);
        return $arr;
    }

}
