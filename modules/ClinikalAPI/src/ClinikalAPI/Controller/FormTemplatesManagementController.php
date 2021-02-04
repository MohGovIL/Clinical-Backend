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
        $data = $this->normalizeDataForTable($this->container->get(GetTemplatesServiceTable::class)->fetchNormalizedData($langCode, ['active' => 1]));
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
                '<button onclick="gotoEdit(' . "'{$item['form_id']}','{$item['field_id']}','{$item['service_type_id']}','{$item['reason_code_id']}','{$item['template_id']}'" . ')">' . xlt('Edit') . '</button>'
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
        $data = $this->normalizeDataForTable($this->container->get(GetTemplatesServiceTable::class)->fetchNormalizedData($langCode, $queryFilters));
        $parms = array('data' => $data);
        return $this->responseWithNoLayout($parms, true);
    }

    public function assignTemplateAction()
    {
        $this->loadClientSideForms();
        $this->loadServiceTypes();
        $this->loadtemplates();

        $parameters = array(
            'title' => xlt(self::TITLE),
            'forms' => $this->forms,
            'serviceTypes' => $this->serviceTypes,
            'templates' => $this->templates,
            'user' => $this->getUserNameById($this->getConnectedUserId())
        );

        if ($this->params()->fromQuery('edit')) {
            $queryFilters = [];
            $queryFilters['form_id'] = $this->params()->fromQuery('form_id');
            $queryFilters['form_field'] = $this->params()->fromQuery('field_id');
            $queryFilters['service_type'] = $this->params()->fromQuery('service_type');
            $queryFilters['reason_code'] = $this->params()->fromQuery('reason_code');
            $queryFilters['message_id'] = $this->params()->fromQuery('template');

            $result = $this->container->get(GetTemplatesServiceTable::class)->get($queryFilters)[0];
            $parameters['record'] = $result;
            $this->loadFormFileds($result['form_id']);
            $this->loadReasonCodes($result['service_type']);
            $parameters['formFiles'] = $this->fileds;
            $parameters['reasonCode'] = $this->reasonCodes;
            $parameters['is_edit'] = true;
        }

        $this->layout('clinikalApi/layout/layout');
        return $parameters;

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
        $reasonCodes = $this->reasonCodes ? $this->reasonCodes : [];
        $allReasonsOptions = [GetTemplatesServiceTable::ALL_REASON_CODE => xlt(GetTemplatesServiceTable::ALL_REASON_CODE_STRING)];

        return $this->responseWithNoLayout(array_merge( $reasonCodes, $allReasonsOptions));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function saveAssignTemplateAction()
    {
        $data = $this->params()->fromPost('data');
        $isEdit = $this->params()->fromPost('is_edit');
        $oldData = $isEdit ? $this->params()->fromPost('old_data') : null;

        if ($this->validateData($data)) {
            $isExistData = $data;
            unset($isExistData['seq'], $isExistData['active']);
            $isExist = !empty($this->container->get(GetTemplatesServiceTable::class)->get($isExistData)) ? true : false;
            if(!$isExist && $isEdit) {
                //delete old record
                $this->container->get(GetTemplatesServiceTable::class)->delete($oldData);
            }
            if($isExist && $isEdit) {
                if($this->primaryKeysNotChanged($data, $oldData)) {
                    $this->container->get(GetTemplatesServiceTable::class)->delete($oldData);
                } else {
                    return $this->responseWithNoLayout('data_conflict', true, 409);
                }
            }
            if ($isExist && !$isEdit) {
                return $this->responseWithNoLayout('data_conflict', true, 409);
            }

            $data['update_by'] = $this->getConnectedUserId();
            $result = $this->container->get(GetTemplatesServiceTable::class)->insert($data);
            return $this->responseWithNoLayout(true, true);

        }
        return $this->responseWithNoLayout(false, 500);
    }

    private function validateData($data)
    {
        $requiredFileds = ['form_id','form_field','service_type','reason_code','message_id','seq','active'];
        foreach ($requiredFileds as $name) {
           if(!isset($data[$name]) || is_null($data[$name]) || $data[$name] == '') {
               return false;
           }
        }
        return true;
    }

    private function primaryKeysNotChanged($data, $oldData)
    {
        $requiredFileds = ['form_id','form_field','service_type','reason_code','message_id'];
        foreach ($requiredFileds as $name) {
            if($data[$name] !== $oldData[$name]) return false;
        }
        return true;
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
