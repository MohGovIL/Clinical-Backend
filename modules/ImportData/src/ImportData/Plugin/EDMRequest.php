<?php

namespace ImportData\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ImportData\Plugin\DummyXML;


class EDMRequest extends AbstractPlugin
{

    const DEV_URL = 'http://192.168.14.249:80/BroadCastService/BroadCastView.svc';
    //const DEV_URL = 'http://ccenterwcf1.moh.health.gov.il:10002/BroadCastService/BroadCastView.svc';
    const PROD_URL = 'http://192.168.14.250:80/BroadCastService/BroadCastView.svc';
    //const PROD_URL = 'http://wcf-int.moh.health.gov.il:10002/BroadCastService/BroadCastView.svc';

    protected $xmlRequest;
    protected $table;
    public $errorReason = '';

    public function init($table)
    {

        $this->table = $table;

        if ($GLOBALS['EDM_dummy']) {
            $this->response_handler = new DummyXML();
        }
    }

    public function getTableName()
    {

        return $this->table;
        $this->response_handler = new DummyXML();
    }

    public function getErrorReason()
    {
        return $this->errorReason;
    }

    public function haveChanges($lastUpdate)
    {
        $this->xmlRequest = $this->openEnvelopeTag();
        $this->xmlRequest .= $this->createHeaderOfGenerationXMLDataTable();
        $this->xmlRequest .= $this->openBodyTag();
        $this->xmlRequest .= '<heal:GetChangTableDate xmlns:heal="http://www.health.gov.il" xmlns:heal1="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.CentralHub" xmlns:heal2="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common">';
        $this->xmlRequest .= $this->createEDMUserXmlForGetChangTableDate(4);
        $this->xmlRequest .= $this->createRequestDataForGetChangTableDate($lastUpdate);
        $this->xmlRequest .= "</heal:GetChangTableDate>";
        $this->xmlRequest .= $this->closeBodyTag();
        $this->xmlRequest .= $this->closeEnvelopeTag();

       // print_r(htmlspecialchars($this->xmlRequest));

        if ($GLOBALS['EDM_dummy']) {
            $tablename = $this->getTableName();
            $response = $this->response_handler->getXmlForGetChangTable();
        } else {
            $response = $this->send('GetChangTableDate');
            if(!$response){
                $this->errorReason = 'Connection to EDM failed';
                return false;
            }
        }

        $hasChanges = $this->parseGetChangTableDateResponse($response);

        return $hasChanges;

    }

    public function getChanges($lastUpdate)
    {

        $this->xmlRequest = $this->openEnvelopeTag();
        $this->xmlRequest .= $this->createHeaderOfGenerationXMLDataTable();
        $this->xmlRequest .= $this->openBodyTag();
        $this->xmlRequest .= '<GenerationXMLDataTable xmlns="http://www.health.gov.il">';
        $this->xmlRequest .= $this->createEDMUserXml();
        $where = $this->createWhereObject($lastUpdate);
        $this->xmlRequest .= $this->createRequestDataForGenerationXMLDataTable($this->table, $where);
        $this->xmlRequest .= '</GenerationXMLDataTable>';
        $this->xmlRequest .= $this->closeBodyTag();

        $this->xmlRequest .= $this->closeEnvelopeTag();
        //print_r(htmlspecialchars($this->xmlRequest));

        //print_r(strip_tags($response));die;
        //DEMO RESPONSE
        if ($GLOBALS['EDM_dummy']) {
            $tablename = $this->getTableName();
            $response = $this->response_handler->getXML($tablename);
            if(is_null($response)){
                $this->errorReason = 'no found demo';
                return false;
            }
           // print_r(strip_tags(html_entity_decode($response)));die;
        } else {
            $response = $this->send('GenerationXMLDataTable');
            if(!$response){
                $this->errorReason = 'Connection to EDM failed';
                return false;
            }
        }

        // END DEMO

        $results = $this->parseGenerationXMLDataTableResponse($response);
        return $results;
    }

    protected function openEnvelopeTag()
    {
        return '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">';
    }

    protected function closeEnvelopeTag()
    {
        return '</s:Envelope>';
    }

    protected function openBodyTag()
    {
        return '<s:Body>';
    }

    protected function closeBodyTag()
    {
        return '</s:Body>';
    }

    protected function createHeaderOfGenerationXMLDataTable()
    {
        $xml = '<s:Header>';
        //$xml .= '<Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">http://www.health.gov.il/IHealthBroadCast/GenerationXMLDataTable</Action>';
        $xml .= '</s:Header>';
        return $xml;
    }

