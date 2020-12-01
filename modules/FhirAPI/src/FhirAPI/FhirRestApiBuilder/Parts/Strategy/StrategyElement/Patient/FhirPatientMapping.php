<?php
/**
 * Date: 21/01/20
 *  @author Eyal Wolanowski <eyalvo@matrix.co.il>
 * This class MAPPING FOR ORGANIZATION
 */

namespace FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\Patient;

use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\MappingData;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIRAddressTrait;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIROrganizationTelecomTrait;
use FhirAPI\Service\FhirBaseMapping;
use GenericTools\Model\ListsTable;
use GenericTools\Model\PatientsTable;
use Interop\Container\ContainerInterface;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\FHIRElementValidation;

/*include FHIR*/

use OpenEMR\FHIR\R4\FHIRDomainResource\FHIRPatient;
use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPoint;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;

use function DeepCopy\deep_copy;

class FhirPatientMapping extends FhirBaseMapping  implements MappingData
{

    const USER_IDS_LIST ='userlist3';

    private $adapter = null;
    private $container = null;
    private $FHIRPatient = null;
    private $idTypes= array();

    use FHIRAddressTrait;
    use FHIROrganizationTelecomTrait;
    use FHIRElementValidation;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->FHIRPatient = new FHIRPatient;

        $ListsTable = $this->container->get(ListsTable::class);
        $list = $ListsTable->getListNormalized(self::USER_IDS_LIST);
        $this->setIdTypes($list);
    }


    /**
     * set fhir object
     */
    public function setFHIR($fhir=null)
    {
        if(is_null($fhir)){
            $this->FHIRPatient = new FHIRPatient;
            return $this->FHIRPatient;
        }
        try{
            $this->FHIRPatient = new FHIRPatient($fhir);
            return $this->FHIRPatient;
        }catch(Exception $e){
            return false;
        }
    }



    /**
     * return fhir object
     */
    public function getFHIR()
    {
            return $this->FHIRPatient;
    }


    public function setIdTypes($types)
    {
        $this->idTypes=$types;
        return $this->idTypes;
    }


    public function getIdTypes()
    {
        return $this->idTypes;
    }




    /**
     * convert FHIRPatient to db array
     *
     * @param FHIRPatient
     *
     * @return array;
     */
    public function fhirToDb($patient)
    {
        $dbPatient = array();

        $dbPatient['pid'] = (is_null($patient->getId())) ? null : $patient->getId()->getValue();

        $pidElement = (is_null($patient->getIdentifier()[0])) ? null : $patient->getIdentifier()[0]->getValue();
        $dbPatient['ss'] = (is_null($pidElement)) ? null : $pidElement->getValue();

        try { $dbPatient['mh_type_id'] =$patient->getIdentifier()[0]->getType()->getCoding()[0]->getCode()->getValue();}
        catch(Exception $e){ $dbPatient['mh_type_id'] =null;}

        $dbPatient['sex'] = (is_null($patient->getGender())) ? null : ucfirst($patient->getGender()->getValue());

        $dbPatient['DOB'] = (is_null($patient->getBirthDate())) ? null : $patient->getBirthDate()->getValue();

        $deceasedDateTime=$patient->getDeceasedDateTime();
        $dbPatient['deceased_date'] = (is_null($deceasedDateTime)) ? null : substr($deceasedDateTime->getValue(),0,10);

        $patientName = $patient->getName()[0];
        $dbPatient['lname'] = (is_null($patientName->getFamily())) ? null : $patientName->getFamily()->getValue();
        $patientLName = (!is_array($patientName->getGiven())) ? null : $patientName->getGiven()[0];
        $patientFName = (!is_array($patientName->getGiven())) ? null : $patientName->getGiven()[1];
        $dbPatient['fname'] = (is_null($patientLName) ) ? null : $patientLName->getValue();

        $dbPatient['mname'] = (is_null($patientFName)) ? null : $patientFName->getValue();
        $dbPatient['mname'] =(is_null($dbPatient['mname'])) ? "" : $dbPatient['mname'];

        $mainAddress = (!is_array($patient->getAddress())) ? null : $patient->getAddress()[0];

        $country = (is_null($mainAddress)) ? null : $mainAddress->getCountry();
        $dbPatient['country_code'] = (is_null($country)) ? null : $country->getValue();
        $dbPatient['country_code'] =(is_null($dbPatient['country_code'])) ? "" : $dbPatient['country_code'];

        $city = (is_null($mainAddress)) ? null : $mainAddress->getCity();
        $dbPatient['city'] = (is_null($city)) ? null : $city->getValue();
        $dbPatient['city'] =(is_null($dbPatient['city'])) ? "" : $dbPatient['city'];

        $postalCode = (is_null($mainAddress)) ? null : $mainAddress->getPostalCode();
        $dbPatient['postal_code'] = (is_null($postalCode)) ? null : $postalCode->getValue();
        $dbPatient['postal_code'] =(is_null($dbPatient['postal_code'])) ? "" : $dbPatient['postal_code'];

        try{
            $managingOrganization=$patient->getManagingOrganization()->getReference()->getValue();
        }catch (Exception $e) {
            $managingOrganization=null;
        }
        $moId=(is_null($managingOrganization))? null : intval(explode("/",$managingOrganization)[1]);
        $dbPatient['mh_insurance_organiz'] = $moId;



        $line=$mainAddress->getLine();

        if(!empty($line)) {

            $addressTypeElm=$mainAddress->getType();
            if(!is_null($addressTypeElm)){
                if (is_string($addressTypeElm)) {
                    $addressType=$addressTypeElm;
                } else {
                    $addressType=$addressTypeElm->getValue();
                }
            }else{
                $addressType=null;
            }

            if ($addressType === "physical" || $addressType === "both") {

                $street=$line[0]->getValue();
                $dbPatient['street'] =(is_null($street)) ? "" : $street;

                $mh_house_no=$line[1]->getValue();
                $dbPatient['mh_house_no'] =(is_null($mh_house_no)) ? "" : $mh_house_no;

                if($addressType === "both"){
                    $mh_pobox=$line[2]->getValue();
                    $dbPatient['mh_pobox'] =(is_null($mh_pobox)) ? "" : $mh_pobox;
                }else{
                    $dbPatient['mh_pobox'] = null;
                }

            } elseif ($addressType === "postal") {
                $mh_pobox=$line[0]->getValue();
                $dbPatient['mh_pobox'] =(is_null($mh_pobox)) ? "" : $mh_pobox;
                $dbPatient['mh_house_no'] = null;
                $dbPatient['street'] = null;
            }
        }


        $telecom = $patient->getTelecom();
        $dbPatient=$this->setTelecom($telecom,$dbPatient);

        return $dbPatient;
    }

    public function setTelecom($telecom,$info)
    {
        $info['email'] = "";
        $info['phone_home'] = "";
        $info['phone_cell'] = "";

        if (!is_null($telecom) && is_array($telecom)) {

            foreach ($telecom as $index => $element) {
                if (!is_null($telecom)) {
                    $system = $element->getSystem();
                    $systemVal = (is_null($system)) ? null : $system->getValue();
                    $type = $element->getUse();
                    $typeVal = (is_null($type)) ? null : $type->getValue();
                    $valElm = $element->getValue();
                    $val = (is_null($valElm)) ? null : $valElm->getValue();
                    $val = (is_null($val)) ? "" : $val;

                    if ($systemVal === "phone" && $typeVal === "home") {
                        $info['phone_home'] = $val;
                        continue;
                    }
                    if ($systemVal === "phone" && $typeVal === "mobile") {
                        $info['phone_cell'] = $val;
                        continue;
                    }
                    if ($systemVal === "email") {
                        $info['email'] = $val;
                        continue;
                    }
                }
            }

        }

        return $info;
    }


    /**
     * create FHIRPatient
     *
     * @param  string
     * @return FHIRPatient
     * @throws
     */
    public function DBToFhir(...$params)
    {
        $patientDataFromDb = $params[0];
        $FHIRPatient =$this->FHIRPatient;

        $FhirId = $this->createFHIRId($patientDataFromDb['pid']);
        $FHIRPatient->setId($FhirId);

        $FhirPatientIdentifier = $this->createFHIRString($patientDataFromDb['ss']);
        $FHIRPatient->getIdentifier()[0]->setValue($FhirPatientIdentifier);
        $FHIRCode=$this->createFHIRCode($patientDataFromDb['mh_type_id']);
        $FHIRPatient->getIdentifier()[0]->getType()->getCoding()[0]->setCode($FHIRCode);

        $ids=$this->getIdTypes();
        $FHIRPatient->getIdentifier()[0]->getType()->setText($ids[$patientDataFromDb['mh_type_id']]);

        if (!is_null($patientDataFromDb['deceased_date']) && $patientDataFromDb['deceased_date']!=="0000-00-00 00:00:00" ) {
            $FHIRBoolean = $this->createFHIRBoolean(1);
            $FHIRPatient->setDeceasedBoolean($FHIRBoolean);
            $FHIRDateTime = $this->createFHIRDateTime($patientDataFromDb['deceased_date']);
            $FHIRPatient->setDeceasedDateTime($FHIRDateTime);
        } else {
            $FHIRBoolean = $this->createFHIRBoolean(0);
            $FHIRPatient->setDeceasedBoolean($FHIRBoolean);
            $FHIRDateTime = new FHIRDateTime;
            $FHIRPatient->setDeceasedDateTime($FHIRDateTime);
        }

        $FHIRPatient->setBirthDate($this->createFHIRDate($patientDataFromDb['DOB']));

        $mho=$patientDataFromDb['mh_insurance_organiz'];
        if(!is_null($mho)  &&  $mho!==""){
            $hnoLink='Organization/'.intval($mho);
            $FHIRPatient->getManagingOrganization()->getReference()->setValue($hnoLink);
        }

        //calls to setGender if valid gender is passed
        $this->setPatientGender($patientDataFromDb['sex']);

        $name=$FHIRPatient->getName()[0];
        $name->getFamily()->setValue($patientDataFromDb['lname']);
        $name->getGiven()[0]->setValue($patientDataFromDb['fname']);
        $name->getGiven()[1]->setValue($patientDataFromDb['mname']);

        $FHIRAddress=$this->createFHIRAddress($patientDataFromDb);
        $address=$FHIRPatient->getAddress()[0];

        $address->setCity($FHIRAddress[0]->getCity());
        $address->setUse($FHIRAddress[0]->getUse());
        $address->setText($FHIRAddress[0]->getText());
        $address->setPostalCode($FHIRAddress[0]->getPostalCode());
        $address->setDistrict($FHIRAddress[0]->getDistrict());
        $address->setState($FHIRAddress[0]->getState());
        $address->setCountry($FHIRAddress[0]->getCountry());
        $address->setType($FHIRAddress[0]->getType());

        $lines=$FHIRAddress[0]->getLine();
        $counter=0;

        foreach($lines as $index => $line){
                $tempLineVal=$line->getValue();
                if(!is_null($tempLineVal)){
                    $address->getLine()[$counter]->setValue($tempLineVal);
                    $counter++;
                }
        }
        if($counter===0){
            $address->getLine()[0]->setValue("  ");
        }

        $FHIRPatient=$this->setFHIRTelecom($patientDataFromDb,$FHIRPatient);

         $this->FHIRPatient=$FHIRPatient;

        return $FHIRPatient;
    }

    /**
     * set FHIRAdministrativeGender element
     * FHIRAdministrativeGender format is    male | female | other | unknown
     *
     * @param string
     * @param FHIRPatient
     *
     * @return bool;
     */
    public function setPatientGender($gender)
    {
        $FHIRGender = $this->createFHIRAdministrativeGender($gender);

        if (!is_null($FHIRGender)) {
            $this->FHIRPatient->setGender($FHIRGender);
            return true;
        } else {
            return false;
        }
    }

    /**
     * set FHIRAddress element
     *
     * @param array
     * @param array
     *
     * @return bool;
     */
    public function setFHIRAddress($patient)
    {
        $FHIRAddress = $this->createFHIRAddress($patient);

        if (!is_null($FHIRAddress)) {
            $this->FHIRPatient->addAddress($FHIRAddress);
            return true;
        } else {
            return false;
        }
    }

    /**
     * set FHIRAddress element
     *
     * @param array
     * @param array
     *
     * @return FHIRPatient;
     */
    public function setFHIRTelecom($patient,$FHIRPatient)
    {
        $info = array();

        if (!empty($patient['email'])) {
            $info['email'] = $patient['email'];
        }
        if (!empty($patient['phone_home'])) {
            $info['phone'] = $patient['phone_home'];
        }
        if (!empty($patient['phone_cell'])) {
            $info['mobile'] = $patient['phone_cell'];
        }

       // foreach ($info as $key => $value) {
            $contactPoint = $this->createFHIRTelecom($info);
            if (is_array($contactPoint)) {
                $telecomArr=$FHIRPatient->getTelecom();

                foreach ($contactPoint as $k =>$telecom){
                    $telecomArr[$k]->setSystem($telecom->getSystem());
                    $telecomArr[$k]->setValue($telecom->getValue());
                    $telecomArr[$k]->setUse($telecom->getUse());
                }


            }
            return $FHIRPatient;
    }

    public function parsedJsonToDb($parsedData)
    {
        $dbPatient = array();
        if($parsedData['resourceType']!=="Patient"){
            return $dbPatient;
        }

        $dbPatient['pid'] = (is_null($parsedData['id'])) ? null : ucfirst($parsedData['id']);
        $dbPatient['ss'] = (empty($parsedData['identifier'])) ? null :$parsedData['identifier'][0]['value'];
        $dbPatient['sex'] = (is_null($parsedData['gender'])) ? null : ucfirst($parsedData['gender']);
        $dbPatient['DOB'] = (is_null($parsedData['birthDate'])) ? null :$parsedData['birthDate'];
       $dbPatient['deceased_date'] = (is_null($parsedData['deceasedDateTime'])) ? null : substr($parsedData['deceasedDateTime'],0,10);

        $patientName = $parsedData['name'][0];
        $dbPatient['lname'] = (is_null($patientName['family'])) ? null : $patientName['family'];

        $dbPatient['fname'] = (is_null($patientName['given'][0])) ? null : $patientName['given'][0];
        unset($patientName['given'][0]);
        $dbPatient['mname'] = (empty($patientName['given'])) ? null : implode(" ",$patientName['given']);

        $mainAddress = $parsedData['address'][0];

        if(!empty($mainAddress['line'])) {
            $addressType = $mainAddress['type'];

            if ($addressType === "postal" || $addressType === "both") {
                $dbPatient['street'] =$mainAddress['line'][0];
                $dbPatient['mh_house_no'] =$mainAddress['line'][1];
                if($addressType === "both"){
                    $dbPatient['mh_pobox'] =$mainAddress['line'][2];
                }
            } elseif ($addressType === "physical") {
                $dbPatient['mh_pobox'] =$mainAddress['line'][0];
            }
        }
        $dbPatient['postal_code'] = (is_null($mainAddress['postalCode'])) ? null : $mainAddress['postalCode'];
        $dbPatient['city'] = (is_null($mainAddress['city'])) ? null : $mainAddress['city'];
        $dbPatient['country_code'] = (is_null($mainAddress['country'])) ? null : $mainAddress['country'];

        $telecom = $parsedData['telecom'];

        if (!is_null($telecom) && is_array($telecom)) {

            foreach ($telecom as $index => $element) {

                    $systemVal = $element['system'];
                    $typeVal = $element['use'];

                    if ($systemVal === "phone" && $typeVal === "home") {
                        $dbPatient['phone_home'] = $element['value'];
                        continue;
                    }
                    if ($systemVal === "phone" && $typeVal === "mobile") {
                        $dbPatient['phone_cell'] = $element['value'];
                        continue;
                    }
                    if ($systemVal === "email") {
                        $dbPatient['email'] = $element['value'];
                        continue;
                    }
            }

        } else {
            $dbPatient['email'] = null;
            $dbPatient['phone_home'] = null;
            $dbPatient['phone_cell'] = null;
        }


        return $dbPatient;
    }


    public function initFhirObject(){

        $FHIRPatient = new FHIRPatient();

        $FhirId = $this->createFHIRId(null);
        $FHIRPatient->setId($FhirId);
        $FhirPatientIdentifier = $this->createFHIRPid(null);

        $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>""));
        $FhirPatientIdentifier->setType($FHIRCodeableConcept);

        $FHIRPatient->addIdentifier($FhirPatientIdentifier);

        $FHIRBoolean = $this->createFHIRBoolean(null);
        $FHIRPatient->setDeceasedBoolean($FHIRBoolean);
        $FHIRDateTime = $this->createFHIRDateTime(null);
        $FHIRPatient->setDeceasedDateTime($FHIRDateTime);

        $FHIRPatient->setBirthDate($this->createFHIRDate(null));
        //calls to setGender if valid gender is passed

        $FHIRGender = $this->createFHIRAdministrativeGender(null);
        $FHIRPatient->setGender($FHIRGender);

        $FHIRHumanName = $this->createFHIRHumanName(null, null, null);
        $FHIRPatient->addName($FHIRHumanName);

        $FHIRAddress=$this->createFHIREmptyAddress();
        $FHIRPatient->addAddress($FHIRAddress);

        $telecom=$this->createFHIREmptyContactPoint();
        $FHIRPatient->addTelecom(deep_copy($telecom));
        $FHIRPatient->addTelecom(deep_copy($telecom));
        $FHIRPatient->addTelecom(deep_copy($telecom));

        $FHIRReference=$this->createFHIRReference(array("reference"=>null));
        $FHIRPatient->setManagingOrganization($FHIRReference);

        $this->FHIRPatient=$FHIRPatient;

        return $FHIRPatient;

    }

    public function parsedJsonToFHIR($data)

    {
        $FHIRPatient =$this->FHIRPatient;

        $FhirId = $this->createFHIRId($data['id']);
        $FHIRPatient->setId($FhirId);
        $FhirPatientIdentifier = $this->createFHIRString($data['identifier']['0']['value']);
        $FHIRPatient->getIdentifier()[0]->setValue($FhirPatientIdentifier);

        $idcodeVal=$data['identifier']['0']['type']['coding'][0]['code'];
        $FHIRCode=$this->createFHIRCode($idcodeVal);
        $FHIRPatient->getIdentifier()[0]->getType()->getCoding()[0]->setCode($FHIRCode);

        if (!is_null($data['deceasedDateTime'])) {
            $FHIRBoolean = $this->createFHIRBoolean(1);
            $FHIRPatient->setDeceasedBoolean($FHIRBoolean);
            $dateTime=$this->convertToDateTime($data['deceasedDateTime']);
            $FHIRDateTime = $this->createFHIRDateTime(null,null,$dateTime);
            $FHIRPatient->setDeceasedDateTime($FHIRDateTime);
        } else {
            $FHIRBoolean = $this->createFHIRBoolean(0);
            $FHIRPatient->setDeceasedBoolean($FHIRBoolean);
        }

        $FHIRPatient->setBirthDate($this->createFHIRDate($data['birthDate']));


        $hmoLink=(is_array($data['managingOrganization']))? $data['managingOrganization']['reference'] : null;
        if(!is_null($hmoLink)  &&  $hmoLink!==""){
            $FHIRPatient->getManagingOrganization()->getReference()->setValue($hmoLink);
        }


        //calls to setGender if valid gender is passed
        $this->setPatientGender($data['gender']);

        $nameArr= $data['name'][0];
        $nameRef=$FHIRPatient->getName()[0];
        $nameRef->getFamily()->setValue($nameArr['family']);
        $nameRef->getGiven()[0]->setValue($nameArr['given'][0]);
        $nameRef->getGiven()[1]->setValue($nameArr['given'][1]);

        $address=$data['address'][0];
        $addressRef=$FHIRPatient->getAddress()[0];

        $addressRef->getCity()->setValue($address['city']);
        $addressRef->getPostalCode()->setValue($address['postalCode']);
        $addressRef->getCountry()->setValue($address['country']);

        foreach($address['line'] as $i => $info){
            $addressRef->getLine()[$i]->setValue($info);
        }

        $addressRef->getType()->setValue($address['type']);

        $telecom=$data['telecom'];

        if (is_array($telecom)){
            foreach($telecom as $j =>$telData){

                $FHIRPatient->getTelecom()[$j]->getSystem()->setValue($telData['system']);
                $FHIRPatient->getTelecom()[$j]->getValue()->setValue($telData['value']);
                $FHIRPatient->getTelecom()[$j]->getUse()->setValue($telData['use']);
            }
        }

        $this->FHIRPatient=$FHIRPatient;

        return $FHIRPatient;
    }


    public function getDbDataFromRequest($data)
    {
        $this->initFhirObject();
        $FHIRAppointment = $this->parsedJsonToFHIR($data);
        $dBdata = $this->fhirToDb($FHIRAppointment);
        return $dBdata;
    }


    public function updateDbData($data,$id)
    {
        $patientTable = $this->container->get(PatientsTable::class);


        /*********************************** validate *******************************/
        $patientDataFromDb = $patientTable->buildGenericSelect(["id"=>$id]);
        $alldata=array('new'=>$data,'old'=>$patientDataFromDb);
        //$mainTable=$patientTable->getTableName();
        $isValid=$this->validateDb($alldata,null);
        /***************************************************************************/
        if($isValid){
            $primaryKey='pid';
            $primaryKeyValue=$id;
            unset($data[$primaryKey]);
            $rez=$patientTable->safeUpdate($data,array($primaryKey=>$primaryKeyValue));
            if(is_array($rez)){
                $this->initFhirObject();
                $patient=$this->DBToFhir($rez);
                return $patient;
            }else{ //insert failed
                ErrorCodes::http_response_code('500','insert object failed :'.$rez);
            }
        }else{ // object is not valid
            ErrorCodes::http_response_code('406','failed validation');
        }
        //this never happens since ErrorCodes call to exit()
        return false;
    }

}





