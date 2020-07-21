<?php


namespace GenericTools\Service;

use Doctrine\CouchDB\HTTP\HTTPException;
use Interop\Container\ContainerInterface;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Service
{

    const STORAGE_METHOD_CODE = 10;

    private $connection;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function connect()
    {
        try {
            $this->connection = new S3Client([
                'version' => $GLOBALS['s3_version'],
                'region' => $GLOBALS['s3_region']
            ]);
            $this->connection->registerStreamWrapper();
        }catch (AwsException $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function saveObject($url, $data)
    {
        try {
            $result = file_put_contents($url, $data);
            return $result;
        }catch (AwsException $e) {
            echo $e->getMessage() . "\n";
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

    public function deleteObject($url)
    {
        try {
            $result = unlink($url);
            return $result;
        }catch (AwsException $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    //todo : implement update object ??
}
