<?php

namespace ClinikalAPI\Controller;

use FhirAPI\FhirRestApiBuilder\Parts\ErrorCodes;
use GenericTools\Controller\BaseController as GenericBaseController;
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

    public function saveDocToStorage($data, $fileName, $date)
    {
        $dataToSave = array();
        $dataToSave['storage']['data'] = $data;
        $dataToSave['documents']['date'] = $date;
        $dataToSave['documents']['url'] = $fileName;
        $rez = $this->uploadToStorage($dataToSave);
        return $rez;
    }

    public function saveDocInfoToDb($storageSave, $configData,$pdfEncoded)
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
                        "base64_data" => $pdfEncoded
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
}
