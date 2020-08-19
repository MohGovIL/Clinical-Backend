<?php
/**
 * Created by PhpStorm.
 * User: eyalvo
 * Date: 23/04/18
 * Time: 14:12
 */

namespace ReportTool\Controller;

use ReportTool\Model\CustomDB;
use Zend\Mvc\Controller\AbstractActionController;
use GenericTools\Controller\BaseController as GenericBaseController;
use Application\Listener\Listener;
use Zend\View\Model\ViewModel;
use ReportTool\Form\FiltersForm;
use GenericTools\Controller\GenericToolsController;
use Interop\Container\ContainerInterface;

class BaseController extends GenericBaseController
{

    const ID = 'id';
    const COLUMNS = 'columns';
    const TITLE = 'title';
    const ROUTE = 'route';
    const DRAW = 'draw';
    const FILTERS = 'filters';
    const DATA = 'data';
    const COUNT = 'count';
    const DEFAULT = 'default';
    const LISTS = 'lists';
    const FILTERS_BINDINGS = 'filters_binding';

    const EPIDEMIOLOGY_GROUP = 'epidemiology';
    const MANUFACTURER_LIST = 'moh_vac_vaccines_manufacturer';
    const DESTINATIONS_FILTERS_LIST = 'moh_filter_destinations';
    const GROUPS_FILTERS_LIST = 'moh_filter_groups';
    const AGE_FILTERS_LIST = 'moh_filter_age';
    const STATUS_FILTERS_LIST = 'moh_filter_status';

    const HEALTH_PROFESSION_CATEGORY = 'moh_profession_category';
    const OCCUPATION = 'moh_occupations';
    const VACCINE_PLAN = 'moh_filter_vaccination_plan';
    const PROGRAM_CONFIRMATION = 'moh_filter_confirmation';
    const MAX_ROW_FOR_PDF = 100000000;

    protected $filtersElements;
    protected $filters = array();
    protected $filtersBinding = array();
    protected $filtersNewLine = array(); // 'false' to keep filter on same line, 'true' to move filter to new line
    protected $header;
    protected $procedureName;


    public function __construct(ContainerInterface $container, $procedureName)
    {
        parent::__construct($container);
        $this->container = $container;
        $this->filtersElements = new FiltersForm();
        $this->procedureName = $procedureName;
    }


    protected function initDefaultFilters()
    {
        //facility
        $facilities = $this->isUserFromDepartmentOfEpidemiology() ? $this->getFacilityTable()->getListForViewForm() : array(GenericToolsController::getCurrentFacility(true) => GenericToolsController::getCurrentFacility(false, true));
        //$facilities['no_filter_facility'] = xlt("Without cut clinic");
        $this->addSelectFilter('facility', 'Facility', $facilities, GenericToolsController::getCurrentFacility(true), 230, true);
        //from date
        $this->addInputFilter('from_date', 'From date', 120, oeFormatShortDate(date('Y-m-01')),true);
        //to date
        $this->addInputFilter('until_date', 'Until date', 120, oeFormatShortDate(),true);

    }

    protected function normalizedGenericFilters($filters)
    {
        $filters['from_date'] = "'" . DateToYYYYMMDD($filters['from_date']) . "'";
        $filters['until_date'] = "'" . DateToYYYYMMDD($filters['until_date']) . "'";
        $filters['facility'] = "'" . $filters['facility'] . "'";

        return $filters;

    }

    protected function AddSpecialFilters($array, $withAll = true, $withNone = true, $withChoose = false)
    {

        $tempArr = array();

        if ($withChoose) {
            $tempArr['-3'] = xlt('Choose');
        }

        if ($withNone) {
            $tempArr['-2'] = xlt('None');
        }

        if ($withAll) {
            $tempArr['-1'] = xlt('All');
        }

        $array = $tempArr + $array;

        return $array;

    }


    protected function renderHeader($title)
    {

        /* default filters  */
        //facility

        $vm = new ViewModel(array(
            'title' => $title,
            'elements' => $this->filtersElements,
            'filters' => $this->filters,
            'new_lines' => $this->filtersNewLine
        ));
        $vm->setTemplate('report-tool/reports/header');

        $renderer = $this->container->get('Zend\View\Renderer\PhpRenderer');
        $this->header = $renderer->render($vm);


    }

    protected function renderReport($helperType, $data = null, $view = null)
    {
        //return
        $viewModel = new ViewModel(array('header' => $this->header, 'helper' => $helperType, 'data' => $data));
        if (!is_null($view)) {
            $viewModel->setTemplate('report-tool/reports/' . $view);
        } else {
            $viewModel->setTemplate('report-tool/reports/index');
        }

        return $viewModel;
    }

