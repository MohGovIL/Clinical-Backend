<?php
/**
 * Date: 05/01/20
 * @author  Eyal Wolanowski <eyal.wolanowski@gmail.com>
 * This class Fhir base elements
 */

namespace FhirAPI\Service;

use DateTime;
use Exception;
use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use Interop\Container\ContainerInterface;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAnnotation;
use OpenEMR\FHIR\R4\FHIRElement\FHIRId;
use OpenEMR\FHIR\R4\FHIRElement\FHIRBoolean;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDateTime;
use OpenEMR\FHIR\R4\FHIRElement\FHIRInstant;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDate;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAdministrativeGender;
use OpenEMR\FHIR\R4\FHIRElement\FHIRHumanName;
use OpenEMR\FHIR\R4\FHIRElement\FHIRIssueSeverity;
use OpenEMR\FHIR\R4\FHIRElement\FHIRIssueType;
use OpenEMR\FHIR\R4\FHIRElement\FHIRNarrative;
use OpenEMR\FHIR\R4\FHIRElement\FHIRNarrativeStatus;
use OpenEMR\FHIR\R4\FHIRElement\FHIRString;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAddress;
use OpenEMR\FHIR\R4\FHIRElement\FHIRCode;
use OpenEMR\FHIR\R4\FHIRElement\FHIRCoding;
use OpenEMR\FHIR\R4\FHIRElement\FHIRCodeableConcept;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAddressType;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAddressUse;
use OpenEMR\FHIR\R4\FHIRElement\FHIRIdentifier;
use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPoint;
use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPointSystem;
use OpenEMR\FHIR\R4\FHIRElement\FHIRContactPointUse;
use OpenEMR\FHIR\R4\FHIRElement\FHIRPositiveInt;
use OpenEMR\FHIR\R4\FHIRElement\FHIRUri;
use OpenEMR\FHIR\R4\FHIRElement\FHIRReference;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle\FHIRBundleEntry;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle\FHIRBundleSearch;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle\FHIRBundleResponse;
use OpenEMR\FHIR\R4\FHIRElement\FHIRSearchEntryMode;
use OpenEMR\FHIR\R4\FHIRElement\FHIRBundleType;
use OpenEMR\FHIR\R4\FHIRResource\FHIRCondition\FHIRConditionEvidence;
use OpenEMR\FHIR\R4\FHIRResource\FHIRCondition\FHIRConditionStage;
use OpenEMR\FHIR\R4\FHIRResourceContainer;
use OpenEMR\FHIR\R4\FHIRElement\FHIRUnsignedInt;
use OpenEMR\FHIR\R4\FHIRDomainResource\FHIROperationOutcome;
use OpenEMR\FHIR\R4\FHIRResource\FHIROperationOutcome\FHIROperationOutcomeIssue;
use OpenEMR\FHIR\R4\FHIRElement\FHIRMarkdown;
use OpenEMR\FHIR\R4\FHIRElement\FHIRDaysOfWeek;
use OpenEMR\FHIR\R4\FHIRElement\FHIRTime;
use OpenEMR\FHIR\R4\FHIRElement\FHIRPeriod;
use OpenEMR\FHIR\R4\FHIRElement\FHIRAttachment;
use OpenEMR\FHIR\R4\FHIRElement\FHIRBase64Binary;
use OpenEMR\FHIR\R4\FHIRElement\FHIRUrl;
use OpenEMR\FHIR\R4\FHIRResource\FHIREncounter\FHIREncounterParticipant;


use function DeepCopy\deep_copy;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\Traits\ConversionsTrait;
use FhirAPI\Service\FhirValidateTypes;


class FhirBaseMapping
{
    private $adapter = null;
    private $container = null;
    private $fhirRequestParamsHandler = null;

