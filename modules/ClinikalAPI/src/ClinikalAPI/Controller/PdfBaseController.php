<?php

namespace ClinikalAPI\Controller;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use GenericTools\Controller\BaseController as GenericBaseController;
use GenericTools\Model\EncounterReasonCodeMapTable;
use GenericTools\Model\FormEncounterTable;
use GenericTools\Model\ListsTable;
use ImportData\Model\CodesTable;
use Interop\Container\ContainerInterface;
use GenericTools\Traits\saveDocToServer;

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

    public function createBase64Pdf($fileName,$bodyPath,$headerPath, $footerPath,$headerData,$bodyData)
    {
        $this->getPdfService()->fileName($fileName);
        $this->getPdfService()->setCustomHeaderFooter($headerPath,$footerPath,$headerData,"datetime");
        $this->getPdfService()->body($bodyPath, $bodyData);
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
}