    /*
     * Examples:
     *
     * The following will fill filter2 select-box with data returned from ajax_url on change of filter1's value
     * addFilterBinding('data_sync', ['filter1', 'filter2', 'ajax_url'])
     *
     * The following will disable filter2 if filter1 is chosen (can extend this functionality later if needed)
     * addFilterBinding('disable', ['filter1', 'filter2'])
     */
    protected function addFilterBinding($type, array $logic)
    {
        $this->filtersBinding[$type][] = $logic;
    }

    protected function addInputFilter($name, $title, $css, $defaultValue = '', $date = false, $newLine = false)
    {
        $class = 'form-control';
        if ($date) {
            $class .= ' datepicker';
        }
        $this->filtersElements->add(array(
            'name' => $name,
            'type' => 'Text',
            'attributes' => array(
                'id' => $name,
                'class' => $class,
                'autocomplete' => 'off',
                'style' =>  is_numeric($css) ? "width:${$css}px" : $css
            ),
            'options' => array(
                'label' => ($title!='') ? xlt($title) . ':' : "",
            ),
        ));
        $this->filtersElements->get($name)->setValue($defaultValue);
        $this->filters[] = $name;
        $this->filtersNewLine[$name] = $newLine;
    }

    protected function addSelectFilter($name, $title, $list, $selected, $width, $multiple = false, $newLine = false, $classString = '') {
        $this->filtersElements->add(array(
            'name' => $name,
            'type' => 'select',
            'attributes' => array(
                'id' => $name,
                'class' => ($classString === '') ? 'form-control' : $classString,
                'multiple' => $multiple,
                'style' => "width:${width}px",
            ),
            'options' => array(
                'label' => ($title == '') ? '' : xlt($title) . ':',
                'value_options' => $list
            )
        ));
        $this->filtersElements->get($name)->setValue($selected);
        $this->filters[] = $name;
        $this->filtersNewLine[$name] = $newLine;
    }


    protected function addRadioFilter($name, $title, $list, $selected, $newLine = false)
    {
        $this->filtersElements->add(array(
            'type' => 'radio',
            'name' => $name,
            'attributes' => array(
                'id' => $name,
                'class' => ''
            ),
            'options' => array(
                'label' => xlt($title) . ':',
                'value_options' => $list
            ),
        ));
        $this->filtersElements->get($name)->setValue($selected);
        $this->filters[] = $name;
        $this->filtersNewLine[$name] = $newLine;
    }


    protected function addLink($name, $title, $link = "javascript:;")
    {
        $this->filtersElements->add(array(
            'type' => 'url',
            'name' => $name,
            'attributes' => array(
                'href' => $link,
                'id' => $name,
                'class' => '',
                'title' => xlt($title),
            ),
            'options' => array(
                'label' => '',
                'class' => 'form-inline form-control',
                'label_attributes' => array()
            ),
        ));
        $this->filtersElements->get($name)->setValue($selected);
        $this->filters[] = $name;
        $this->filtersNewLine[$name] = $newLine;
    }


    protected function unsetFieldFromDataSet($dataSet, $fieldName)
    {

        foreach ($dataSet as $key => $value) {
            unset($dataSet[$key][$fieldName]);
        }
        return $dataSet;

    }

    protected function GetCustomDB()
    {

        $dbAdapter = $this->container->get('Zend\Db\Adapter\Adapter');
        $CustomDb = new CustomDB($dbAdapter);
        return $CustomDb;
    }

    public function isUserFromDepartmentOfEpidemiology()
    {
        $aclGroup = $this->getAclTables()->whatIsUserAroGroups($_SESSION['authUserID']);
        return in_array(self::EPIDEMIOLOGY_GROUP, $aclGroup) ? true : false;
    }

    protected function createFormResultForTable($rows, $draw, $arrayColumns, $columnsCount)
    {
        $newArrayObject = [];
        $newArrayObject['draw'] = $draw;
        $newArrayObject['recordsTotal'] = $columnsCount;//$rows[0]['rows_count'];
        $newArrayObject['recordsFiltered'] = $columnsCount;
        $newArrayObject['title'] = "Patient data by ";

        $arr = array();
        $strData = '';
        foreach ($rows as $row) {
            $arrTemp = [];
            foreach ($arrayColumns as $column) {
                $arrTemp[$column] = xlt($row[$column]);
            }
            $arr[] = $arrTemp;
        }
        if (empty($arr)) {
            $newArrayObject['data'] = array();
        }
        $newArrayObject['data'] = $arr;

        return $newArrayObject;
    }

    public function generateExcel($dataToProcedure, $columns, $fileName, $tabTitle)
    {

        $result = $this->getData($dataToProcedure, $columns);
        //CV-2891
        //die(var_dump($result)); -- check for array
        // no need foer this it is already array
        //$data = json_decode($result, true)['data'];
        $data =  $result['data'] ;
        $columnsName = array();
        foreach ($columns as $key => $nameArr) {
            $columnsName[$key] = $nameArr['title'];
        }

        $excelService = $this->getExcelService();
        $excelService->setColumnsNames($columnsName);
        $excelService->setData($data);
        $excelService->setTabTitle($tabTitle);
        $excelService->fileName($fileName);
        $excelService->downloadFile();
    }

