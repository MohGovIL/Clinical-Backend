<?php
/**
 * Date: 21/01/20
 * @author  eyal wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR Condition
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Observation;

use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsOpenEmrTable;
use GenericTools\Model\ListsTable;
use Interop\Container\ContainerInterface;

/*include FHIR*/
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRObservation;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;

use function DeepCopy\deep_copy;

class FhirObservationMapping extends FhirBaseMapping  implements MappingData
{
    private $adapter = null;
    private $container = null;
    private $FHIRObservation = null;

    /*
    private $outcomeTypes= array();
    */



    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRObservation = new FHIRObservation;

        $ListsTable = $this->container->get(ListsTable::class);

        /*
        $listOutcome = $ListsTable->getListNormalized(self::OUTCOME_LIST);
        $this->setOutcomeTypes($listOutcome);
        */


    }


    /**
     * set fhir object
     */
    public function setFHIR($fhir=null)
    {
        if(is_null($fhir)){
            $this->FHIRObservation = new FHIRObservation;
            return $this->FHIRObservation;
        }
        try{
            $this->FHIRObservation = new FHIRObservation($fhir);
            return $this->FHIRObservation;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * return fhir object
     */
    public function getFHIR()
    {
        return $this->FHIRObservation;
    }

    /*
    public function setOutcomeTypes($types)
    {
        $this->outcomeTypes=$types;
        return $this->outcomeTypes;
    }

    public function getOutcomeTypes()
    {
        return $this->outcomeTypes;
    }
    */




    /**
     * convert FHIRObservation to db array
     *
     * @param FHIRObservation
     *
     * @return array;
     */
    public function fhirToDb($FHIRObservation)
    {
        $dbCondition = array();

        $dbCondition['id']=$FHIRObservation->getId()->getValue();


        return $dbCondition;
    }

    /**
     * create FHIRObservation
     *
     * @param  string
     * @return FHIRObservation
     * @throws
     */


    public function parsedJsonToDb($parsedData)
    {
        $dbObservation = array();

        return $dbObservation;
    }

    public function validateDb($data){
        $flag =true;
        return $flag;
    }

    public function initFhirObject(){

        $FHIRObservation = new FHIRObservation();
        $FhirId = $this->createFHIRId(null);
        $FHIRObservation->setId($FhirId);

        $this->FHIRObservation=$FHIRObservation;
        return $FHIRObservation;

    }

    public function DBToFhir(...$params)
    {
        $observationDataFromDb = $params[0];
        $FHIRObservation =$this->FHIRObservation;
        $FHIRObservation->getId()->setValue($observationDataFromDb['id']);



        $this->FHIRObservation=$FHIRObservation;

        return $FHIRObservation;
    }

    public function parsedJsonToFHIR($data)

    {
        $FHIRObservation =$this->FHIRObservation;


        $this->FHIRObservation=$FHIRObservation;

        return $FHIRObservation;
    }

    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        //$FHIRAppointment = $this->parsedJsonToFHIR($data);
        $this->arrayToFhirObject($this->FHIRObservation,$data);
        $dBdata = $this->fhirToDb($this->FHIRObservation);
        return $dBdata;
    }

    public function updateDbData($data,$id)
    {
        $listsOpenEmrTable = $this->container->get(ListsOpenEmrTable::class);
        $flag=$this->validateDb($data);
        if($flag){
            $primaryKey='id';
            $primaryKeyValue=$id;
            unset($data[$primaryKey]);
            $rez=$listsOpenEmrTable->safeUpdate($data,array($primaryKey=>$primaryKeyValue));
            if(is_array($rez)){
                $this->initFhirObject();
                $patient=$this->DBToFhir($rez);
                return $patient;
            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','object is not valid');
        }
        //this never happens since ErrorCodes call to exit()
        return false;
    }


}








