<?php
/**
 * Created by PhpStorm.
 * User: yuriyge
 * Date: 2/24/19
 * Time: 5:59 PM
 */

namespace ClinikalApi\Controller;

use GenericTools\Controller\BaseController;
use GenericTools\Model\LangLanguagesTable;
use GenericTools\Model\ListsTable;
use Interop\Container\ContainerInterface;
use ClinikalAPI\Model\GetTemplatesServiceTable;
use GenericTools\Model\RegistryTable;

class FormTemplatesManagementController extends BaseController

{
    const TITLE = "templates Managment";
    private $forms;
    private $fileds;
    private $serviceTypes;
    private $reasonCodes;
    private $templates;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    public function templatesManagementIndexAction()
    {

//        if (!acl_check('modules', 'warnings_and_contraindications_management', '', 'write')) {
//            $this->redirect()->toRoute('errors', array('action' => 'access-denied'));
//        }
        $this->loadClientSideForms();
        $this->loadServiceTypes();
        $this->loadtemplates();

        $langCode = ($this->container->get(LangLanguagesTable::class)->getLangCode($_SESSION['language_choice'] ? $_SESSION['language_choice'] : $this->container->get(LangLanguagesTable::class)->getLangIdByGlobals()));
        $data = $this->normalizeDataForTable($this->container->get(GetTemplatesServiceTable::class)->fetch($langCode, ['active' => 1]));
        //$this->die_r($data);

        $parameters = array(
            'title' => xlt(self::TITLE),
            'data'  => $data,
            'forms' => $this->forms,
            'serviceTypes' => $this->serviceTypes,
            'templates' => $this->templates
        );
        $this->layout('clinikalApi/layout/layout');
        return $parameters;
    }

    private function loadClientSideForms()
    {
        $this->forms = $this->container->get(RegistryTable::class)->getFormsKeyDirectoryValueName(['category' => 'React form']);
    }

    private function loadFormFileds($formId = null)
    {
        $this->fileds = is_null($formId)
            ?
            $this->container->get(ListsTable::class)->getListForViewForm('clinikal_form_fields_templates', true)
            :
            $this->container->get(ListsTable::class)->getListForViewForm('clinikal_form_fields_templates', true, ['notes' => $formId]);
    }

    private function loadServiceTypes()
    {
        $this->serviceTypes = $this->container->get(ListsTable::class)->getListForViewForm('clinikal_service_types', true);
    }

    private function loadReasonCodes($serviceType = null)
    {
        $this->reasonCodes = is_null($serviceType)
            ?
            $this->container->get(ListsTable::class)->getListForViewForm('clinikal_reason_codes', true)
            :
            $this->container->get(ListsTable::class)->getListForViewForm('clinikal_reason_codes', true, ['notes' =>  $serviceType]);
    }

    private function loadtemplates()
    {
        $this->templates = $this->container->get(ListsTable::class)->getListForViewForm('clinikal_templates', true);
    }

    private function normalizeDataForTable($dbMapping)
    {
        $results = [];
        foreach ($dbMapping as $item) {
            $results[] = [
                $item['form'],
                $item['field'],
                $item['service_type'],
                $item['reason_code'],
                $item['template'],
                (intval($item['active']) === 1) ? "<i style='color: green;font-size: 1.7em;' class='fas fa-check-circle'></i>" : "<i style='color: red;  font-size: 1.7em;' class='fas fa-times-circle'></i>",
                '<button onclick="gotoEdit(' . "'{$item['form']}','{$item['field']}',{$item['service_type']},{$item['reason_code']}" . ')">' . xlt('Edit') . '</button>'
            ];
        }
        return $results;
    }


    public function templatesManagementAjaxAction()
    {
        $queryFilters = [];
        if ($this->params()->fromQuery('form_name') && $this->params()->fromQuery('form_name') !== 'all') {
            $queryFilters['form_id'] = $this->params()->fromQuery('form_name');
        }
        if ($this->params()->fromQuery('field_name') && $this->params()->fromQuery('field_name') !== 'all') {
            $queryFilters['form_field'] = $this->params()->fromQuery('field_name');
        }
        if ($this->params()->fromQuery('service_type') && $this->params()->fromQuery('service_type') !== 'all') {
            $queryFilters['service_type'] = $this->params()->fromQuery('service_type');
        }
        if ($this->params()->fromQuery('reason_code') && $this->params()->fromQuery('reason_code') !== 'all') {
            $queryFilters['reason_code'] = $this->params()->fromQuery('reason_code');
        }
        if ($this->params()->fromQuery('template') && $this->params()->fromQuery('template') !== 'all') {
            $queryFilters['message_id'] = $this->params()->fromQuery('template');
        }
        if (!is_null($this->params()->fromQuery('active')) && $this->params()->fromQuery('active') !== 'all' && $this->params()->fromQuery('active') !== '') {
            $queryFilters['active'] = $this->params()->fromQuery('active');
        }

        $langCode = ($this->container->get(LangLanguagesTable::class)->getLangCode($_SESSION['language_choice'] ? $_SESSION['language_choice'] : $this->container->get(LangLanguagesTable::class)->getLangIdByGlobals()));
        $data = $this->normalizeDataForTable($this->container->get(GetTemplatesServiceTable::class)->fetch($langCode, $queryFilters));
        $parms = array('data' => $data);
        return $this->responseWithNoLayout($parms, true);
    }

