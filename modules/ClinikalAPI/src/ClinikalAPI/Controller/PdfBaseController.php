<?php

namespace ClinikalAPI\Controller;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest\FhirServiceRequestMapping;
use FhirAPI\FhirRestApiBuilder\Parts\Strategy\StrategyElement\ServiceRequest\ServiceRequest;
use FhirAPI\Model\FhirServiceRequestTable;
use Formhandler\View\Helper\GenericTable;
use GenericTools\Controller\BaseController as GenericBaseController;
use GenericTools\Model\EncounterReasonCodeMapTable;
use GenericTools\Model\FormEncounterTable;
use GenericTools\Model\ListsTable;
use GenericTools\Model\Prescriptions;
use GenericTools\Model\PrescriptionsTable;
use GenericTools\Model\ValueSetsTable;
use ImportData\Model\CodesTable;
use Interop\Container\ContainerInterface;
use GenericTools\Traits\saveDocToServer;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;

class PdfBaseController extends GenericBaseController
{
    use saveDocToServer;

    const HEADER_PATH = 'clinikal-api/pdf/default-header';
    const FOOTER_PATH = 'clinikal-api/pdf/default-footer';
    const DOC_TYPE = "file_url";
    const PDF_MINE_TYPE = "application/pdf";

    private $container;
    public $postData = array();
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    /**
     * get facility data
     */
    public function getFacilityInfo($id = null)
    {
        if (!is_null($id)) {
            $info = $this->container->get('GenericTools\Model\FacilityTable')->getFacility(intval($id));
            $data = array();
            $data['clinic'] = $info->name;
            $data['address'] = $info->street . " " . $info->city;
            $data['phone'] = $info->phone;
            $data['email'] = $info->email;
            return $data;

        } else {
            return array();
        }
    }

    public function getPatientInfo($id = null)
    {
        if (!is_null($id)) {
            $info = $this->container->get('GenericTools\Model\PatientsTable')->getPatientDataById(intval($id));
            $data = array();
            $data['name'] = $info['fname']." ".$info['lname'];
            $data['id_number'] = $info['ss'];
            $data['id_type'] = $info['mh_type_id'];
            $data['Gender'] = $info['sex'];
            $data['birthdate'] = $info['DOB_DMY'];
            $data['age'] = $info['age'];
            $data['phone'] = ($info['phone_cell'] ? $info['phone_cell']  : ($info['phone_home'] ? $info['phone_home'] : ($info['phone_contact'] ? $info['phone_contact'] :""))) ;
            $data['HMO'] = $info['insurance_organiz_name'];
            return $data;

        } else {
            return array();
        }
    }

    public function getListsOpenEMRInfo($type=null,$pid,$outcome)
    {
        if (!is_null($type)) {
            $info = $this->container->get('GenericTools\Model\ListsOpenEmrTable')->getListWithTheType($type,$pid,$outcome);
            return $info;

        } else {
            return array();
        }
    }

    public function getUserInfo($id = null)
    {
        if (!is_null($id)) {
            $info = $this->container->get('GenericTools\Model\UserTable')->getUser(intval($id));
            $data = array();
            $data['name'] = xl("Dr.")." ".($info->fname?$info->fname:"")." ". ($info->mname?$info->mname:"")." ".($info->lname?$info->lname:"");
            $data['state_license_number'] = $info->state_license_number?$info->state_license_number:"";
            return $data;

        } else {
            return array();
        }
    }

    public function saveDocToStorage($data, $fileName, $date)
    {
        $dataToSave = array();
        $dataToSave['storage']['data'] = $data;
        $dataToSave['documents']['date'] = $date;
        $dataToSave['documents']['url'] = $fileName;
        $rez = $this->uploadToStorage($dataToSave);
        return $rez;
    }

    public function saveDocInfoToDb($storageSave, $configData, $pdfEncoded, $fileName = null)
    {

        if ($storageSave['id']) {

            $configData = array_merge($configData, $storageSave);

            $dbStructuredData = $this->buildArrToDb($configData);

            if (empty($dbStructuredData)) {
                ErrorCodes::http_response_code('500', 'failed to build data to db');
                return array();
            } else {

                $save = $this->saveDocToDb($dbStructuredData);

                if($save){
                    return array(
                        "id" => $save,
                        "base64_data" => $pdfEncoded,
                        "mimetype" => $configData['mimetype'],
                        "file_name" => !is_null($fileName) ? $fileName : ''
                    );
                }else{
                    ErrorCodes::http_response_code('500', 'failed to build data to db');
                    return array();
                }
            }
        } else {
            ErrorCodes::http_response_code('500', 'failed to save document');
            return array();
        }
    }

