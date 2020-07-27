<?php


namespace GenericTools\Traits;

use GenericTools\Service\CouchdbService;
use GenericTools\Service\S3Service;
use GenericTools\Model\DocumentsCategoriesTable;
use GenericTools\Model\DocumentsTable;


trait saveDocToServer
{
    /**
     * @param $arr
     * @param $updateArr
     * @return array | null
     */
    // Uploads to the storage engine/service in use (e.g. couchdb or S3).
    // In case of an update, can pass required params to $updateArr.
    // Note that an S3 update is actually a delete + insert.
    public function uploadToStorage($arr, $updateArr = array())
    {
        $result=array();
        $result['id'] = false;
        $result['url'] = null;
        $result['rev'] = null;

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
            $result['id'] = $s3Service->saveObject($url, $decoData);
            if ($result['id'] != false) {
                $result['url'] = $url;
            }
        }
        elseif ($GLOBALS['clinikal_storage_method'] == CouchdbService::STORAGE_METHOD_CODE) {
            // save to couchdb
            $couchdbService = new CouchdbService($this->getContainer());
            $couchdbService->connect();
            if(empty($updateArr)) {
                $couchSave = $couchdbService->putDocument($arr['storage']['data'], $updateArr['id'], $updateArr['rev'], false);
            }
            else {
                $couchSave = $couchdbService->saveDoc($arr['storage']['data'], false);
            }
            if(is_array($couchSave)){
                $result['id'] = $couchSave['id'];
                $result['rev'] = $couchSave['rev'];
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

    /**
     * build array for saveDocToDb function
     * @param $data
     * @return array|array[]
     *
     */
    public function buildArrToDb($data)
    {
        if (empty($data['category']) || empty($data['encounter']) || empty($data['mimetype'])) {
            return array();
        }

        $dbStructuredData = array(
            'documents' => array(
                'id' => null,     //will be filled later
                'type' => self::DOC_TYPE,
                'storagemethod' => $GLOBALS['clinikal_storage_method'],
                'mimetype' => $data['mimetype'],
                'owner' => (empty($data['owner'])) ? $_SESSION['authUserID'] : $data['owner'],
                'foreign_id' => (empty($data['patient'])) ? null : $data['patient'],      //patient
                'encounter_id' => $data['encounter'],
                'date' => date('Y-m-d H:i:s'),
            ),
            'categories_to_documents' => array(
                'document_id' => null,   //will be filled later
                'category_id' => $data['category'],
            )
        );

        if($GLOBALS['clinikal_storage_method'] == S3Service::STORAGE_METHOD_CODE) {
            $dbStructuredData['documents']['url'] = $data['url'];
            $dbStructuredData['documents']['couch_docid'] = null;
            $dbStructuredData['documents']['couch_revid'] = null;
        }
        elseif($GLOBALS['clinikal_storage_method'] == CouchdbService::STORAGE_METHOD_CODE) {
            $dbStructuredData['documents']['couch_docid'] = $data['id'];
            $dbStructuredData['documents']['couch_revid'] = $data['rev'];
        }

        return $dbStructuredData;
    }

    /**
     * save document info to db
     * @param $data
     * @return array|array[]
     *
     */
    public function saveDocToDb($dbStructuredData)
    {
        $documentsTable = $this->container->get(DocumentsTable::class);
        $documentsCategoriesTable = $this->container->get(DocumentsCategoriesTable::class);

        $id= $documentsTable->lastId() + 1;

        $dbStructuredData['documents']['id'] = $id;
        $dbStructuredData['categories_to_documents']['document_id']= $id;

        // save to documents table
        $insertInfo = $documentsTable->insert($dbStructuredData['documents']);
        if ($insertInfo == false) {
            return false;
        }

        // save to categories_to_documents table
        $insertCategory = $documentsCategoriesTable->insert($dbStructuredData['categories_to_documents']);
        if ($insertCategory == false) {
            return false;
        }

        return $id;
    }

}
