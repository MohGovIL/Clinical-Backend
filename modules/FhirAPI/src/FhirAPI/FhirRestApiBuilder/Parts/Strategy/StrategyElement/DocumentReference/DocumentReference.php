<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\DocumentReference;

use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use GenericTools\Model\DocumentsTable;
use GenericTools\Model\DocumentsCategoriesTable;
use GenericTools\Service\CouchdbService;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;

class DocumentReference extends Restful implements  Strategy
{

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

        //get data from couchdb
        $couchdbService = new CouchdbService($this->getContainer());
        $couchdbService->connect();
        $documentsDataFromDb['couchdbData'] = $couchdbService->fetchDoc($documentsDataFromDb['couchDocId'], false);

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
        $couchdbService = new CouchdbService($this->getContainer());
        $couchdbService->connect();

        $this->mapping->initFhirObject();

        $json = $this->paramsFromBody['POST_PARSED_JSON'];
        $json['id'] = $documentsTable->lastId() + 1;;
        $fhirDocumentReference = $this->mapping->parsedJsonToFhir($json);
        $dbStructuredData = $this->mapping->fhirToDb($fhirDocumentReference);

        $valid = $this->mapping->validateDb($dbStructuredData);
        if(!$valid) {
            return self::$errorCodes::http_response_code(406);
        }
        /*
        // save to couchdb
        In the new env couchdb not working and we will work with S3
        Until it will develop the document will not saved
        $couchdbIds = $couchdbService->saveDoc($dbStructuredData['couchdb']['data'], false);
        if( $couchdbIds == false ) {
            return self::$errorCodes::http_response_code(500);
        }
        */
        // todo - replace it
        $couchdbIds['id'] = 5;
        $couchdbIds['rev']  = md5('string');
        // save to documents table
        $dbStructuredData['documents']['couch_docid'] = $couchdbIds['id'];
        $dbStructuredData['documents']['couch_revid'] = $couchdbIds['rev'];
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
}
