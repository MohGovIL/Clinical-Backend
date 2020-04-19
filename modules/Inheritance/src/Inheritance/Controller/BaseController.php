<?php

namespace Inheritance\Controller;

use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Listener\Listener;
use Inheritance\Model\CustomSql;
use Zend\Mvc\Controller\ActionController;
use Zend\View\Model\ViewModel;
use Interop\Container\ContainerInterface;

class BaseController extends AbstractActionController
{
    private $configParams = null;

    /**
     * path to file after base pass from ModuleconfigController
     * @var array
     */
    protected $jsFiles = array(
        //jquery
        '/jquery.min.js',
        //bootstrap
        '/bootstrap.min.js',
        '/inheritance/jquery.dataTables.min.js',
        '/inheritance/alertify.min.js',

    );

    /**
     * path to file after base pass from ModuleconfigController
     * @var array
     */
    protected $cssFiles = array(
        //bootstrap
        '/inheritance/bootstrap/bootstrap.min.css',
        '/inheritance/bootstrap/bootstrap-rtl.min.css',
        '/font-awesome/css/font-awesome.min.css',
        '/style.css',
        '/inheritance/main.css',
        '/inheritance/hierarchy-view.css',
        '/inheritance/jquery.dataTables.min.css',
        '/inheritance/alertify.rtl.min.css',
        '/multipledb/multipledb.css'


    );

    public function __construct(ContainerInterface $container)
    {
        //load translation class
        $this->translate = new Listener();
        $this->container = $container;
    }

    /**
     * Add js files per method.
     * @param $method __METHOD__ magic constant or __class__
     * @return array
     */
    protected function getJsFiles($method = null)
    {
        switch($method){

            case 'Inheritance\Controller\PumpsController::indexAction':
                $this->jsFiles[] = '/example/pump_configuration.js';
                break;
            case 'Inheritance\Controller\InventoryController::indexAction':
                $this->jsFiles[] = '/lib/datatables/datatables.min.js';
                $this->jsFiles[] = '/lib/datatables/dataTables.bootstrap.min.js';
                $this->jsFiles[] = '/lib/datatables/dataTables.buttons.min.js';
                break;
            case 'Inheritance\Controller\InheritanceController::listAction':
                $this->jsFiles[] = '/inheritance/list.js';
                break;
            case 'Inheritance\Controller\InheritanceController::permissionsAction':
                $this->jsFiles[] = '/inheritance/permissions.js';
                break;
            case 'Inheritance\Controller\InheritanceController::icdAction':
                $this->jsFiles[] = '/inheritance/icd.js';
                break;
            case 'Inheritance\Controller\InheritanceController::rulesAction':
                $this->jsFiles[] = '/inheritance/rules.js';
                break;
            case 'Inheritance\Controller\InheritanceController::templateAction':
                $this->jsFiles[] = '/inheritance/template.js';
                break;
            case 'Inheritance\Controller\InheritanceController::additionalTablesAction':
                $this->jsFiles[] = '/inheritance/additional-tables.js';
                break;
            case 'Inheritance\Controller\InheritanceController::ratesAction':
                $this->jsFiles[] = '/inheritance/rates.js';
                break;
            case 'Inheritance\Controller\InheritanceController::translationsAction':
                $this->jsFiles[] = '/inheritance/translations.js';
                break;
            case 'Inheritance\Controller\InheritanceController::createTreeAction':
                $this->jsFiles[] = '/inheritance/createTree.js';
                break;
            case 'Inheritance\Controller\InheritanceController::indexAction':
                $this->jsFiles[] = '/inheritance/treant-js/vendor/raphael.js';
                $this->jsFiles[] = '/inheritance/treant-js/Treant.js';
                $this->jsFiles[] = '/lib/jquery/jquery-ui.min.js';
                $this->jsFiles[] = '/inheritance/inheritance.js';
                break;
        }
        return $this->jsFiles;
    }

    /**
     * Add css files per method.
     * @param $method __METHOD__ magic constant
     * @return array
     */
    protected function getCssFiles($method = null)
    {

        switch($method){

            case 'Inheritance\Controller\InheritanceController::listAction':
                $this->cssFiles[] = '/inheritance/lists.css';
                break;
            case 'Inheritance\Controller\InheritanceController::permissionsAction':
                $this->cssFiles[] = '/inheritance/permissions.css';
                break;
            case 'Inheritance\Controller\InheritanceController::icdAction':
                $this->cssFiles[] = '/inheritance/icd.css';
                break;
            case 'Inheritance\Controller\InheritanceController::icdAction':
                $this->cssFiles[] = '/inheritance/permissions.css';
                break;
            case 'Inheritance\Controller\InheritanceController::rulesAction':
                $this->cssFiles[] = '/inheritance/rules.css';
                break;
            case 'Inheritance\Controller\InheritanceController::templateAction':
                $this->cssFiles[] = '/inheritance/template.css';
                break;
            case 'Inheritance\Controller\InheritanceController::additionalTablesAction':
                $this->cssFiles[] = '/inheritance/additional-tables.css';
                break;
            case 'Inheritance\Controller\InheritanceController::pullfilesAction':
                $this->cssFiles[] = '/inheritance/pullfiles.css';
                break;
            case 'Inheritance\Controller\InheritanceController::addclinicAction':
                $this->cssFiles[] = '/inheritance/addclinic.css';
                break;
            case 'Inheritance\Controller\InheritanceController::ratesAction':
                $this->cssFiles[] = '/inheritance/rates.css';
                break;
            case 'Inheritance\Controller\InheritanceController::translationsAction':
                $this->cssFiles[] = '/inheritance/translations.css';
                break;
            case 'Inheritance\Controller\InheritanceController::indexAction':
                $this->cssFiles[] = '/inheritance/Treant/Treant.css';
                $this->cssFiles[] = '/jquery/jquery-ui.min.css';
                $this->cssFiles[] = '/inheritance/inheritance.css';
                break;
        }

        return $this->cssFiles;
    }


