<?php
/**
 * Date: 24/03/20
 *  @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Questionnaire
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest;

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRServiceRequest;
use function DeepCopy\deep_copy;
use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use Interop\Container\ContainerInterface;



/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRQuestionnaire;
use OpenEMR\FHIR\R4\FHIRElement\FHIRPublicationStatus;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;


class FhirServiceRequestMapping extends FhirBaseMapping implements MappingData
{

    private $adapter = null;
    private $container = null;
    private $FHIRServiceRequest = null;
    private $formQuestionMapping;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRServiceRequest = new FHIRServiceRequest;
    }

    public function fhirToDb($FhirObject)
    {
        return array();
    }

    public function getFormQuestionMapping()
    {
        return $this->formQuestionMapping;
    }


    public function initFhirObject()
    {
        $FHIRServiceRequest = new FHIRServiceRequest;

        $FhirId = $this->createFHIRId(null);
        $FHIRServiceRequest->setId($FhirId);

        $this->FHIRServiceRequest=$FHIRServiceRequest;
        return $FHIRServiceRequest;
    }

    /**
     * create FHIRServiceRequest
     *
     * @param array
     * @param bool
     *
     * @return FHIRServiceRequest | FHIRBundle | null
     * @throws
     */
    public function DBToFhir(...$parmas)
    {
        $ServiceRequestFromDb=$parmas[0];

        if (!is_array($ServiceRequestFromDb) || count($ServiceRequestFromDb) < 1) {
            return null;
        }
        $FHIRServiceRequest=$this->FHIRServiceRequest;

        $FHIRServiceRequest->setId($ServiceRequestFromDb['id']);


        return $FHIRServiceRequest;


    }


    public function validateDb($data)
    {
        return true;
    }


    public function parsedJsonToDb($parsedData)
    {
        return array();

    }

    public function parsedJsonToFHIR($parsedData)
    {

        return array();

    }

    public function getDbDataFromRequest($data)
    {
        return array();
    }

    public function updateDbData($data,$id)
    {
        return array();
    }


}
