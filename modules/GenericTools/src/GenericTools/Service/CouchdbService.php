<?php


namespace GenericTools\Service;

use Doctrine\CouchDB\HTTP\HTTPException;
use Formhandler\Plugin\CouchDBHandle;
use http\Exception;
use Interop\Container\ContainerInterface;

class CouchdbService
{

    const STORAGE_METHOD_CODE = 1;

    private $connection;


    /**
     * CouchdbService constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->continer = $container;
    }

    public function connect()
    {
        $couchPlugin = new CouchDBHandle($this->continer);
        $this->connection = $couchPlugin->couchDBConnection();
    }

    public function saveDoc($data, $encode = true)
    {
        if($encode) {
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

    public function fetchDoc($docId, $decode = true)
    {
        $response = $this->connection->findDocument($docId);
        if($response && $response->status == 200 ) {
            $data = $response->body['data'];
            if($decode) {
                $data = base64_decode($data);
            }
            return $data;
        } else {
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
