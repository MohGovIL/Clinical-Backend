<?php
/**
 * Date: 29/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class strategy Fhir  ORGANIZATION
 *
 *
 */


namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Practitioner;

use FhirAPI\FhirRestApiBuilder\Parts\Registry;
use FhirAPI\FhirRestApiBuilder\Parts\Restful;
use FhirAPI\FhirRestApiBuilder\Parts\Search\SearchContext;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Strategy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Practitioner\FhirPractitionerMapping;
use GenericTools\Model\UserTable;



class Practitioner Extends Restful implements Strategy
{

    public function __construct($params=null)
    {
        if(!is_null($params))
        {
            $this->initParams($params);
        }
    }

    private function initParams($initials){
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


    public function setMapping($container)
    {
        $this->mapping = new FhirPractitionerMapping($container);
    }

    public function read()
    {
        $fhirPractitionerMapping =$this->mapping;
        $usersTable = $this->container->get(UserTable::class);
        $userFromDb = $usersTable->getUser($this->paramsFromUrl[0]);
        if(!$userFromDb)
        {
            //not found
            return self::$errorCodes::http_response_code(204);
        }
        $fhirPractitionerMapping->initFhirObject();
        $user=$fhirPractitionerMapping->DBToFhir($userFromDb);
        $fhirPractitionerMapping->initFhirObject();

        return $user;

    }


    public function search()
    {

        $paramsToSearch = array(
            'tableToSearchOnOrm'=>$this->container->get(UserTable::class),
            'fhirObj'=>new FhirPractitionerMapping($this->container),
            'paramsToSearch'=>null,
            'container'=>$this->container,
            'paramsFromUrl'=>$this->paramsFromUrl,
            'paramsFromBody'=>$this->paramsFromBody,
            'buildThisSearch' => self::SEARCHSTRATEGYPATH . 'PractitionerSearch'
        );
        $searchContext = new SearchContext($paramsToSearch);
        return $searchContext->doSearch();

        //return SearchContext::search($paramsToSearch);

    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

}
