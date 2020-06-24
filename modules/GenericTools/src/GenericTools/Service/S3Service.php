<?php


namespace GenericTools\Service;

use Doctrine\CouchDB\HTTP\HTTPException;
use Interop\Container\ContainerInterface;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Service
{

    private $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function connect()
    {
        try {
            $this->connection = new Aws\S3\S3Client([
                'version' => $GLOBALS['s3_version'],
                'region' => $GLOBALS['s3_region']
            ]);
            $this->connection->registerStreamWrapper();
        }catch (AwsException $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function saveDoc($data, $encoded = true)
    {
        if($encoded) {
            $data =  base64_encode($data);
        }
        try {
            $couch  = $this->connection->postDocument(array(
                'data' => $data,
                'encounter' => '',
                'mimetype' => 'application/pdf',
                'pid' => isset($_SESSION['pid']) ? $_SESSION['pid'] : ''
            ));
            return array('id' => $couch[0], 'rev' => $couch[1]);
        } catch (HTTPException $e) {
            error_log('Save doc error : ' . json_encode((array)$e));
            return false;
        }
    }

    public function fetchObject($url)
    {
        try {
            $result = file_get_contents($url);
            return $result;
        }catch (AwsException $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    public function deleteDocument($id, $rev)
    {
        try{
            $response = $this->connection->deleteDocument($id, $rev);

        } catch(HTTPException $e){
            $message=$e->getMessage();
            return $message;
        }

        return true;
    }
}
