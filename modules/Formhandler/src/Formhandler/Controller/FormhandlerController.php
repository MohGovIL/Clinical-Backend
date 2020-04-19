<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 03/07/16
 * Time: 12:28
 */

namespace Formhandler\Controller;

use Formhandler\Validator\ServerValidationHandler;
use Interop\Container\ContainerInterface;
use Zend\Validator\Callback;
use Zend\Validator;
use Formhandler\Controller\BaseController;
use Formhandler\Plugin\CouchDBHandle;
use Formhandler\View\Helper\DrugAndAlcoholUsageTable;
use Zend\Json\Server\Exception\ErrorException;
use Zend\View\Model\ViewModel;
use Zend\Form\Element;
use Zend\Form\Form;
use Formhandler\Model\customDB;
use TwbBundle\Form\Element\StaticElement;
class FormhandlerController extends BaseController
{

    const DOCUMENT="document";
    const LABEL="label";
    const ID="_id";
    const SQL="sql";
    const DRAFT="draft";
    const VALIDATION="validators";
    const CONDITIONAL="conditional";
    const FIELDS="fields";
    const ATTRIBUTES="attributes";
    const TYPE="type";
    const INPUT_ID="id";
    const REQUIRED="required";
    const NAME="name";
    const CONDITIONS="conditions";
    const JSADDONS="jsAddons";
    const PHPADDONS="PHPAddons";
    const SQL_MUST_PARM_AMOUNT=6;
    const NEW_FORM_DIR_PATH="/forms/";
    const FORM_NAME_PLACEHOLDER = "#FORMNAME#";
    const FORM_TITLE_PLACEHOLDER= "#FORMTITLE#";
    const FORM_SQL_PLACEHOLDER="#SQL#";
    const INFO_FILE="info.txt";
    const NEW_FILE="new.php";
    const REPORT_FILE="report.php";
    const TABLE_FILE="table.sql";
    const VIEW_FILE="view.php";
    const GROUPS="groups";
    const VALIDATORS="validators";
    const OPTIONS="options";
    const VALUE_OPTIONS="value_options";
    const PLACEHOLDER="placeholder";
    const FIELD_STABLE="fields_table";
    const BODY="body";
    const FORMPREFIX="form_";
    const EMPTY_VALUE="empty";
    const DAS_FORM="danger_assessment_scoring";
    const SAVEANDPRINT="saveAndPrint";

    public static $maxFields =1;   //max fields for the report
    public static $newFormDirPath ="";