    public function createConfigData($postData,$mimetype, $category)
    {
        if (empty($postData['facility']) || empty($postData['encounter'])) {
            return array();
        }

        $configData = array(
            'mimetype' => $mimetype,
            'category' => $category,
            'encounter' => $postData['encounter']
        );

        if (empty($postData['owner'])) {
            $configData['owner'] = $postData['owner'];
        }

        if (empty($postData['patient'])) {
            $configData['patient'] = $postData['patient'];
        }
        return $configData;
    }

    public function createBase64Pdf($fileName,$bodyPath,$headerPath, $footerPath,$headerData,$bodyDataTemp)
    {
        $this->getPdfService()->fileName($fileName);
        $this->getPdfService()->setCustomHeaderFooter($headerPath,$footerPath,$headerData,"datetime");
        //added multi-paged functionality to letter creator.
        if(is_array($bodyDataTemp))
        {
            foreach($bodyPath as $key=>$path) {
                $bodyData=$bodyDataTemp[$key];
                $this->getPdfService()->bodyBuilder($path, $bodyData);
                $this->getPdfService()->pagebreak();
            }
        }
        else {
            $bodyData=$bodyDataTemp;
            $this->getPdfService()->body($bodyPath, $bodyData);
        }
        $this->getPdfService()->returnBinaryString();
        $binary=$this->getPdfService()->render();
        $pdfEncoded= base64_encode($binary);

        return $pdfEncoded;
    }

    public function getTitleOfOptionFromListTable($list,$option){
        $listsTable= $this->container->get(ListsTable::class);
        $title = $listsTable->getSpecificTitle($list,$option);
        return $title;
    }

    public function getReasonCodesTitles($list,$codes){
        $listsTable= $this->container->get(ListsTable::class);
        $titles = $listsTable->getTitles($list,$codes);
        return $titles;
    }
    public function getReasonCodes($encounter_id){
        $reasonCode= $this->container->get(EncounterReasonCodeMapTable::class);
        $reasonCodes = $reasonCode->fatchAllByEID($encounter_id,true);
        return $reasonCodes;
    }


    public function getQData($qid,$class){
        $FormMedicalAdmissionQTable= $this->container->get($class);
        $dbData=$FormMedicalAdmissionQTable->getLastQuestionnaireAnswer($this->postData['encounter'],$qid);
        return $dbData['answer'];
    }

    public function getServiceTypeAndReasonCode(){
        //get encounter
        $FormMedicalAdmissionQTable= $this->container->get(FormEncounterTable::class);
        $encounter = $dbData=$FormMedicalAdmissionQTable->fetchById($this->postData['encounter']);
        //get clinikal_service_types and reason codes
        $service_type = $this->getTitleOfOptionFromListTable("clinikal_service_types",$encounter['service_type']);
        $reason_codes = $this->getReasonCodes($encounter['id'],true);
        $reason_codes_titles = $this->getReasonCodesTitles("clinikal_reason_codes",$reason_codes,true);
        foreach($reason_codes_titles as $key=>$title)
        {
            $reason_codes_titles[$key] = xl($title);
        }
        return xl($service_type)." - ".implode(",",$reason_codes_titles) ."<br/>" . $encounter['reason_codes_details'];
    }

