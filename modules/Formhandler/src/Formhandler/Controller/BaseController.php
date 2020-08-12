<?php

namespace Formhandler\Controller;

use Formhandler\Model;
use Formhandler\Model\customDB;
use Laminas\InputFilter\InputFilter;
use Laminas\Mvc\Controller\AbstractActionController;
use Application\Listener\Listener;
use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\ActionController;
use Laminas\View\Model\ViewModel;

class BaseController extends AbstractActionController
{
    private $configParams = null;

    /**
     * path to file after base pass from ModuleconfigController
     * @var array
     */

    protected $jsFiles = array(

    );

    /**
     * path to file after base pass from ModuleconfigController
     * @var array
     */
    protected $cssFiles = array(

    );

    public function __construct(ContainerInterface $container = null)
    {

        $this->container = $container;
        $this->translate = new Listener();
    }

    /**
     * Add js files per method.
     * @param $method __METHOD__ magic constant or __class__
     * @return array
     */
    protected function getJsFiles($method = null)
    {


                    // $this->jsFiles[] = '/datatables.net/js/jquery.dataTables.js';
                 //   $this->jsFiles[] = '/jquery-ui-1-10-4/ui/i18n/jquery.ui.datepicker-he.js';
                    $this->jsFiles[] = '/../../interface/modules/zend_modules/public/js/formhandler/jquery_ui/ui/i18n/datepicker-he.js';


        return $this->jsFiles;
     }

    /**
     * Add css files per method.
     * @param $method __METHOD__ magic constant
     * @return array
     */
    protected function getCssFiles($method = null)
    {


       // $this->cssFiles[] = '/datatables.net-jqui/css/dataTables.jqueryui.css';

        return $this->cssFiles;
    }


    /**
     * get the current language
     * @return mixed
     */
    protected function getLanguage(){


        $dbAdapter = $this->container->get('Laminas\Db\Adapter\Adapter');
        $sql = new CustomSql($dbAdapter);

        $lang = $sql->getCurrentLang();

        return $lang;
    }

    /**
     * @return post params as array
     */
    protected function getPostParamsArray()
    {
        $putParams = array();
        parse_str($this->getRequest()->getContent(), $putParams);
        return $putParams;
    }
    /**
     * return current user id
     * @return int
     */
    protected function getUserId(){

        return $_SESSION['authUserID'];
    }

