<?php
/**
 * Date: 21/01/20
 * @author  Dror Golan <drorgo@matrix.co.il>
 * This class MAPPING FOR ORGANIZATION
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Practitioner;

use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;

/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRPractitioner;


class FhirPractitionerMapping extends FhirBaseMapping implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIRPractitioner = null;
    private $requestParams;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Zend\Db\Adapter\Adapter');
        $this->setFHIRPractitioner(new FHIRPractitioner());
   //     $FhirRequestParamsHandler=$this->container->get(FhirRequestParamsHandler::class);
    //    $this->requestParams=$FhirRequestParamsHandler->getRequestParams();
    }



    /**
     * convert FHIRPractitioner to db array
     *
     * @param FHIRPractitioner
     *
     * @return array;
     */
    public function fhirToDb($Organization)
    //public function dbFormat(FHIROrganization $Organization)
    {

    }



    /**
     * create FHIRPractitioner
     *
     * @param  string
     * @return FHIRPractitioner
     * @throws
     */
    public function DBToFhir(...$params)
    {

        $userFromDb = $params[0];
        if(is_object($userFromDb)){
            $userFromDb=(array)$userFromDb;
        }

        $FHIRPractitioner=$this->FHIRPractitioner;
        $FHIRPractitioner->getId()->setValue($userFromDb['id']);
        $this->FHIRPractitioner->getActive()->setValue($userFromDb['active']);;
        $FHIRIdentifier=$this->createFHIRIdentifier($userFromDb['federaltaxid']);
        $this->FHIRPractitioner->getIdentifier()[0]->setValue($FHIRIdentifier);
        $name=$FHIRPractitioner->getName()[0];
        $name->getFamily()->setValue($userFromDb['lname']);
        $name->getGiven()[0]->setValue($userFromDb['fname']);
        $name->getGiven()[1]->setValue($userFromDb['mname']);

        return $this->FHIRPractitioner;
    }

    /**
     * @param FHIROrganization $FHIROrganization
     *
     * @return FhirPractitionerMapping
     */
    public function setFHIRPractitioner(FHIRPractitioner $FHIRPractitioner)
    {
        $this->FHIRPractitioner = $FHIRPractitioner;
        return $this;
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

        $FHIRPractitioner = new FHIRPractitioner();
        $FhirId = $this->createFHIRId(null);
        $FHIRPractitioner->setId($FhirId);
        $FHIRBoolean=$this->createFHIRBoolean(null);
        $FHIRPractitioner->setActive($FHIRBoolean);
        $FHIRIdentifier=$this->createFHIRIdentifier(null);
        $FHIRPractitioner->addIdentifier($FHIRIdentifier);
        $FHIRHumanName = $this->createFHIRHumanName(null, null, null);
        $FHIRPractitioner->addName($FHIRHumanName);

        $this->FHIRPractitioner= $FHIRPractitioner;

        return $this->FHIRPractitioner;

    }
}