    public $arrayOfDbValues = array();

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        //adding the js and css for all class
        $this->getJsFiles();
        $this->getCssFiles(__CLASS__);
        $this->formname="null";
        $this->formname="null";
        self::$newFormDirPath=$GLOBALS['include_root'].self::NEW_FORM_DIR_PATH;
    }
    public function getAllDocumentsAction()
    {
        $couchDBConnection=new CouchDBHandle();
        $connection=$couchDBConnection->couchDBConnection();
        $allDocumentArray=$connection->allDocs();

        die(json_encode(json_decode(json_encode($allDocumentArray),true)));
    }
    public function saveDraftAction(){
        $params=$this->getPostParamsArray();
        if(!$params) die("no params where sent");
        $jasonnyArray=json_decode($params['couchElement']);

        try {
            $couchDBConnection=new CouchDBHandle();
            $id=$jasonnyArray->_id;
            $document=$couchDBConnection->getDocument($id);
            if(!isset($document->body['error']))
            {
                $couchDBConnection->saveDraftCouchDocument(array("document"=>$jasonnyArray->document,"draft"=>"","jsAddons"=>$jasonnyArray->jsAddons,"sql"=>$jasonnyArray->sql,"conditions"=>$jasonnyArray->conditions,"PHPAddons"=>$jasonnyArray->PHPAddons),$id);
                return $couchDBConnection;
            }
            $couchDBConnection->saveCouchDocument(array("document"=>$jasonnyArray->document,"draft"=>"","jsAddons"=>$jasonnyArray->jsAddons,"sql"=>$jasonnyArray->sql,"conditions"=>$jasonnyArray->conditions,"PHPAddons"=>$jasonnyArray->PHPAddons),$id);

            return  $this->responseWithNoLayout(array("status"=>"ok"),true,200);
        }
        catch (Error $err)
        {
            return  $this->responseWithNoLayout(array("status"=>"false"),true,200);
        }
    }


    public function createDosageAction()
    {
        /*get files for layout*/
        $this->getJsFiles(__METHOD__);
        $this->getCssFiles(__METHOD__);

        /*get Db handlers */
        $customDBHandler=$this->getCustomDB();  //SQL
        $couchDBConnection=new CouchDBHandle(); //couch

        /*get and prepare form data*/
        $PostParams=$this->getPostParamsArray();
        unset($PostParams["form_name"]);
        unset($PostParams["button_submit"]);

        $sessionParm=$this->getFormSessionParm();

        //need to add multiple table fields params;



        $tableParams=array();

        if(isset($this->tableName) && $this->tableName!=''){
            $tableName=$this->tableName;
        }
        else {
            $tableName = $this->params('tableName');
        }
        $formName=$couchDBConnection->getLabel($tableName);
        $getDocument=$couchDBConnection->getDocument($tableName);
        foreach($getDocument->body['document']['fields_table'] as $key=>$value){

            if(!$tableParams[$value]) {

                $tableParams[$value]=[];

            }
            array_push($tableParams[$value] ,$key);
        }



        /** START CUSTOM SERVER VALIDATION**/
        $validationMatrix=$couchDBConnection->getValidationMatrix($tableName);
        $validatorhandler=new ServerValidationHandler($PostParams,$validationMatrix);
        $serverValidationRes=$validatorhandler->isValid();
        $passServerValidation=$validatorhandler->checkResult();
        if (!$passServerValidation){
            $serverValidationRes=$validatorhandler->errorReduce($serverValidationRes);
            $url=str_replace("save","index",$this->getRequest()->getUri()->toString());
            return $this->redirect()->toUrl($url."?edit=true&form=".$tableName."&notValidate=true&params=".json_encode($PostParams)."&validate=".json_encode($serverValidationRes));
        }
        /**   END OF CUSTOM SERVER VALIDATION***/

        /*validiate and save or edit*/

        $filter = $customDBHandler->getInputFilter();
        $isValid = $filter->setData($PostParams)->isValid();
        if($isValid) {
            $id=$PostParams["id"];
            if ($id!=0 && $id!='' && isset($id))
            {
                unset($PostParams["id"]);

                foreach($tableParams as $key=>$value) {
                    $paramsToSave=[];
                    foreach($value as $k=>$v) {
                        $paramsToSave[$v]=$PostParams[$v];
                    }
                    $tableParm = array_merge($sessionParm,$paramsToSave);
                    //if(!strpos($key,"form_")>=0) {


                    $tableName=$this->AlterFormPrefixName($key);
                    $customDBHandler->updateForm($tableParm, $tableName, $formName, $id);

                }
            }
            else
            {
                foreach($tableParams as $key=>$value) {
                    $paramsToSave=[];
                    foreach($value as $k=>$v) {
                        $paramsToSave[$v]=$PostParams[$v];
                    }
                    $tableParm = array_merge($sessionParm,$paramsToSave);
                    //if(!strpos($key,"form_")>=0) {
                    $customDBHandler->saveForm($tableParm, $key, $formName);
                    //}
                    //else{
                    //  $customDBHandler->saveForm($tableParm, "form_" . $key, $formName);

                    //}

                }
            }

            $this->responseWithNoLayout(array( $status = 'success'));


        } else {

            $this->responseWithNoLayout(array( $status = 'failed'));
        }


    }


    public function saveAction()
    {
        /*get files for layout*/
        $this->getJsFiles(__METHOD__);
        $this->getCssFiles(__METHOD__);

        /*get Db handlers */
        $customDBHandler=$this->getCustomDB();  //SQL
        $couchDBConnection=new CouchDBHandle(); //couch

        /*get and prepare form data*/
        $PostParams=$this->getPostParamsArray();
        unset($PostParams["form_name"]);
        unset($PostParams["button_submit"]);

        $sessionParm=$this->getFormSessionParm();

        //need to add multiple table fields params;
        $feedback = null;


        $tableParams=array();
        $tableName=$this->params('tableName');
        $formName=$couchDBConnection->getLabel($tableName);
        $getDocument=$couchDBConnection->getDocument($tableName);

        //check if need to do action after saving
        $afterMath=$getDocument->body['AfterMath'];
        $afterMathLocation="";
        if (!is_null($afterMath)){
            $afterMathLocation=$afterMath['controller']."\\".$afterMath['action'];
        }

        foreach($getDocument->body['document']['fields_table'] as $key=>$value){

            if(!$tableParams[$value]) {

                $tableParams[$value]=[];

            }
            array_push($tableParams[$value] ,$key);
        }






        /** START CUSTOM SERVER VALIDATION**/
        $validationMatrix=$couchDBConnection->getValidationMatrix($tableName);
        $validatorhandler=new ServerValidationHandler($PostParams,$validationMatrix);
        $serverValidationRes=$validatorhandler->isValid();
        $passServerValidation=$validatorhandler->checkResult();
        if (!$passServerValidation){
            $serverValidationRes=$validatorhandler->errorReduce($serverValidationRes);
            $url=str_replace("save","index",$this->getRequest()->getUri()->toString());
            return $this->redirect()->toUrl($url."?edit=true&form=".$tableName."&notValidate=true&params=".json_encode($PostParams)."&validate=".json_encode($serverValidationRes));
        }
        /**   END OF CUSTOM SERVER VALIDATION***/

        /** START OF GENERIC TABLES VALIDATIONS */
        $validationMatrix=$couchDBConnection->getValidationMatrixGenericTable($tableName);

        foreach($validationMatrix as $table=>$rowsToValidate) {

            $arrayOfRowsFields = json_decode($PostParams[$table]);
            foreach($arrayOfRowsFields as $row=>$cols) {

                $PostParamsShredded=[];
                $PostParamsRealNames=[];
                if(count($cols)>0) {
                    //collect post params
                    $name = '';
                    foreach($cols as $c=>$p) {
                        $raw_name = explode("_", $p->name);
                        $name="";

                        foreach($raw_name as $nameVal) {
                            if(is_numeric($nameVal))
                            {

                            }
                            else {
                                $name .= $nameVal . "_";
                            }

                        }
                        if(substr($name,strlen($name)-1)=="_")
                            $name=substr($name,0,strlen($name)-1);
                        $PostParamsShredded[$name] = $p->value;
                        $PostParamsRealNames[$p->name] = $p->value;
                    }



                    foreach($validationMatrix[$table] as $row=>$validationMatrixTable)
                    {
                        foreach($validationMatrixTable as $name=>$validation) {
                            $validation['name']=$validation['action'];
                            $validationMatrixTable[$name] = [$validation];
                            $validatorhandler = new ServerValidationHandler($PostParamsShredded, $validationMatrixTable);
                            $serverValidationRes = $validatorhandler->isValid();
                            $passServerValidation = $validatorhandler->checkResult();
                            if (!$passServerValidation) {


                                foreach($PostParamsRealNames  as $k=>$v) {
                                    if (FormhandlerController::str_contains($k,$name)){
                                        switch($validation['action']) {

                                            case "requireIf":
                                                $serverValidationRes[$k] = ["Required" => "The field is Required"];
                                            break;
                                        }
                                        // unset($serverValidationRes[$k]);
                                    }
                                }

                                $serverValidationRes = $validatorhandler->errorReduce($serverValidationRes);
                                $url = str_replace("save", "index", $this->getRequest()->getUri()->toString());
                                return $this->redirect()->toUrl($url . "?edit=true&form=" . $tableName . "&notValidate=true&params=" . json_encode($PostParams) . "&validate=" . json_encode($serverValidationRes));
                            }
                        }
                    }
                }
            }
        }


        /** END OF GENERIC TABLES VALIDATIONS */

        /*validiate and save or edit*/

        $filter = $customDBHandler->getInputFilter();
        $isValid = $filter->setData($PostParams)->isValid();
        $afterMathFormId=null;
        if($isValid) {
            $id=$PostParams["id"];
            if ($id!=0 && $id!='' && isset($id))
            {
                $afterMathFormId=$id;
                unset($PostParams["id"]);

                foreach($tableParams as $key=>$value) {
                    $paramsToSave=[];
                    foreach($value as $k=>$v) {
                        $paramsToSave[$v]=$PostParams[$v];
                    }
                    $tableParm = array_merge($sessionParm,$paramsToSave);
                    //if(!strpos($key,"form_")>=0) {


                       $tableName=$this->AlterFormPrefixName($key);

                        $customDBHandler->updateForm($tableParm, $tableName, $formName, $id);



                }
            }
            else
            {
                foreach($tableParams as $key=>$value) {
                    $paramsToSave=[];
                    foreach($value as $k=>$v) {
                        $paramsToSave[$v]=$PostParams[$v];
                    }
                    $tableParm = array_merge($sessionParm,$paramsToSave);

                    if( $key == "form_".str_replace(" ","_",  lcfirst($formName))) {
                        $tempAfterMathFormId = $customDBHandler->saveForm($tableParm, $key, $formName);
                    }
                    else{
                        $tempAfterMathFormId = $customDBHandler->saveForm($tableParm, $key, $formName);
                    }

                    // save the first original form id for aftermath
                    if(!(is_object($tempAfterMathFormId))  &&  $afterMathFormId==null){
                        $afterMathFormId=$tempAfterMathFormId;
                    }


                }
            }

             if($afterMathLocation!="" &&  $afterMathLocation!="\\" && (string)(int)$afterMathFormId == $afterMathFormId ){

                 $controller = $afterMath['controller'];
                 $action =$afterMath['action'];
                 $instance = new $controller($this->container);
                 $data =  $instance->$action($afterMathFormId,$tableName);
                 if($data['status']){
                     $feedback = $data['message'];
                 }

                // $afterMathController=$this->getServiceLocator()->get($afterMath['controller']);

             }

            $returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
            $address = "{$GLOBALS['rootdir']}/patient_file/encounter/$returnurl";
         //   echo "\n<script language='Javascript'>top.restoreSession();if(typeof(top.frames['RBot'])!='undefined'){top.frames['RBot'].location.href='$address'}else{window.parent.parent.location='$address';};</script>\n";
            //echo "\n<script language='Javascript'>top.restoreSession();top.frames['RBot'].location.href='$address';</script>\n";


            return array('address' => $address, 'feedback' => $feedback);
        } else {
            $status = 'failed';
            //throw new \Exception("server side validation - not valid content");
            echo "<div class='bt-alert bt-alert-danger'>" ."Couldn't save data couldn't validate fields"."</div>";
            exit ;
        }

    }

    public function GetValue($field,$value,$couchDB,$valSql){
        $jsonElement = json_decode($value);
        $keys = array_keys((array)$jsonElement);
        $translatedValue = "";
        switch ($keys[0]){
            case "value":
                    if(trim(((array)$jsonElement)['value'])==""){
                        //This means That I want the value from couchDB
                        $optionsArray = $couchDB['fields'][$field]['options']['value_options'];
                        $translatedValue = xl($optionsArray[$valSql]);

                    }


                  break;
            case "list_options":
                $params = (array)$jsonElement;
                $translatedValue = xl($this->getCustomDB()->getValueFromList($valSql,$params['list_options'][1],$params['list_options'][0]));
                break;
            case "table":
                $params = (array)$jsonElement;
                $translatedValue = xl($this->getCustomDB()->getValueFromTable($params['table'][0],$params['table'][1],$params['table'][2],$valSql  ));

                break;

        }
        return $translatedValue;
    }

    public function reportAction()
    {

        $form_name = $this->params()->fromQuery('form');
        $acl = $this->getCustomDB()->getFormsAclFromRegistry($form_name);
        $acl = explode('|',$acl);
        if(!acl_check($acl[0], $acl[1],false,'view')){
           exit();
        }

        /* get parms from report get*/
        $parms=(array)$this->getRequest()->getQuery();
        $customDBHandler=$this->getCustomDB();
        $couchDBConnection=new CouchDBHandle();
        $formStructure=$couchDBConnection->getDocument($parms['form']);
        /* get the structure of the form */
        if(!strpos($parms['form'],"form_")) {
            $parms['form']=$this->AlterFormPrefixName($parms['form']);
        }
        /* get data from a spesific table*/
        $sqlResualt=$customDBHandler->getSqlReportTable($parms);
        $tableFieldCounter=count($sqlResualt)-self::SQL_MUST_PARM_AMOUNT;
        if($tableFieldCounter>0) {
            $form_fields = $formStructure->body['document']['fields'];
            //TODO handle multipe tabeles

            if($form_fields) {

                $fields_table_report_view= $formStructure->body['document']['fields_table_report_view'];

                if($fields_table_report_view){
                    $form_data_fields = array_keys($fields_table_report_view);
                }else{
                    $form_data_fields = array_keys($formStructure->body['document']['fields_table']);
                }

                $raw_table = [];
                $customDBHandler->validationsInit($form_fields, $form_data_fields);
                /*loop to fetch labels*/
                if ($form_data_fields) {
                    foreach ($form_data_fields as $key => $field) {
                        $lable = $form_fields[$field]['options']['label'];

                        if(is_array($fields_table_report_view[$field]) && isset($fields_table_report_view[$field]['concat']))
                        {
                            $raw_table[$lable] .= $sqlResualt[$field]." " .  $this->translate->z_xlt($sqlResualt[$fields_table_report_view[$field]['concat']]);
                            continue;
                        }



                        if($lable==''){
                            if ($field=='user_name' )
                            {
                                $sqlResualt[$field]=$customDBHandler->getUserName($sqlResualt[$field]);
                            }
                            $lable=$this->translate->z_xlt(str_replace("_", " ", $field));
                        }
                        if( isset($form_fields[$field]['type']) && $form_fields[$field]['type']=='checkbox') {
                            if($this->isJson($fields_table_report_view[$field])){
                                $valTemp = json_decode($fields_table_report_view[$field]) ;
                                $valJson = "";
                                $title="";
                                foreach($valTemp as $key=>$val){

                                    switch($key){
                                        case "title":
                                            $title = $val;
                                            break;
                                        case "value":
                                            $valJson = $sqlResualt[$val];
                                            break;
                                    }

                                }
                                if(!$valJson) continue;

                                $raw_table[xlt($lable)." : ".$valJson] = $sqlResualt[$field]=="1"?xlt("Yes"):xlt("No");
                                continue;
                            }
                            elseif($sqlResualt[$field]==1) {
                                $raw_table[$lable] =  $this->translate->z_xlt('checked');
                            }

                        } else if ( $form_fields[$field]['type']=='select'){

                            if(isset($form_fields[$field]['attributes']['list'])){

                                $listValues=$this->getCustomDB()->getListParamsByOptionId($form_fields[$field]['attributes']['list']);
                                $key = trim($sqlResualt[$field]);
                                if($form_fields[$field]['attributes']['list']=="country"){
                                    $keys = explode("_", $key);
                                    $key=$keys[1];

                                }
                                $titleValue=$listValues[$key]['title'];
                                $raw_table[$lable] =$this->translate->z_xlt($titleValue);
                            }

                        }else if ( $form_fields[$field]['attributes']['IsUser']=='true'){

                            $raw_table[$lable] =$customDBHandler->getUserFnameLname($sqlResualt[$field]);

                        }
                        else {

                            if (is_array($fields_table_report_view[$field])){
                                $controller = $fields_table_report_view[$field]['controller'];
                                $action =$fields_table_report_view[$field]['action'];
                                $lable=$fields_table_report_view[$field]['label'];

                                if ($controller!="" && $action!=""){
                                    $instance = new $controller($this->container);
                                    $sqlResualt[$field]=  $instance->$action($sqlResualt[$field]);
                                }

                                if ($lable!="" && $lable!=null){
                                    $sqlResualt[$lable]=$sqlResualt[$field];
                                    unset($sqlResualt[$field]);
                                    $field=$lable;
                                }

                            }

                            $fieldInReportView = $fields_table_report_view[$field];
                            if($this->isJson($fieldInReportView)){

                                $value =  $this->GetValue($field,$fields_table_report_view[$field],$formStructure->body['document'],$sqlResualt[$field]);
                                $title="";
                                if($this->isJson($fields_table_report_view[$field])){
                                    $hasTitle=json_decode($fields_table_report_view[$field]);
                                    if(array_key_exists ('title',$hasTitle)){
                                        if($field == "date")
                                        {
                                            $value = oeFormatDateTime(date(explode(" ",$sqlResualt[$field])[0]),false,false);
                                        }elseif(FormhandlerController::str_contains("date",$field)) {
                                            $value = oeFormatDateTime(date($sqlResualt[$field]));
                                        }
                                        else{
                                            $from_db=json_decode($fields_table_report_view[$field]);
                                            if($from_db->from_db){
                                                $table = $from_db->from_db->table;
                                                $column =  $from_db->from_db->column;
                                                $from_list = $from_db->from_db->from_list;

                                                $value = $this->getCustomDB()->selectDataFromDB($table,$column,null,"pid=".$sqlResualt['pid'])[0][$column];
                                                $value = xlt($this->getCustomDB()->getListParamsByOptionIdEx($from_list,$value,true, 'title'));
                                            }
                                            else {
                                                $value = $sqlResualt[$field];
                                            }
                                        }
                                        $title=xlt($hasTitle->title);
                                        $lable = $title!=""?$title:$lable;
                                    }

                                }


                                $raw_table[$lable] = $value;
                            }
                            else {
                                if ($this->isJson($sqlResualt[$field])) {
                                    $raw_table[$lable] = $this->htmlTableJsonParser($sqlResualt[$field]);
                                } else {
                                    $bbOCode = $sqlResualt[$field];
                                    $bbOCode = str_replace("\r\n", "[!%newline%!]", $bbOCode);

                                    // if ($form_name=="medical_anamnesis") { }
                                    $specialWords = array("Is Clear",
                                        "Unclear",
                                        "Matches",
                                        "Doesn't Match",
                                        "Proper",
                                        "Improper",
                                        "trip",
                                        "business",
                                        "errand",
                                        "other",
                                        "yes",
                                        "no",
                                        "true",
                                        "false"
                                    );


                                    if (in_array($bbOCode, $specialWords)) {
                                        $bbOCode = $this->translate->z_xlt($bbOCode);
                                    }


                                    /*** case danger assesment form replace values of radio btns***/
                                    //if ($form_name=="danger_assessment_scoring"){}
                                    switch ($bbOCode) {
                                        case "zero":
                                            $bbOCode = "0";
                                            break;
                                        case "one":
                                            $bbOCode = "1";
                                            break;
                                        case "two":
                                            $bbOCode = "2";
                                            break;
                                        default:
                                            $bbOCode = $bbOCode;
                                    }

                                    /**************************************************************/

                                    $raw_table[$lable] = xl($sqlResualt[$field]);

                                    $raw_table[$lable] = str_replace("[!%newline%!]", "<br/>", $bbOCode);
                                }
                            }
                        }
                        // delete empty fields , to see all fields replace $filtered_table with $raw_table
                        //create_function deprecated changed on 12/06/2019
                        //$filtered_table=array_filter($raw_table, create_function('$value', 'return $value != "";'));
                        $callback = function ($value) {
                            return $value != "";
                        };
                        $filtered_table=array_filter($raw_table, $callback);


                    }
                    $output = $this->htmleTableWrapper($filtered_table);
                } else {

                    $output = $this->htmleTableWrapper([]);
                }
            }
            else{
                $output ="<div class='bt-alert bt-alert-danger' role='alert'>
                            <strong>Warning!</strong> The couch DB isn't configured with fields Table.
                        </div>";
            }
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($output);
            return $response;
        }
        else{

            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent(null);
            return $response;
        }
        //$this->responseWithNoLayout ($output);
    }

    public function reportAction2()
    {
      /* get parms from report get*/
      $parms=(array)$this->getRequest()->getQuery();
      $customDBHandler=$this->getCustomDB();
      $couchDBConnection=new CouchDBHandle();
      /* get the structure of the form */
       // if(!strpos($parms['form'],"form_")<0) {
          //  $parms['form']="form_".$parms['form'];
       // }
      $formStructure=$couchDBConnection->getDocument($parms['form']);
      /* get data from a spesific table*/
      $sqlResualt=$customDBHandler->getSqlReportTable($parms);
      $tableFieldCounter=count($sqlResualt)-self::SQL_MUST_PARM_AMOUNT;
        if($tableFieldCounter>0) {
            /*    $form_fields = $formStructure->body['document']['fields'];
                //TODO handle multipe tabeles

                if($form_fields) {
                    $form_data_fields = array_keys($formStructure->body['document']['fields_table']);
                    $raw_table = [];
                    foreach ($form_data_fields as $key=>$value)
                    {
                        if(!isset($form_fields[$value]))
                            $form_fields[$value]=array(
                                'name' => 'text',
                                'attributes' => array( "validators"=>array(),
                                    'type' => 'text',
                                    'placeholder' => 'Text input',
                                    'id' => 'text'
                                ),
                                'options' => array(
                                    'label' => 'free text',
                                    'class' => 'form-control')
                            );
                    }
                    $customDBHandler->validationsInit($form_fields, $form_data_fields);
                    //loop to fetch labels
                    if ($form_data_fields) {
                        foreach ($form_data_fields as $key => $field) {
                            $lable = $form_fields[$field]['options']['label'];
                            $raw_table[$lable] = $sqlResualt[$field];
                            // delete empty fields , to see all fields replace $filtered_table with $raw_table
                            $filtered_table=array_filter($raw_table, create_function('$value', 'return $value != "";'));
                        }
                        $output = $this->htmleTableWrapper($filtered_table);
                    } else {

                        $output = $this->htmleTableWrapper([]);
                        }*/
            $output="<table border='1'>";
            $output.="<tbody>";
            unset($sqlResualt['encounter']);
            unset($sqlResualt['groupname']);
            unset($sqlResualt['authorized']);
            unset($sqlResualt['user']);
            unset($sqlResualt['id']);
            unset($sqlResualt['pid']);
            unset($sqlResualt['date']);

                foreach ($sqlResualt as $key => $value) {


                    $output.="<tr>";
                    $output.="<td>".$key."</td>"."<td>".$value."</td>";
                    $output.="</tr>";
                }
            $output.="</tbody>";
            $output.="</table>";
            }
            else{
                $output ="<div class='bt-alert bt-alert-danger' role='alert'>
                            <strong>Warning!</strong> The couch DB isn't configured with fields Table.
                        </div>";
            }
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->setContent($output);
            return $response;
        }



    public function getFormSessionParm()
    {
        $form_parm['pid']=$_SESSION["pid"];//pid
        $form_parm['encounter']=$_SESSION["encounter"];//encounter
        $form_parm['authProvider']=$_SESSION["authProvider"];//groupname
        $form_parm['authUser']=$_SESSION["authUserID"];//user
        $form_parm['authorized']= empty($_SESSION['userauthorized']) ? 0 : $_SESSION['userauthorized'];//authorized
        $form_parm['date']= date("Y-m-d H:i:s");
        return $form_parm;
    }

    public function generatePreviewAction()
    {

        //TODO: translate all labels
        //adding js nd css for this method
        $this->getJsFiles(__METHOD__);
        $this->getCssFiles(__METHOD__);


        $form=null;
        $elements=(array)json_decode($this->params()->fromPost('couchElement'));
        $form =  new \Zend\Form\Form("Preview");

        $arrayOfFields=json_decode(json_encode($elements[self::DOCUMENT]->fields),true);

        $arrayOfGroups=json_decode(json_encode($elements[self::DOCUMENT]->groups),true);
        $conditions=json_decode(json_encode($elements[self::DOCUMENT]->conditions),true);
        $form_title =json_decode(json_encode($elements[self::DOCUMENT]->_id),true);
        $JSAddons=json_decode(json_encode($elements[self::DOCUMENT]->JSAddons),true);
        $PHPAddons=json_decode(json_encode($elements[self::DOCUMENT]->PHPaddons),true);




        unset($arrayOfFields['undefined']);
       foreach ($arrayOfFields as $key => $controllers) {
           try {


                                    foreach($controllers as $keyInner=>$valueInner)
                                            {
                                                if ($keyInner=="options")

                                                    foreach($valueInner as $keyInnerInner=>$valueInnerInner)
                                                        if ($keyInnerInner=="value_options") {
                                                            $controllers[$keyInner][$keyInnerInner] =explode(",",$valueInnerInner);
                                                        }
                                             }



         //      $arrayOfFields["options"]["value_options"]=explode(",",$arrayOfFields["options"]["value_options"]);
                $elementArray=$controllers;
               $elementArray = $this->translateControlElement($elementArray);
               $form->add($elementArray);
           }
           catch(\Error $err)
           {
               return  $this->responseWithNoLayout(array("status"=>"failed : ".$err),true,200);
           }
           /*array(
               'name' => 'submit',
               'type' => 'Submit',
               'attributes' => array(
                   'value' => 'Go',
                   'id' => 'submitbutton',
               ),
           ));
           $form->add(array(
               'name' => 'id',
               'type' => 'Hidden',
           ));
           $form->add(array(
               'name' => 'title',
               'type' => 'Text',
               'options' => array(
                   'label' => 'Title',
               ),
           ));
           $form->add(array(
               'name' => 'artist',
               'type' => 'Text',
               'options' => array(
                   'label' => 'Artist',
               ),
           ));
           $form->add(array(
               'name' => 'submit',
               'type' => 'Submit',
               'attributes' => array(
                   'value' => 'Go',
                   'id' => 'submitbutton',
               ),
           )*/
       }

        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('FormhandleJSFiles', $this->FormhandleJSFiles);
        $this->layout()->setVariable('FormhandleCSSFiles', $this->FormhandleCSSFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);


        return array("status"=>"ok","message"=>"form can be created visualy",'groups'=>$arrayOfGroups,'form' => $form,"conditions"=>$conditions,'form_title'=>$form_title ,'translate' => $this->translate,'JSAddons'=>$JSAddons,'PHPAddons'=>$PHPAddons);

    }

   private function isJson($string) {


       if (is_object(json_decode($string)) || is_array(json_decode($string))){
           return true;
       } else {
           return false;
       }




    }
    public function translateControlElement($controller)
    {
       if($controller[self::ATTRIBUTES]) {
           if ($controller[self::ATTRIBUTES][self::PLACEHOLDER])
               $controller[self::ATTRIBUTES][self::PLACEHOLDER] = $this->translate->z_xlt($controller[self::ATTRIBUTES][self::PLACEHOLDER]);
       }

       if($controller[self::OPTIONS]) {
            if ($controller[self::OPTIONS][self::LABEL])
                $controller[self::OPTIONS][self::LABEL] = $this->translate->z_xlt($controller[self::OPTIONS][self::LABEL]);
            if ($controller[self::OPTIONS][self::VALUE_OPTIONS])
                foreach ($controller[self::OPTIONS][self::VALUE_OPTIONS] as $key => $value) {
                    $controller[self::OPTIONS][self::VALUE_OPTIONS][$key] = $this->translate->z_xlt($value);
                }
        }
        return $controller;
    }
    /*
     * example for simple method
     * */
    public function indexAction() {

        $sqlResualt="";
        $form_name = $this->params()->fromQuery('form');
        $acl = $this->getCustomDB()->getFormsAclFromRegistry($form_name);
        $acl = explode('|',$acl);
        if(!acl_check($acl[0], $acl[1],false,'write')){
            return $this->redirect()->toRoute('errors', array('action' => 'access-denied'));
        }

        //adding js nd css for this method
        $this->getJsFiles(__METHOD__);
        $this->getCssFiles(__METHOD__);

        $couchDBConnection = new CouchDBHandle();
        $form_name = $this->params()->fromQuery('form');
        $JSAddons = null;
        $PHPAddons = null;

        /*check if this is new or edit mod*/
        $notValidate = $this->params()->fromQuery('notValidate')=== 'true'? true: false;
        $edit = $this->params()->fromQuery('edit')=== 'true'? true: false;
        $id = $this->params()->fromQuery('id');

        $formShowLabelsIds = false;
        $patientDataFlag = false;

        $validators=$this->params()->fromQuery('validate');

        $forms = $couchDBConnection->getDocument($form_name);
        $returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
        // not create new frame in cancel button
        //$returnurl = 'forms.php';
        $address = "{$GLOBALS['rootdir']}/patient_file/encounter/$returnurl";

        if($forms) {

            $form_params = $forms->body;

            //TODO: make globals from document,label,_id,sql,draft,validation,conditional

            $form_title = $this->translate->z_xlt($form_params[self::DOCUMENT][self::LABEL]);
            $form =  new \Zend\Form\Form($form_params[self::ID]);

            $url=$this->getBasePath()."/save/".$form_name;
            $form->setAttribute('action', $url);

            $formId =$form_params[self::ID];
            $arrayOfFields=$form_params[self::DOCUMENT][self::FIELDS];
            $arrayOfGroups=$form_params[self::DOCUMENT][self::GROUPS];

            if ($form_params[self::DOCUMENT]['labelPrefix']) {
                $formShowLabelsIds = true;
            }

            if($edit) /* edit form */
            {
                $customDBHandler=$this->getCustomDB();

                if($notValidate){
                    $sqlResualt=(array)json_decode($this->params()->fromQuery('params'));
                    $url=str_replace("index","save",$this->getRequest()->getUri()->toString());
                    $form->setAttribute('action', $url);
                }
                else {
                    $parms = $this->getRequest()->getQuery();
                    $parms['form']=$this->AlterFormPrefixName($parms['form']);
                    $sqlResualt = $customDBHandler->getSqlReportTable($parms);

                    if ($form_name==self::DAS_FORM) {

                        $das_parms = array(
                            'form' => self::DAS_FORM,
                            'pid' => $_SESSION['pid'],
                            'id'=>"",
                            'encounter'=>""
                        );

                        $das_parms['form'] = $this->AlterFormPrefixName($das_parms['form']);


                        //$sqlRetFirst=$customDBHandler->getFirstFormNotDeleted($parms['pid'],self::DAS_FORM);
                        $sqlRetFirst=$customDBHandler->getLastFormNotDeleted($parms['pid'],self::DAS_FORM,$sqlResualt['id'],$sqlResualt['date']);
                        $sqlResLast=$customDBHandler->getLastFormNotDeleted($parms['pid'],self::DAS_FORM,$sqlResualt['id'],$sqlResualt['date']);

                        if ($sqlRetFirst){
                            $das_parms['id']=$sqlRetFirst['form_id'];
                            $das_parms['encounter']=$sqlRetFirst['encounter'];
                        }
                        else{
                            $das_parms['id']="0";
                            $das_parms['encounter']="0";
                        }

                        $das_sqlResualt = $customDBHandler->getSqlReportTable($das_parms);

                        if ($sqlResLast){
                            $das_parms['id']=$sqlResLast['form_id'];
                            $das_parms['encounter']=$sqlResLast['encounter'];
                        }
                        else{
                            $das_parms['id']="0";
                            $das_parms['encounter']="0";
                        }

                        $das_last_sqlResualt = $customDBHandler->getSqlReportTable($das_parms);
                    }

                }
                foreach ($arrayOfFields as $key => $controllers) {
                    if ($sqlResualt[$key])
                    {
                        $controllers[self::ATTRIBUTES]['value']=$sqlResualt[$key];
                    }
                    if($sqlResualt[$key] == "0" && $controllers['type']=="checkbox")
                    {
                        $controllers[self::ATTRIBUTES]['value']=$sqlResualt[$key];
                    }
                    if($sqlResualt[$key] == "" && $controllers['type']=="checkbox")
                    {
                        $controllers[self::ATTRIBUTES]['value']=0;
                    }
                    if ($form_name==self::DAS_FORM){
                           if ($das_sqlResualt!=null) {  // note that if there is no first there is no last
                               $filter_flag = in_array($key, $arrayOfGroups['Historic']);
                               if ($das_sqlResualt[$key] && $filter_flag) {
                                   $controllers[self::ATTRIBUTES]['value'] = $das_sqlResualt[$key];
                               }
                               else if ($key=="previous_intervention_plan" && $das_last_sqlResualt!=null){
                                   if ($das_last_sqlResualt['intervention_plan']){
                                       $controllers[self::ATTRIBUTES]['value'] = $das_last_sqlResualt['intervention_plan'];
                                   }

                               }
                           }
                    }

                    //--------------------------------------------------
                    // Make sure the date types have a current date
                    // as a default value - {{current_date}} macro
                    // required
                    //--------------------------------------------------
                    $controllers = $this->parseEncounterDate($controllers);

                    $controllers = $this->replaceMacros($controllers);
                    $controllers = $this->translateControlElement($controllers);
                    $form->add($controllers);
                    if ($controllers[self::ATTRIBUTES][self::REQUIRED]) {
                        $this->addCustomClientSideValidation($controllers[self::NAME], "require");
                    }
                    /**********************************************************************************/
                    $customeValidationArr=$controllers[self::ATTRIBUTES][self::VALIDATORS];
                    if ($customeValidationArr!=null&&is_array($customeValidationArr)) {
                        foreach($customeValidationArr as $keyName => $validatorsRules){
                            foreach($validatorsRules as $key => $rule) {
                                $this->addCustomClientSideValidation($controllers[self::NAME], $rule,$sqlResualt);
                            }
                        }
                    }
                    /**********************************************************************************/
                    if ($controllers[self::ATTRIBUTES]["id"] == 'button_submit') {
                        $submitButtonId = $controllers[self::ATTRIBUTES][self::INPUT_ID];
                    }
                }
                $form->add(array("name"=>"id","type"=>"hidden","attributes"=>array("value"=>$id)));
            }
            else {  /* new form */

                $insdieConditions = array();
                $radioBtnsLastId = "";


                foreach ($arrayOfFields as $key => $controllers) {

                    if (strpos($key, 'label') === false && strpos($key, 'title') === false) {
                        //echo '"'.$key.'": "form_asi",<br>';
                        //echo $key.", ";
                        //echo $key." text default NULL,";
                    }

                    //--------------------------------------------------
                    // Add inside conditions array to the global
                    // conditions array
                    //--------------------------------------------------

                    if (!empty($controllers['conditions'])) {
                        foreach ($controllers['conditions'] as $v)
                            $insdieConditions[] = array( $key => $v );
                    }

                    if ($controllers[self::ATTRIBUTES]['type'] == "radio") {
                        if ($radioBtnsLastId != $controllers['name']) {
                            $controllers[self::ATTRIBUTES]['checked'] = "checked";
                            $radioBtnsLastId = $controllers['name'];
                        }
                    }

                    //--------------------------------------------------
                    // Do we need to show the label prefix?
                    //--------------------------------------------------

                    if ( ($controllers['type'] != "button") &&
                         ( ($controllers['options']['showLabelId'] == "true") || ( $controllers['options']['showLabelId'] != "false" && $formShowLabelsIds == true) )) {
                        $questionPrefix = str_replace('moh_', '', $controllers['name']);
                        $questionPrefix = str_replace('_', '-', $questionPrefix);
                        $controllers['options']['label'] = $questionPrefix .'. '. $controllers['options']['label'];
                    }

                    //--------------------------------------------------
                    // Make sure the date types have a current date
                    // as a default value - {{current_date}} macro
                    // required
                    //--------------------------------------------------
                    $controllers = $this->parseEncounterDate($controllers);

                    //--------------------------------------------------
                    // Load input macro values from db and replace
                    // the macros
                    //--------------------------------------------------

                    $controllers = $this->replaceMacros($controllers);

                    $controllers = $this->translateControlElement($controllers);
                    $form->add($controllers);

                    if ($controllers[self::ATTRIBUTES][self::REQUIRED]) {
                        $this->addCustomClientSideValidation($controllers[self::NAME], "require");
                    }

                    /**********************************************************************************/
                    $customeValidationArr=$controllers[self::ATTRIBUTES][self::VALIDATORS];
                    if ($customeValidationArr!=null&&is_array($customeValidationArr)) {
                        foreach($customeValidationArr as $keyName => $validatorsRules){
                            foreach($validatorsRules as $key => $rule) {
                                $this->addCustomClientSideValidation($controllers[self::NAME], $rule,$sqlResualt);
                            }
                        }
                    }
                    /**********************************************************************************/
                    if ($controllers[self::ATTRIBUTES]["id"] == 'button_submit') {
                        $submitButtonId = $controllers[self::ATTRIBUTES][self::INPUT_ID];
                    }
                }
            }


            if ($form_name==self::DAS_FORM && !$edit) {


                $customDBHandler = $this->getCustomDB();
                $parms = array(
                    'form' => self::DAS_FORM,
                    'pid' => $_SESSION['pid'],
                    'id'=>"",
                    'encounter'=>""
                );

                $parms['form'] = $this->AlterFormPrefixName($parms['form']);

                //$sqlRetFirst=$customDBHandler->getFirstFormNotDeleted($parms['pid'],self::DAS_FORM);
                $sqlRetFirst=$customDBHandler->getLastFormNotDeleted($parms['pid'],self::DAS_FORM);
                $sqlResLast=$customDBHandler->getLastFormNotDeleted($parms['pid'],self::DAS_FORM);

                if ($sqlRetFirst){
                    $parms['id']=$sqlRetFirst['form_id'];
                    $parms['encounter']=$sqlRetFirst['encounter'];
                }

                $sqlResualt = $customDBHandler->getSqlReportTable($parms);


                if ($sqlResualt!= null) {

                        foreach ($arrayOfFields as $key => $controllers) {

                            $filter_flag = in_array($key, $arrayOfGroups['Historic']);
                            if ($sqlResualt[$key] && $filter_flag) {
                                $controllers[self::ATTRIBUTES]['value'] = $sqlResualt[$key];
                            }
                            else if ($key=="previous_intervention_plan"){
                                if ($sqlResLast){
                                    $parms['id']=$sqlResLast['form_id'];
                                    $parms['encounter']=$sqlResLast['encounter'];
                                }
                                $sqlResualtLast = $customDBHandler->getSqlReportTable($parms);
                                if ($sqlResualtLast['intervention_plan']){
                                    $controllers[self::ATTRIBUTES]['value'] = $sqlResualtLast['intervention_plan'];
                                }
                            }

                            //--------------------------------------------------
                            // Make sure the date types have a current date
                            // as a default value - {{current_date}} macro
                            // required
                            //--------------------------------------------------
                            $controllers = $this->parseEncounterDate($controllers);

                            $controllers = $this->replaceMacros($controllers);
                            $controllers = $this->translateControlElement($controllers);
                            $form->add($controllers);
                            if ($controllers[self::ATTRIBUTES][self::REQUIRED]) {
                                $this->addCustomClientSideValidation($controllers[self::NAME], "require");
                            }
                            //**********************************************************************************
                            $customeValidationArr = $controllers[self::ATTRIBUTES][self::VALIDATORS];
                            if ($customeValidationArr != null && is_array($customeValidationArr)) {
                                foreach ($customeValidationArr as $keyName => $validatorsRules) {
                                    foreach ($validatorsRules as $key => $rule) {
                                        $this->addCustomClientSideValidation($controllers[self::NAME], $rule, $sqlResualt);
                                    }
                                }
                            }
                            //**********************************************************************************
                            if ($controllers[self::ATTRIBUTES]["id"] == 'button_submit') {
                                $submitButtonId = $controllers[self::ATTRIBUTES][self::INPUT_ID];
                            }
                        }

                }

            }

            $this->excuteClientSideValidation();
            if($form_params[self::JSADDONS])
                $JSAddons =$form_params[self::JSADDONS];
            if($form_params[self::PHPADDONS]) {
                $PHPAddons = $form_params[self::PHPADDONS];

                foreach ($PHPAddons as $key => $phpRow) {
                    if($_SESSION['pid'])
                        $str=str_ireplace('$patient_id_DB', $_SESSION['pid'], $phpRow);

                    if($_SESSION['authUserID'])
                        $str=str_ireplace('$user_id_DB', $_SESSION['authUserID'], $str);

                    if($_SESSION['encounter'])
                        $str = str_ireplace('$encounter_id', $_SESSION['encounter'], $str);

                    if(!is_null($id)) {
                        $str = str_ireplace('$id', $id, $str);
                        $str = str_ireplace('$encounter_form_id', $id, $str);
                    } else {
                        $str = str_ireplace('$id', '\'\'', $str);
                        $str = str_ireplace('$encounter_form_id', '\'\'', $str);
                    }

                    if($_SESSION['encounter'])
                        $str = str_ireplace('$encounter', $_SESSION['encounter'], $str);

                    $PHPAddons[$key] = $str;
                }
            }

            if($form_params[self::CONDITIONS]) {
                $conditions = $form_params[self::CONDITIONS];

                if ( !empty($insdieConditions) )
                    $conditions = array_merge($conditions, $insdieConditions);
            }
        }
        else{
            die("couldn't retrieve element from CouchDB");
        }


        $gender=$this->getCustomDB()->getCurrentUserGender();
        $patientInfo=array('pid'=>$_SESSION['pid'],'gender'=>$gender);

        $saveAndPrint= ($form_params[self::SAVEANDPRINT]==='true') ? 'true': 'false';

        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('FormhandleJSFiles', $this->FormhandleJSFiles);
        $this->layout()->setVariable('FormhandleCSSFiles', $this->FormhandleCSSFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);
        $this->layout()->setVariable('submitButtonId',$submitButtonId);
        $this->layout()->setVariable('formId',$formId);

       // return array('validationLog'=>$validators ,'address'=> $address,'form' => $form,"conditions"=>$conditions,'form_title'=>$form_title ,'translate' => $this->translate,'JSAddons'=>$JSAddons);



        return array('validationLog'=>$validators,
                     'groups'=>$arrayOfGroups,
                     'address'=> $address,
                     'form' => $form,
                     "conditions"=>$conditions,
                     'form_title'=>$form_title ,
                     'form_name'=>$form_name ,
                     'translate' => $this->translate,
                     'JSAddons'=>$JSAddons,
                     'PHPAddons'=>$PHPAddons,
                     'patientInfo'=>$patientInfo,
                     'saveAndPrint'=>$saveAndPrint,
                     'date' => ($edit && !$notValidate ? strtotime(date("Y-m-d", strtotime(trim($sqlResualt["date"])))) : strtotime(date("Y-m-d")) )
                    );


    }

    private function replaceMacros($controllers){
        $text = $controllers[self::ATTRIBUTES]['value'];
        preg_match_all('#\{\{(.*?)\}\}#', $text, $match);

        if (isset($match[1])) {

            foreach ($match[1] as $v) {
                $macro = explode(".", $v);
                $table = trim($macro[0]);
                $field = trim($macro[1]);

                switch($table) {
                    case 'patient_data':
                    case 'moh_patient_status_history':
                        $dataId = 'pid='.$_SESSION['pid'];
                        break;
                    case 'users':
                        $dataId = 'id='.$_SESSION['authUserID'];
                        break;
                }

                if($table == 'moh_patient_status_history' && $field == 'updated_date' && !array_key_exists($table, $this->arrayOfDbValues)){
                    $this->arrayOfDbValues[$table] = $this->dataToFields($table, 'max(updated_date) as updated_date ', $dataId.' GROUP BY pid');
                }
                elseif (!array_key_exists($table, $this->arrayOfDbValues)) {
                    $this->arrayOfDbValues[$table] = $this->dataToFields($table, '*', $dataId);
                }
                //convert to date format as global
                switch($field){
                    case 'DOB':
                    case 'date':
                        {
                            $this->arrayOfDbValues[$table][$field]=$this->ConvertDateAsGlobals($this->arrayOfDbValues[$table][$field]);
                        }
                        break;
                    case "status":
                        {
                            $status = $this->arrayOfDbValues[$table]['status'];
                            if($status!="") {
                                $status = $this->dataToFields("list_options", "title", "list_id='marital'  AND option_id ='" . $status . "'");
                                $this->arrayOfDbValues[$table]['status'] = xlt($status['title']);
                            }
                            break;
                        }
                    default :
                        {
                            /*if needed research this : */
                            /*  $this->arrayOfDbValues[$table]['status'] = xlt(str_replace(" ", "_", $this->arrayOfDbValues[$table]['status']));
                              $this->arrayOfDbValues[$table]['contact_relationship'] = xlt($this->arrayOfDbValues[$table]['contact_relationship']);
                              $this->arrayOfDbValues[$table]['sex'] = xlt($this->arrayOfDbValues[$table]['sex']);
                              $this->arrayOfDbValues[$table]['deceased_reason'] = xlt($this->arrayOfDbValues[$table]['deceased_reason']);
                              $this->arrayOfDbValues[$table]['mh_passport_country'] = xlt($this->arrayOfDbValues[$table]['mh_passport_country']);*/
                            break;
                        }
                }

                if (array_key_exists($field, $this->arrayOfDbValues[$table]))
                    $controllers[self::ATTRIBUTES]['value'] = str_replace('{{'. $v .'}}', $this->translate->z_xlt($this->arrayOfDbValues[$table][$field]), $controllers[self::ATTRIBUTES]['value']);
            }
        }
        return $controllers;
    }
    public function ConvertDateAsGlobals($date){
        return oeFormatShortDate($date,true);
    }

    public function patientDataToFields() {
        $customDBHandler=$this->getCustomDB();

        return $customDBHandler->selectPatientDataFromDB('patient_data', '*', $_SESSION['pid']);
    }

    public function dataToFields($table, $fields, $id) {
        $customDBHandler=$this->getCustomDB();
        return $customDBHandler->selectFromDB($table, $fields, $id);
    }

     /*    $validators
     * get Model, before you use in this method you must add configuration to Model.php (at getServiceConfig())
     */
    public function getExampleTable()
    {
        if (!$this->exampleTable) {
            //$sm = $this->contsainer;
            $this->exampleTable = $this->container->get('Formhandler\Model\ExampleTable');
        }
        return $this->exampleTable;
    }

    public function createFormAction(){
        $oForm = array();
        $oForm["email"]=array(
            'name' => 'email',
            'attributes' => array(
                                 'type' => 'email',
                                 'placeholder' => 'Enter email',
                                 'id' => 'exampleInputEmail1'
                                 ),
            'options' => array(
                'label' => 'Email address',
                'class' => 'form-control')
        );

        $oForm["password"]=array(
            'name' => 'password',
            'attributes' => array(
                                  'type' => 'password',
                                  'placeholder' => 'Password',
                                   'id' => 'exampleInputPassword1'
                                 ),
            'options' => array(
                'label' => 'Password',
                'class' => 'form-control')
        );

        $oForm["file"]=array(
            'name' => 'file',
            'attributes' => array(
                               'type' => 'file',
                               'id' => 'exampleInputFile'
                                 ),
            'options' => array(
                'label' => 'File input',
                'help-block' => 'Example block-level help text here.',
                'class' => 'form-control'
            )
        );

        $oForm["checkbox"]=array(
            'name' => 'checkbox',
            'attributes' => array(
                                   'id' => 'checkbox'
                                 ),
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Check me out',
                'class' => 'form-control')
        );

        $oForm["text"]=array(
            'name' => 'text',
            'attributes' => array( "validators"=>array(),
                           'type' => 'text',
                           'placeholder' => 'Text input',
                            'id' => 'text'
                                 ),
            'options' => array(
                'label' => 'free text',
                'class' => 'form-control')
        );

        $oForm["textarea"]=array(
            'name' => 'textarea',
            'type' => 'textarea',
            'attributes' => array(
                                'row' => 3,
                                 'id' => 'textarea'
                                ),
            'options' => array(
                'label' => 'free text',
                'class' => 'form-control')
        );

        $oForm["radio"]=array(
            'name' => 'optionsRadios',
            'attributes' => array(
                                 'id' => 'radio'

                                 ),
            'type' => 'radio',
            'options' => array(
                'label' => 'choose me',
                'value_options' => array(
                    'option1' => 'Yes',
                    'optionsRadios2' => 'No'
                ),
                'class' => 'form-control'
            )
        );

        $oForm["multicheckbox"]=array(
            'name' => 'optionsRadios',
            'attributes' => array(
                                  'id' => 'multicheckbox'
                                 ),
            'type' => 'MultiCheckbox',
            'options' => array(
                'label' => 'check me',
                'value_options' => array(
                    array('label' => '1','value' => 'option1', 'attributes' => array('id' => 'inlineCheckbox1')),
                    array('label' => '2','value' => 'option2', 'attributes' => array('id' => 'inlineCheckbox2')),
                    array('label' => '3','value' => 'option3', 'attributes' => array('id' => 'inlineCheckbox3'))
                ),
                'class' => 'form-control'
            )
        );

        $oForm["optionsRadiosNoInline"]=array(
            'name' => 'optionsRadiosNoInline',
            'attributes' => array(
                                'id' => 'optionsRadiosNoInline'
                                ),
            'type' => 'MultiCheckbox',
            'options' => array(
                'label' => 'check me',
                'value_options' => array(
                    array('label' => '1','value' => 'option1', 'attributes' => array('id' => 'noInlineCheckbox1')),
                    array('label' => '2','value' => 'option2', 'attributes' => array('id' => 'noInlineCheckbox2')),
                    array('label' => '3','value' => 'option3', 'attributes' => array('id' => 'noInlineCheckbox3'))
                ),
                'class' => 'form-control',
                'inline' => false
            )
        );

        $oForm["select"]=array(
            'name' => 'select',
            'attributes' => array(
                'id' => 'select'
            ),
            'type' => 'select',
            'options' => array(
                'label' => 'select me',
                'value_options' => array(1,2,3,4,5),
                'class' => 'form-control')
        );

        $oForm["multiple_select"]=array(
            'name' => 'multiple-select',
            'attributes' => array(
                                'multiple' => true,
                                 'id' => 'multiple_select'
                                 ),
            'type' => 'select',
            'options' => array(
                'label' => 'select me',
                'value_options' => array(1,2,3,4,5),
                'class' => 'form-control'),

        );

        $oForm["static"]=array(
            'name' => 'static-element',
            'attributes' => array(
                            'value' => 'email@example.com',
                             'id' => 'static'
                              ),
            'type' => '\TwbBundle\Form\Element\StaticElement',
            'options' => array(
                'label' => 'Email','column-size' => 'lg-10',
                'class' => 'form-control')
        );

        $oForm["static1"]=array(
            'name' => 'input-text-disabled',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Disabled input here...',
                'id' => 'disabledInput'
            ), 'options' => array(
                'label' => '',
                'class' => 'form-control')
        );

        $oForm["success"]=array(
            'name' => 'input-text-success',
            'attributes' => array(
                'type' => 'text',
                'id' => 'inputSuccess',

            ),
            'options' => array(
                'label' => 'Input with success',
                'validation-state' => 'success',
                'class' => 'form-control'
            )
        );

        $oForm["sizing"]=array(
            'name' => 'input-text-lg',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => '.input-lg',

                'id' => 'sizing'
            ), 'options' => array(
                'class' => 'input-lg form-control form-control')
        );

        $oForm["sizing1"]=array(
            'name' => 'input-text-sm',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => '.input-sm' ,
                'id' => 'sizing1'
            ), 'options' => array(
                'class' => 'input-lg form-control form-control')
        );

        $oForm["sizing2"]=array(
            'name' => 'lg-select',
            'type' => 'select',
            'options' => array(
                'value_options' => array('' => '.input-lg'),
                'class' => 'input-lg form-control form-control'),
            'attributes' => array(
                                 'class' => 'input-lg form-control',
                                 'id' => 'sizing2'
                                 )
        );

        $oForm["sizing3"]=array(
            'name' => 'default-select',
             'attributes' => array(
                                 'id' => 'sizing3'
                                 ),
            'type' => 'select',
            'options' => array(
                'value_options' => array('' => 'Default select'),   'class' => 'form-control')
        );

        $oForm["sizing4"]=array(
            'name' => 'sm-select',
            'type' => 'select',
            'options' => array(
                'value_options' => array('' => '.input-sm'), 'class' => 'input-sm form-control'),
            'attributes' => array(
                                'class' => 'input-sm form-control',
                                 'id' => 'sizing4'
                                )
        );
        $oForm["col_sizing"]=array(
            'name' => 'input-text-col-lg-2',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => '.col-lg-2',
                'id' => 'col_sizing'
            ),
            'options' => array('column-size' => 'lg-2',
                'class' => 'form-control')
        );

        $oForm["col_sizing1"]=array(
            'name' => 'input-text-col-lg-3',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => '.col-lg-3',
                 'id' => 'col_sizing1'
            ),
            'options' => array('column-size' => 'lg-3',
                'class' => 'form-control')
        );

        $oForm["col_sizing2"]=array(
            'name' => 'input-text-col-lg-4',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => '.col-lg-4',
                'id' => 'col_sizing2'
            ),
            'options' => array('column-size' => 'lg-4',
                'class' => 'form-control')
        );

        $oForm["button1"]=array(
            'label' => 'Primary button',
            'attributes' =>array(
                            'class' => 'btn-primary btn-lg form-control',
                             'disabled' => true,
                             'id' => 'button1'
                              ),
            'options' => array('class' => 'form-control')
        );

        $oForm["dropdown"]=array(
            'label' => 'Dropdown',
            'name' => 'dropdownMenu1',
            'attributes' => array(
                             'class' => 'clearfix form-control',
                              'id' => 'dropdown'
                               ),
            'list_attributes' => array('aria-labelledby' => 'dropdownMenu1'),
            'items' => array(
                'Action',
                'Another action',
                'Something else here',
                '\TwbBundle\View\Helper\TwbBundleDropDown::TYPE_ITEM_DIVIDER',
                'Separated link'
            ),
            'options' => array('class' => 'form-control')
        );

        //button bootstrap 3 - <button type="submit" class="btn btn-default">Submit</button>
        $oForm["button_submit"]=array(
           "name"=> "button-submit",
           "attributes"=> array(
                "id"=> "button_submit",
                "type"=>"submit"

           ),
           "type"=> "button",
           "options"=>array(
                "label"=> "save",
               "column-size"=> "sm-6  pull-right",
               "button-group"=> "group-1",
               "class"=>"btn btn-default form-control"
           )
       );

        $oForm["button_cancel"]=array(
            "name"=> "cancel",
           "type"=> "button",
           "attributes"=>array(
                "id"=> "button_cancel"
           ),
           "options"=>array(
                "label"=> "cancel",
               "column-size"=> "sm-6  pull-right",
               "button-group"=> "group-1",
               'class' => 'btn btn-default form-control'
           )
        );

        $oForm["label"]=array(
            'name' => 'label',
             'attributes' => array(
                "style"=>  "display:none",
                "type"=>  "text",
                "disabled"=>  "disabled",
                'id' => 'label'
            ),
            'options' => array(
            'label' => 'free text',
            'class' => 'form-control'
            )
        );


        //TODO: translate all labels
        //adding js nd css for this method
        $this->getJsFiles(__METHOD__);
        $this->getCssFiles(__METHOD__);
        $couchDBConnection=new CouchDBHandle();
        //$this->excuteClientSideValidation();

        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('FormhandleJSFiles', $this->FormhandleJSFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);
        return array("formElementsObjects"=>$oForm,"formElementsTypes"=>json_encode(array_keys($oForm)));
    }

    public function partition(Array $list, $p) {
        $listlen = count($list);
        if($p>0) {
            $partlen = floor($listlen / $p);
            $partrem = $listlen % $p;
            $partition = array();
            $mark = 0;
            for ($px = 0; $px < $p; $px++) {
                $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
                $partition[$px] = array_slice($list, $mark, $incr);
                $mark += $incr;
            }
            return $partition;
        }
        else{
            return [];
        }
    }

    public function htmlTableJsonParser($Json){
        $data =  json_decode($Json);
         //count row break param for each tr
        $name=$data[0]->name;
        $returnedTable="<table border='1'>";
        // Output a row
        $returnedTable.="<thead>";


        $counter=0;

        foreach($data as $val) {


            $value=$val->name;
            $name_translated=str_replace("moh_","",$value);
            $name_translated=str_replace("_"," ",$name_translated);


            //Find the translation
            if($this->translate->z_xlt($name_translated)!=$name_translated)
            {
                $name_translated=$this->translate->z_xlt($name_translated);
            }
            else {
                if ($this->translate->z_xlt(ucfirst($name_translated)) != ucfirst($name_translated)) {
                    $name_translated = $this->translate->z_xlt(ucfirst($name_translated));
                }
                else{

                    if ($this->translate->z_xlt(str_replace(" ","",$name_translated)) != str_replace(" ","",$name_translated)) {
                        $name_translated = $this->translate->z_xlt(str_replace(" ","",$name_translated));
                    }
                    else{
                        if ($this->translate->z_xlt(str_replace(" ","",ucfirst($name_translated))) != ucfirst(str_replace(" ","",$name_translated)))
                        {
                            $name_translated = $this->translate->z_xlt(str_replace(" ","",ucfirst($name_translated)));
                        }
                    }
                }
            }


            $value=$val->name;

            if($val->name!=$name)
            {

                    $returnedTable .= "<th>" . $name_translated . "</th>";

            }
            else{
                if($counter>0) {
                    break;
                }
                else{

                        $returnedTable .= "<th>" . $name_translated . "</th>";

                    $counter++;
                }

            }
        }
        $returnedTable.="</thead>";


        $returnedTable.="<tbody>";

         $tableArray=array();
        $counter=0;
        $fieldLists=DrugAndAlcoholUsageTable::getFieldLists();
        foreach($data as $val) {
            if(in_array($val->name,$fieldLists)){
                $listVals=$this->getCustomDB()->getListParamsByOptionId($val->name);
                $value = $listVals[$val->value]['title'];
            } else {
                $value = $val->value;
            }
            if($val->name==$name){

                $counter++;
                if(!is_array($tableArray[$counter]))
                    $tableArray[$counter]=array();
                if($this->translate->z_xlt(ucfirst($value))!=ucfirst($value)) {
                    array_push($tableArray[$counter], $this->translate->z_xlt(ucfirst($value)));
                }
                else{
                    array_push($tableArray[$counter], $this->translate->z_xlt($value));
                }
            }
            else{
                if($this->translate->z_xlt(ucfirst($value))!=ucfirst($value)) {
                    array_push($tableArray[$counter], $this->translate->z_xlt(ucfirst($value)));
                }
                else{
                    array_push($tableArray[$counter], $this->translate->z_xlt($value));
                }
            }

        }

        foreach($tableArray as $key=>$val) {
            $returnedTable.="<tr>";
            for($i=0;$i<sizeof($val);$i++){
                if($this->translate->z_xlt(ucfirst($val[$i]))!=ucfirst($val[$i])) {
                    $returnedTable .= "<td>" . $this->translate->z_xlt(ucfirst($val[$i])) . "</td>";
                }
                else{
                    $returnedTable .= "<td>" . $this->translate->z_xlt($val[$i]) . "</td>";
                }
            }
            $returnedTable.="</tr>";
        }


        $returnedTable.="</tbody>";

            // Close the table
        $returnedTable.="</table>";

        return $returnedTable;

    }
    /*this function gets array with 2 line 1 for titles and the othe for values
     * The function wrap the data in html simple table
     */
    public function htmleTableWrapper($table){
        $slices=(int)(count(array_keys($table))/$this::$maxFields+1);
        $parts=$this->partition($table, $slices);
        $html_table = '<table style=border-collapse:collapse;border-spacing:0;width: 100%;><tr>';
        foreach ($parts as $key => $chunk) {
            $keys = array_keys($chunk);
            $values = array_values($chunk);

            foreach ($keys as $key => $titls) {
                $html_table .= "<td align='center' style='border:1px solid #ccc;padding:4px;'><span class=bold>" . $this->translate->z_xlt($titls) . "</span></td>";
                foreach ($values as $key => $value) {
                    if ($value == "Yes" || $value == "No") {
                        $value = xl($value);
                    }
                    $html_table .= "<td style='border:1px solid #ccc;padding:4px;'> " .strip_tags($value,'<table><th>,<thead>,<tbody>,<tr>,<td>,<br>,<p>') . " </td>";
                }
            }
            $html_table .= '</tr>';

        }
        $html_table .= '</table>';

     return $html_table;

    }


    public function renderFormAction(){
        $params=$this->getPostParamsArray();
        if(!$params) die("no params where sent");
        $jasonnyArray=json_decode($params['couchElement']);
        $form_name=$jasonnyArray->_id;
        $form_title=$jasonnyArray->document->label;
        $sql=$jasonnyArray->sql;
        $dir_path=self::$newFormDirPath;
        $des_dir=$GLOBALS['OE_SITE_DIR']."/documents/";
        $src_dir=$GLOBALS['server_document_root'].$GLOBALS['rootdir']."/modules/zend_modules/module/Formhandler/src/Formhandler/Templates/";

    //    die($des_dir."<br/>".$src_dir);
        $template_names=$this->returnDefaultTemplatesNames();

        try {
            $this->makeFormDir($form_name, $des_dir);
            $this->bringTemplateToDir($src_dir, $template_names, $des_dir . $form_name . "/");
            $this->editFormFiles($form_name, $form_title, $sql, $des_dir, $template_names);
            $couchDBConnection=new CouchDBHandle();
            $id=$jasonnyArray->_id;
            $document=$couchDBConnection->getDocument($id);
            if(!isset($document->body['error']))
            {
                die ("Document allready exists in, db please change form_id");
            }
                $couchDBConnection->saveCouchDocument(array("document"=>$jasonnyArray->document,"draft"=>"","jsAddons"=>$jasonnyArray->jsAddons,"sql"=>$jasonnyArray->sql,"conditions"=>$jasonnyArray->conditions,"PHPAddons"=>$jasonnyArray->PHPAddons),$id);

           return  $this->responseWithNoLayout(array("status"=>"ok"),true,200);
        }
        catch (Error $err)
        {
            return  $this->responseWithNoLayout(array("status"=>"false"),true,200);
        }
    }

    public function returnDefaultTemplatesNames(){
        $names=array(self::INFO_FILE,self::NEW_FILE,self::REPORT_FILE,self::TABLE_FILE,self::VIEW_FILE);
        return $names;
    }
    /*
    *
    */
    public function editFormFiles($name,$title,$sql,$path,$filelist){

        /*
        $template = file_get_contents($path.$name."/table.sql");
        $template = str_replace("#SQL#", $sql, $template);
        $fp = fopen($path.$name."/table.sql", 'w');
        fwrite($fp, $template);
        fclose($fp);*/

        $replace_arr=array(self::FORM_NAME_PLACEHOLDER=>$name,self::FORM_TITLE_PLACEHOLDER=>$title,self::FORM_SQL_PLACEHOLDER=>$sql);

        $path .=$name."/";
        if (file_exists($path)) {

            foreach ($filelist as $key => $filename) {
                $template = file_get_contents($path.$filename);
                foreach ($replace_arr as $placeholder => $value) {
                    $template = str_replace($placeholder,$value, $template);
                }
                $fp = fopen($path.$filename, 'w');
                fwrite($fp, $template);
                fclose($fp);
            }
            return true;
        }
        return false;
    }

    /*this function gets array with 2 line 1 for titles and the othe for values
    * The function wrap the data in html simple table
     */
    public function makeFormDir($dir_name,$dir_path){

        if (!file_exists($dir_path.$dir_name)) {
            mkdir($dir_path.$dir_name, 0777, true);
            return true;
        }
        return false;
    }

    /*this function gets array with 2 line 1 for titles and the othe for values
     * The function wrap the data in html simple table
      */
    public function bringTemplateToDir($dir_source,$names,$destination)
    {
         foreach ($names as $key => $field) {
            copy($dir_source.$field, $destination.$field);
        }
    }

    /*Add a prefix to form name*/
    public function AlterFormPrefixName($name)
    {
      if (count(explode(',',$name))<=1) {
          if (strpos($name, "form_") === false || strpos($name, "form_") >0 )
              $name = self::FORMPREFIX . $name;
      }
       return $name;
    }

    /*--------------------------------------------------*/
    /* Make sure the date types have a current date
    /* as a default value - {{current_date}} macro
    /* required
    /*--------------------------------------------------*/
    private function parseEncounterDate($controllers)
    {

        if ( $controllers['type'] == 'date' && $controllers[self::ATTRIBUTES]['value'] == '{{current_date}}') {
            $controllers[self::ATTRIBUTES]['value'] = date('d/m/Y');
        }

        // Encounter Date
        if ( $controllers['type'] == 'date' && $controllers[self::ATTRIBUTES]['value'] == '{{encounter_date}}' && isset($_SESSION['encounter'])) {
            $controllers[self::ATTRIBUTES]['value'] = date('d-m-Y', strtotime($this->getCustomDB()->getEncounterDate()));;
        }

        return $controllers;
    }


    public function translateMessageAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()){
            $message = $request->getPost('message');
            return $this->ajaxOutPut($this->translate->z_xlt($message));
        }
    }
}