    /**
     * enable to add validation for inputs that doesn't exist in the server
     * @param $name - name of input
     * @param $rule - name of rule from getJsValidateConstraints()
     */
    protected function addCustomClientSideValidation($name, $rule,$postParams=null){

        $validation = $this->getJsValidateConstraints();


            $ruleTemp=explode("(",$rule);
            $ruleAttributesTemp=explode(")",$rule);
            $ruleAttributes=explode(",",explode("(",explode(")",$rule)[0])[1]);

            $rule=$ruleTemp[0];


        if( is_null($this->validate) && isset($this->validate[$name])) {
            $this->validate[$name] =  array_merge($this->validate[$name] , $validation[$rule]);
        } else {

            if($rule=='Checked'){
                if (!is_null($validation[$rule]['numericality'])) {
                    $validation[$rule]['numericality']['greaterThan'] = 0;
                }

            }
            if($rule=='AdditiveEquality') {
                if (!is_null($validation[$rule]['numericality'])) {
                    $validation[$rule]['numericality']['greaterThan'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                }
            }

            if($rule=='Equality') {
                switch($ruleAttributes[2])
                {
                    case 'Date':

                            switch (str_ireplace("'","",$ruleAttributes[0])) {
                                case '<':
                                    $validation[$rule]['datetime']=[];
                                    $validation[$rule]['datetime']['dateOnly'] = "true";
                                    $validation[$rule]['datetime']["latest"]= "moment($('#committee_date').val())";
                                    $validation[$rule]['datetime']["format"]=  $GLOBALS['date_display_format'] == 2 ? "DD/MM/YYYY" : 'YYYY-MM-DD';
                                    break;

                                case '>':
                                    $validation[$rule]['datetime']=[];
                                    $validation[$rule]['datetime']['dateOnly'] = "true";
                                    $validation[$rule]['datetime']["earliest"]= "moment($('#committee_date').val())";
                                    $validation[$rule]['datetime']["format"]=  $GLOBALS['date_display_format'] == 2 ? "DD/MM/YYYY" : 'YYYY-MM-DD';
                                    break;

                                case '==':
                                    $validation[$rule]['datetime']=[];
                                    $validation[$rule]['datetime']['dateOnly'] = "true";
                                    $validation[$rule]['datetime']["dateOnly"]= "moment($('#committee_date').val())";
                                    $validation[$rule]['datetime']["format"]=  $GLOBALS['date_display_format'] == 2 ? "DD/MM/YYYY" : 'YYYY-MM-DD';
                                    break;
                            }
                       break;
                    case 'number':

                    if (!is_null($validation[$rule]['numericality'])) {
                        switch (str_ireplace("'","",$ruleAttributes[0]))
                        {
                            case '>=':
                                $validation[$rule]['numericality']['greaterThanOrEqualTo'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));

                                break;
                            case '>':
                                $validation[$rule]['numericality']['greaterThan'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                                break;

                            case '<=':
                                $validation[$rule]['numericality']['lessThanOrEqualTo'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                                break;

                            case '<':
                                $validation[$rule]['numericality']['lessThan'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                                break;

                            case '==':
                                $validation[$rule]['numericality']['equalTo'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                                break;

                            case '!=':
                                $validation[$rule]['numericality']['notEqualTo'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                                break;
                        }
                        break;


                }
                    case 'default':
                        break;



                    //$validation[$rule]['numericality']['greaterThan'] = "#".trim(str_ireplace("'","",$ruleAttributes[1]));
                }

                if(str_ireplace("'","",$ruleAttributes[0])=='<=') {

                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Date') $validation[$rule]['date']['message'] = $this->translate->z_xlt('The end time must be greater then the one you choose');
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Number') $validation[$rule]['numericality']['message'] = 'Value must be greater then equale other  values';
                }
                if(str_ireplace("'","",$ruleAttributes[0])=='<') {
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Date') $validation[$rule]['date']['message'] = $this->translate->z_xlt('The end time must be greater then the one you choose');
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Number') $validation[$rule]['numericality']['message'] = 'Value must be greater then other  values';
                }
                if(str_ireplace("'","",$ruleAttributes[0])=='>') {
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Date') $validation[$rule]['date']['message'] = $this->translate->z_xlt('The end time must be later then the one you choose');
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Number') $validation[$rule]['numericality']['message'] = 'Value must be less then other  values';
                }
                if(str_ireplace("'","",$ruleAttributes[0])=='>=') {

                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Date') $validation[$rule]['date']['message'] = $this->translate->z_xlt('The end time must be later then the one you choose');
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Number') $validation[$rule]['numericality']['message'] = $this->translate->z_xlt('Value must be less then equal to the other values');
                }
                if(str_ireplace("'","",$ruleAttributes[0])=='==') {
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Date') $validation[$rule]['date']['message'] = $this->translate->z_xlt('The end time must be equale to the one you choose');
                    if (str_ireplace("'", "", $ruleAttributes[2]) == 'Number') $validation[$rule]['numericality']['message'] = $this->translate->z_xlt('Value must be equal to the other values');
                }
            }

            if($rule=='RequiredBy') {

                $inputName=$ruleAttributes[0];
                    if($postParams[$inputName]!=$ruleAttributes[1])
                    {
                        $this->validate[$name]=array();
                        return;

                    }
                    else{
                        $rule="Required";
                    }


            }
             if(!is_array($this->validate[$name])) {

                if(!$name)
                   return;
                $this->validate[$name] = array();
                $this->validate[$name][array_keys($validation[$rule])[0]] = [];
                $this->validate[$name][array_keys($validation[$rule])[0]] = $validation[$rule][array_keys($validation[$rule])[0]];
            }
            else{
                $this->validate[$name][array_keys($validation[$rule])[0]]=$validation[$rule][array_keys($validation[$rule])[0]];
            }


        }

    }
    protected function getBasePath(){

        return $this->getRequest()->getUri()->getPath();
        //return $this->getRequest()->getUri()->getScheme()."://". $this->getRequest()->getUri()->getHost().$this->getRequest()->getUri()->getPath();
    }
    protected function excuteClientSideValidation()
    {
        $this->layout()->setVariable('validateJs', json_encode($this->validate));
        $this->layout()->setVariable('constraintsJs', json_encode($this->getJsValidateConstraints()));
        $this->FormhandleJSFiles[] =  $this->getRequest()->getbaseUrl() . '/js/formhandler/validateMatrix/validate.js';


        $this->FormhandleJSFiles[] =   $this->getRequest()->getbaseUrl() . "/js/formhandler/custom_validate.js";
        $this->FormhandleJSFiles[] =   $this->getRequest()->getbaseUrl() . "/js/formhandler/moment.js";
        $this->FormhandleCSSFiles[] =   $this->getRequest()->getbaseUrl() . "/css/formhandler/styles.css";
        $this->FormhandleJSFiles[] =   $this->getRequest()->getbaseUrl() . "/js/formhandler/ui_addons.js"; //prevent js cache
       // $this->FormhandleJSFiles[] =   $this->getRequest()->getbaseUrl() . "/js/lib/select2/select2.full.min.js";
      //  $this->FormhandleCSSFiles[] =   $this->getRequest()->getbaseUrl() . "/css/select2/select2.min.css";
        $this->FormhandleJSFiles[] =   $this->getRequest()->getbaseUrl() . "/js/clinikalmohil/lib/tooltipster.bundle.min.js";
        $this->FormhandleCSSFiles[] =   $this->getRequest()->getbaseUrl() . "/css/clinikalmohil/tooltipster.bundle.min.css";

    }


    /**
     * @param $data
     * @param bool $convertToJson
     * @param int $responsecode
     * @return \Laminas\Stdlib\ResponseInterface
     * @comment to use this function return this $response in your controller
     */
    protected function responseWithNoLayout($data, $convertToJson=true, $responsecode=200){
        $response = $this->getResponse();
        $response->setStatusCode($responsecode);
        if($convertToJson) {
            $response->setContent(json_encode($data));
        }
        else{
            $response->setContent($data);
        }
        return $response;
    }

    /**
     *Uniform stracture for ajax response
     * */
    protected function ajaxOutPut($data, $code = 0, $status = 'success'){

        return $this->responseWithNoLayout(array(
            'code' => $code,
            'status' => $status,
            'output' => $data
        ));
    }


    /**
     * function for debugger
     * */
    protected function die_r($dada) {
        echo "<pre>";
        print_r($dada);
        echo "</pre>";
        die;
    }
    protected function getCustomDB(){

       // $container = $this->getServiceLocator();


        $dbAdapter = $this->container->get('Laminas\Db\Adapter\Adapter');
        $CustomDb = new CustomDb($dbAdapter);
        return $CustomDb;
    }

    /**
     * create client side validation according the server side rules.
     * @param $modelObject - model object of the form
     * @param null $formId - id of the form default is the name of the model (lowercase)
     * @param string $submitButtonId - id of button that make submit (mustn't be button type=submit (recursive problem))
     */
    protected function prepareClientSideValidation($modelObject, $formId = null, $submitButtonId = 'save'){

        if(is_null($formId)) {
            $className = get_class($modelObject);
            $arrClasssName = explode("\\", $className);
            $formId = strtolower(end($arrClasssName));
        }

        //get validation rules
        $validation = $this->getJsValidateConstraints();
        //create array for validate js json
        $this->validate =  array();

        $inputs = $modelObject::$inputsValidations;
        //echo "<pre>";print_r($inputs);die;
        foreach($inputs as $key => $input) {
            //require validate
            if(isset($input['required'])) {
                $this->validate[$input['name']] = $validation['require'];
            }
            //add filters
            if(isset($input['filters'])) {
                foreach($input['filters'] as $filter) {
                    switch(strtolower($filter['name'])) {
                        case 'int':
                        case 'digits':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['int']);
                            } else {
                                $this->validate[$input['name']] =  $validation['int'];
                            }
                            break;
                    }
                }
            }
            //add validators
            if(isset($input['validators'])) {
                foreach($input['validators'] as $validator) {

                    switch(strtolower($validator['name'])) {
                        case 'float':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['float']);
                            } else {
                                $this->validate[$input['name']] =  $validation['float'];
                            }
                            break;

                        case 'DateFromNow':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['DateFromNow']);
                            } else {
                                $this->validate[$input['name']] =  $validation['DateFromNow'];
                            }
                            break;
                        case 'NoPastDate':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['NoPastDate']);
                            } else {
                                $this->validate[$input['name']] =  $validation['NoPastDate'];
                            }
                            break;

                        case 'Age':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['Age']);
                            } else {
                                $this->validate[$input['name']] =  $validation['Age'];
                            }
                            break;

