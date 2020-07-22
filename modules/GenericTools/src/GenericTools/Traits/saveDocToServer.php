<?php


namespace GenericTools\Traits;

use GenericTools\Service\CouchdbService;
use GenericTools\Service\S3Service;

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

}