    /**
     * get the current language
     * @return mixed
     */
    protected function getLanguage(){

        $dbAdapter = $this->container->get('Zend\Db\Adapter\Adapter');
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
                    }
                }

            }
        }

        $this->layout()->setVariable('formId', $formId);
        $this->layout()->setVariable('submitButtonId', $submitButtonId);
    }


    /**
     * enable to add validation for inputs that doesn't exist in the server
     * @param $name - name of input
     * @param $rule - name of rule from getJsValidateConstraints()
     */
    protected function addCustomClientSideValidation($name, $rule){

        $validation = $this->getJsValidateConstraints();

        if(isset($this->validate[$name])) {
            $this->validate[$name] =  array_merge($this->validate[$name] , $validation[$rule]);
        } else {
            $this->validate[$name] =  $validation[$rule];
        }

    }

    protected function excuteClientSideValidation()
    {
        $this->layout()->setVariable('validateJs', json_encode($this->validate));
        $this->layout()->setVariable('constraintsJs', json_encode($this->getJsValidateConstraints()));
        $this->jsFiles[] =  '/lib/validate/validate.js';
        $this->jsFiles[] =  '/custom_validate.js';
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
                'require' => array(
                    'presence' => array(
                        'message' => $this->translate->z_xlt('Value is required')
                    )
                ),
                'int' => array(
                    'format' => array(
                        'pattern' => "[0-9]+",
                        'message' => $this->translate->z_xlt('Only numbers')
                    )
                ),
                'float' => array(
                    'format' => array(
                        'pattern' => "(\d+(\.\d+)?)",
                        'message' => $this->translate->z_xlt('Only numbers')
                    )
                )
            );
        }

        return $this->validation;
    }



    /**
     * @param $data
     * @param bool $convertToJson
     * @param int $responsecode
     * @return \Zend\Stdlib\ResponseInterface
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



    /*
     * get Model, before you use in this method you must add configuration to Model.php (at getServiceConfig())
     * */
    protected function getInheritanceTable()
    {

        if (!$this->InheritanceTable) {
            $this->InheritanceTable = $this->container->get('Inheritance\Model\InheritanceTable');
        }

        return $this->InheritanceTable;
    }


    /*
     * get Model, before you use in this method you must add configuration to Model.php (at getServiceConfig())
     * */
    protected function getNetworkingTable()
    {

        if (!$this->NetworkingTable) {
            $this->NetworkingTable = $this->container->get('Inheritance\Model\NetworkingTable');
        }

        return $this->NetworkingTable;
    }

    protected function getNetworkingDBTable()
    {

        if (!$this->NetworkingDBTable) {
            $this->NetworkingDBTable = $this->container->get('Inheritance\Model\NetworkingDBTable');
        }

        return $this->NetworkingDBTable;
    }


    protected function getListsTable()
    {

        if (!$this->ListsTable) {
            $this->ListsTable = $this->container->get('Inheritance\Model\ListsTable');
        }
        return $this->ListsTable;
    }

    protected function getCodesTable()
    {

        if (!$this->CodesTable) {
            $this->CodesTable = $this->container->get('Inheritance\Model\CodesTable');
        }
        return $this->CodesTable;
    }



    protected function getMenu($link)
    {


        // menu for pouring medicine to patient
        $general = array(
            array(
                'name' => $this->translate->z_xlt('Inheritance'),
                'url' => '/inheritance',
                'id' =>  'inheritance'
            ),
            array(
                'name' => $this->translate->z_xlt('List'),
                'url' => '/inheritance/list',
                'id' =>  'list'
            ),
            array(
                'name' => $this->translate->z_xlt('ICD'),
                'url' => '/inheritance/icd',
                'id' =>  'icd'
            ),
            array(
                'name' => $this->translate->z_xlt('Permissions'),
                'url' => '/inheritance/permissions',
                'id' =>  'permission'
            ),
            array(
                'name' => $this->translate->z_xlt('rules'),
                'url' => '/inheritance/rules',
                'id' =>  'rules'
            ),
            //remove templates inheritance
            /*array(
                'name' => $this->translate->z_xlt('Template'),
                'url' => '/inheritance/template',
                'id' =>  'template'
            ),*/
            array(
                'name' => $this->translate->z_xlt('Rates'),
                'url' => '/inheritance/rates',
                'id' =>  'rates'
            ),
            array(
                'name' => $this->translate->z_xlt('Translations'),
                'url' => '/inheritance/translations',
                'id' =>  'translations'
            ),
            array(
                'name' => $this->translate->z_xlt('Additional Tables Inheritance'),
                'url' => '/inheritance/additional-tables',
                'id' =>  'additional-tables'
            )
        );

        $create_tree = array();

        if(is_array($$link)) {
            return $$link;
        } else {
            throw new \Exception('Menu does not exist');
        }


    }


}