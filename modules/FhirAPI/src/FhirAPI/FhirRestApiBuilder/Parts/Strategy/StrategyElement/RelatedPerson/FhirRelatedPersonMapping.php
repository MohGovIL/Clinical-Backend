<?php
/**
 * Date: 23/04/20
 * @author  Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR ORGANIZATION
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\RelatedPerson;

use function DeepCopy\deep_copy;
use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\RelatedPersonTable;
use Interop\Container\ContainerInterface;



/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRRelatedPerson;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Patient\FhirPatientMapping;



class FhirRelatedPersonMapping extends FhirBaseMapping implements MappingData
{

    private $adapter = null;
    private $container = null;
    private $FHIRRelatedPerson = null;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRRelatedPerson = new FHIRRelatedPerson;
    }

    public function fhirToDb($FhirObject)
    {
        $dbRelatedPerson=array();

        $dbRelatedPerson['id'] = (is_null($FhirObject->getId())) ? null : $FhirObject->getId()->getValue();

        $identifier=$FhirObject->getIdentifier();
        $identifier= ( !is_array($identifier) ) ? null : $identifier[0];
        $dbRelatedPerson['identifier'] = ( is_null($identifier) ) ? null : $identifier->getValue();

        try{
            $type=$identifier->getType()->getCoding()[0]->getCode();
        } catch (Exception $e){
            $type=null;
        }
        $dbRelatedPerson['identifier_type'] = (is_null($type)) ? null : $type;

        $active=$FhirObject->getActive()->getValue();
        $dbRelatedPerson['active'] = ($active===true || $active==="true") ? 1 : 0;

        $reference=$FhirObject->getPatient()->getReference()->getValue();
        if (strpos($reference, 'Patient/') !== false) {
            $dbRelatedPerson['pid']=(!empty($reference)) ? substr($reference,8,20) : null;
        }

        $relationship=$FhirObject->getRelationship()[0]->getCoding()[0]->getCode()->getValue();
        $dbRelatedPerson['relationship'] = ( is_null($relationship) ) ? null : $relationship;
        $dbRelatedPerson['gender'] = (is_null($FhirObject->getGender())) ? null : $FhirObject->getGender()->getValue();

        $FhirPatientMapping= new FhirPatientMapping($this->container);
        $telecom = $FhirObject->getTelecom();
        $dbRelatedPerson=$FhirPatientMapping->setTelecom($telecom,$dbRelatedPerson);

        return array("related_person"=>$dbRelatedPerson);
    }


    /**
     * create FHIRRelatedPerson
     *
     * @param array
     * @param bool
     *
     * @return FHIRRelatedPerson | FHIRBundle | null
     * @throws
     */
    public function DBToFhir(...$parmas)
    {
        $relatedPersonFromDb=$parmas[0];

        if (!is_array($relatedPersonFromDb) || count($relatedPersonFromDb) < 1) {
            return null;
        }
        $FHIRRelatedPerson=$this->FHIRRelatedPerson;

        $FHIRRelatedPerson->setId($relatedPersonFromDb['id']);
        $FHIRRelatedPerson->getIdentifier()[0]->setValue($relatedPersonFromDb['identifier']);
        $FHIRRelatedPerson->getIdentifier()[0]->getType()->getCoding()[0]->setCode($relatedPersonFromDb['identifier_type']);
        $active= ($relatedPersonFromDb['active']) ? true :false;
        $FHIRRelatedPerson->getActive()->setValue($active);
        $patientReference='Patient/'.$relatedPersonFromDb['pid'];
        $FHIRString=$this->createFHIRString($patientReference);
        $FHIRRelatedPerson->getPatient()->setReference($FHIRString);
        $FHIRGender = $this->createFHIRAdministrativeGender($relatedPersonFromDb['gender']);
        $FHIRRelatedPerson->getRelationship()[0]->getCoding()[0]->getCode()->setValue($relatedPersonFromDb['relationship']);
        $FHIRRelatedPerson->getGender()->setValue($FHIRGender);

        $FhirPatientMapping= new FhirPatientMapping($this->container);
        $FhirPatientMapping->setFHIRTelecom($relatedPersonFromDb,$FHIRRelatedPerson);


        return $FHIRRelatedPerson;


    }


    /**
     * check if RelatedPerson data is valid
     *
     * @param array
     * @return bool
     */
    public function validateDb($data)
    {
        return true;
    }



    public function parsedJsonToDb($parsedData)
    {
        return $parsedData;

    }

    public function parsedJsonToFHIR($parsedData)
    {
        $relatedPerson=$parsedData;

        if (!is_array($relatedPerson) || count($relatedPerson) < 1) {
            return null;
        }

        $FHIRRelatedPerson=$this->FHIRRelatedPerson;

        $FHIRRelatedPerson->getId()->setValue($parsedData['id']);

        $identifier=$parsedData['identifier'][0]['value'];
        $FHIRRelatedPerson->getIdentifier()[0]->setValue($identifier);

        $type=$parsedData['identifier'][0]['type']['coding'][0]['code'];
        $FHIRRelatedPerson->getIdentifier()[0]->getType()->getCoding()[0]->setCode($type);

        $FHIRRelatedPerson->getActive()->setValue($parsedData['active']);

        $patientReference=$parsedData['patient']['reference'];
        $FHIRString=$this->createFHIRString($patientReference);
        $FHIRRelatedPerson->getPatient()->setReference($FHIRString);

        try{
            $relationship=$parsedData['relationship'][0]['coding'][0]['code'];
        } catch (Exception $e){
            $relationship=null;
        }
        $FHIRRelatedPerson->getRelationship()[0]->getCoding()[0]->getCode()->setValue($relationship);

        $FHIRRelatedPerson->getGender()->setValue($parsedData['gender']);

        $telecom=$parsedData['telecom'];

        foreach($telecom as $j =>$telData){

            $FHIRRelatedPerson->getTelecom()[$j]->getSystem()->setValue($telData['system']);
            $FHIRRelatedPerson->getTelecom()[$j]->getValue()->setValue($telData['value']);
            $FHIRRelatedPerson->getTelecom()[$j]->getUse()->setValue($telData['use']);

        }

        return $FHIRRelatedPerson;

    }

    public function initFhirObject()
    {
        $FHIRRelatedPerson = new FHIRRelatedPerson;

        $FhirId = $this->createFHIRId(null);
        $FHIRRelatedPerson->setId($FhirId);
        $FhirPatientIdentifier = $this->createFHIRIdentifier(null);
        $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array('code'=>null));
        $FhirPatientIdentifier->setType($FHIRCodeableConcept);
        $FHIRRelatedPerson->addIdentifier($FhirPatientIdentifier);
        $FHIRBoolean=$this->createFHIRBoolean(null);
        $FHIRRelatedPerson->setActive($FHIRBoolean);
        $FHIRReference=$this->createFHIRReference(array('reference'=>null));
        $FHIRRelatedPerson->setPatient($FHIRReference);
        $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array('code'=>null));
        $FHIRRelatedPerson->addRelationship($FHIRCodeableConcept);
        $telecom=$this->createFHIREmptyContactPoint();
        $FHIRRelatedPerson->addTelecom(deep_copy($telecom));
        $FHIRRelatedPerson->addTelecom(deep_copy($telecom));
        $FHIRRelatedPerson->addTelecom(deep_copy($telecom));
        $FHIRGender = $this->createFHIRAdministrativeGender(null);
        $FHIRRelatedPerson->setGender($FHIRGender);

        $this->FHIRRelatedPerson=$FHIRRelatedPerson;
        return $FHIRRelatedPerson;
    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        $FHIRRelatedPerson = $this->parsedJsonToFHIR($data);
        $dBdata = $this->fhirToDb($FHIRRelatedPerson);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        $relatedPersonTable = $this->container->get(RelatedPersonTable::class);
        $primaryKey='id';
        $updated=$relatedPersonTable->safeUpdate($data['related_person'],array($primaryKey=>$id));
        $this->initFhirObject();
        return $this->DBToFhir($updated);
    }


}
