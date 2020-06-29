<?php

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\DocumentReference;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRDocumentReference;
use OpenEMR\FHIR\R4\FHIRResource\FHIRDocumentReference\FHIRDocumentReferenceContent;
use OpenEMR\FHIR\R4\FHIRResource\FHIRDocumentReference\FHIRDocumentReferenceContext;
use GenericTools\Service\CouchdbService;
use GenericTools\Service\S3Service;

class FhirDocumentReferenceMapping extends FhirBaseMapping  implements MappingData
{
    const DOC_TYPE = "file_url";
    const CURRENT_STATUS = "current";

    private $adapter = null;
    private $container = null;
    private $FHIRDocumentReference = null;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRDocumentReference = new FHIRDocumentReference;
    }

    public function setFHIR($fhir=null)
    {
        if(is_null($fhir)){
            $this->FHIRDocumentReference = new FHIRDocumentReference;
            return $this->FHIRDocumentReference;
        }
        try{
            $this->FHIRDocumentReference = new FHIRDocumentReference($fhir);
            return $this->FHIRDocumentReference;
        }catch(Exception $e){
            return false;
        }
    }

    public function fhirToDb($FhirObject){
        //Prepare array of data to be inserted into mariadb (documents + categories_to_documents tables) and storage engine
        $dbArr = array(
            "documents" => array(),
            "categories_to_documents" => array(),
            "storage" => array()
        );

        if($GLOBALS['use_s3']) {
            $storageMethod = S3Service::STORAGE_METHOD_CODE;
        }
        else {
            $storageMethod = CouchdbService::STORAGE_METHOD_CODE;
        }

        $dbArr["documents"]["type"] = self::DOC_TYPE;
        $dbArr["documents"]["storagemethod"] = $storageMethod;

        $dbArr["documents"]["id"] = $FhirObject->id->getValue();
        $dbArr["documents"]["url"] = $FhirObject->content[0]->attachment->url->getValue();
        $dbArr["documents"]["mimetype"] = $FhirObject->content[0]->attachment->contentType->getValue();
        $dbArr["documents"]["owner"] = explode("/", $FhirObject->author[0]->reference->getValue())[1];
        $dbArr["documents"]["foreign_id"] = explode("/", $FhirObject->context->sourcePatientInfo->reference->getValue())[1];
        $dbArr["documents"]["encounter_id"] = explode("/", $FhirObject->context->encounter[0]->reference->getValue())[1];
        $dbArr['categories_to_documents']['document_id'] = $FhirObject->id->getValue();
        $dbArr["categories_to_documents"]["category_id"] = $FhirObject->category[0]->coding[0]->code->getValue();
        $dbArr["storage"]["data"] = $FhirObject->content[0]->attachment->data->getValue();

        return $dbArr;
    }

    public function DBToFhir(...$params){
        $dbData = $params[0];
        $this->FHIRDocumentReference->id->setValue($dbData["id"]);
        $this->FHIRDocumentReference->status->setValue(self::CURRENT_STATUS);
        $this->FHIRDocumentReference->content[0]->attachment->url->setValue($dbData["url"]);
        $this->FHIRDocumentReference->content[0]->attachment->contentType->setValue($dbData["mimeType"]);
        $this->FHIRDocumentReference->author[0]->reference->setValue("Practitioner/{$dbData['owner']}");
        $this->FHIRDocumentReference->context->sourcePatientInfo->reference->setValue("Patient/{$dbData['pid']}");
        $this->FHIRDocumentReference->context->encounter[0]->reference->setValue("Encounter/{$dbData['encounterId']}");
        $this->FHIRDocumentReference->category[0]->coding[0]->code->setValue($dbData["categoryId"]);
        $this->FHIRDocumentReference->category[0]->coding[0]->display->setValue($dbData["categoryName"]);
        $this->FHIRDocumentReference->content[0]->attachment->data->setValue($dbData["fileData"]);

        return $this->FHIRDocumentReference;
    }

    public function parsedJsonToFhir($parsedData)
    {
        $this->arrayToFhirObject($this->FHIRDocumentReference, $parsedData);
        return $this->FHIRDocumentReference;
    }

    public function validateDb($data)
    {
        return true;
    }

    public function initFhirObject()
    {
        $FHIRDocumentReference = new FHIRDocumentReference();

        $id = $this->createFHIRId(null);
        $FHIRDocumentReference->setId($id);

        $status = $this->createFHIRCode(null);
        $FHIRDocumentReference->setStatus($status);

        $category = $this->createFHIRCodeableConcept(array('code' => null, 'display' => null));
        $FHIRDocumentReference->addCategory($category);

        $author = $this->createFHIRReference(array('reference' => null, 'display' => null));
        $FHIRDocumentReference->addAuthor($author);

        $arr = array(
            'attachment' => array(
                'contentType' => null,
                'data' => null,
                'url' => null
            )
        );
        $content = $this->createFHIRDocumentReferenceContent($arr);
        $FHIRDocumentReference->addContent($content);

        $arr = array(
            'encounter' => array('reference' => null),
            'sourcePatientInfo' => array('reference' => null)
        );
        $context = $this->createFHIRDocumentReferenceContext($arr);
        $FHIRDocumentReference->setContext($context);

        $this->FHIRDocumentReference = $FHIRDocumentReference;
    }


    private function createFHIRDocumentReferenceContent($arr)
    {
        $FHIRDocumentReferenceContent = new FHIRDocumentReferenceContent();

        if (key_exists('attachment', $arr)) {
            $attachment = $this->createFHIRAttachment($arr['attachment']);
            $FHIRDocumentReferenceContent->setAttachment($attachment);
        }

        return $FHIRDocumentReferenceContent;
    }


    private function createFHIRDocumentReferenceContext($arr)
    {
        $FHIRDocumentReferenceContext = new FHIRDocumentReferenceContext();

        if (key_exists('encounter', $arr)) {
            $ref = $this->createFHIRReference($arr['encounter']);
            $FHIRDocumentReferenceContext->addEncounter($ref);
        }

        if (key_exists('sourcePatientInfo', $arr)) {
            $ref = $this->createFHIRReference($arr['sourcePatientInfo']);
            $FHIRDocumentReferenceContext->setSourcePatientInfo($ref);
        }

        return $FHIRDocumentReferenceContext;
    }

}
