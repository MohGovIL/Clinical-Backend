<?php
/**
 * Date: 21/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class MAPPING FOR ORGANIZATION
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Organization;



use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIRAddressTrait;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIROrganizationTelecomTrait;
use FhirAPI\Service\FhirBaseMapping;
use FhirAPI\Service\FhirRequestParamsHandler;
use Interop\Container\ContainerInterface;
use GenericTools\Model\FacilityTable;
/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIROrganization;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResourceContainer;


class FhirOrganizationMapping extends FhirBaseMapping implements MappingData
{
    private $adapter = null;
    private $container = null;
    private  $FHIROrganization = null;
    private $requestParams;
    use FHIRAddressTrait;
    use FHIROrganizationTelecomTrait;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Zend\Db\Adapter\Adapter');
        $this->setFHIROrganization(new FHIROrganization());
   //     $FhirRequestParamsHandler=$this->container->get(FhirRequestParamsHandler::class);
    //    $this->requestParams=$FhirRequestParamsHandler->getRequestParams();
    }



    /**
     * convert FHIROrganization to db array
     *
     * @param FHIROrganization
     *
     * @return array;
     */
    public function fhirToDb($Organization)
    //public function dbFormat(FHIROrganization $Organization)
    {
        $dbFacility=array();

        $dbFacility['id']=(is_null($Organization->getId())) ?  null : $Organization->getId()->getValue();
        $dbFacility['name']=(is_null($Organization->getName())) ?  null : $Organization->getName()->getValue();
        $dbFacility['facility_code']=(is_null($Organization->getAlias())) ?  null : $Organization->getAlias()->getValue();

        $telecom= $Organization->getTelecom();

        if(!is_null($telecom)&&is_array($telecom)){

            foreach($telecom as $index => $element){
                if(!is_null($telecom)){
                    $system=$element->getSystem();
                    $systemVal=(is_null($system)) ?  null : $system->getValue();
                    $use= $element->getUse();
                    $useVal=(is_null($use)) ?  null : $use->getValue();
                    $valElm=$element->getValue();
                    $val=(is_null($valElm)) ?  null : $valElm->getValue();

                    if($systemVal==="phone" || $useVal==="fax"  || $useVal==="email" ){
                        $valElm=$element->getValue();
                        $dbFacility[$systemVal]=$val;
                        continue;
                    }

                }
            }

        }


        $dbFacility['line']=(is_null($Organization->getAddress()['line'])) ?  null : $Organization->getAddress()['line'];
        $dbFacility['city']=(is_null($Organization->getAddress()['city'])) ?  null : $Organization->getAddress()['city'];
        $dbFacility['state']=(is_null($Organization->getAddress()['state'])) ?  null : $Organization->getAddress()['state'];
        $dbFacility['postal_code']=(is_null($Organization->getAddress()['postalcode'])) ?  null : $Organization->getAddress()['postalcode'];
        $dbFacility['country']=(is_null($Organization->getAddress()['country'])) ?  null : $Organization->getAddress()['country'];

        return $dbFacility;
    }



    /**
     * create FHIROrganization
     *
     * @param  string
     * @return FHIROrganization
     * @throws
     */
    public function DBToFhir(...$params)
    {

        $facilityFromDB = $params[0];

        $dbArray = [];
        if(is_array($facilityFromDB)){
            $facilityFromDB = json_decode(json_encode($facilityFromDB), FALSE);
        }

       // $dbArray['identifier']=[$this->createFHIRIdentifier($facilityFromDB->id)];
        $dbArray['id']=$this->createFHIRId($facilityFromDB->id);
        $dbArray['alias']=!is_null($facilityFromDB->facility_code)? [$this->createFHIRString($facilityFromDB->facility_code)]:null;
        $dbArray['line']=!is_null($facilityFromDB->street)?[$facilityFromDB->street]:null;

        $address['city'] = $facilityFromDB->city;
        $address['state'] = $facilityFromDB->state;
        $address['street'] = $facilityFromDB->street;
        $address['postalcode'] = $facilityFromDB->postal_code;
        $address['country'] = $facilityFromDB->country;
        $dbArray['address'] = $this->createFHIRAddress($address);

        $telecom['fax'] = $facilityFromDB->fax;
        $telecom['email'] = $facilityFromDB->email;
        $telecom['phone_work'] = $facilityFromDB->phone;
        $dbArray['telecom'] = $this->createFHIRTelecom($telecom);

        $dbArray['name']=$this->createFHIRString($facilityFromDB->name);



        $this->setFHIROrganization(new FHIROrganization($dbArray));
        return $this->FHIROrganization;
    }

    /**
     * @param FHIROrganization $FHIROrganization
     * @return FhirPractitionerMapping
     */
    public function setFHIROrganization(FHIROrganization $FHIROrganization)
    {
        $this->FHIROrganization = $FHIROrganization;
        return $this;
    }



    public function DbFhir($dbArray)
    {
        // TODO: Implement DbFhir() method.
    }

    public function parsedJsonToDb($parsedData)
    {
        // TODO: Implement parsedJsonToDb() method.
    }

    public function validateDb($data)
    {
        // TODO: Implement validateDb() method.
    }
    public function initFhirObject()
    {
        // TODO: Implement initFhirObject() method.
        /*
        $FHIROrganization = new FHIROrganization;
        $this->FHIROrganization=$FHIROrganization;
        return $FHIROrganization;
        */
    }
}





