<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Is the restful playground of all the elements that where built by the builder class
 */


namespace FhirAPI\FhirRestApiBuilder\Parts;


use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\FHIRRestInt;

abstract  class Restful extends Registry implements FHIRRestInt
{

    protected $container;
    protected $functionName;
    protected $paramsFromUrl;
    protected $paramsFromBody;
    protected $mapping;
    protected $operations;
    protected static $errorCodes = null;
    const SEARCHSTRATEGYPATH = "FhirAPI\FhirRestApiBuilder\Parts\Search\SearchStrategies\\";

     /**
     * @return null
     */
    public static function getErrorCodes()
    {
        return self::$errorCodes;
    }

    /**
     * @param null $errorCodes
     */
    public static function setErrorCodes(): void
    {
        if(is_null(self::$errorCodes)) {
            self::$errorCodes = new ErrorCodes();
        }
    }


    public function read()
     {
         // TODO: Implement read() method.
         return self::$errorCodes::http_response_code(501);
     }

    /*
     * couldn't just use the read function because 2 routes can't be mapped to same function
     */
    public function readOp()
    {

         $result = $this->read();
         return $result;
    }

     public function vread()
     {
         // TODO: Implement vread() method.
         return self::$errorCodes::http_response_code(501);
     }

     public function update()
     {
         // TODO: Implement update() method.
         return self::$errorCodes::http_response_code(501);
     }

     public function patch()
     {
         // TODO: Implement patch() method.
         return self::$errorCodes::http_response_code(501);
     }

     public function delete()
     {
         // TODO: Implement delete() method.
         return self::$errorCodes::http_response_code(501);
     }

     public function history()
     {
         // TODO: Implement history() method.
         return self::$errorCodes::http_response_code(501);
     }

     public function create()
     {
         // TODO: Implement create() method.
         return self::$errorCodes::http_response_code(501);
     }

     public function search()
     {
         // TODO: Implement search() method.
         return self::$errorCodes::http_response_code(501);
     }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function setParamsFromUrl($paramsFromUrl)
    {
        // set only params that are not operations
        $this->paramsFromUrl = array_filter($paramsFromUrl, function($param) {
            return !$this->isOperation($param);
        });
    }

    public function setParamsFromBody($paramsFromBody)
    {
        $this->paramsFromBody = $paramsFromBody;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    protected function setOperations($paramsFromUrl)
    {
        $this->operations = array_filter($paramsFromUrl, [$this, "isOperation"]);
    }

    protected function isOperation($param)
    {
        $isOperation = substr($param, 0, 1) === '$';
        return $isOperation;
    }
 }
