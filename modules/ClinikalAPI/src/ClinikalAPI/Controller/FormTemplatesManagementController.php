<?php
/**
 * Created by PhpStorm.
 * User: yuriyge
 * Date: 2/24/19
 * Time: 5:59 PM
 */

namespace ClinikalApi\Controller;

use Interop\Container\ContainerInterface;

class FormTemplatesManagementController extends BaseController

{
    const TITLE = "Managment of email address for serious side-effects";
    const MAILS_LIST = "moh_vac_list_mails";

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    public function TempleteManagementIndexAction()
    {
//        if (!acl_check('modules', 'warnings_and_contraindications_management', '', 'write')) {
//            $this->redirect()->toRoute('errors', array('action' => 'access-denied'));
//        }

        /*$data            = $this->getVacListMailsTable()->fetchAll();
        $facilities_list = $this->getFacilityTable()->getListWithSomeKeyValue("facility_code", "name");
        $recipient_mail  = $this->getRecordsByField($data, "recipient_email");
        $data            = $this->renderDataForDataTable($data, $facilities_list);

        $parameters = array(
            'data'              => $data,
            'title'             => xlt(self::TITLE),
            'facilities_list'   => $facilities_list,
            'recipient_mail'    => $recipient_mail,
            'recipientType'     => Array(
                                         "Permanent" => xlt("Permanent"),
                                         "Variable" => xlt("Variable"),
                                        ),
        );*/
        $parameters = [];
        return $this->renderView($parameters, true);
    }

    public function templatesManagementAjaxAction()
    {
        /*if (isset($_GET['action'])) {
            $recipient_email = ($this->params()->fromQuery('recipient_email') !== null) ? $this->params()->fromQuery('recipient_email') : 'all';
            $recipient_type  = ($this->params()->fromQuery('recipient_type') !== null) ? $this->params()->fromQuery('recipient_type') : 'all';
            $facility_id     = ($this->params()->fromQuery('facility_id') !== null) ? $this->params()->fromQuery('facility_id') : 'all';

            $data = $this->getVacListMailsTable()->getAddresses($recipient_email, $recipient_type, $facility_id );
        } else {
            $data = $this->getVacListMailsTable()->fetchAll();
        }

        $facilities_list = $this->getFacilityTable()->getListWithSomeKeyValue("facility_code", "name");

        $data = $this->renderDataForDataTable($data, $facilities_list);*/
        $parms = array('data' => $data);
        return $this->responseWithNoLayout($parms, true);
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function checkIfTempletesExistAction()
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

    /**
     * @param $dataArr
     * @param $facilities_list
     * @return array
     */
    private function renderDataForDataTable($dataArr,$facilities_list)
    {
        $resultAll = array();

        $counter = 0;
        foreach ($dataArr as $index => $temp_record) {
            $resultAll[$counter][] = xlt($temp_record['id']);
            $resultAll[$counter][] = "<span id='editRow_" . $temp_record['id'] . "' class='editRow' style='color:blue' ><u>" . attr($temp_record['recipient_email']) . "</u> </span>";
            $resultAll[$counter][] = xlt($temp_record['recipient_type']);
            $resultAll[$counter][] = (array_key_exists($temp_record["facility_id"], $facilities_list) ? xlt($facilities_list[$temp_record['facility_id']]) : "");
            $counter++;
        }

        return $resultAll;
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
