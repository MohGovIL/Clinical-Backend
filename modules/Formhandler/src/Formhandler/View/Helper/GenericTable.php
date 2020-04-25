<?php
/**
 * Created by DROR GOLAN.
 * User: drorgo
 */

namespace Formhandler\View\Helper;

use Application\Listener\Listener;
use Formhandler\Model\customDB;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Laminas\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;


class GenericTable extends AbstractHelper
{

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    private $edit_params = null;

    public function setEditParams($edit_params)
    {
        $this->edit_params = $edit_params;
    }

    public static $fieldLists = array(
        "moh_materials_type"
    );
    protected $dbAdapter;

    /* public function __construct($dbAdapter)
     {
         $string=$dbAdapter;
         $this->dbAdapter = new $dbAdapter;
         __invoke("hello");
     }*/


    public function setDbAdapter($adapter)
    {
        $this->dbAdapter = $adapter;
    }

    /**
     * Return the variable that contains the fields that use lists
     * to translate  them from id to value
     */
    public static function getFieldLists()
    {
        return self::$fieldLists;
    }

    protected function getCustomDB()
    {
        $CustomDb = new CustomDb($this->dbAdapter);
        return $CustomDb;
    }

    public function FetchDataFromApi($RouteControllerAction)
    {

        $RouteControllerAction = str_replace("/", "\\", $RouteControllerAction);
        $arrControllerAction = explode("::", $RouteControllerAction);
        $controller = $arrControllerAction[0];
        $action = $arrControllerAction[1];
        $instance = new $controller($this->sm);
        $data = $instance->$action();

        return $data;
        //call for Amiel's API code
        return '[[{"name":"country_name","value":"Israel"},{"name":"group_1","value":"A# b*"},{"name":"group_2","value":"a+ Y-"},{"name":"group_3","value":"k* d*"},{"name":"comment","value":"KUHABANGA"}],[
 {"name":"country_name","value":"Africa"},{"name":"group_1","value":"a# b*"},{"name":"group_2","value":"k* d*"},{"name":"group_3","value":"a+ y-"},{"name":"comment","value":""}]]';


    }

    public static function DecorateRowData($blobData, $listReturned, $customdb)
    {

        $table = $blobData[0]['table'];
        $column = $blobData[0]['column'];
        $pid = $blobData[0]['pid'];
        $columnsTypes = $blobData[0]['columnsTypes'];
        $columnsNames = $blobData[0]['columnsNames'];
        $click = $blobData[0]['columnsClicks'];
        $change = $blobData[0]['columnsChanges'];
        $columnName = $blobData[0]['columnName'];
        $attributes = $blobData[0]['columnsAttributes'];
        $mouseDown = $blobData[0]['columnsMouseDown'];
        $mouseUp = $blobData[0]['columnsMouseUp'];
        $doTranslation = $blobData[0]['columnTranslate'];
        $translate = new Listener();

        if (is_null($listReturned)) {
            return "[]";
        }

        $data = [];

        $rowCounter = 0;

        //$listReturned   = json_decode($listReturned);
        foreach ($listReturned as $listRowData) {
            $counter = 0;
            $data[$rowCounter] = array();
            $selectBoxLabel = "";

            foreach ($listRowData as $colData) {

                $mulitpleElements = true;
                if(!is_array($colData[0])) {
                    $colData = (object)$colData;
                    $disabled=$colData->disabled;
                    $mulitpleElements = false;
                }
                else{
                    $disabled=$colData['disabled'];
                }
                if (count($listRowData) != count($columnsTypes)) {
                    $data[0] = $listRowData;
                    continue;
                }
                if ( \Formhandler\Controller\FormhandlerController::str_contains($colData->class, 'as-label'))//create empty row with intect classes but when data comes from api convert to input
                {
                    //$columnsTypes[$counter]='input';
                    $showAsInput = true;
                } else {
                    $showAsInput = false;
                }
                if (is_array($columnsTypes[$counter])) {
                    //build select box from data and select the coldata in it;

                    switch ($columnsTypes[$counter][0]) {

                        case "selectBox2":
                        case "selectBox":


                            //selectDataFromDB($table,$fields,$id = null,$where = null,$valueFromList=null)
                            $dataForSelectBox = $customdb->getColumnDataWhere($columnsTypes[$counter][1], $columnsTypes[$counter][2], $columnsTypes[$counter][3], $columnsTypes[$counter][5] ? $columnsTypes[$counter][5] : "");
                            $classSelectBox2 = $columnsTypes[$counter][0] == "selectBox2" ? " selectbox2 " : "";


                            $colData->name = strtolower(str_replace(" ", "_", $colData->name));

                            $selectBox = "<select $disabled class='form-control $classSelectBox2  $colData->class' " . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "'  class='form-control'  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . ">";
                            $columns = explode(",", str_replace("*", "", $columnsTypes[$counter][2]));
                            if (count($columns) > 1) {
                                $selectBox .= "<option value=''>" . $translate->z_xlt('choose') . "</option>";
                                foreach ($dataForSelectBox as $value) {

                                    if ($value[$columns[0]] == $colData->value) {
                                        $selectBox .= "<option selected  value='" . $value[$columns[0]] . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "</option>";
                                        if ($showAsInput) {

                                            $selectBoxLabel = "<label " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . ">" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "   </label>";
                                        }
                                    } else {
                                        $selectBox .= "<option value='" . $value[$columns[0]] . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "</option>";
                                    }


                                }
                            } else {
                                foreach ($dataForSelectBox as $value) {
                                    $value = $value[$columnsTypes[$counter][2]];
                                    if ($value == $colData->value) {
                                        $selectBox .= "<option selected  value='" . $colData->name . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value) : $value) . "</option>";
                                        if ($showAsInput) {

                                            $selectBoxLabel = "<label " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "> " . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value) : $value) . "     </label>";
                                        }
                                    } else {
                                        $selectBox .= "<option value='" . $colData->value . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value) : $value) . "</option>";
                                    }


                                }
                            }
                            $selectBox .= "</select>";

                            $selectBox .= $selectBoxLabel;
                            $selectBoxLabel = "";
                            array_push($data[$rowCounter], $selectBox);


                            break;
                    }
                }


                if ($showAsInput) {

                    $showAsInputInTable = "<label " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . ">" . $translate->z_xlt($colData->value) . "   </label>";
                }

                switch ($columnsTypes[$counter]) {

                    case "text":
                    case "label":
                        array_push($data[$rowCounter], "<label   ".($disabled!=""?$disabled:"") . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . " class='$colData->class'" . "  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . " >" . $colData->value . "</label>");
                        break;
                    case "number":
                    case "numeric":
                        array_push($data[$rowCounter], "<div> $showAsInputInTable <input type='number' " .($disabled!=""?$disabled:"") . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "  class='form-control $colData->class'   name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' value='" . $colData->value . "' /></div>");
                        break;
                    case "textarea":
                        array_push($data[$rowCounter], "<div>$showAsInputInTable <textarea " .($disabled!=""?$disabled:"") . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . " onclick=" . $click[$counter] . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "class='form-control $colData->class'" . "  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . ">" . $colData->value . "</textarea></div>");
                        break;
                    case "icon":
                        //array_push($data[$rowCounter], "<input " . $attributes[$counter] . 'onclick="' . $click[$counter] . '"' . " id='" . $columnName . "_icon_" . $rowCounter . "_" . $counter . "' " . " value='" . $colData->value . "' class='hidden  $colData->class ' name='" . $columnName . "_icon_" . $rowCounter . "_" . $counter . "'/> <p " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "style='font-size:19px;'><span class=' $colData->class " . $colData->value . "' ></span></p>");
                        if($mulitpleElements){
                            $elementString = '';
                            foreach($colData as $col) {

                                $elementString .= "<input " . $attributes[$counter] . 'onclick="' .($disabled!=""?$disabled:"") . $click[$counter] . '"' . " id='" . $columnName . "_icon_" . $rowCounter . "_" . $counter . "' " . " value='" . $col["value"] . "' class='hidden " . $col["class"] . "' name='" . $columnName . "_icon_" . $rowCounter . "_" . $counter . "'/> <p " . ($col["tooltip"] ? "data-toggle='tooltip' title='" . $col["tooltip"] . "'" : "") . "style='font-size:19px;'><span class='" .  $col["class"] . " " . $col["value"] . "' ></span></p>";
                                $elementEnvelope = "<div style='display:inline-flex;'>".$elementString."</div>";
                            }
                            array_push($data[$rowCounter], $elementEnvelope);
                        }
                        else{
                            array_push($data[$rowCounter], "<input " . $attributes[$counter] .($disabled!=""?$disabled:"") . 'onclick="' . $click[$counter] . '"' . " id='" . $columnName . "_icon_" . $rowCounter . "_" . $counter . "' " . " value='" . $colData->value . "' class='hidden  $colData->class ' name='" . $columnName . "_icon_" . $rowCounter . "_" . $counter . "'/> <p " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "style='font-size:19px;'><span class=' $colData->class " . $colData->value . "' ></span></p>");
                        }
                        break;
                    case "checkbox":
                        array_push($data[$rowCounter], "<div>$showAsInputInTable <input " .($disabled!=""?$disabled:"") . ' onmouseup="' . $mouseUp[$counter] . '"' . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . "style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "class='$colData->class' type='checkbox' " . "  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->value ? "checked" : "") . " /></div>");
                        break;

                    case "datePicker":
                        array_push($data[$rowCounter], "<div>$showAsInputInTable <input " .($disabled!=""?$disabled:"") . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . "style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "class='form-control $colData->class' " . "  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . " value='" . $colData->value . "' ".$colData->disabled_data." /></div>");
                        break;
                    /*case "radio":

                        break;*/
                    case "button":
                        array_push($data[$rowCounter], "<button " . $attributes[$counter] .($disabled!=""?$disabled:"") . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "type='button' class='btn btn-info' class='form-control $colData->class'" . "  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ">" . $colData->value . "</button>");
                        break;
                    case "input":
                        array_push($data[$rowCounter], "<div>$showAsInputInTable <input " .($disabled!=""?$disabled:"") . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . "style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "  class='form-control $colData->class'   " . "  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . " value='" . $colData->value . "' /></div>");
                        break;

                    case "delete":
                        if (($colData->value === true || $colData->value == "true")) {
                            array_push($data[$rowCounter], '<input ' . ' onmousedown="' .($disabled!=""?$disabled:"") . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ' value=true class="hidden" ' . '  name="' . $colData->name . "_" . $rowCounter . "_" . $counter . ' " ' . ' /><button ' . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . ' removeThisRow(\'' . $columnName . '_table\',$(this).parent().parent());" ' . ($colData->tooltip ? "data-toggle=\'tooltip\' title=\'$colData->tooltip\'" : "") . '  type="button" class="pull-center' . str_replace("hidden", "", $colData->class) . ' " style="-webkit-appearance: none;padding: 6px 24px;cursor: pointer;background: 0 0;border: 0;color: red !important; top: 0;right: 0;">X</button>');
                        } else {
                            array_push($data[$rowCounter], '<input ' . ' onmousedown="' .($disabled!=""?$disabled:"") . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ' value=false class="hidden" name="' . $columnsTypes[$counter] . "_" . $rowCounter . "_" . $counter . '" />');
                        }

                        break;
                    case "href":
                        if ($colData->value != null && $colData->value != "#") {
                            array_push($data[$rowCounter], '<a target="_blank" href="' .($disabled!=""?$disabled:"") . ($colData->value != null ? $colData->value . '"' : '#') . '">CDC</a>');
                        } else {
                            array_push($data[$rowCounter], '<label></label>');
                        }
                        break;
                }
                $showAsInputInTable = '';
                $counter++;
            }

            $rowCounter++;
        }

        return json_encode($data);

    }


    public function CreateTableFromBlobJsonData(
        $table,
        $column,
        $pid,
        $columnsTypes,
        $columnsNames,
        $fromDB = false,
        $RouteControllerAction = "",
        $noData = false,
        $click = null,
        $change = null,
        $attributes = null,
        $mouseDown = null,
        $mouseUp = null,
        $doTranslation = null
    ) {
        $translate = new Listener();
        $customdb = $this->getCustomDB();
        $parseNoData = false;
        $listReturned = [];
        $arrIcons=['vaccines-icon', 'groups-icon', 'travel-update-icon'];
        if ($fromDB) {
            $customdb = $this->getCustomDB();
            if ($this->edit_params['pid'] && $this->edit_params['id'] && $this->edit_params['encounter']) {
                $listReturned = $customdb->getColumnBlobData($table, $column, $this->edit_params['pid'], $this->edit_params['id'], $this->edit_params['encounter']);
            } else {
                $listReturned == '[[]]';
            }

            //new                                            //nodata
            if ($listReturned == '[[]]' && !$noData) {

                return [];
            }

            if ($noData) {


                //$listReturned = json_decode($listReturned);
                $listReturned = [];
                $listReturnedRow = [];
                if (!$noData) //if there is no data from the table and this is not an addon button
                {
                    return "[]";
                }
                for ($i = 0; $i < count($columnsTypes); $i++) {
                    $data = array();
                    $data['name'] = '-';
                    $data['value'] = '';
                    array_push($listReturnedRow, $data);

                }
                array_push($listReturned, $listReturnedRow);
                $listReturned = json_encode($listReturned);
            }

        } else {

            if ($RouteControllerAction) {
                $listReturned = $this->FetchDataFromApi($RouteControllerAction);
                if (!$noData) //if there is no data from the table and this is not an addon button
                {
                    return "[]";
                }
            } else {

                if (!$listReturned) {
                    $listReturned = [];
                    $listReturnedRow = [];
                    if (!$noData) //if there is no data from the table and this is not an addon button
                    {
                        return "[]";
                    }
                    for ($i = 0; $i < count($columnsTypes); $i++) {
                        $data = array();
                        $data['name'] = '-';
                        $data['value'] = '';
                        array_push($listReturnedRow, $data);

                    }
                    array_push($listReturned, $listReturnedRow);
                    $listReturned = json_encode($listReturned);
                } else {
                    return "[]";
                }
            }

        }


        if (is_null($listReturned)) {
            return "[]";
        }

        $data = array();

        $rowCounter = 200;

        if(!is_array($listReturned)){
            $listReturned = json_decode($listReturned);
        }

        foreach ($listReturned as $listRowData) {
            $counter = 0;

            $data[$rowCounter] = array();
            $selectBoxLabel = "";

            foreach ($listRowData as $colData) {


                $showAsInput = false;
                if ($noData === true) {
                    $colData->value = '';
                    $colData->name = $columnsNames[$counter];
                    $colData->class .= "inputAddedFromClient";
                }
                if ($colData->class == 'as-label' && $noData === false)//create empty row with intect classes but when data comes from api convert to input
                {
                    //$columnsTypes[$counter]='input';
                    $showAsInput = true;
                } else {
                    $showAsInput = false;
                }
                if (is_array($columnsTypes[$counter])) {
                    //build select box from data and select the coldata in it;
                    $colData->name = strtolower(str_replace(" ", "_", $colData->name));
                    switch ($columnsTypes[$counter][0]) {
                        case "selectBox2":
                        case "selectBox":
                            //selectDataFromDB($table,$fields,$id = null,$where = null,$valueFromList=null)
                            $dataForSelectBox = $customdb->getColumnDataWhere($columnsTypes[$counter][1], $columnsTypes[$counter][2], $columnsTypes[$counter][3], $columnsTypes[$counter][5] ? $columnsTypes[$counter][5] : "");


                            $classSelectBox2 = $columnsTypes[$counter][0] == "selectBox2" && $noData ? " selectbox2 " : "";

                            $selectBox = "<select" . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . $attributes[$counter] . " onclick=\"" . trim($click[$counter]) . "\" style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "'  class=' $classSelectBox2 form-control $colData->class '  name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . ">";
                            $columns = explode(",", str_replace("*", "", $columnsTypes[$counter][2]));
                            if (count($columns) > 1) {
                                $selectBox .= "<option value=''>" . $translate->z_xlt('choose') . "</option>";
                                foreach ($dataForSelectBox as $value) {

                                    if ($value[$columns[0]] == $colData->value) {
                                        $selectBox .= "<option selected  value='" . $colData->value . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "</option>";
                                        if ($showAsInput) {

                                            $selectBoxLabel = "<label " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . ">" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "   </label>";
                                        }
                                    } else {
                                        $selectBox .= "<option value='" . $value[$columns[0]] . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "</option>";
                                    }


                                }
                            } else {
                                foreach ($dataForSelectBox as $value) {
                                    $value = $value[$columnsTypes[$counter][2]];
                                    if ($value == $colData->value) {
                                        $selectBox .= "<option selected  value='" . $colData->name . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value) : $value) . "</option>";
                                        if ($showAsInput) {

                                            $selectBoxLabel = "<label " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "> " . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "     </label>";
                                        }
                                    } else {
                                        $selectBox .= "<option value='" . $colData->value . "'>" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value[$columns[1]]) : $value[$columns[1]]) . "</option>";
                                    }


                                }
                            }
                            $selectBox .= "</select>";

                            $selectBox .= $selectBoxLabel;
                            $selectBoxLabel = "";
                            array_push($data[$rowCounter], $selectBox);


                            if ($noData) {
                                continue;
                            }

                            break;
                    }
                }


                if ($showAsInput) {

                    $showAsInputInTable = "<label " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . ">" . ($doTranslation[$counter] != "D" ? $translate->z_xlt($value) : $value) . "   </label>";
                }
                $colData->name = strtolower(str_replace(" ", "_", $colData->name));
                switch ($columnsTypes[$counter]) {

                    case "text":
                    case "label":
                        array_push($data[$rowCounter], "<label  " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . " class='$colData->class' name='" . $colData->name . "'>" . $colData->value . "</label>");
                        break;
                    case "number":
                    case "numeric":
                        array_push($data[$rowCounter], "$showAsInputInTable <input type='number' " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "  class='form-control $colData->class'   name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' value='" . $colData->value . "' />");

                        break;
                    case "textarea":
                        array_push($data[$rowCounter], "$showAsInputInTable <textarea " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . "style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "class='form-control $colData->class' name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'" . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'>" . $colData->value . "</textarea>");
                        break;
                    case "icon":
                        $elementString = "<div style='display:inline-flex;'>";
                        foreach($arrIcons as $class) {
                            $elementString .= "<input " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . "value='" . $colData->value . "' class='hidden $class $colData->class ' name='icon'/> <p " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "style='font-size:19px;'><span class=' $colData->class " . $colData->value . "' ></span></p>";
                        }
                        $elementString .= "</div>";
                        array_push($data[$rowCounter],$elementString );
                        break;
                    case "checkbox":
                        array_push($data[$rowCounter], "$showAsInputInTable <input " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onmouseup="' . $mouseUp[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "class='$colData->class' type='checkbox' name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->value ? "checked" : "") . " />");
                        break;

                    case "datePicker":
                        array_push($data[$rowCounter], "$showAsInputInTable <input " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "class='form-control $colData->class' name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' value='" . $colData->value . "' />");
                        break;
                    /*case "radio":

                        break;*/
                    case "button":
                        array_push($data[$rowCounter], "<button " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "type='button' class='btn btn-info' class='form-control $colData->class' name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "'>" . $colData->value . "</button>");
                        break;
                    case "input":
                        array_push($data[$rowCounter], "$showAsInputInTable <input " . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " style='" . ($showAsInput ? "visibility: hidden;position:absolute;" : "") . "' " . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ($colData->tooltip ? "data-toggle='tooltip' title='$colData->tooltip'" : "") . "  class='form-control $colData->class'   name='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' value='" . $colData->value . "' />");


                        break;

                    case "delete":
                        if (($colData->value === true || $colData->value == "true") || $noData === true) {
                            array_push($data[$rowCounter], '<input ' . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . ' value=true class="hidden" name="' . $colData->name . "_" . $rowCounter . "_" . $counter . '" /><button ' . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . ' removeThisRow(\'' . $column . '_table\',$(this).parent().parent());" ' . ($colData->tooltip ? "data-toggle=\'tooltip\' title=\'$colData->tooltip\'" : "") . '  type="button" class="pull-center' . str_replace("hidden", "", $colData->class) . ' " style="-webkit-appearance: none;padding: 6px 24px;cursor: pointer;background: 0 0;border: 0;color: red !important; top: 0;right: 0;">X</button>');
                        } else {
                            array_push($data[$rowCounter], '<input ' . $attributes[$counter] . ' onmousedown="' . $mouseDown[$counter] . '"' . ' onchange="' . $change[$counter] . '"' . ' onclick="' . $click[$counter] . '"' . " id='" . $colData->name . "_" . $rowCounter . "_" . $counter . "' " . 'value=false class="hidden" name="' . $columnsTypes[$counter] . "_" . $rowCounter . "_" . $counter . '" />');
                        }
                        break;
                    case "href":
                        if ($colData->value != null && $colData->value != "#") {
                            array_push($data[$rowCounter], '<a target="_blank" href="' . ($colData->value != null ? $colData->value . '"' : '#') . '">CDC</a>');
                        } else {
                            array_push($data[$rowCounter], '<label></label>');
                        }
                        break;
                }
                $showAsInputInTable = '';
                $counter++;
            }


            $emptyRowData = $data[$rowCounter];
            if ($noData === true) {
                return json_encode($emptyRowData);
            }


            $rowCounter++;
        }


        return json_encode($data);
    }


    public function CreateSelectBoxFromList($list, $selected = null)
    {
        $translate = new Listener();

        $customdb = $this->getCustomDB();
        $listReturned = $customdb->getListParams($list);
        if (is_null($listReturned)) {
            return "<p class='text-danger'> missing list : $list</p>";
        }


        $selectBox = "<select   class='form-control' name='$list'>";
        foreach ($listReturned as $value) {
            if ($selected == $value['title']) {
                $selectBox .= "<option   selected value='" . $value['option_id'] . "'>" . $translate->z_xlt($value['title']) . "</option>";
            } else {
                $selectBox .= "<option   value='" . $value['option_id'] . "'>" . $translate->z_xlt($value['title']) . "</option>";
            }
        }
        $selectBox .= "</select>";
        return $selectBox;
    }

    public function __invoke($id, $encounter_form_id = null, $encounter_id = null, $jsSort = true)
    {


        $dataFromClient = json_decode($id);
        $columnName = $dataFromClient->columnName;
        $columnsTypes = $dataFromClient->columnsTypes;
        $tableName = $dataFromClient->tableName;
        $inputName = $dataFromClient->inputName;
        $strippedtable = $dataFromClient->strippedTable;
        $tablesCols = $dataFromClient->tablesCols;
        $tablesColOrder = $dataFromClient->tablesColOrder;
        $showAddDeleteButtons = $dataFromClient->showAddDeleteButtons === false ? false : true;
        $addButtonText = $dataFromClient->addButtonText ? $dataFromClient->addButtonText : "+";
        $showDeleteButton = $dataFromClient->showDeleteButton === false ? false : true;
        $deleteButtonText = $dataFromClient->deleteButtonText ? $dataFromClient->deleteButtonText : "-";
        $columnsTypesAddRow = $dataFromClient->columnsTypesAddRow ? $dataFromClient->columnsTypesAddRow : $columnsTypes;
        $columnWidth = $dataFromClient->columnWidth;
        $fromDB = $dataFromClient->fromDb ? $dataFromClient->fromDb : false;
        $routeControllerAction = $dataFromClient->routeController;
        $pid = $_SESSION ['pid'];
        $noBorder = $dataFromClient->noBorder;
        $clicks = str_replace("*", "'", $dataFromClient->clicks);
        $changes = str_replace("*", "'", $dataFromClient->changes);
        $mousedown = str_replace("*", "'", $dataFromClient->mousedown);
        $mouseup = str_replace("*", "'", $dataFromClient->mouseup);
        $doTranslation = str_replace("*", "'", $dataFromClient->doTranslation); // D  - don't translate  other/empty Translate
        if ($dataFromClient->attributes != "") {
            $attributes = str_replace("*", "'", $dataFromClient->attributes);
        } else {

            $attributes = "";
        }


        $translate = new Listener();
        if ($showAddDeleteButtons) {
            if ($showDeleteButton) {

                $buttons = '<div  id="menu_' . $columnName . '" class="form-group" role="group">
        <div  id="button_description" class="button_description"> <label>' . $translate->z_xlt("Press here to add or remove records") . '</label></div>
        <input type="button" id="addRow_' . $columnName . '"  class="btn" value="' . $translate->z_xlt($addButtonText) . '"/>';
                $buttons .= '<input type="button" id="deleteRow_' . $columnName . '"  class="btn" value="' . $translate->z_xlt($deleteButtonText) . '"/></div>';
            } else {
                $buttons = '<div  id="menu_' . $columnName . '" class="form-group" role="group">
        <!--div  id="button_description" class="button_description"> <label>' . $translate->z_xlt("Add") . '</label></div-->
        <input type="button" id="addRow_' . $columnName . '"  class="btn" value="' . $translate->z_xlt($addButtonText) . '"/>';
            }
        }


        $table = '<div id="' . $columnName . '_parent"><table class="table ' . $strippedtable . '  dataTable " id="' . $columnName . '_table">';
        $table .= '
                    <thead>';

        foreach ($tablesCols as $tableCol) {
            $table .= '<th>' . $translate->z_xlt($tableCol) . '</th>';
        }


        $table .= '
                    </thead>';
        //   $table.='<tbody><tr><td><input name="hello" id="hello"/></td><td><input name="hello3" id="hello3"/></td><td><input name="bye" id="bye"/></td><td><input name="hello34" id="hello34" /></td><td><input name="hello32" id="hello32"/></td><td><input name="hello22" id="hello22"/></td></tr>';
        $table .= '
                    </tbody>';

        /* $table.='<tfoot>';
         foreach ($tablesCols as $tableCol){
             $table.='<th>' .$translate->z_xlt($tableCol).'</th>';
         }
         $table.='</tfoot>';*/


        $table .= $buttons . '
                            </table>';
        $table .= '
                            </div>';


        $script = ' 
        <script>
        $(document).ready(function() {
            dontShowErrors=false;
            

            dontDoAjax = false;
             $("#button_submit").on("click",function(){
                 tryingToSave = true;
                 refreshTableJson_' . $columnName . '();
                // dontDoAjax = true;
                 submitFlow = true;
             
             });
            
            // submitFlow = false;
             
        $("#' . $columnName . '").hide();
    
    
                 t=$("#' . $columnName . '_table").DataTable({ 
                 // this does not work well 
                 "language": 
                       {
                        "processing":   "מעבד...",
                        "lengthMenu":   "הצג _MENU_ פריטים",
                        "zeroRecords":  "לא נמצאו רשומות מתאימות",
                        "emptyTable":   "לא נמצאו רשומות מתאימות",
                        "info": "_START_ עד _END_ מתוך _TOTAL_ רשומות" ,
                        "infoEmpty":    "0 עד 0 מתוך 0 רשומות",
                        "infoFiltered": "(מסונן מסך _MAX_  רשומות)",
                        "infoPostFix":  "",
                        "search":       "חפש:",
                        "url":          "",
                        "paginate": {
                            "first":    "ראשון",
                            "previous": "קודם",
                            "next":     "הבא",
                            "last":     "אחרון"
                        },
                    },
                    
                 "LengthChange": false,  "searching": false ,"paging":false,"info":false';
        if (count($columnWidth) > 0) {

            $columnsWidthSize = ' autoWidth:false,columns:[';
            foreach ($columnWidth as $columnSize) {
                $columnsWidthSize .= '{"width" : "' . $columnSize . '"},';
            }
            $columnsWidthSize .= ']';
            $script .= ',' . $columnsWidthSize . '});';
        } else {
            $script .= '});';
        }

        $jsSortString = ($jsSort) ? 'true' : 'false';

        $script .= '
                
             if(typeof blobData == \'undefined\')
                blobData = {};
             
              
             blobData["' . $columnName . '"]=[
             {
             "table":"' . $tableName . '",
             "columnName":"' . $columnName . '",
             "pid":"' . $pid . '",
             "columnsTypes":' . json_encode($columnsTypes) . ',
             "columnsNames":' . json_encode($tablesCols) . ',
             "tablesColOrder":' . json_encode($tablesColOrder) . ',
             "columnsClicks":' . json_encode($clicks) . ',
              "columnsChanges":' . json_encode($changes) . ',
              "columnsAttributes":' . json_encode($attributes) . ',
               "columnsMouseDown":' . json_encode($mousedown) . ',
               "columnsMouseUp":' . json_encode($mouseup) . ',
               "columnTranslate":' . json_encode($doTranslation) . '
             }
             ];
         
             
             
                
             window.data_' . $columnName . '=' . $this->CreateTableFromBlobJsonData($tableName, $columnName, $pid, $columnsTypes, $tablesCols, $fromDB, $routeControllerAction, false, $clicks, $changes, $attributes, $mousedown, $mouseup, $doTranslation) . ';
             window.emptyData_' . $columnName . '=' . $this->CreateTableFromBlobJsonData($tableName, $columnName, $pid, $columnsTypes, $tablesCols, $fromDB, $routeControllerAction, true, $clicks, $changes, $attributes, $mousedown, $mouseup, $doTranslation) . ';
             
            $.when( $.each(data_' . $columnName . ',function(index,value){
             
             t.row.add(value).draw(true);
              
             
             })).done(function(ev){
             
              adjustSelect2("#' . $columnName . '_table_wrapper", ' . $jsSortString . ');
           
              
             });
     
       
        $("#' . $columnName . '_table_wrapper").appendTo($("#' . $inputName . '").parent());
        $("#menu_' . $columnName . '").appendTo($("#' . $columnName . '_table_wrapper").parent());
                
      
        
            $("#addRow_' . $columnName . '").on( "click", function (event) {
                addRowWasClicked = true;
                event.stopPropagation();
                var t=$("#' . $columnName . '_table").DataTable();
                
                
                  var rowsLast = t.data().length 
                  var emptDataTemp'.$columnName.'=emptyData_' . $columnName.'.slice(0);
                  
                $.each(emptDataTemp' . $columnName . ',function(k){
                
               if($( emptDataTemp' . $columnName . '[k]).attr("name") != undefined && $( emptDataTemp' . $columnName . '[k]).attr("name").includes("id")){
               
               emptDataTemp' . $columnName . '[k]  = emptDataTemp' . $columnName . '[k].replace("><",">"+rowsLast+"<");
               emptDataTemp' . $columnName . '[k]  = emptDataTemp' . $columnName . '[k].replace(">"," style=\"display:none;\" >");
               
             
               
            //   $("#recommended_vaccines_table_generic_table").find("tr th:nth-child(10)").each(function(){$(this).hide()});
             //  $("#recommended_vaccines_table_generic_table").find("tr td:nth-child(10)").each(function(){$(this).hide()});
               
             //   emptDataTemp' . $columnName . '[k]  = emptDataTemp' . $columnName . '[k].replace(">","style=\'display:none;\'>");
               }
                //replace id and name with correct index
               emptDataTemp' . $columnName . '[k]  = emptDataTemp' . $columnName . '[k].replace(/(\sname=.\S*_)([0-9]*)(_.*)/gi,"$1"+rowsLast+"$3").replace(/(\sid=.\S*_)([0-9]*)(_.*)/gi,"$1"+rowsLast+"$3");
           
                }) 
                 
                t.row.add( emptDataTemp' . $columnName . ' ).draw(true);
              
                
               // t.draw();
                
                
                
                refreshTableJson_' . $columnName . '();
                addOnChangeToInput_' . $columnName . '();
                
                
                
                $("#' . $columnName . '_table").find("input,select,textarea").each(function(ev){
                 if($(this).is(":checked")) {
               /*  $(this).trigger("click")
                 $(this).trigger("click")*/
                 }
                
                /* if($(this).val()!="")
                 $(this).change()*/
                 
                });
               
                var clickFunction' . $columnName . ' = $(($(this).closest(".form-group-TWB").find(".selectbox2")[0])).attr("onclick");
                $(".selectbox2").each(function(ev){
                
                if(!($(this).hasClass("as-label") || $(this).hasClass("select2-hidden-accessible"))){
                var select2 =$(this).select2({  "dir":"' . addslashes($_SESSION['language_direction']) . '",
                "language": {
                            errorLoading: function () {
                            return "' . xls('The results could not be loaded') . '";
                            },
                            inputTooLong: function (args) {
                            return "' . xls('Please delete characters') . '";
                            },
                            inputTooShort: function (args) {
                            return "' . xls('Please enter more characters') . '";
                            },
                            loadingMore: function () {
                            return "' . xls('Loading more results') . '";
                            },
                            maximumSelected: function (args) {
                            var message = "' . xls('You can only select') . '" + args.maximum;
                            if (args.maximum != 1) {
                                    message += "' . xls('items') . '";
                            } else {
                                    message += "' . xls('item') . '";
                            }
                            return message;
                            },
                            noResults: function () {
                                return "' . xls('No results found') . '";
                            },
                            searching: function () {
                                return "' . xla('Searching') . '";
                            }
},});
                select2.trigger("change");
                select2.on("change",function(e){ 
                var clickFunction' . $columnName . ' = $(($(this).closest(".form-group-TWB").find(".selectbox2")[0])).attr("onclick");
                if(clickFunction' . $columnName . '!="")
                {
                 var functionName =  clickFunction' . $columnName . '.split("(")[0];
                var param = clickFunction' . $columnName . '.split("(")[1].split(")")[0] =="this"?this:clickFunction' . $columnName . '.split("(")[1].split(")")[0];
                window[functionName](param);
                refreshTableJson_' . $columnName . '();
                }
                  
                });
                
                }
                
                });
                
            });
            
            
            
             
             
            $("#deleteRow_' . $columnName . '").on( "click,mousedown,change", function (event) {
                event.stopPropagation();
                var t=$("#' . $columnName . '_table").DataTable();
                //t.row.add(emptyData_' . $columnName . ').draw(true);
                refreshTableJson_' . $columnName . '();
                addOnChangeToInput_' . $columnName . '();
                
            });';


        if ($noBorder) {

            $script .= '
                
                $("#' . $columnName . '_table_wrapper thead").remove();
                $("#' . $columnName . '_table_wrapper table").css("width", "100%");
                $("#' . $columnName . '_table_wrapper").parent().css("padding",0);
                $("#' . $columnName . '_table_wrapper").css("left","13px");
                $("#' . $columnName . '_table_wrapper table>tbody>tr>td").css("border-top",0);
                ';

        }

        $script .= '     
                
                 
                 
                    });
                

               
                 
        function addOnChangeToInput_' . $columnName . '(){
        $("#' . $columnName . '_table_wrapper input").on("change",function(e){
                   refreshTableJson_' . $columnName . '();
                   
        });

        $("#' . $columnName . '_table_wrapper select").on("change",function(e){
                   refreshTableJson_' . $columnName . '();
        });

        $("#' . $columnName . '_table_wrapper tbody").on("change",function(e){
                   refreshTableJson_' . $columnName . '();
        });
        }
        function refreshTableJson_' . $columnName . '()
        {
         // debugger
        //$("#' . $columnName . '").val(JSON.stringify($("#' . $columnName . '_table_wrapper tr ").find("select, input").serializeArray()));
        
      //   dontShowErrors = true;
         dontDoAjax = false;
         $("#button-submit").on("click",function(ev){
         dontShowErrors = false;
         submitFlow =true;
         
         });
        var arrayToSaveRows=[];
        var arrayToSaveCols=[];
 
            
            if($("#' . $columnName . '_table_wrapper thead").length>0){
            startFrom = 0;
            }
            else{
            startFrom = -1;
            }
 
            $("#' . $columnName . '_table_wrapper tr").each(function(tr){
 
           // console.log(tr.length);
            
            
            if(tr>startFrom){
                       trNumber=tr;
                
                $(this).find("td").each(
                        function(td){

var element = $($($("#' . $columnName . '_table_wrapper tr")[trNumber]).find("td")[td]).find("input,select")[0];
if(typeof element!="undefined"){

var classOf = $(element).attr("class");
var classDef = null;
if(typeof classOf!="undefined"){
 classDef = $(element).attr("class").replace("hidden","");
}

var name = $(element).attr("name");
var value = $(element).val();
if(value=="true"){
    value=true;
}
if(value=="false"){
    value=false;
}
if( $(element)[0].type=="checkbox")
{
    if($(element)[0].checked)
    {
       value=true;
    }
    else
    {
       value=false;
    }
}
if( $(element)[0].name=="icon")
{
    var p = $(element).parent().find("p");
    var span =  p.find("span");
    value = $(span).attr("class");
    classDef = $(p).attr("class");
}

    ' . ($columnName == "countries_table_generic" ? "if(value!='' && value != undefined)" : "") . '
    arrayToSaveCols.push({"name":name,"value":value,"class":classDef});
}

});
                ' . ($columnName == "countries_table_generic" ? "if(arrayToSaveCols.length>0 && $(this).find('td').length == arrayToSaveCols.length)" : "if(arrayToSaveCols.length>0)") . '
                {
                    arrayToSaveRows.push(arrayToSaveCols);
                }
                arrayToSaveCols = [];
                
                
                }
            });
           $("#' . $columnName . '").val(JSON.stringify(arrayToSaveRows));
                    //  $("#' . $columnName . '").show();
                       $("#' . $columnName . '").attr("disabled",false);
        }
        
        
             
      
    function afterError(event) {
  
         if(typeof submitFlow != "undefined" &&  submitFlow && !dontShowErrors) {
                  if(typeof id != "undefined"){
                    ValidateTable(id);
                  }
                      
         }
          // $("#" + $(element).closest(".tab-pane").attr("id") + "_group").trigger("click");     
    }
   
                     </script>';


        return "<br/>" . $table . "<br/>" . $script . "<br/>";
    }
}