    public function getSensitivities(){
        return $this->getListsOpenEMRInfo("sensitive",$this->postData['patient'],1);
    }
    public function getMedicalProblems(){
        return $this->getListsOpenEMRInfo("medical_problem",$this->postData['patient'],1);
    }
    public function getMedicine(){
        return $this->getListsOpenEMRInfo("medication",$this->postData['patient'],1);
    }
    public function createTableJsonFromVitals($vitals){
        $arrTemp = [];
        foreach($vitals as $rk => $rows)
        {
            foreach($rows as $ck => $column)
            {
               $arrTemp['columns'][$ck]=$column[0];
            }
            break;
        }

        $arrTemp ['rows'] = $vitals;

        return $arrTemp;
    }
    public function getConstantVitals($pid,$category,$acitivity,$order)
    {
        $vitals = $this->getVitals($pid,$category,$acitivity,$order);
        foreach($vitals as $k=>$v)
        {
            if($k === 0) {
                foreach ($v as $key => $value) {
                    if ($key !== 'height' && $key !== 'weight') {
                        unset($vitals[$k][$key]);
                    }
                }
            }
            else{
                unset($vitals[$k]);
            }
        }
        return sizeof($vitals[0]) > 1 ? $this->createTableJsonFromVitals($vitals) : null;
    }
    public function getVariantVitals($pid,$category,$acitivity,$order)
    {
        $vitals = $this->getVitals($pid,$category,$acitivity,$order);



        foreach($vitals as $key=>$value){
            foreach($value['bpd'] as $k=>$v) {
                if($k==0)
                {
                    $vitals[$key]['pressure'][$k] = $vitals[$key]['bpd'][$k];
                }
                else {
                    $vitals[$key]['pressure'][$k] = $vitals[$key]['bpd'][$k] . "/" . $vitals[$key]['bps'][$k];
                }
            }

            foreach($value as $k=>$v) {
                switch($k){
                    case 'height':
                    case 'weight':
                    case 'temp_method':
                    case 'BMI':
                    case 'BMI_status':
                    case 'waist_circ':
                    case 'head_circ':
                        unset($vitals[$key][$k]);
                        break;
                    case 'date':
                        break;
                    default:
                        $vitals[$key][$k][2] = $vitals[$key][$k] && (is_null($v[2]) || $v[2] == "" || $v[2] == 0 && $v[2] == "0.00") ?"-":$v[2];
                        break;
                }
            }



            unset($vitals[$key]['bps']);
            unset($vitals[$key]['bpd']);
        }

        return $this->createTableJsonFromVitals($vitals);
    }
    public function getVitals($pid,$category,$acitivity,$order){
        $observationList =  $this->container->get('GenericTools\Model\ListsTable')->getList("loinc_org");
        $observationTitleList = [];
        foreach($observationList as $key=>$value)
        {
            $notes = json_decode($value['notes']);
            $observationTitleList[$value['mapping']] = [xl($notes->label),xl($value['subtype'])];
        }
        $observations = $this->container->get('GenericTools\Model\FormVitalsTable')->getVitals($pid,$category,$acitivity,$order);
        $observedArr = [];

        foreach($observations as $keyParent=>$valuesParent)
        {
            $observedArr[$keyParent]=[];
            foreach($valuesParent as $key=>$value) {
                if($observationTitleList[$key]) {
                    $observedArr[$keyParent][$key] = array_merge($observationTitleList[$key], [$value]);
                }
                if($key==="date"){
                    $observedArr[$keyParent][$key]=[xlt("Hour"), explode(" ",$value)[1]];
                }
            }

        }
        return $observedArr;
    }
    public function getPrescriptions($eid,$pid){
     return $FhirServiceRequestTable = $this->container->get(PrescriptionsTable::class)->getPatientPrescription($eid,$pid);
    }
    public function getServiceRequest($eid,$pid,$status,$xrayList){
        $FhirServiceRequestTable = $this->container->get(FhirServiceRequestTable::class);
        $serviceRequests = $FhirServiceRequestTable->getTeastAndTreatmentsPreformed($eid,$pid,$status);

        foreach($serviceRequests as $key=>$value)
        {
           switch($value['instruction_code']) {
               case "x_ray_type":
                   $value['order_detail_code'] = xl($xrayList[$value['order_detail_code']]);
               break;
               case "providing_medicine":
                   $valueSetsTable = $this->container->get(ValueSetsTable::class);
                   $where=array ('filter' => array (0 => array ('value' => $value['order_detail_code'], 'operator' => '=',),),);
                   $drug = $valueSetsTable->getValueSetById('details_providing_medicine',$where);
                   $serviceRequests[$key]['order_detail_code'] = xl($drug[0]['display']);
               break;
           }
        }
        return $serviceRequests;

    }
}