    protected function createEDMUserXml($appId = 1, $custId = 99, $userName = 'EDMDeveloperUser')
    {
        if($GLOBALS['EDM_prod_url'] == 1){
            $userName =  $GLOBALS['edm_moh_user'];
            $custId = 1;
        }

        $xml = '<sUser xmlns:d4p1="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.CentralHub" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $xml .= "<d4p1:AppId>$appId</d4p1:AppId>";
        $xml .= "<d4p1:CustId>$custId</d4p1:CustId>";
        $xml .= "<d4p1:UserName>$userName</d4p1:UserName>";
        $xml .= '</sUser>';

        return $xml;
    }

    protected function createEDMUserXmlForGetChangTableDate($appId = 4, $custId = 99, $userName = 'amiel.matrix')
    {
        if($GLOBALS['EDM_prod_url'] == 1){
            $userName =  $GLOBALS['edm_moh_user'];
            $custId = 1;
        }

        $xml = '<heal:sUser xmlns:d4p1="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.CentralHub" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $xml .= "<heal1:AppId>$appId</heal1:AppId>";
        $xml .= "<heal1:CustId>$custId</heal1:CustId>";
        $xml .= "<heal1:UserName>$userName</heal1:UserName>";
        $xml .= '</heal:sUser>';

        return $xml;
    }


    protected function createFromDateXml($date)
    {

        $date_details = date_parse_from_format("Y-m-d H:i:s", $date);

        $xml = "<d4p1:FromDate>";
        $xml .= "<d4p1:Day>{$date_details['day']}</d4p1:Day>";
        $xml .= "<d4p1:HebrowDate i:nil=\"true\" />";
        $xml .= "<d4p1:Month>{$date_details['month']}</d4p1:Month>";
        $xml .= "<d4p1:Year>{$date_details['year']}</d4p1:Year>";
        $xml .= "</d4p1:FromDate>";

        return $xml;
    }

    protected function createToDateXml($date)
    {

        $date_details = date_parse_from_format("Y-m-d H:i:s", $date);

        $xml = "<d4p1:ToDate>";
        $xml .= '<d4p1:Jobname i:nil="true" />';
        $xml .= '<d4p1:RunDateTime i:nil="true" />';
        $xml .= "<d4p1:Day>{$date_details['day']}</d4p1:Day>";
        $xml .= '<d4p1:HebrowDate i:nil="true" />';
        $xml .= "<d4p1:Month>{$date_details['month']}</d4p1:Month>";
        $xml .= "<d4p1:Year>{$date_details['year']}</d4p1:Year>";
        $xml .= "</d4p1:ToDate>";

        return $xml;
    }

    public function createRequestDataForGetChangTableDate($date)
    {

        $date_details = date_parse_from_format("Y-m-d H:i:s", $date);

        $xml = "<heal:RequestData>";
        $xml .= "<heal1:RequestedDate>";
        $xml .= "<heal2:Day>{$date_details['day']}</heal2:Day>";
        $xml .= "<heal2:Month>{$date_details['month']}</heal2:Month>";
        $xml .= "<heal2:Year>{$date_details['year']}</heal2:Year>";
        $xml .= "</heal1:RequestedDate>";
        $xml .= "<heal1:TableName>{$this->table}</heal1:TableName>";
        $xml .= "</heal:RequestData>";

        return $xml;

    }

    /**
     * @param $table
     * @param $where element from createWhereObject method
     * @param int $serviceId
     * @param string $includHistory
     */
    protected function createRequestDataForGenerationXMLDataTable($table, $where = null, $serviceId = 1, $includHistory = 'false')
    {

        $xml = '<RequestData xmlns:d4p1="http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.CentralHub" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        $xml .= "<d4p1:IncludHistory>$includHistory</d4p1:IncludHistory>";
        $xml .= "<d4p1:ServiceId>$serviceId</d4p1:ServiceId>";
        $xml .= "<d4p1:TableName>$table</d4p1:TableName>";
        if (!is_null($where)) $xml .= $where;
        $xml .= "</RequestData>";

        return $xml;
    }

