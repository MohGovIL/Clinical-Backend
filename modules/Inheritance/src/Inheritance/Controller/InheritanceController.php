<?php
namespace Inheritance\Controller;

use Inheritance\Controller\BaseController;
use Inheritance\Model\Inheritance;
use Inheritance\Model\InheritanceTable;
use Inheritance\Form\InheritanceForm;
use Inheritance\Model\Networking;
use Inheritance\Model\NetworkingDB;
use Zend\View\Model\ViewModel;
use Interop\Container\ContainerInterface;


class InheritanceController extends BaseController
{
    CONST ICD9CODE="9909";
    CONST ICD10CODE="9910";

    protected $InheritanceTable;
    protected $list_id = "";

    /**
     * InheritanceController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->list_id = empty($_REQUEST['list_id']) ? 'language' : $_REQUEST['list_id'];
        $this->container = $container;
    }

    /*
     * example for simple method
     * */
    public function indexAction()
    {
        $this->getNetworkingTable()->setMyClientId();
        $this->layoutGlobal();

        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);

        $this->getNetworkingTable()->setMyClientId();
        $allTreesJson = json_encode($this->getNetworkingTable()->newGetAllZeroTrees());

        return new ViewModel(array(
            'trees' => $allTreesJson,
            'clinic_name' => $_SESSION['my_client']->clinic_name
        ));
    }

    public function saveTreeAjaxAction()
    {
        $params = $this->getRequest()->getPost();

        $this->getNetworkingTable()->updateParent($params['draggableId'], $params['droppableId']);

        $allTreesJson = $this->getNetworkingTable()->newGetAllZeroTrees();

        return $this->ajaxOutPut($allTreesJson);
    }

    public function createTreeAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            //$this->die_r($params);
            if($params['method_type'] == 'parent_zero'){
                $networking = new Networking();
                $networking->exchangeArray($params);
                $networking->parent=0;
                $networking->type=0;
                $network = $this->getNetworkingTable()->save($networking);

                $networkDB = new NetworkingDB();
                $networkDB->exchangeArray($params);
                $networkDB->clinic_id=$network['id'];
                $networkDB->password = my_encrypt($params['password']);
                $network = $this->getNetworkingDBTable()->save($networkDB);

            } elseif ($params['method_type'] == 'create_child'){
                $networking = new Networking();
                $networking->exchangeArray($params);
                if($params['type'] == 'zero')$networking->type=0;
                $network = $this->getNetworkingTable()->save($networking);

                $networkDB = new NetworkingDB();
                $networkDB->exchangeArray($params);
                $networkDB->clinic_id=$network['id'];
                $networkDB->password = my_encrypt($params['password']);
                $network = $this->getNetworkingDBTable()->save($networkDB);
            } elseif ($params['method_type'] == 'edit'){
                $networking = new Networking();
                $networking->exchangeArray($params);
                if($params['type'] == 'zero')$networking->type=0;
                $network = $this->getNetworkingTable()->save($networking);

                //set id of networking in clinic_ic
                $params['clinic_id'] = $params['id'];
                //change id to id of networking_db_id
                $params['id']=$params['networking_db_id'];
                $networkDB = new NetworkingDB();
                $networkDB->exchangeArray($params);
                if($networkDB->password == '****' ||  $networkDB->password == ''){
                    $networkDB->password = $this->getNetworkingDBTable()->getPassword($networkDB->id);
                } else {
                    $networkDB->password = my_encrypt($params['password']);
                }

                $network = $this->getNetworkingDBTable()->save($networkDB);
            }

        }
        $this->layout()->setVariable('menuBar', $this->getMenu('create_tree'));
        $this->getJsFiles(__METHOD__);
        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);

        $parameters['zeroConnected'] = $this->getNetworkingDBTable()->isHasConnection(array('dbname' => $GLOBALS['dbase'], 'host' => $GLOBALS['host']));
        if($parameters['zeroConnected']){
            $parameters['inheritance'] = $this->getNetworkingTable()->get_tree();
        }
        return $parameters;


    }

    public function getDataAction(){

        $clinic_id = $id = (int) $this->params()->fromRoute('id');
        $networking = $this->getNetworkingTable()->getSingle($clinic_id);
        $networking_db = $this->getNetworkingDBTable()->getSingleByClinicId($networking->id);
        $networking_db->networking_db_id=$networking_db->id;
        unset($networking_db->id);
        unset($networking_db->clinic_id);
        if($networking->type == 0)$networking->type='zero';
        $networking_db->password='****';
        $data = array_merge((array)$networking, (array)$networking_db);
        return $this->ajaxOutPut($data);
    }

    public function deleteClinicAction(){
        $clinic_id = $id = (int) $this->params()->fromRoute('id');
        $networking = $this->getNetworkingTable()->remove($clinic_id);
        $networking_db = $this->getNetworkingDBTable()->removeByClinicID($clinic_id);
        return ($networking_db) ? $this->ajaxOutPut(true): $this->ajaxOutPut(false,1);
    }

    public function getparentlistsjsonAction()
    {
        $clinic_id = (int)(substr($_SESSION['my_client']->id, 0, 11));
        if (isset($clinic_id)) {
            echo json_encode(array("data" => $this->createParentList($clinic_id)));
        }
        exit();
    }

    public function geticd9listsjsonAction()
    {
        $clinic_id = (int)(substr($_SESSION['my_client']->id, 0, 11));

        $offset=$_POST['start'];
        $limit=$_POST['length'];
        $list_type=($_POST['list_type']=='icd9') ? '9909' : '9910';
        //$draw=$_POST['draw'];

        $search="";
        if (!empty($_POST['search'])) {
            $search=$_POST['search']['value'];
        }


        $number_of_rows="".$this->getCodesTable()->countList($list_type,$search);

        if (isset($clinic_id)) {

            $codes= $this->createicdList($clinic_id,$offset,$limit,$list_type,$search);
            $data=array(
                "recordsTotal"=> $number_of_rows,
                "recordsFiltered"=> $number_of_rows,
                "data"=>$codes
            );

            echo json_encode($data);
        }
        exit();
    }




    public function updateAllInheritableAction()
    {
        $state=($_POST['state']=='true') ? true : false;
        $list_type=($_POST['list']=='icd9') ? self::ICD9CODE : self::ICD10CODE;

        $res=$this->getCodesTable()->updateAllInheritable($state,$list_type);

        return ($state) ? $this->ajaxOutPut(true): $this->ajaxOutPut(false);

        exit();
    }


    public function getrulesjsonAction()
    {
        echo json_encode(array("data" => $this->createRulesList()));
        exit();
    }

    public function gettemplatesjsonAction()
    {
        echo json_encode(array("data" => $this->getTemplatesList()));
        exit();
    }

    public function syncIcdRecordAction()
    {
        $action=$_POST['action'];
        $data="";

        if ($action=='icd'){

            $icd_code=($_POST['sync']=='icd9') ? self::ICD9CODE : self::ICD10CODE;
            $code_id=$_POST['code'];
            $state=$_POST['state'];

            $data="".$this->getCodesTable()->updateInheritable($icd_code,$code_id,$state);
        }

        echo json_encode(array("data" => "$data"));
        exit();
    }

    public function syncjsonAction()
    {
        $this->getNetworkingTable()->setMyClientId();
        $clinic_id = (int)(substr($_SESSION['my_client']->id, 0, 11));
        $action = htmlentities($_REQUEST['action'], ENT_QUOTES | ENT_IGNORE, "UTF-8");

        $syncResult=array();

        if ($action) {

            switch ($action) {

                case 'list':
                    $edit_id = json_decode($_REQUEST['edit']);
                    $sync_id = json_decode($_REQUEST['sync']);
                    $this->getListsTable()->storeListOptionPermission($clinic_id, $edit_id);

                    $this->getNetworkingTable()->sync_edit_list_permission($edit_id, $sync_id, $clinic_id, $this->getListsTable());
                    break;
                case 'icd':
                    $sync_id = $_REQUEST['sync'];

                    if($sync_id == 'icd9'){
                        $sync_id = 9909;
                    }else if($sync_id == 'icd10'){
                        $sync_id = 9910;
                    }else{
                        $sync_id = -1;
                    }

                    if($sync_id > 0){
                        $this->getNetworkingTable()->sync_icd($clinic_id, $sync_id, $this->getInheritanceTable());
                    }
                    break;
                case 'permission':
                    $this->getNetworkingTable()->sync_permissions($clinic_id, $this->getInheritanceTable());
                    break;
                case 'rules':
                    $sync_id = json_decode($_REQUEST['sync']);
                    $this->getNetworkingTable()->sync_rules($clinic_id, $sync_id, $this->getInheritanceTable());
                    break;
                case 'template':
                    $sync_id = json_decode($_REQUEST['sync']);
                    $this->getNetworkingTable()->sync_template($clinic_id, $sync_id, $this->getInheritanceTable());
                    break;
                case 'rates':
                    $sync_id = json_decode($_REQUEST['sync']);
                    $this->getNetworkingTable()->sync_rates($clinic_id, $this->getInheritanceTable());
                    break;
                case 'translations':
                    $sync_id = json_decode($_REQUEST['sync']);
                    $this->getNetworkingTable()->sync_translations($clinic_id, $this->getInheritanceTable());
                    break;
                case 'additional':
                    $syncResult=$this->getNetworkingTable()->sync_additionals($clinic_id, $this->getInheritanceTable());
                    break;

            }


            $getSsuccessSonsConnection = $this->getNetworkingTable()->getSsuccessSonsConnection();
            $getFailSonsConnection = $this->getNetworkingTable()->getFailSonsConnection();
            $getErrorCode = $this->getNetworkingTable()->getErrorCode();
            $sql_errors_exception_code = '';

            if ($_SESSION['sql_errors_exception_code']) {
                $sql_errors_exception_code = implode(',', $_SESSION['sql_errors_exception_code']);
            }

            if (!empty($syncResult)){

                echo json_encode($syncResult);

            }else{
                echo json_encode(array('errors' => 1, 'fail' => $getFailSonsConnection, 'success' => $getSsuccessSonsConnection, 'error_code' => $getErrorCode, 'sql_fail_execute' => $sql_errors_exception_code));
            }


        }
        exit();
    }

    public function downloadjsonAction()
    {
        $clinic_id = (int)(substr($_SESSION['my_client']->id, 0, 11));
        $action = htmlentities($_REQUEST['action'], ENT_QUOTES | ENT_IGNORE, "UTF-8");

        if ($action) {

            switch ($action) {
                case 'permission':
                    die($this->getNetworkingTable()->download_permissions($this->getInheritanceTable()));
                    break;

            }

        }
        exit();
    }

    public function listAction(){
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'translate' => $this->translate,
            'basePatch' => $this->basePath()
        ));
    }

    public function icdAction(){
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'translate' => $this->translate,
            'basePatch' => $this->basePath()
        ));

    }

    public function ratesAction(){
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'translate' => $this->translate,
            'basePatch' => $this->basePath()
        ));

    }

    public function translationsAction(){
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'translate' => $this->translate,
            'basePatch' => $this->basePath()
        ));

    }


    public function permissionsAction(){
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'translate' => $this->translate,
            'basePatch' => $this->basePath()
        ));

    }

    public function rulesAction()
    {
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'translate' => $this->translate,
            'basePatch' => $this->basePath()
        ));

    }

    public function templateAction()
    {
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'translate' => $this->translate,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'basePatch' => $this->basePath()
        ));

    }

    public function additionalTablesAction()
    {
        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();
        $tables = $this->getInheritanceTable()->getAdditionalTables();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'translate' => $this->translate,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'basePatch' => $this->basePath(),
            'tables' => $tables
        ));

    }

    public function addclinicAction(){

        if(!isset($_GET['pass']) OR $_GET['pass'] != 'kokojambo'){
            exit();
        }

        $this->getCssFiles(__METHOD__);
        $this->getJsFiles(__METHOD__);
        $this->layoutGlobal();

        return new ViewModel(array(
            'list_id' => $this->list_id,
            'translate' => $this->translate,
            'clinic_name' => $_SESSION['my_client']->clinic_name,
            'basePatch' => $this->basePath()
        ));

    }

    private function createParentList($clinic_id)
    {
        $rows = $this->getListsTable()->getParentList();
        $permission_list = $this->getListsTable()->get_list_option_permission($clinic_id);

        $array = array();
        $input_edit = "";
        $input_sync = "";
        $text = "";
        $i = 0;
        if ($rows) {
            foreach ($rows as $row) {

                $key = $row['option_id'];

                $input_edit = '<input type="checkbox" class="edit" value="' . $key . '"';
                if (in_array($key, $permission_list)) {
                    $input_edit .= " checked";
                }
                $input_edit .= ">";

                $input_sync = '<input type="checkbox" class="sync" value="' . $key . '"';
                if (in_array($key, $permission_list)) {
                    $input_sync .= " checked";
                }
                $input_sync .= ">";

                $text = '' . $row['title'] . "";

                $array[$i][] = $text;
                $array[$i][] = $input_sync;
                $array[$i][] = $input_edit;


                $i++;
            }
        }

        return $array;

    }

    private function createRulesList()
    {
        $rows = $this->getInheritanceTable()->getRule();

        $array = array();
        $input_edit = "";
        $input_sync = "";
        $text = "";
        $i = 0;
        if ($rows) {
            foreach ($rows as $row) {
                $input_sync = '<input type="checkbox" class="sync" value="' . $row['option_id'] . '">';

                $text = '' . xl($row['title']) . "";

                $array[$i][] = $text;
                $array[$i][] = $input_sync;

                $i++;
            }
        }

        return $array;
    }

    private function getTemplatesList()
    {
        $templatedir = $GLOBALS['OE_SITE_DIR'] . "/documents/doctemplates";
        $scannedFiles = scandir($templatedir);

        $array = array();
        $input_edit = "";
        $input_sync = "";
        $text = "";
        $i = 0;

        foreach ($scannedFiles as $file) {
            if (!in_array(trim($file), ['.', '..'])) {
                $input_sync = '<input type="checkbox" class="sync" value="' . $file . '">';

                $text = '' . $file . "";

                $array[$i][] = $text;
                $array[$i][] = $input_sync;

                $i++;
            }
        }

        return $array;

    }



    private function layoutGlobal(){
        $this->layout()->setVariable('menuBar', $this->getMenu('general'));
        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);

    }


    public function basePath()
    {
        $basePath = $this->container->get('ViewHelperManager')->get('basePath');
        return $basePath();
    }


    private function createicdList($clinic_id,$offset,$limit,$list_type,$search)
    {
        $rows = $this->getCodesTable()->fetchPartialList($offset,$limit,$list_type,$search);

        $array = array();
        $i = 0;
        if ($rows) {
            foreach ($rows as $row) {

                $key = $row['enable_disable'];

                $input_sync = '<input type="checkbox" class="sync" value="' . $key . '"';
                if ($key) {
                    $input_sync .= " checked";
                }
                $input_sync .= ">";

                //$text = '' . $row['name'] . "";
                $array[$i]['name'] = $row['name'];
                $array[$i]['code'] = $row['code'];
                $array[$i]['enable_disable'] = $input_sync;

                $i++;
            }
        }

        return $array;

    }
}
