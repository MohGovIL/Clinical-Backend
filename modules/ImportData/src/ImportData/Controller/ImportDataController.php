<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 03/07/16
 * Time: 12:28
 */

namespace ImportData\Controller;

use Dompdf\Exception;
use Laminas\Mvc\Controller\AbstractActionController;
use ImportData\Controller\BaseController;
use Laminas\View\Model\ViewModel;
use ImportData\Plugin\EDMRequest;
use ImportData\Lists\CountriesList;
use ImportData\Model\ImportDataLog;
use Interop\Container\ContainerInterface;




/*
 *      In order to import new CSV do the following
 *      -------------------------------------------
 *
 *      1) Enter new record to list options with the list id as option id and list id equal to lists.
 *      2) Translate the list name otherwise it won't be visible to the zero ui interface.
 *      3) Insert into "moh_import_data" the following :
 *              a) `external_name` - name of the table in the edm.
 *              b) `clinikal_name` - name of the list in list options.
 *              c) `static_name` - name of the class.
 *      4) Add the new class to the routing using initTable($tableName).
 *      5) create the class in folder lists.
 *
 *
 * */


/**
 * Class ImportDataController
 * @package ImportData\Controller
 */
class ImportDataController extends  BaseController /*AbstractActionController*/
{


    const LIST_TABLES = 'list_options';
    const SSO_LIST = 'sso_list';
    const CODE_TABLE = 'codes';
    const ICD9_CODE = '9909';
    const ICD10_CODE = '9910';
    const MOH_DRUGS = '9911';

    public function __construct(ContainerInterface $container)
    {
        //todo add permission to system
        parent::__construct($container);
        $this->container = $container;

    }



    /**
     * Update list and tables in OpenEMR DATABASE.
     * This action get change from some resources
     *  1. from EDM sever in moh using soap request
     *  2. from CSV files
     * This interface enable to add rows / edit value of exist row / to move row to inactive
     */
    public function updateTableAction(){

        $allTables = $this->getImportDataTable()->getAll();
        echo 'start process - ' . date('Y-m-d H:i:s') . PHP_EOL;
        foreach ($allTables as $table){

            $init = $this->initTable($table->static_name);
            if(!$init){
                error_log('missing code for table - ' . $table->static_name );
                continue;
            }

            switch ($table->source){
                case 'EDM':
                  $this->updateFormEDM($table);
            }

        }
        exit('update was finished' . PHP_EOL);
    }

    /**
     * This method get changes from CSV files and save them in the DB
     * @return bool
     * @internal param $
     */
    public function csvAction(){

        $actionType = $this->params()->fromRoute('type', 'edm_init');

        $this->getJsFiles(__METHOD__);
        $this->getCssFiles(__METHOD__);
        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);


        switch ($actionType){
            case 'edm_init':
                $lists = $this->getImportDataTable()->getEdmLists();
                break;
            case 'update_list':
                $lists = $this->getImportDataTable()->getCsvLists();
                break;
        }
        $viewParams = array('lists' => $lists, 'action_type' => $actionType);

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            if(!isset($post['upload']))return;
            if(empty($post['table_name']) || empty($post['file_date'])){
                $viewParams['errorMsg'] = 'Missing mandatory parameter';
                return $viewParams;
            }

            //get data about present list
            $tableData = $this->getImportDataTable()->fetch(array('static_name' => $post['table_name']));

            $init = $this->initTable($tableData->static_name);
            if(!$init){
                $viewParams['errorMsg'] = xlt('Missing code for table') . ' - ' . $tableData->static_name;
                return $viewParams;
            }

            try{
                // check the file before process
                $file = $this->checkSecureFile($post['csv_file']);
            } catch (\Exception $e){
                $viewParams['errorMsg'] =$e->getMessage();
                return $viewParams;
            }

            // backup data
            $backup=null;
            $backup=$this->createBackupFile($backup,$tableData->external_name);

            // truncate  table / list before insert
            switch($this->listController::$table){
                case self::LIST_TABLES:
                case self::SSO_LIST:
                    $this->getListOptionsTable()->truncateList($tableData->clinikal_name);
                    break;
                case self::CODE_TABLE:

                    switch($tableData->static_name){
                        case "icd9":
                            $icdType =self::ICD9_CODE ;
                            break;
                        case "icd10":
                            $icdType = self::ICD10_CODE;
                            break;
                        case "moh_drugs":
                            $icdType = self::MOH_DRUGS;
                            break;
                    }

                   $this->getCodesTable()->truncate($icdType);
            }