    /**
     * now this function crete where element for xml that only find row that has changed since last update
     * in he future  enable to make it for generic queries
     * @param $lastUpdate
     */
    protected function createWhereObject($lastUpdate)
    {

        $xml = "<d4p1:where xmlns:d5p1=\"http://schemas.datacontract.org/2004/07/HealthBroadCast.Contracts.Common\">";
        $xml .= "<d5p1:BroadCastWhereStatementGen>";
        $xml .= "<d5p1:ConditionsField>Update_date</d5p1:ConditionsField>";
        $xml .= "<d5p1:ConditionsFieldType>Date</d5p1:ConditionsFieldType>";
        $xml .= "<d5p1:ConditionsOperationField>Greater</d5p1:ConditionsOperationField>";
        $xml .= "<d5p1:ConditionsFieldValue>" . date('Ymd', strtotime($lastUpdate)) . "</d5p1:ConditionsFieldValue>";
        $xml .= "<d5p1:BetweenConditionsFieldValue></d5p1:BetweenConditionsFieldValue><d5p1:LogicalOperation>AndOperation</d5p1:LogicalOperation>";
        $xml .= "</d5p1:BroadCastWhereStatementGen>";
        $xml .= "</d4p1:where>";

        return $xml;

    }


    public function send($method)
    {

        $header = array(
            "Accept-Encoding: gzip,deflate",
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: "http://www.health.gov.il/IHealthBroadCast/' . $method . '"',
            'Content-Length: ' . strlen($this->xmlRequest),
            'Host: 192.168.14.250:80',
            'User-Agent: Apache-HttpClient/4.1.1 (java 1.5)'
        );

        $soap_do = curl_init();

        if($GLOBALS['EDM_prod_url'] == 1){
            curl_setopt($soap_do, CURLOPT_URL, self::PROD_URL);
        } else {
            curl_setopt($soap_do, CURLOPT_URL, self::DEV_URL);
        }

        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $this->xmlRequest);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($soap_do);
        if ($response === false) {
            $err = 'Curl error: ' . curl_error($soap_do);
            curl_close($soap_do);
            // print $err;
        } else {
            curl_close($soap_do);
            // print 'Operation completed without any errors';
        }

        return $response;

    }

    // GenerationXMLDataTable service
    protected function parseGenerationXMLDataTableResponse($xml)
    {

    $clean_xml =str_ireplace(['s:', 'xs:', 'a:'], '', $xml);

    try{

        $allResponse = simplexml_load_string($clean_xml);
        //  echo "<pre>";print_r($allResponse);echo "</pre>";

        if(isset($allResponse->Body->GenerationXMLDataTableResponse->pResult)) {
          $result = $allResponse->Body->GenerationXMLDataTableResponse->pResult;
        } else {
          throw new \Exception('Bad xml structure from GenerationXMLDataTableResponse service no found pResult');
        }

        if($result != 'CompletSuccessfully'){

          $this->errorReason = $result;
          return false;
        }

        if (isset($allResponse->BroadCastTransDataSet)) {
            $results = $allResponse->BroadCastTransDataSet;
            return $results->{$this->table};
        } elseif (isset($allResponse->Body->GenerationXMLDataTableResponse->GenerationXMLDataTableResult->XMLData->BroadCastTransDataSet)) {
            $results = $allResponse->Body->GenerationXMLDataTableResponse->GenerationXMLDataTableResult->XMLData->BroadCastTransDataSet;
            return $results->{$this->table};
        } elseif (isset($allResponse->Body->GenerationXMLDataTableResponse->GenerationXMLDataTableResult->XMLData)){
            // all this code necessary because xml response return with <![CDATA[]]> on the tags and only with DOMdocument I succeeded to parese it
            $xml = $allResponse->Body->GenerationXMLDataTableResponse->GenerationXMLDataTableResult->XMLData;
            $xml = str_ireplace(array('&lt;', '&gt;'), array('<','>'), $xml);

            $doc = \DOMDocument::loadXML($xml);
            $return = array();
            $i=0;
            while(is_object($element = $doc->getElementsByTagName($this->table)->item($i)))
            {
                $data = array();
                foreach($element->childNodes as $nodename)
                {
                    if($nodename->nodeType == 1)$data[$nodename->tagName] = $nodename->nodeValue;

                }
                $return[] = $data;
                $i++;
            }
        //echo "<pre>";print_r($return);
                    return $return;

        } else {
            throw new \Exception('Bad xml structure from GenerationXMLDataTable service');
        }

    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }

    }


    // GenerationXMLDataTable service
    protected function parseGetChangTableDateResponse($xml)
    {

        $clean_xml = (string)str_ireplace(['s:', 'xs:', 'a:', 'b:'], '', $xml);

        try {

            $allResponse = simplexml_load_string($clean_xml, null, LIBXML_NOCDATA);

            if(isset($allResponse->Body->GetChangTableDateResponse->pResult)) {
                $result = $allResponse->Body->GetChangTableDateResponse->pResult;
            } else {
                throw new \Exception('Bad xml structure from GetChangTableDateResponse service');
            }

            if($result == 'CompletSuccessfully'){
                return true;
            } else {
                $this->errorReason = $result;
                return false;
            }


        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }


}