    public function loadFiledsAction()
    {
        $form = $this->params()->fromQuery('filter');
        if (empty($form)) {
            throw new \Exception('Missing filter parameter');
        }
        $this->loadFormFileds($form);
        return $this->responseWithNoLayout($this->fileds ? $this->fileds : []);
    }

    public function loadReasonCodesAction()
    {
        $serviceType = $this->params()->fromQuery('filter');
        if (empty($serviceType)) {
            throw new \Exception('Missing filter parameter');
        }
        $this->loadReasonCodes($serviceType);
        return $this->responseWithNoLayout($this->reasonCodes ? $this->reasonCodes : []);
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function checkIftemplatesExistAction()
    {
        /*$row_id          = intval($_POST['row_id']);
        $recipient_email = $_POST['recipient_email'];
        $recipient_type  = $_POST['recipient_type'];
        $facility_id     = $_POST['facility_id'];
        $isEdit          = $_POST['is_edit'];

        if ($isEdit == "0") {
            $rez     = empty($this->getVacListMailsTable()->getAllActiveNames(array(
                    'recipient_email' => $recipient_email,
            )));
            $isExist = ($rez) ? 0 : 1;
            $data    = array(
                'is_exist' => $isExist
            );
            $parms   = array(
                'data' => $data
            );
        }
        else {
            $isExist = 0;
            $data    = array(
                'is_exist' => $isExist
            );
            $parms   = array(
                'data' => $data
            );
        }*/
        return $this->responseWithNoLayout($parms, true);
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function saveAssignTempleteAction()
    {
       /* $row_id          = intval($_POST['row_id']);
        $recipient_email = $_POST['recipient_email'];
        $recipient_type  = $_POST['recipient_type'];
        $facility_id     = $_POST['facility_id'];
        $isEdit          = $_POST['is_edit'];
        $updateDate      = date("Y-m-d");
        $updateBy        = $_SESSION['authUserID'];

        if ($isEdit == 1) {
            $this->getVacListMailsTable()->updateRow($row_id, $recipient_email, $recipient_type, $facility_id, $updateDate, $updateBy);
        } else {
            $this->getVacListMailsTable()->insertRow($recipient_email, $recipient_type, $facility_id, $updateDate, $updateBy);
        }
        $data       = "";
        $parameters = array(
            'data' => $data
        );*/
        return $this->responseWithNoLayout($parameters, true);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function addEditEmailsVaccinesManagementAction()
    {
       /* $id              = $this->params()->fromQuery('row_id');
        $object          = $this->getVacListMailsTable()->fetchRow($id);
        $facilities_list = $this->getFacilityTable()->getListWithSomeKeyValue("facility_code", "name");
        $list = array(
                        "facilities_list" => $facilities_list,
                     );

        if (!empty($id)) {
            $is_edit    = 1;
            $row_id     = $id;
            $date       = $object['updated_date'];
            $userName   = $this->getUserNameById($object['updated_by']);
            $form       = new EmailsVaccinesAddEditForm($list);
            $form->setData($object);
        } else {
            $is_edit    = 0;
            $row_id     = 0;
            $date       = "";
            $userName   = "";
            $form       = new EmailsVaccinesAddEditForm($list);
        }*/

        return $this->renderView(array(
            'title'     => xlt(self::TITLE),
            'form'      => $form,
            'userName'  => $userName,
            'date'      => $date,
            'is_edit'   => $is_edit,
            'row_id'    => $row_id,
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function deleteAssignTempleteAjaxAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost()['id'];
            $status = $this->getVacListMailsTable()->deleteRow($id);
            return $this->ajaxOutPut( ($status ? 'success' : 'false') );
        }
    }



    function getRecordsByField($data, $field_name)
    {
        $result = false;
        if (is_array($data) && strlen($field_name) > 0) {
            foreach ($data as $row) {
                if (array_key_exists($field_name, $row) && strlen($row[$field_name]) > 0 ) {
                    $result[] = $row[$field_name];
                }
            }
        }
        return (is_array($result) ? array_unique($result) : $result);
    }
}
