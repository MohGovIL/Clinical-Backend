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

    const COUCHDB_STORAGE = 1;
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

    private function setMapping($container)
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

        if($GLOBALS['use_s3']) {
            //get file from S3
            $s3Service = new S3Service($this->getContainer());
            $s3Service->connect();
            $data = $s3Service->fetchObject($fullUrl);
            $encData = base64_encode($data);
            $documentsDataFromDb['fileData'] = $encData;
        }
        else {
            //get document from couchdb
            $couchdbService = new CouchdbService($this->getContainer());
            $couchdbService->connect();
            $documentsDataFromDb['fileData'] = $couchdbService->fetchDoc($documentsDataFromDb['couchDocId'], false);
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

        //get db connections
        $documentsTable = $this->container->get(DocumentsTable::class);
        $documentsCategoriesTable = $this->container->get(DocumentsCategoriesTable::class);

        $this->mapping->initFhirObject();

        $json = $this->paramsFromBody['POST_PARSED_JSON'];
        $json['id'] = $documentsTable->lastId() + 1;;
        $fhirDocumentReference = $this->mapping->parsedJsonToFhir($json);
        $dbStructuredData = $this->mapping->fhirToDb($fhirDocumentReference);

        $creationDate = date("Y-m-d H:i:s");
        $dbStructuredData['date'] = $creationDate;

        $valid = $this->mapping->validateDb($dbStructuredData);
        if(!$valid) {
            return self::$errorCodes::http_response_code(406);
        }

        if($GLOBALS['use_s3']) {
            // save to S3
            $creationDateUnixTs = strtotime($creationDate);
            $dbStructuredData['documents']['url'] = $this->createS3Url(
                $GLOBALS['s3_bucket_name'],
                $GLOBALS['s3_path'],
                $dbStructuredData['documents']['url'],
                $creationDateUnixTs
            );
            $s3Service = new S3Service($this->getContainer());
            $s3Service->connect();
            $decoData = base64_decode($dbStructuredData['storage']['data']);
            $result = $s3Service->saveObject($fullUrl, $decoData);
            if ($result == false) {
                return self::$errorCodes::http_response_code(500);
            }
        }
        else {
            // save to couchdb
            $couchdbService = new CouchdbService($this->getContainer());
            $couchdbService->connect();
            $couchdbIds = $couchdbService->saveDoc($dbStructuredData['storage']['data'], false);
            if ($couchdbIds == false) {
                return self::$errorCodes::http_response_code(500);
            }
            $dbStructuredData['documents']['couch_docid'] = $couchdbIds['id'];
            $dbStructuredData['documents']['couch_revid'] = $couchdbIds['rev'];
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
        $couchDocId=$documentsDataFromDb['couchDocId'];
        $couchRevId=$documentsDataFromDb['couchRevId'];
        $mysqlID=$documentsDataFromDb['id'];

        //get data from couchdb
        $couchdbService = new CouchdbService($this->getContainer());
        $couchdbService->connect();
        $rez = $couchdbService->deleteDocument($couchDocId, $couchRevId);

        if($rez!==true){
            $explanation="failed to delete from doc db";
            return $this->mapping->createDeleteFailRespond($docId,$explanation,$rez);
        }else{

            $delete=$documentsTable->deleteDataByParams(array("id"=>$mysqlID));
            if($delete===1){
                return $this->mapping->createDeleteSuccessRespond();
            }else{
                $explanation="failed to delete from db ";
                return $this->mapping->createDeleteFailRespond($docId,$explanation);
            }

        }

    }

    private function createS3Url($bucket, $path, $filename, $unixtime)
    {
        $separator = "_";
        return "s3://${$bucket}/${$path}/${unixtime}${separator}${filename}";
    }

    private function parseS3Url($url)
    {
        $url = ltrim($url, "s3://");
        $arr = explode("/", $url);
        return $arr;
    }
}