                        case 'DoubleDigit':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['DoubleDigit']);
                            } else {
                                $this->validate[$input['name']] =  $validation['DoubleDigit'];
                            }
                            break;
                        case 'IntNotZero':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['IntNotZero']);
                            } else {
                                $this->validate[$input['name']] =  $validation['IntNotZero'];
                            }
                            break;
                        case 'Year':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['DoubleDigit']);
                            } else {
                                $this->validate[$input['name']] =  $validation['DoubleDigit'];
                            }
                            break;
                        case 'NUM':
                            if(isset($this->validate[$input['name']])) {
                                $this->validate[$input['name']] =  array_merge($this->validate[$input['name']] ,$validation['NUM']);
                            } else {
                                $this->validate[$input['name']] =  $validation['NUM'];
                            }
                            break;

                    }
                }

            }
        }

        $this->layout()->setVariable('formId', $formId);
        $this->layout()->setVariable('submitButtonId', $submitButtonId);
    }

    /**
     * get all constraints for client side validation (for validate.js library) with translation
     * Important - when you add new server validation that not exist here you should adding it with validate.js syntax
     * @return array
     */
    protected function getJsValidateConstraints()
    {

        if (!$this->validation) {

            $this->validation =  array(
                'RequiredBy'=>array(
                    'presence' => array(
                        'message' => $this->translate->z_xlt('Value is required')
                    )
                ),
                'Required' => array(
                    'presence' => array(
                        'message' => $this->translate->z_xlt('Value is required')
                    )
                ),
                'required' => array(
                    'presence' => array(
                        'message' => $this->translate->z_xlt('Value is required')
                    )
                ),
                'require' => array(
                    'presence' => array(
                        'message' => $this->translate->z_xlt('Value is required')
                    )
                ),
                'Int' => array(
                    'numericality' => array(
                        "onlyInteger"=> true,
                        "message"=> $this->translate->z_xlt("Must be number"))
                ),
                'Numeric' => array(
                    'numericality'=> array('strict'=> true,'message' => $this->translate->z_xlt('Only numbers'))




                ),

                'DateFromNow'=> array(
                                    "date"=>array(
                                              "latest"=> $GLOBALS['date_display_format'] == 2 ? date("d/m/Y") : date("Y-m-d"),
                                              "message"=> $this->translate->z_xlt("Can not pick future date"),
                                              "format"=>  $GLOBALS['date_display_format'] == 2 ? "DD/MM/YYYY" : 'YYYY-MM-DD'

                                    )
                ),
                'NoPastDate'=> array(
                    "date"=>array(
                        "earliest"=> $GLOBALS['date_display_format'] == 2 ? date("d/m/Y") : date("Y-m-d"),
                        "message"=> $this->translate->z_xlt("Can not pick past date"),
                        "format"=>  $GLOBALS['date_display_format'] == 2 ? "DD/MM/YYYY" : 'YYYY-MM-DD'

                    )
                ),
                'EncounterUntilToday'=> array(
                    "date"=>array(
                        "latest"=> $GLOBALS['date_display_format'] == 2 ? date("d/m/Y") : date("Y-m-d"),
                        "earliest"=> $this-> getEncounterDate(),
                        "message"=> $this->translate->z_xlt("Date is not in range"),
                        "format"=>  $GLOBALS['date_display_format'] == 2 ? "DD/MM/YYYY" : 'YYYY-MM-DD'

                    )
                ),

                'Age'=> array(
                    'numericality'=>array(
                        'onlyInteger'=> true,
                        'greaterThan'=> 0,
                        'lessThan' => 999,
                        'message'=> $this->translate->z_xlt("Age must be between 0 to 999"),


                    ),


                ),
                'DoubleDigit'=> array(
                    'numericality'=>array(
                        'onlyInteger'=> true,
                        'greaterThan'=> 0,
                        'lessThanOrEqualTo' => 99,
                        'message'=> $this->translate->z_xlt("Should be between 0 to 99")
                    )

                ),
                'IntNotZero'=> array(
                    'numericality'=>array(
                        'onlyInteger'=> true,
                        'greaterThan'=> 0,
                        'message'=> $this->translate->z_xlt("Should be greater than 0")
                    )

                ),
                'Year'=> array(
                    "numericality"=>array(
                        "onlyInteger"=> true,
                        "greaterThan"=> 1000,
                        "lessThanOrEqualTo" => 9999,
                        "message"=> $this->translate->z_xlt("Age must be between 0 to 999")
                    )

                ),
                'NUM'=> array(
                    'numericality' => array(
                        "onlyInteger"=> true,
                        "message"=> $this->translate->z_xlt("Must be number")

                    )

                ),
                'Equality'=>array(





                ),
                'AdditiveEquality'=>array(

                    'numericality'=>array(
                        'greaterThan'=> "#start_date",
                        "message"=> $this->translate->z_xlt("The end time must be later then the start one")
                    )

                ),
                'Checked'=>array(

                    'numericality'=>array(
                        'greaterThan'=> "0",
                        "message"=> $this->translate->z_xlt("This should be checked")
                    )
                )
            );
        }
        return $this->validation;
    }



    public static function str_contains($haystack, $needles) {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function getEncounterDate() {


        $db=$this->getCustomDB();
        $date=$db->getCurrentEncounterDate();
        $date=oeFormatDateTime($date);
        return $date;
    }

}