    use ConversionsTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        try{
            $this->adapter = $container->get('Laminas\Db\Adapter\Adapter');
            $this->fhirRequestParamsHandler = $container->get(FhirValidateTypes::class);
        }catch(Exception $e){

        }


    }

    /**
     * create FHIRId element
     *
     * @param integer
     * @return FHIRId
     */
    public function createFHIRId($id)
    {
        $this->fhirRequestParamsHandler::checkByPreg($id, 'id', 'ALLOW_NULL_ERROR');
        $FhirId = new FHIRId;
        $FhirId->setValue($id);
        return $FhirId;
    }

    /**
     * create FHIRDate element
     * FHIRDateTime format of date is YYYY, YYYY-MM, YYYY-MM-DD
     *
     * @param string
     *
     * @return FHIRDate
     */
    public function createFHIRDate($date)
    {
        $this->fhirRequestParamsHandler::checkByPreg($date, 'date', 'ALLOW_NULL_ERROR');
        $FHIRDate = new FHIRDate;
        $FHIRDate->setValue($date);

        return $FHIRDate;
    }

    /**
     * create FHIRDateTime element
     * FHIRDateTime format of date is YYYY, YYYY-MM, YYYY-MM-DD
     * FHIRDateTime format of time is Thh:mm:ss+zz:zz
     *
     * @param string
     * @param string
     * @param string
     *
     * @return FHIRDateTime
     */
    public function createFHIRDateTime($date, $time = null, $dateTime = null)
    {
        $FHIRDateTime = new FHIRDateTime;

        if (is_null($date) && is_null($dateTime)) {

            $FHIRDateTime->setValue(null);
            $this->fhirRequestParamsHandler::checkByPreg($FHIRDateTime->getValue(), 'dateTime', 'ALLOW_NULL_ERROR');
            return $FHIRDateTime;
        }

        if (!is_null($dateTime)) {
            $validDate = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
            if ($validDate) {
                $dateTime = str_replace(" ", "T", $dateTime) . ".000Z";
                $FHIRDateTime->setValue($dateTime);
            } else {
                return null;
            }
        } else {
            if (is_null($time)) {
                $FHIRDateTime->setValue($date . "T" . date('H:i:s') . ".000Z");
            } else {
                $FHIRDateTime->setValue($date . "T" . $time . ".000Z");
            }
        }

        // this must be done at the end since params are converted to fhir
        $this->fhirRequestParamsHandler::checkByPreg($FHIRDateTime->getValue(), 'dateTime', 'ALLOW_NULL_ERROR');

        return $FHIRDateTime;
    }

    /**
     * create FHIRInstant element
     * FHIRDateTime format of date is YYYY, YYYY-MM, YYYY-MM-DD
     * FHIRDateTime format of time is Thh:mm:ss+zz:zz
     *
     * @param string
     * @param string
     * @param string
     *
     * @return FHIRInstant | null
     */
    public function createFHIRInstant($date, $time, $dateTime = null)
    {
        $FHIRInstant = new FHIRInstant;

        if (is_null($date) && is_null($dateTime)) {

            $FHIRInstant->setValue(null);
            $this->fhirRequestParamsHandler::checkByPreg($FHIRInstant->getValue(), 'instant', 'ALLOW_NULL_ERROR');
            return $FHIRInstant;
        }

        if (!is_null($dateTime)) {
            $validDate = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
            if ($validDate) {
                $dateTime = str_replace(" ", "T", $dateTime) . ".000Z";
                $FHIRInstant->setValue($dateTime);
            } else {
                return null;
            }
        } else {
            if (is_null($time)) {
                $FHIRInstant->setValue($date . "T" . date('H:i:s') . ".000Z");
            } else {
                $FHIRInstant->setValue($date . "T" . $time . ".000Z");
            }
        }

        // this must be done at the end since params are converted to fhir
        $this->fhirRequestParamsHandler::checkByPreg($FHIRInstant->getValue(), 'instant', 'ALLOW_NULL_ERROR');

        return $FHIRInstant;
    }

    /**
     * create FHIRAdministrativeGender element
     * FHIRAdministrativeGender format is    male | female | other | unknown
     *
     * @param string
     *
     * @return FHIRAdministrativeGender | null
     */
    public function createFHIRAdministrativeGender($gender)
    {
        if(is_null($gender)){
            return new FHIRAdministrativeGender;
        }

        $FHIRAdministrativeGender = new FHIRAdministrativeGender;
        $FHIRGender = null;
        $gender = strtolower($gender);

        switch ($gender) {
            case 'f':
            case 'female':
                $FHIRGender = 'female';
                break;
            case 'm':
            case 'male':
                $FHIRGender = 'male';
                break;
            case 'unknown':
                $FHIRGender = 'unknown';
                break;
            case 'other':
                $FHIRGender = 'other';
                break;
            default:
                $FHIRGender = null;
        }

        $FHIRAdministrativeGender->setValue($FHIRGender);


        // this must be done at the end since params are converted to fhir
        $this->fhirRequestParamsHandler::checkByPreg($FHIRAdministrativeGender->getValue(), 'code', 'ALLOW_NULL_ERROR');

        return $FHIRAdministrativeGender;

    }

    /**
     * create FHIRHumanName element
     *
     * @param string | null
     * @param string | null
     * @param string | null
     *
     * @return FHIRHumanName
     */
    public function createFHIRHumanName($fName, $lName, $mName = null)
    {
        $this->fhirRequestParamsHandler::checkByPreg($fName, 'string', 'ALLOW_NULL_ERROR');
        $this->fhirRequestParamsHandler::checkByPreg($lName, 'string', 'ALLOW_NULL_ERROR');
        $this->fhirRequestParamsHandler::checkByPreg($mName, 'string', 'ALLOW_NULL_ERROR');

        $FHIRHumanName = new FHIRHumanName;

        if(is_null($fName) && is_null($lName) && is_null($mName)){
            $FHIRHumanName->addGiven(new FHIRString());
            $FHIRHumanName->addGiven(new FHIRString());
            $FHIRHumanName->setFamily(new FHIRString());
            return $FHIRHumanName;
        }


        if (!empty($fName)) {
            $fNameString = new FHIRString();
            $fNameString->setValue($fName);
            $FHIRHumanName->addGiven($fNameString);
        }
        if (!empty($mName)) {
            $mNametring = new FHIRString();
            $mNametring->setValue($mName);
            $FHIRHumanName->addGiven($mNametring);
        }

        if (!empty($lName)) {
            $lNameString = new FHIRString();
            $lNameString->setValue($lName);
            $FHIRHumanName->setFamily($lNameString);
        }

        return $FHIRHumanName;

    }

    /**
     * create FHIRIdentifier to represent pid
     *
     * @param string
     *
     * @return FHIRIdentifier | null
     */
    public function createFHIRPid($id)
    {

        $this->fhirRequestParamsHandler::checkByPreg($id, 'string', 'ALLOW_NULL_ERROR');

        if (empty($id)) {
            return new FHIRIdentifier;
        }
        $FHIRIdentifier = new FHIRIdentifier;
        $id = preg_replace('/[^0-9]/', '', $id);
        $FHIRIdString = new FHIRString();
        $FHIRIdString->setValue($id);
        $FHIRIdentifier->setValue($FHIRIdString);

        return $FHIRIdentifier;

    }

    /**
     * create createFHIRPositiveInt
     *
     * @param string
     *
     * @return FHIRPositiveInt | null
     */
    public function createFHIRPositiveInt($val)
    {

        $this->fhirRequestParamsHandler::checkByPreg($val, 'positiveInt', 'ALLOW_NULL_ERROR');

        if (is_null($val)) {
            return new FHIRPositiveInt;
        }

        $val = preg_replace('/[^0-9]/', '', $val);

        if (empty($val)) {
            return null;
        }

        $FHIRIdentifier = new FHIRPositiveInt;
        $FHIRIdString = new FHIRString();
        $FHIRIdString->setValue($val);
        $FHIRIdentifier->setValue($FHIRIdString);

        return $FHIRIdentifier;

    }

    /**
     * create FHIRCodeableConcept
     *
     * @param array
     *
     * @return FHIRCodeableConcept | null
     */
    public function createFHIRCodeableConcept(array $codeArr)
    {
        $FHIRCodeableConcept = new FHIRCodeableConcept();

        $FHIRCoding = $this->createFHIRCoding($codeArr);

        if (key_exists('display', $codeArr)) {
            $FHIRString = $this->createFHIRString($codeArr['display']);
            $FHIRCoding->setDisplay($FHIRString);
        }else{
            $FHIRString = $this->createFHIRString(null);
            $FHIRCoding->setDisplay($FHIRString);
        }

        if (key_exists('system', $codeArr)) {
            $FHIRUri = $this->createFHIRUri($codeArr['system']);
            $FHIRCoding->setSystem($FHIRUri);
        }else{
            $FHIRUri = $this->createFHIRUri(null);
            $FHIRCoding->setSystem($FHIRUri);
        }

        $FHIRCodeableConcept->addCoding($FHIRCoding);

        if (key_exists('text', $codeArr)) {
            $FHIRString = $this->createFHIRString($codeArr['text']);
            $FHIRCodeableConcept->setText($FHIRString);
        }else{
            $FHIRString = $this->createFHIRString(null);
            $FHIRCodeableConcept->setText($FHIRString);
        }


        return $FHIRCodeableConcept;
    }

    /**
     * create FHIRCoding
     *
     * @param array
     *
     * @return FHIRCoding | null
     */
    public function createFHIRCoding(array $codeArr)
    {
        if (empty($codeArr) || !(key_exists('code', $codeArr))) {
            $codeArr['code']= null;
        }

        $FHIRCoding = new FHIRCoding();
        $FHIRCode = $this->createFHIRCode($codeArr['code']);
        $FHIRCoding->setCode($FHIRCode);

        if (key_exists('system', $codeArr) && !is_null($codeArr['system'])) {
            $FHIRUri = $this->createFHIRUri($codeArr['system']);
            $FHIRCoding->setSystem($FHIRUri);
        }else{
            $FHIRUri = $this->createFHIRUri(null);
            $FHIRCoding->setSystem($FHIRUri);
        }

        if (key_exists('version', $codeArr) && !is_null($codeArr['version'])) {
            $FHIRString = $this->createFHIRString($codeArr['version']);
            $FHIRCoding->setVersion($FHIRString);
        }else{
            $FHIRString = $this->createFHIRString(null);
            $FHIRCoding->setVersion($FHIRString);
        }

        if (key_exists('display', $codeArr) && !is_null($codeArr['display'])) {
            $FHIRString = $this->createFHIRString($codeArr['display']);
            $FHIRCoding->setDisplay($FHIRString);
        }else{
            $FHIRString = $this->createFHIRString(null);
            $FHIRCoding->setDisplay($FHIRString);
        }

        if (key_exists('userSelected', $codeArr) && !is_null($codeArr['userSelected'])) {
            $FHIRBoolean = $this->createFHIRBoolean($codeArr['userSelected']);
            $FHIRCoding->setUserSelected($FHIRBoolean);
        }else{
            $FHIRBoolean = $this->createFHIRBoolean(null);
            $FHIRCoding->setUserSelected($FHIRBoolean);
        }

        return $FHIRCoding;
    }

    /**
     * create FHIRCode
     *
     * @param string
     *
     * @return FHIRCode | null
     */
    public function createFHIRCode($code)
    {

        $this->fhirRequestParamsHandler::checkByPreg($code, 'code', 'ALLOW_NULL_ERROR');

        if (empty($code)) {
            return new FHIRCode();
        }

        $FHIRCode = new FHIRCode();
        $FHIRCode->setValue($code);

        return $FHIRCode;
    }

    /**
     * create FHIRUri
     *
     * @param string
     *
     * @return FHIRUri | null
     */
    public function createFHIRUri($uri)
    {
        $this->fhirRequestParamsHandler::checkByPreg($uri, 'uri', 'ALLOW_NULL_ERROR');

        $FHIRUri = new FHIRUri();
        $FHIRUri->setValue($uri);

        return $FHIRUri;
    }

    /**
     * create FHIRString
     *
     * @param string
     *
     * @return FHIRString | null
     */
    public function createFHIRString($string)
    {
        //toDo : need to think if we need this line notice checkByPreg falls on empty string
        if ($string==="") $string=null;

        $this->fhirRequestParamsHandler::checkByPreg($string, 'string', 'ALLOW_NULL_ERROR');

        $FHIRIdString = new FHIRString();
        $FHIRIdString->setValue($string);

        return $FHIRIdString;
    }

    /**
     * create FHIRBoolean element
     *
     * @param integer
     * @return FHIRBoolean
     */
    public function createFHIRBoolean($bool)
    {

        $FHIRBoolean = new FHIRBoolean;

        if(!is_null($bool)){
            $bool= ($bool && $bool!='false') ? "true" : "false";
        }

        $this->fhirRequestParamsHandler::checkByPreg($bool, 'boolean', 'ALLOW_NULL_ERROR');

        $FHIRBoolean->setValue($bool);
        return $FHIRBoolean;
    }

    /**
     * create FHIRReference
     *
     * @param array
     *
     * @return FHIRReference | null
     */
    public function createFHIRReference($referenceArr)
    {
        if (empty($referenceArr) || !(key_exists('reference', $referenceArr))) {
            $referenceArr['reference']= null;
        }

        $FHIRReference = new FHIRReference();
        $FHIRString = $this->createFHIRString($referenceArr['reference']);
        $FHIRReference->setReference($FHIRString);


        if (key_exists('display', $referenceArr)) {
            $FHIRString = $this->createFHIRString($referenceArr['display']);
            $FHIRReference->setDisplay($FHIRString);
        }

        if (key_exists('identifier', $referenceArr)) {
            $FHIRIdentifier = $this->createFHIRIdentifier($referenceArr['identifier']);
            $FHIRReference->setIdentifier($FHIRIdentifier);
        }

        if (key_exists('type', $referenceArr)) {
            $FHIRUri = $this->createFHIRUri($referenceArr['type']);
            $FHIRReference->setType($FHIRUri);
        }

        return $FHIRReference;
    }

    /**
     * create FHIRIdentifier
     *
     * @param string
     *
     * @return FHIRIdentifier | null
     */
    public function createFHIRIdentifier($id)
    {
        $this->fhirRequestParamsHandler::checkByPreg($id, 'string', 'ALLOW_NULL_ERROR');

        $FHIRIdentifier = new FHIRIdentifier;
        $FHIRIdString = new FHIRString();
        $FHIRIdString->setValue($id);
        $FHIRIdentifier->setValue($FHIRIdString);

        return $FHIRIdentifier;
    }

    /**
     * Add resource to bundle only for Search
     *
     * @param FHIRBundle
     * @param FHIRResourceContainer
     * @param string  match | include | outcome
     *
     * @return FHIRBundle | null
     */
    public function addResourceToBundle(FHIRBundle $FHIRBundle,FHIRResourceContainer $resource, $mode=null,$needToCount=false)
    {

        $FHIRBundleEntry=new FHIRBundleEntry;
        $FHIRBundleSearch= new FHIRBundleSearch;
        $FHIRSearchEntryMode= new FHIRSearchEntryMode;

        $this->fhirRequestParamsHandler::checkByPreg($mode, 'code', 'ALLOW_NULL_ERROR');
        $FHIRSearchEntryMode->setValue($mode);

        $FHIRBundleSearch->setMode($FHIRSearchEntryMode);

        //toDo add link
        //$FHIRBundleEntry->addLink()

        $FHIRBundleEntry->setSearch($FHIRBundleSearch);
        $FHIRBundleEntry->setResource($resource);

        if($mode==='match' || $needToCount){
            $total=$FHIRBundle->getTotal()->getValue();
            $total=($total===null || $total===0) ? 1 : $total+1;
            $intValue = new FHIRUnsignedInt();
            $intValue->setValue($total);
            $FHIRBundle->setTotal($intValue);
        }


        $FHIRBundle->addEntry($FHIRBundleEntry);

        return $FHIRBundle;
    }

    /**
     * create FHIRBundle and init search params
     *
     *
     * @return FHIRBundle | null
     */
    public function createSearchBundle()
    {
        $bundle= $this->createHttpOkBundle('searchset');
        $FHIRUnsignedInt= new FHIRUnsignedInt;
        $FHIRUnsignedInt->setValue(0);
        $bundle->setTotal($FHIRUnsignedInt);
        return $bundle;
    }

    /**
     * create FHIRBundle and init search params
     *
     * @param string
     * @return FHIRBundle | null
     */
    public function createHttpOkBundle($type=null)
    {
        $FHIRBundle = new FHIRBundle;
        $FHIRBundleType= new FHIRBundleType;

        $this->fhirRequestParamsHandler::checkByPreg($type, 'code', 'ALLOW_NULL_ERROR');
        $FHIRBundleType->setValue($type);

        $FHIRBundle->setType($FHIRBundleType);
        $FHIRInstant= new FHIRInstant;
        $FHIRInstant->setValue($this->createFHIRInstant(null,null,date('Y-m-d H:i:s')));
        $FHIRBundle->setTimestamp($FHIRInstant);
        $FHIRBundleResponse=new FHIRBundleResponse;
        $FHIRBundleEntry=new FHIRBundleEntry;
        $FHIRString= new FHIRString;
        $FHIRString->setValue('200');
        $FHIRBundleResponse->setStatus($FHIRString);
        $FHIROperationOutcome=new FHIROperationOutcome;
        $FHIRResourceContainer = new FHIRResourceContainer($FHIROperationOutcome);
        $FHIRBundleResponse->setOutcome($FHIRResourceContainer);
        $FHIRBundleEntry->setResponse($FHIRBundleResponse);
        $FHIRBundle->addEntry($FHIRBundleEntry);

        return $FHIRBundle;
    }

    /**
     * create error bundle
     *
     * @param FHIRBundle
     * @param FHIRResourceContainer
     * @param array
     * @param string
     *
     * @return FHIRBundle | null
     */
    public function createNotValidErrorBundle($code='406',$msg='Not Acceptable: data is not valid')
    {
        $FHIRBundle = new FHIRBundle;
        $FHIRBundleEntry=new FHIRBundleEntry;
        $FHIRBundleSearch= new FHIRBundleSearch;
        $FHIRSearchEntryMode= new FHIRSearchEntryMode;
        $FHIRSearchEntryMode->setValue('outcome');
        $FHIRBundleSearch->setMode($FHIRSearchEntryMode);
        $FHIRBundleEntry->setSearch($FHIRBundleSearch);
        $FHIRUnsignedInt= new FHIRUnsignedInt;
        $FHIRUnsignedInt->setValue(0);
        $FHIRBundle->setTotal($FHIRUnsignedInt);
        $FHIRBundleResponse=new FHIRBundleResponse;
        $FHIRString= new FHIRString;
        $FHIRString->setValue($code);
        $FHIRBundleResponse->setStatus($FHIRString);
        $FHIROperationOutcome=new FHIROperationOutcome;
        $FHIRResourceContainer = new FHIRResourceContainer($FHIROperationOutcome);
        $FHIROperationOutcomeIssue=new FHIROperationOutcomeIssue;

        $errorDetails=array('code'=>$code,'text'=>$msg);
        $error=$this->createFHIRCodeableConcept($errorDetails);
        $FHIROperationOutcomeIssue->setDetails($error);
        $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);

        $FHIRBundleResponse->setOutcome($FHIRResourceContainer);
        $FHIRBundleEntry->setResponse($FHIRBundleResponse);
        $FHIRBundle->addEntry($FHIRBundleEntry);

        return $FHIRBundle;
    }

    /**
     * create error bundle
     *
     * @param FHIRBundle
     * @param FHIRResourceContainer
     * @param array
     * @param string
     *
     * @return FHIRBundle | null
     */
    public function createErrorBundle(FHIRBundle $FHIRBundle,$params,$custom=array(),$code='406')
    {
        $FHIRBundleEntry=new FHIRBundleEntry;
        $FHIRBundleSearch= new FHIRBundleSearch;
        $FHIRSearchEntryMode= new FHIRSearchEntryMode;
        $FHIRSearchEntryMode->setValue('outcome');
        $FHIRBundleSearch->setMode($FHIRSearchEntryMode);
        $FHIRBundleEntry->setSearch($FHIRBundleSearch);
        $FHIRUnsignedInt= new FHIRUnsignedInt;
        $FHIRUnsignedInt->setValue(0);
        $FHIRBundle->setTotal($FHIRUnsignedInt);


        $FHIRBundleResponse=new FHIRBundleResponse;
        $FHIRString= new FHIRString;

        $this->fhirRequestParamsHandler::checkByPreg($code, 'string', 'ALLOW_NULL_ERROR');
        $FHIRString->setValue($code);
        $FHIRBundleResponse->setStatus($FHIRString);
        $FHIROperationOutcome=new FHIROperationOutcome;
        $FHIRResourceContainer = new FHIRResourceContainer($FHIROperationOutcome);

        $FHIROperationOutcomeIssue=new FHIROperationOutcomeIssue;

        foreach( $params['ARGUMENTS'] as $type => $data){

            $text=$type." "."is not supported by this server";
            $errorDetails=array('code'=>'406','text'=>$text);
            $error=$this->createFHIRCodeableConcept($errorDetails);
            $FHIROperationOutcomeIssue->setDetails($error);
            $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);
        }

        foreach( $params['PARAMETERS_FOR_SEARCH_RESULT'] as $type => $data){

            $text=$type." "."is not supported by this server";
            $errorDetails=array('code'=>'406','text'=>$text);
            $error=$this->createFHIRCodeableConcept($errorDetails);
            $FHIROperationOutcomeIssue->setDetails($error);
            $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);
        }

        foreach( $params['PARAMETERS_FOR_ALL_RESOURCES'] as $type => $data){

            $text=$type." "."is not supported by this server";
            $errorDetails=array('code'=>'406','text'=>$text);
            $error=$this->createFHIRCodeableConcept($errorDetails);
            $FHIROperationOutcomeIssue->setDetails($error);
            $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);
        }

        if(!empty($custom)){
            foreach($custom as $index => $data){
                $text=$data['text'];
                $code=$data['code'];
                $errorDetails=array('code'=>$code,'text'=>$text);
                $error=$this->createFHIRCodeableConcept($errorDetails);
                $FHIROperationOutcomeIssue->setDetails($error);
                $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);
            }
        }

        $FHIRBundleResponse->setOutcome($FHIRResourceContainer);
        $FHIRBundleEntry->setResponse($FHIRBundleResponse);
        $FHIRBundle->addEntry($FHIRBundleEntry);


        return $FHIRBundle;
    }

    /**
     * Create FHIRMarkdown
     *
     * @param string
     *
     * @return FHIRMarkdown | null
     */
    public function createFHIRMarkdown($md)
    {

        $this->fhirRequestParamsHandler::checkByPreg($md, 'markdown', 'ALLOW_NULL_ERROR');
        $FHIRMarkdown = new FHIRMarkdown;
        $FHIRMarkdown->setValue($md);

        return $FHIRMarkdown;
    }

    /**
     * Create FHIRDaysOfWeek
     *
     * @param $day
     * @return FHIRDaysOfWeek|null
     */
    public function createFHIRDaysOfWeek($day)
    {
        $this->fhirRequestParamsHandler::checkByPreg($day, 'DaysOfWeek', 'ALLOW_NULL_ERROR');

        $FHIRDaysOfWeek = new FHIRDaysOfWeek;
        $FHIRDaysOfWeek->setValue($day);

        return $FHIRDaysOfWeek;
    }

    /**
     * Create FHIRTime
     *
     * @param $time
     * @return FHIRTime|null
     */
    public function createFHIRTime($time)
    {

        $this->fhirRequestParamsHandler::checkByPreg($time, 'time', 'ALLOW_NULL_ERROR');
        $FHIRTime = new FHIRTime;
        $FHIRTime->setValue($time);

        return $FHIRTime;
    }

    /**
     * Create FHIRPeriod
     *
     * @param $period
     * @return FHIRPeriod|null
     */
    public function createFHIRPeriod($period)
    {
        $FHIRPeriod = new FHIRPeriod;

        if (empty($period) || !is_array($period)) {

            $FHIRPeriod->setStart($this->createFHIRDateTime(null));
            $FHIRPeriod->setEnd($this->createFHIRDateTime(null));
            return $FHIRPeriod;
        }

        $FHIRDateTime = $this->createFHIRDateTime($period['start']);
        $FHIRPeriod->setStart($FHIRDateTime);

        $FHIRDateTime = $this->createFHIRDateTime($period['end']);
        $FHIRPeriod->setEnd($FHIRDateTime);

        return $FHIRPeriod;
    }

    /**
     * create FHIRUnsignedInt
     *
     * @param string
     *
     * @return FHIRUnsignedInt | null
     */
    public function createFHIRUnsignedInt($val)
    {

        $this->fhirRequestParamsHandler::checkByPreg($val, 'unsignedInt', 'ALLOW_NULL_ERROR');
        $FHIRUnsignedInt = new FHIRUnsignedInt;
        $FHIRUnsignedInt->setValue($val);

        return $FHIRUnsignedInt;

    }

    /**
     * Create FHIRBase64Binary
     *
     * @param $data
     * @return FHIRBase64Binary
     */
    public function createFHIRBase64Binary($data)
    {
        $this->fhirRequestParamsHandler::checkByPreg($data, 'base64Binary', 'ALLOW_NULL_ERROR');
        $FHIRBase64Binary = new FHIRBase64Binary();
        $FHIRBase64Binary->setValue($data);
        return $FHIRBase64Binary;
    }

    /**
     * Create FHIRUrl
     *
     * @param $url
     * @return FHIRUrl
     */
    public function createFHIRUrl($url)
    {
        $this->fhirRequestParamsHandler::checkByPreg($url, 'url', 'ALLOW_NULL_ERROR');
        $FHIRUrl = new FHIRUrl();
        $FHIRUrl->setValue($url);
        return $FHIRUrl;
    }

    /**
     * Create FHIRAttachment
     *
     * @param array
     *
     * @return FHIRAttachment
     */
    public function createFHIRAttachment($arr)
    {
        $FHIRAttachment = new FHIRAttachment();

        if (key_exists('contentType', $arr)) {
            $code = $this->createFHIRCode($arr['contentType']);
            $FHIRAttachment->setContentType($code);

            //If there is data there must be a contentType according to FHIR
            if (key_exists('data', $arr)) {
                $binary = $this->createFHIRBase64Binary($arr['data']);
                $FHIRAttachment->setData($binary);
            }
        }

        if (key_exists('url', $arr)) {
            $FHIRUrl = $this->createFHIRUrl($arr['url']);
            $FHIRAttachment->setUrl($FHIRUrl);
        }

        return $FHIRAttachment;
    }

    /**
     * create FHIRContactPoint
     *
     * @return FHIRContactPoint
     */
    public function createFHIREmptyContactPoint()
    {
        $FHIRContactPoint =  new FHIRContactPoint();
        $FHIRContactPointUse= new FHIRContactPointUse();
        $FHIRContactPointUse->setValue(null);
        $FHIRContactPoint->setUse($FHIRContactPointUse);
        $FHIRString=$this->createFHIRString(null);
        $FHIRContactPoint->setValue($FHIRString);
        $FHIRContactPointSystem=new FHIRContactPointSystem;
        $FHIRContactPointSystem->setValue(null);
        $FHIRContactPoint->setSystem($FHIRContactPointSystem);

        return $FHIRContactPoint;

    }

    /**
     * create FHIRAddress
     *
     * @return FHIRAddress
     */
    public function createFHIREmptyAddress()
    {
        $FHIRAddress=new FHIRAddress();
        $FHIRString=$this->createFHIRString(null);

        $FHIRAddress->setCity(deep_copy($FHIRString));

        $FHIRAddressUse = new FHIRAddressUse;
        $FHIRAddressUse->setValue(null);
        $FHIRAddress->setUse($FHIRAddressUse);

        //$FHIRAddress->setText(deep_copy($FHIRString));
        $FHIRAddressType = new FHIRAddressType;
        $FHIRAddressType->setValue(null);
        $FHIRAddress->setType($FHIRAddressType);

        $FHIRAddress->setPostalCode(deep_copy($FHIRString));
        //$FHIRAddress->setDistrict(deep_copy($FHIRString));
        //$FHIRAddress->setState(deep_copy($FHIRString));
        $FHIRAddress->setCountry(deep_copy($FHIRString));

        $FHIRString=$this->createFHIRString(null);
        $FHIRAddress->addLine(deep_copy($FHIRString));
        $FHIRAddress->addLine(deep_copy($FHIRString));
        $FHIRAddress->addLine(deep_copy($FHIRString));

        return $FHIRAddress;

    }

    public function convertToDateTime($timestamp)
    {
        $timestamp=str_replace("T", " ", $timestamp);
        $timestamp=str_replace(".000Z", "", $timestamp);

        return $timestamp;
    }

    public function createFHIRAddressType($type)
    {
        $FHIRAddressType = new FHIRAddressType;
        $FHIRAddressType->setValue($type);

        return $FHIRAddressType;
    }

    public function createDeleteSuccessRespond($time=null){

        $FHIROperationOutcome =new FHIROperationOutcome;

        $FHIRNarrative=new FHIRNarrative;
        $FHIRNarrativeStatus = new FHIRNarrativeStatus;
        $FHIRNarrativeStatus->setValue("generated");
        $FHIRNarrative->setStatus($FHIRNarrativeStatus);
        $FHIROperationOutcome->setText($FHIRNarrative);

        $FHIROperationOutcomeIssue= new FHIROperationOutcomeIssue;

        $FHIRIssueSeverity= new FHIRIssueSeverity;
        $FHIRIssueSeverity->setValue('information');
        $FHIROperationOutcomeIssue->setSeverity($FHIRIssueSeverity);

        $FHIRIssueType= new FHIRIssueType;
        $FHIRIssueType->setValue("informational");
        $FHIROperationOutcomeIssue->setCode($FHIRIssueType);

        $FHIRString= new FHIRString;

        $diagnostics="Successfully deleted 1 resource(s)";

        if(!is_null($time)){
            $diagnostics.=" in ".$time."ms" ;
        }

        $FHIRString->setValue($diagnostics);
        $FHIROperationOutcomeIssue->setDiagnostics($FHIRString);

        $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);

        return $FHIROperationOutcome;
    }

    public function createDeleteFailRespond($docId="unknown",$explanation="",$moreInfo=""){

        $FHIROperationOutcome =new FHIROperationOutcome;

        $FHIRNarrative=new FHIRNarrative;
        $FHIRNarrativeStatus = new FHIRNarrativeStatus;
        $FHIRNarrativeStatus->setValue("generated");
        $FHIRNarrative->setStatus($FHIRNarrativeStatus);
        $FHIROperationOutcome->setText($FHIRNarrative);


        /******************************************************/

        /*
         * diagnostics example
         * "diagnostics": "Unable to delete DocumentReference/1
         * because at least one resource has a reference to this resource.
         * First reference found was resource Provenance/1 in path Provenance.target"
         */

        $FHIROperationOutcomeIssue= new FHIROperationOutcomeIssue;
        $FHIRIssueSeverity= new FHIRIssueSeverity;
        $FHIRIssueSeverity->setValue('error');
        $FHIROperationOutcomeIssue->setSeverity($FHIRIssueSeverity);
        $FHIRIssueType= new FHIRIssueType;
        $FHIRIssueType->setValue("processing");
        $FHIROperationOutcomeIssue->setCode($FHIRIssueType);
        $FHIRString= new FHIRString;
        $diagnostics="Unable to delete DocumentReference/".$docId.".".$explanation;
        $FHIRString->setValue($diagnostics);
        $FHIROperationOutcomeIssue->setDiagnostics($FHIRString);
        $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);

        /************************************************************/

        /*   diagnostics example
         *  "diagnostics": "Note that cascading deletes are not active for this request.
         *   You can enable cascading deletes by using the \"_cascade=delete\" URL parameter."
         */

        $FHIROperationOutcomeIssue= new FHIROperationOutcomeIssue;
        $FHIRIssueSeverity= new FHIRIssueSeverity;
        $FHIRIssueSeverity->setValue('information');
        $FHIROperationOutcomeIssue->setSeverity($FHIRIssueSeverity);
        $FHIRIssueType= new FHIRIssueType;
        $FHIRIssueType->setValue("information");
        $FHIROperationOutcomeIssue->setCode($FHIRIssueType);
        $FHIRString= new FHIRString;
        $FHIRString->setValue($moreInfo);
        $FHIROperationOutcomeIssue->setDiagnostics($FHIRString);
        $FHIROperationOutcome->addIssue($FHIROperationOutcomeIssue);

        return $FHIROperationOutcome;
    }



    /**
     * create FHIRConditionStage
     *
     * @param array
     *
     * @return FHIRConditionStage | null
     */
    public function createFHIRConditionStage(array $stageArr)
    {
        $FHIRConditionStage = new FHIRConditionStage;

        if (key_exists('summary', $stageArr)) {

            $FHIRConditionStage->setSummary($stageArr['summary']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
            $FHIRConditionStage->setSummary($FHIRCodeableConcept);
        }

        if (key_exists('assessment', $stageArr)) {
            $FHIRConditionStage->addAssessment($stageArr['assessment']);

        }else{
            $FHIRReference = $this->createFHIRReference(null);
            $FHIRConditionStage->addAssessment($FHIRReference);
        }

        if (key_exists('type', $stageArr)) {
            $FHIRConditionStage->setType($stageArr['type']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
            $FHIRConditionStage->setType($FHIRCodeableConcept);
        }

        return $FHIRConditionStage;
    }


    /**
     * create FHIRConditionEvidence
     *
     * @param array
     *
     * @return FHIRConditionEvidence | null
     */
    public function createFHIRConditionEvidence(array $conditionEvidenceArr)
    {
        $FHIRConditionEvidence = new FHIRConditionEvidence;

        if (key_exists('code', $conditionEvidenceArr)) {

            $FHIRConditionEvidence->addCode($conditionEvidenceArr['code']);
        }else{
            $FHIRCodeableConcept=$this->createFHIRCodeableConcept(array("code"=>null,"text"=>"","system"=>""));
            $FHIRConditionEvidence->addCode($FHIRCodeableConcept);
        }

        if (key_exists('detail', $conditionEvidenceArr)) {
            $FHIRConditionEvidence->addDetail($conditionEvidenceArr['detail']);

        }else{
            $FHIRReference = $this->createFHIRReference(null);
            $FHIRConditionEvidence->addDetail($FHIRReference);
        }

        return $FHIRConditionEvidence;
    }


    /**
     * create FHIRAnnotation
     *
     * @param array
     *
     * @return FHIRAnnotation | null
     */
    public function createFHIRAnnotation(array $annotationArr)
    {
        $FHIRAnnotation = new FHIRAnnotation();

        if (key_exists('text', $annotationArr)) {

            $FHIRAnnotation->setText($annotationArr['text']);
        }else{
            $FHIRMarkdown= $this->createFHIRMarkdown(null);
            $FHIRAnnotation->setText($FHIRMarkdown);
        }

        if (key_exists('authorReference', $annotationArr)) {
            $FHIRAnnotation->setAuthorReference($annotationArr['authorReference']);

        }else{
            $FHIRReference = $this->createFHIRReference(null);
            $FHIRAnnotation->setAuthorReference($FHIRReference);
        }

        if (key_exists('authorString', $annotationArr)) {
            $FHIRAnnotation->setAuthorString($annotationArr['authorString']);

        }else{
            $FHIRString = $this->createFHIRString(null);
            $FHIRAnnotation->setAuthorString($FHIRString);
        }

        if (key_exists('time', $annotationArr)) {
            $FHIRAnnotation->setTime($annotationArr['time']);

        }else{
            $FHIRDateTime = $this->createFHIRDateTime(null);
            $FHIRAnnotation->setTime($FHIRDateTime);
        }

        return $FHIRAnnotation;
    }


}