    public function pdfDefaultSettings()
    {

        $filters = (array)json_decode($this->params()->fromQuery(self::FILTERS));
        $filters['offset'] = 0;
        $filters['limit'] = self::MAX_ROW_FOR_PDF;
        $filters['facility'] = implode(',', $filters['facility']);
        $columns = (array)json_decode($this->params()->fromQuery(self::COLUMNS), true);

        $settings = array('filters' => $filters, 'columns' => $columns);

        return $settings;

    }

    public function ajaxDefaultSettings()
    {

        $filters = $this->params()->fromQuery(self::FILTERS);
        $filters['offset'] = $this->params()->fromQuery('start');
        $filters['limit'] = $this->params()->fromQuery('length');
        $columns = $this->params()->fromQuery(self::COLUMNS);

        $settings = array('filters' => $filters, 'columns' => $columns);

        return $settings;

    }

    public function createReportPdf($dataToProcedure, $columns, $reportName)
    {
        $data = $this->getData($dataToProcedure, $columns);
        $title = $this->params()->fromQuery(self::TITLE);
        $this->pdfWithStandardHeaderFooter('report-tool/pdf/report-pdf', array(
            'title' => $title,
            'data' => $data,
            'columns' => $columns
        ), 'summary_' . $reportName . date("Y_m_d"));
    }


    /**
     * get instance of VacCommercialNames
     * @return array|object
     */
    protected function getVacCommercialNamesTable()
    {
        if (!$this->VacCommercialNames) {
            $this->VacCommercialNames = $this->container->get(\LogisticManagement\Model\VacCommercialNamesTable::class);
        }
        return $this->VacCommercialNames;
    }


    /**
     * get instance of PatientVaccines
     * @return array|object
     */
    protected function getVacVaccinesTable()
    {
        if (!$this->VacVaccines) {
            $this->VacVaccines = $this->container->get(\LogisticManagement\Model\VacVaccinesTable::class);
        }
        return $this->VacVaccines;
    }

    protected function getRabiesVeterinaryServiceTable()
    {
        if (!$this->RabiesVeterinaryService) {
            $this->RabiesVeterinaryService = $this->container->get(\RabiesIncident\Model\RabiesVeterinaryServiceTable::class);
        }

        return $this->RabiesVeterinaryService;
    }


    /**
     * get instance of getPatientVaccinesMailLogTable
     * @return array|object
     */
    protected function getPatientVaccinesMailLogTable()
    {
        if (!$this->PatientVaccinesMailLog) {
            $this->PatientVaccinesMailLog = $this->container->get(\PatientVaccines\Model\PatientVaccinesMailLogTable::class);
        }
        return $this->PatientVaccinesMailLog;
    }



    /**
     * get instance of PatientVaccines
     * @return array|object
     */
    protected function getPostcalendarCategoriesTable()
    {
        if (!$this->PostcalendarCategories) {
            $this->PostcalendarCategories = $this->container->get(\GenericTools\Model\PostcalendarCategoriesTable::class);
        }
        return $this->PostcalendarCategories;
    }

    /**
     * get data from procedure
     * @return array|object
     */
    protected function getData($dataToProcedure, $columns, $additionalParams = array())
    {

        $draw = $this->params()->fromQuery('draw');
        $data = $this->GetCustomDB()->CreateReportSql($dataToProcedure, $this->procedureName);

        if (!empty($data)) {
            $rowsCount = $data[0]['count'];
            $data = $this->unsetFieldFromDataSet($data, 'count');

        } else {
            $rowsCount = 0;
        }

        $arrColumnsNames = [];
        foreach ($columns as $colName) {
            array_push($arrColumnsNames, $colName[self::DATA]);
        }

        $result = $this->createFormResultForTable($data, $draw, $arrColumnsNames, $rowsCount);
        if (!empty($additionalParams)) {
            $result['additionalParams'] = $additionalParams;
        }

        return $result;
    }


    public function searchPatient(){

        $pageSize=500;

        $searchTerm=$_POST['search'];
        $searchTerm=trim($searchTerm);
        $searchTerm=preg_replace('/[^A-Z a-zא-ת\'\-]/', '', $searchTerm);
        $searchTerm='%'.preg_replace('/[ ]/', '%', $searchTerm).'%';

        $names = $this->GetCustomDB()->findPatientByName($searchTerm);

        $page=array_slice($names, 0, $pageSize);
        $page[]=  array ('id' => '-1', 'text' => xlt('All'));
        $more=array_slice($names, $pageSize);

        $result=array(
            "results"=>$page,
            "pagination"=>array(
                "more"=>$more,
            )
        );
        $result=json_encode($result);

        return $result;

        //$draw = $this->params()->fromQuery('draw');
        //$data = $this->GetCustomDB()->CreateReportSql($dataToProcedure, $this->procedureName);
    }



}