            try {
                $stats = $this->saveChangesFromCsv($file, $tableData);
            }catch (\Exception $e){
                $viewParams['errorMsg'] =$e->getMessage();
                return $viewParams;
            }


            // update date in import_data table
            $this->getImportDataTable()->updateDate($tableData->id, $post['file_date']);

            // write to log
            $log = $this->createLogObject($tableData->static_name, 'success', $stats['countAffectedRecords']);
            $log->info = json_encode(array('type' => 'Initial from csv'));
            if(!empty($stats['recordsFailed'])){
                $log->info = json_encode(array('type' => 'Initial from csv','failed_records' => $stats['recordsFailed']));
                if($stats['countAffectedRecords'] == 0)$log->status = 'failed';
            }


            $this->getImportDataLogTable()->save($log);
            $viewParams['successMsg'] = $tableData->external_name . ' ' . xlt('has saved successfully');
            $viewParams['csv_backup'] =$backup;
        }

        return $viewParams;

    }

    /**
     * function called from command line to automatically load EDM lists after new installation
     */
    public function loadAllListsAction(){

        $filesArray = $this->getRequest()->getPost()->toArray();

        foreach($filesArray['files'] as $file) {


            //get data about present list
            $tableData = $this->getImportDataTable()->fetch(array('static_name' => $file['staticName']));

            $init = $this->initTable($tableData->static_name);

            // truncate  table / list before insert
            switch ($this->listController::$table) {
                case self::LIST_TABLES:
                case self::SSO_LIST:
                    $this->getListOptionsTable()->truncateList($tableData->clinikal_name);
                    break;
                case self::CODE_TABLE:
                    $icdType = $tableData->static_name == 'icd9' ? self::ICD9_CODE : self::ICD10_CODE;
                    $this->getCodesTable()->truncate($icdType);
            }

            try {
                $stats = $this->saveChangesFromCsv($filesArray['edmLocation'] . $file['fileName'], $tableData);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            // update date in import_data table (use date from config unless wasn't provided or is in the wrong format
            $this->getImportDataTable()->updateDate($tableData->id, ($file['fileDate'] != ""  && $this->validateDate($file['fileDate'])) ? $file['fileDate'] : date('Y-m-d'));

            // write to log
            $log = $this->createLogObject($tableData->static_name, 'success', $stats['countAffectedRecords']);
            $log->info = json_encode(array('type' => 'Initial from csv'));
            if (!empty($stats['recordsFailed'])) {
                $log->info = json_encode(array('type' => 'Initial from csv', 'failed_records' => $stats['recordsFailed']));
                if ($stats['countAffectedRecords'] == 0) $log->status = 'failed';
            }

            $this->getImportDataLogTable()->save($log);

            echo "Successfully uploaded " . $file['staticName'] . "\n";
        }
        exit;
    }



    public function createBackupFile($file,$table_name){

        $arr=$this->getListOptionsTable()->getList($table_name);

        if ($table_name=='MOH_KUPAT_CHOLIM_BRANCHES') {
            $file = '';
            $Institute_code='';
            $Branch_code='';
            $Heb_branch_name = '';
            $English_branch_name = '';
            $Type_of_facility_code = '';
            $Type_of_facility = '';
            $area = '';
            $city = '';
            $street = '';
            $number = '';


            foreach ($arr as $value) {
                $info=json_decode($value['notes']);

                $Institute_code=$info->Institute_code ;
                $Branch_code=$info->Branch_code;
                $Heb_branch_name =$info->Heb_branch_name  ;
                $English_branch_name = $info->English_branch_name;
                $Type_of_facility_code = $info->Type_of_facility_code;
                $Type_of_facility = $info->Type_of_facility;
                $area = $info->area;
                $city = $info->city;
                $street = $info->street;
                $number = $info->number;

                $file .= $Institute_code . '|' . $Branch_code . '|' . $Heb_branch_name . '|' . $English_branch_name . '|' . $Type_of_facility_code . '|' . $Type_of_facility . '|' . $area . '|' . $city . '|' . $street . '|' . $number . '\r\n';
            }
        }
                /* // for testing
                    $file = '3|60001991|îø.øôåàé îæøç é-í||09|îøëæ øôåàé(ñðéó)|îçåæ éøåùìéí åäùôìä|éøåùìéí|àì-àñôäàðé|8' . '\r\n';
                    $file .= '3|60001991|îø.øôåàé îæøç é-í||09|îøëæ øôåàé(ñðéó)|îçåæ éøåùìéí åäùôìä|éøåùìéí|àì-àñôäàðé|8' . '\r\n';
                    $file .= '3|60002007|îø.øôåàé øåèø||09|îøëæ øôåàé(ñðéó)|îçåæ éøåùìéí åäùôìä|éøåùìéí|àâøéôñ|40' . '\r\n';
                    $file .= '3|60002016|îø.øôåàé ëøîéàì||09|îøëæ øôåàé(ñðéó)|îçåæ öôåï|ëøîéàì|äùåùðéí|128' . '\r\n';
                    $file .= '1|60002028|îø.øôåàé ðöøú òéìéú||09|îøëæ øôåàé(ñðéó)|îçåæ öôåï|ðöøú òéìéú|îòìä éöç÷|14' . '\r\n';
                    $file .= '1|60002034|îø.øôåàé îòìåú||09|îøëæ øôåàé(ñðéó)|îçåæ öôåï|îòìåú-úøùéçà|ùøéøà ùìîä|3' . '\r\n';
                    $file .= '2|60002036|îø.øôåàé ðùø||09|îøëæ øôåàé(ñðéó)|îçåæ öôåï|ðùø|ãøê äùìåí|16' . '\r\n';
                    $file .= '2|60002044|îø.øôåàé ÷.ùîåðä||09|îøëæ øôåàé(ñðéó)|îçåæ öôåï|÷øééú ùîåðä|àøìåæåøåá|1' . '\r\n';
                    $file .= '2|60002051|îø.øôåàé ÷.èáòåï||09|îøëæ øôåàé(ñðéó)|îçåæ öôåï|÷øééú èáòåï|äøéîåðéí|1' . '\r\n';
                    $file .= '4|60002071|îø.øôåàé àìåï||09|îøëæ øôåàé(ñðéó)|îçåæ éøåùìéí åäùôìä|éøåùìéí|ùã âåìãä îàéø|225' . '\r\n';
                    $file .= '4|60002088|îø.øôåàé ôñâú æàá||09|îøëæ øôåàé(ñðéó)|îçåæ éøåùìéí åäùôìä|éøåùìéí|úåøï çééí|45' . '\r\n';
                 */



        return $file;
    }


    /**
     * This method get changes from EDM sever and save them in the DB
     * @param $table table name in the EDM sever
     * @return bool
     */
    public function updateFormEDM($table){

        // 1. init current table in the request plugin
        $this->getEDMRequestPlugin()->init($table->external_name);
        // 1. check in the EDM sever in have changes
        $isHaveChanges = $this->getEDMRequestPlugin()->haveChanges($table->update_at);
        if(!$isHaveChanges){
            $errorReason = $this->getEDMRequestPlugin()->getErrorReason();
            $log = $this->createLogObject($table->static_name,'failed');
            $log->info = json_encode(array('error_reason' => $errorReason));
            $this->getImportDataLogTable()->save($log);
            return false;
        }

        // get array of records that changed from EDM sever
        $changes = $this->getEDMRequestPlugin()->getChanges($table->update_at);
        if(!$changes){
            $errorReason = $this->getEDMRequestPlugin()->getErrorReason();

            $log = $this->createLogObject($table->static_name,'failed');
            $log->info = json_encode(array('error_reason' => $errorReason));
            $this->getImportDataLogTable()->save($log);
            return false;
        }

        $stats = $this->saveRecordsFromEdm($changes, $table);


        // update date in import_data table

        $this->getImportDataTable()->updateDate($table->id, date('Y-m-d H:i:s'));
        //write to log
        $log = $this->createLogObject($table->static_name, 'success', $stats['countAffectedRecords']);
        if(!empty($stats['recordsFailed'])){
            $log->info = json_encode(array('failed_records' => $stats['recordsFailed']));
            if($stats['countAffectedRecords'] == 0)$log->status = 'failed';
        }
        $this->getImportDataLogTable()->save($log);
        echo $table->static_name . ' was updated.' . PHP_EOL;
    }



    /**
     * insert /update data in Clinikal DB
     *
     **/
    private function saveRecordsFromEdm($changes, $table){

        // $this->die_r($changes);
        $countAffectedRecords = 0;
        $recordsFailed = array();

        //save the changes per row
        foreach ($changes as $change){
            $result = $this->saveRecord($change, $table, $countAffectedRecords);
            if($result === 'skip')continue;
            $countAffectedRecords++;
        }

        return array(
            'countAffectedRecords' => $countAffectedRecords,
            'recordsFailed' => $recordsFailed
        );
    }

    private function saveRecord($change , $table, $countAffectedRecords)
    {
        //create instance to row
        try{
            $listRecord = new $this->listController($change);
        } catch (\Exception $e){
            if($e->getCode() == 10){
                //record that should be ignored
                return 'skip';
            } else {
                error_log('Import data error:' . $table->external_name . ' ' . $e->getMessage());
                $recordsFailed[] = $change;
                //continue if there is empty description
                return 'skip';
            }
        }

        //convert to clinikal keys
        $listRecord->convertKeys();
        //get model with data (for example - for list Lists model etc.)
        $row = $listRecord->getTableObject();
        switch($listRecord::$table){
            case self::LIST_TABLES:
                //insert or update (if option id already exist)
                $this->getListOptionsTable()->save($row, $table->clinikal_name);
                break;
            case self::SSO_LIST:
                // Social security branches is exception list that your title is city name of the branch
                $cityName = $this->getListOptionsTable()->getCityName($listRecord->EDMdata['City']);
                if(!$cityName){
                    error_log('Import data error: missing city name for social security branch, city ID ' . $listRecord->EDMdata['City']);
                    $recordsFailed[] = $change;
                    continue;
                }
                $row->title = $cityName;
                $this->getListOptionsTable()->save($row, $table->clinikal_name);
                break;
            case self::CODE_TABLE:
                //insert or update (if option id already exist)
                $this->getCodesTable()->save($row);
                break;
        }
        // insert /update translation for current row
        $translation = $listRecord->getTranslation();
        if(!empty($translation)) {
            $this->setTranslation($translation['constant'], $translation['english'], $translation['hebrew']);
        }
        return true;
    }


    /**
     * insert or update translation in the all language table
     * @param $en_constant
     * @param $hebrew
     */
    private function setTranslation($constant, $english ,$hebrew){

            $constantId = $this->getLangConstantsTable()->getConstantId($constant);
            $consId = $this->getLangConstantsTable()->save($constantId, $constant);

            if(!$constantId){
                $this->getLangDefinitionsTable()->insert($consId, trim($hebrew), trim($english));
            } else {
                $this->getLangDefinitionsTable()->update($consId, trim($hebrew), trim($english));
            }

    }


    /**
     * @param $tableName
     * @return bool
     */
    private function initTable($tableName){

        switch ($tableName){

            case 'countries':
                $this->listController = 'ImportData\Lists\CountriesList';
                break;
            case 'city':
                $this->listController = 'ImportData\Lists\CityList';
                break;
            case 'death':
                $this->listController = 'ImportData\Lists\DeathList';
                break;
            case 'elsList':
                $this->listController = 'ImportData\Lists\ElsList';
                break;
            case 'expertise':
                $this->listController = 'ImportData\Lists\ExpertiseList';
                break;
            case 'hmo':
                $this->listController = 'ImportData\Lists\HmoList';
                break;
            case 'language':
                $this->listController = 'ImportData\Lists\LanguageList';
                break;
            case 'sso':
                $this->listController = 'ImportData\Lists\SsoList';
                break;
            case 'street':
                $this->listController = 'ImportData\Lists\StreetList';
                break;
            case 'title':
                $this->listController = 'ImportData\Lists\TitleList';
                break;
            case 'idtype':
                $this->listController = 'ImportData\Lists\IdtypeList';
                break;
            case 'gender':
                $this->listController = 'ImportData\Lists\GenderList';
                break;
            case 'frequency':
                $this->listController = 'ImportData\Lists\FrequencyList';
                break;
            case 'famillystatus':
                $this->listController = 'ImportData\Lists\FamillystatusList';
                break;
            case 'institute':
                $this->listController = 'ImportData\Lists\InstituteList';
                break;
            case 'institutetype':
                $this->listController = 'ImportData\Lists\InstitutetypeList';
                break;
            case 'icd9':
                $this->listController = 'ImportData\Lists\Icd9List';
                break;
            case 'icd10':
                $this->listController = 'ImportData\Lists\Icd10List';
                break;
            case 'mkcb':
                $this->listController = 'ImportData\Lists\MkcbList';
                break;
            case 'continents':
                $this->listController = 'ImportData\Lists\ContinentList';
                break;
            case 'healthdistrict':
                $this->listController = 'ImportData\Lists\HealthDistrict';
                break;
            case 'moh_drugs':
                $this->listController = 'ImportData\Lists\MohDrugs';
                break;
            //if not found code for this result
            default:
                return false;
        }

        return true;
    }


    /**
     * check if has error in the file or if is not csv file
     * @param $file
     * @return mixed
     * @throws \Exception
     */
    private function checkSecureFile($file){

        $errMessage = 'Error: Only .csv file is accepted for upload';
        if($file['error'] == 0) {
            //Check if the file is JPEG image and it's size is less than 350Kb
            $ext = pathinfo($file['name'],PATHINFO_EXTENSION);
            if ($ext == strtolower('csv')) {

                return $file['tmp_name'];

            } else {
                throw new \Exception(xla($errMessage));
            }
        } else {
            $errorMsg = $this->codeToMessage($file['error']);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * convert from csv to assoc array like data that return from EDM
     * @param $file
     * @return array
     * @throws \Exception
     */
    private function saveChangesFromCsv($file, $table){

        // $this->die_r($changes);
        $countAffectedRecords = 0;
        $recordsFailed = array();


        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, '|')) !== FALSE) {
                $row = array();
                foreach ($this->listController::$columns as $key => $column){
                    $row[$column] =  iconv('windows-1255', 'utf-8', $data[$key]);
                }
                $result = $this->saveRecord($row, $table, $countAffectedRecords);
                if($result === 'skip')continue;
                $countAffectedRecords++;
            }
            fclose($handle);
        } else {
            throw new \Exception('The file could not opened');
        }
        if(empty($result)){
            throw new \Exception('The file is empty');
        }

        return array(
            'countAffectedRecords' => $countAffectedRecords,
            'recordsFailed' => $recordsFailed
        );
    }

    private function createLogObject($table, $status, $affected_records = 0){

        $log = new ImportDataLog();
        $log->id= time();
        $log->table = $table;
        $log->status = $status;
        $log->affected_records = $affected_records;
        $log->info = '';
        $log->update_at = date("Y-m-d H:i:s");

        return $log;
    }

    /*/**
     * get instance of PatientData class
     * @return array|object
     */
    /**
     * @return EDMRequest
     */
    private function getEDMRequestPlugin()
    {
        if (!$this->EDMRequestPlugin) {
            $this->EDMRequestPlugin = new EDMRequest();
        }
        return $this->EDMRequestPlugin;
    }


    /*/**
     * get instance of PatientData class
     * @return array|object
     */
    /**
     * @return array|object
     */
    private function getImportDataTable()
    {
        if (!$this->importDataTable) {
            $this->importDataTable = $this->container->get('ImportData\Model\ImportDataTable');
        }
        return $this->importDataTable;
    }

    /**
     * @return array|object
     */
    private function getListOptionsTable()
    {

        if (!$this->ListsTable) {
            $this->ListsTable = $this->container->get('ImportData\Model\ListsTable');
        }
        return $this->ListsTable;
    }

    /**
     * @return array|object
     */
    private function getCodesTable()
    {

        if (!$this->CodesTable) {
            $this->CodesTable = $this->container->get('ImportData\Model\CodesTable');
        }
        return $this->CodesTable;
    }
    /**
     * @return array|object
     */
    private function getLangDefinitionsTable()
    {

        if (!$this->LangDefinitionsTable) {
            $this->LangDefinitionsTable = $this->container->get('ImportData\Model\LangDefinitionsTable');
        }
        return $this->LangDefinitionsTable;
    }

    /**
     * @return array|object
     */
    private function getLangConstantsTable()
    {

        if (!$this->LangConstantsTable) {
            $this->LangConstantsTable = $this->container->get('ImportData\Model\LangConstantsTable');
        }
        return $this->LangConstantsTable;
    }

    /**
     * @return array|object
     */
    private function getImportDataLogTable()
    {

        if (!$this->importDataLogTable) {
            $this->importDataLogTable = $this->container->get('ImportData\Model\ImportDataLogTable');
        }
        return $this->importDataLogTable;
    }


    /**
     * get the error message according error code
     * @param $code
     * @return string
     */
    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = xlt("The uploaded file exceeds the upload_max_filesize directive in php.ini");
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = xlt("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form");
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = xlt("The uploaded file was only partially uploaded");
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = xlt("No file was uploaded");
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = xlt("Missing a temporary folder");
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = xlt("Failed to write file to disk");
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = xlt("File upload stopped by extension");
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    private function validateDate($date)
    {
        $tempDate = explode('-', $date);
        // checkdate(month, day, year)
        return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
    }


/**
     * function for debugger
     */
    public function die_r($dada) {
        echo "<pre>";
        print_r($dada);
        echo "</pre>";
        die;
    }

}


