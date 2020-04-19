<?php
/**
 * Date: 29/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class Fhir builder strategy context class
 */


namespace  FhirAPI\FhirRestApiBuilder\Parts\Strategy ;

class Context
{
    /**
     * @var Strategy The Context maintains a reference to one of the Strategy
     * objects. The Context does not know the concrete class of a strategy. It
     * should work with all strategies via the Strategy interface.
     */
    private $strategy;
    private $type;
    private $params;
    private $strategyName;
    private $container;

    /**
     * Usually, the Context accepts a strategy through the constructor, but also
     * provides a setter to change it at runtime.
     */
    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Usually, the Context allows replacing a Strategy object at runtime.
     */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * set new type  at runtime.
     */
    public function setTypeOfRestCall($type)
    {
        $this->type = $type;
    }

    /**
     * set params of rest call  at runtime.
     */
    public function setParamsFromUrl($paramsFromUrl)
    {
        $this->paramsFromUrl = $paramsFromUrl;
    }

    /**
     * The Context delegates some work to the Strategy object instead of
     * implementing multiple versions of the algorithm on its own.
     */
    public function doSomeBusinessLogic()
    {
       return $this->strategy->doAlgorithm(["type" => $this->type,"paramsFromUrl"=>$this->paramsFromUrl,'paramsFromBody'=>$this->paramsFromBody,'strategyName'=>$this->strategyName,"container"=>$this->container]);
    }

    public function setStrategyName($name)
    {
        $this->strategyName=$name;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function setBodyParams($paramsFromBody)
    {
        $this->paramsFromBody = $paramsFromBody;
    }
}
