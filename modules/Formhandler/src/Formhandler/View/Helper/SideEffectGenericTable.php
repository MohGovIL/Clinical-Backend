<?php
/**
 * Created by DROR GOLAN.
 * User: drorgo
 */

namespace Formhandler\View\Helper;
use Application\Listener\Listener;
use Formhandler\Model\customDB;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Mvc\Controller\AbstractActionController;



class SideEffectGenericTable  extends AbstractHelper
{
    const TIME_INTERVAL_LIST= array(0=>'minutes', 1=>'hours', 2=>'days', 3=>'weeks');
    const SYMPTOM_DURATION_LIST= array(0=>'time description', 1=>'continues phenomena', 2=>'unknown');


    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    private $edit_params = null;
    public function setEditParams($edit_params){
        $this->edit_params = $edit_params;
    }

    public static $fieldLists = array(
        "moh_materials_type"
    );
    protected $dbAdapter;

    public function setDbAdapter($adapter){
        $this->dbAdapter=$adapter;
    }

    /**
     * Return the variable that contains the fields that use lists
     * to translate  them from id to value
     */
    public static function getFieldLists(){
        return self::$fieldLists;
    }

    protected function getCustomDB(){
        $CustomDb = new CustomDb( $this->dbAdapter);
        return $CustomDb;
    }

    public function FetchDataFromApi($RouteControllerAction){

        $RouteControllerAction=str_replace("/","\\",$RouteControllerAction);
        $arrControllerAction = explode("::",$RouteControllerAction);
        $controller = $arrControllerAction[0];
        $action = $arrControllerAction[1];
        $instance = new $controller($this->sm);
        $data =  $instance->$action();


        return $data;

    }

    public function listToOption($list,$val  = null){

        $op="";
        foreach($list as $key=>$value){
            if($val == $value)
            {
                $option = "<option  selected; value='" . $key . "'>" . xl($value) . "</option>";
            }
            else {
                $option = "<option value='" . $key . "'>" . xl($value) . "</option>";
            }
            $op.=$option;
        }
        return $op;
    }

    public function __invoke($sidesEffect,$anflaxises,$vaccinesFromDate,$symptomsValues = null)
    {


    //    $this->create_custom_control_from_list("",'moh_trip_goal','selectBox','symptom_duration',"","",'form_vaccine_advisor','','custom-inline-input');

        $rows =[];
        $rowsEmpty = [];
        $constraints = [];

        array_push($rowsEmpty, "<div class='col-md-12' style='width:97%;'><table class='table phenomenon'>" .
            "<tr>" . '<td>' . xl("Side effect description") . '</td><td>' . xl("Date of the phenomenon") . '</td><td></td><td>' . xl("Duration of the phenomenon") . '</td><td ></td><td></td><td></td>' . "</tr>" .
            "<tr>" . "<td><select name='symptom_description' value='' class='form-control categories' ></select></td>" . "<td ><input name='symptom_date' class='form-control' value='' type='text'/></td>" . "<td ><select  class='form-control' name='time_weight_one'>" . $this->listToOption(self::TIME_INTERVAL_LIST) . "</select></td>" . "<td><select id='symptom_duration' name='symptom_duration' class='form-control'   >" . $this->listToOption(self::SYMPTOM_DURATION_LIST) . "</select></td>" . "<td><input  class='form-control' type='text' value='' name='time_const'/></td>" . "<td><select name='time_weight_two' class='form-control' >" . $this->listToOption(['minutes', 'hours', 'days', 'weeks']) . "</select></td>" . "<td><icon class='glyphicon glyphicon-remove'></icon></td>" . "</tr>" .
            "</table></div>");
        array_push($rowsEmpty, "<div style='display:none' class='form-group  d-inline col-md-6  anaphylaxis'><div class='col-md-4'><label  >" . xl("Details for Anaphylaxis") . "</label></div><div class='col-md-8'><select id='details_for_anaphylaxis' name='details_for_anaphylaxis' class='details_for_anaphylaxis form-control ' multiple='multiple' ></select></div></div>");
        array_push($rowsEmpty, "<div style='display:none' class='form-group  d-inline col-md-5 details'><div class='col-md-3'><label >" . xl("Details") . "</label></div><div class='col-md-3'><input name='details' value='' class='form-control'type='text'/></div></div>");
        array_push($rowsEmpty, "<div style='display:none' class='form-group  d-inline col-md-3 affiliation_to_vaccination'><div class='col-md-8'><label >" . xl("Affiliation to vaccination") . "</label></div><div class='col-md-1'><select name='affiliation_to_vaccination' class='form-control vaccines_of_today' ></select></div></div>");
        array_push($rowsEmpty, "<div style='display:none' class='form-group  d-inline col-md-5  documentExists'><div class='col-md-1'><input name='existing_document' value='' class='form-control' type='checkbox'></div><div class='col-md-11'><label style=' margin-top: 4px;'>" . xl("There is a document with medical diagnosis") . "</label></div></div>");





       $symptomsValuesArray = json_decode($symptomsValues);

        if($symptomsValues!="")
        {

            $counter = 1;
            foreach($symptomsValuesArray as $key=>$symptom)
            {
                $keys = array_keys((array)$symptom);
                $rowNumber = explode("row_", $keys[0])[1];

                $symptom_description = "symptom_description_row_" . $rowNumber;
                $symptom_description_value = $symptom->$symptom_description;

                $symptom_date = "symptom_date_row_" . $rowNumber;
                $symptom_date_value = $symptom->$symptom_date;

                $time_weight_one = "time_weight_one_row_" . $rowNumber;
                $time_weight_one_value = $symptom->$time_weight_one;

                $symptom_duration = "symptom_duration_row_" . $rowNumber;
                $symptom_duration_value = $symptom->$symptom_duration;

                $time_const = "time_const_row_" . $rowNumber;
                $time_const_value = $symptom->$time_const;

                $time_weight_two = "time_weight_two_row_" . $rowNumber;
                $time_weight_two_value = $symptom->$time_weight_two;

                $details_for_anaphylaxis = "details_for_anaphylaxis_row_" . $rowNumber;
                $details_for_anaphylaxis_value = $symptom->$details_for_anaphylaxis;

                $details = "details_row_" . $rowNumber;
                $details_value = $symptom->$details;

                $affiliation_to_vaccination = "affiliation_to_vaccination_row_" . $rowNumber;
                $affiliation_to_vaccination_value = $symptom->$affiliation_to_vaccination;

                $existing_document = "existing_document_row_" . $rowNumber;
                $existing_document_value = $symptom->$existing_document;


                $rowNumber = $counter;
                $symptom_description = "symptom_description_row_" . $rowNumber;
                $symptom_date = "symptom_date_row_" . $rowNumber;
                $time_weight_one = "time_weight_one_row_" . $rowNumber;
                $symptom_duration = "symptom_duration_row_" . $rowNumber;
                $time_const = "time_const_row_" . $rowNumber;
                $time_weight_two = "time_weight_two_row_" . $rowNumber;
                $details_for_anaphylaxis = "details_for_anaphylaxis_row_" . $rowNumber;
                $details = "details_row_" . $rowNumber;
                $affiliation_to_vaccination = "affiliation_to_vaccination_row_" . $rowNumber;
                $existing_document = "existing_document_row_" . $rowNumber;

                array_push($rows, "<div class='container' id='row_$rowNumber'>");
                array_push($rows, "<div class='col-md-12' style='width:97%;'><table class='table phenomenon'>" .
                    "<tr>" . '<td>' . xl("Side effect description") . '</td><td>' . xl("Date of the phenomenon") . '</td><td></td><td>' . xl("Duration of the phenomenon") . '</td><td ></td><td></td><td></td>' . "</tr>" .
                    "<tr>" . "<td><select id='$symptom_description' name='$symptom_description' value='$symptom_description_value' class='form-control categories' ></select></td>" . "<td ><input name='$symptom_date' class='form-control' value='$symptom_date_value' type='text'/></td>" . "<td ><select  class='form-control' name='$time_weight_one'>" . $this->listToOption(['minutes', 'hours', 'days', 'weeks'],$time_weight_one_value) . "</select></td>" . "<td><select id='symptom_duration' name='$symptom_duration' class='form-control'   >" . $this->listToOption(['time description', 'continues phenomena', 'unknown'],$symptom_duration_value) . "</select></td>" . "<td><input  class='form-control' type='text' value='$time_const_value' name='$time_const'/></td>" . "<td><select name='$time_weight_two' class='form-control' >" . $this->listToOption(['minutes', 'hours', 'days', 'weeks'],$time_weight_two_value) . "</select></td>" . "<td><icon class='glyphicon glyphicon-remove'></icon></td>" . "</tr>" .
                    "</table></div>");

                array_push($rows, "<div style='display:none' class='form-group  d-inline col-md-6  anaphylaxis'><div class='col-md-4'><label  >" . xl("Details for Anaphylaxis") . "</label></div><div class='col-md-8'><select id='$details_for_anaphylaxis' name='$details_for_anaphylaxis' class='details_for_anaphylaxis form-control ' multiple='multiple' ></select></div></div>");
                array_push($rows, "<div style='display:none' class='form-group  d-inline col-md-5 details'><div class='col-md-3'><label >" . xl("Details") . "</label></div><div class='col-md-3'><input name='$details' value='$details_value' class='form-control'type='text'/></div></div>");
                array_push($rows, "<div style='display:none' class='form-group  d-inline col-md-3 affiliation_to_vaccination'><div class='col-md-8'><label >" . xl("Affiliation to vaccination") . "</label></div><div class='col-md-1'><select name='$affiliation_to_vaccination' class='form-control vaccines_of_today' ></select></div></div>");
                array_push($rows, "<div style='display:none' class='form-group  d-inline col-md-5  documentExists'><div class='col-md-1'><input name='$existing_document' class='form-control' type='checkbox'></div><div class='col-md-11'><label style='margin-top: 4px;'>" . xl("There is a document with medical diagnosis") . "</label></div></div>");
                array_push($rows, '</div>');
                array_push($constraints,'constraints["symptom_description_row_'.$rowNumber.'"] = {};');
                array_push($constraints,'constraints["symptom_description_row_'.$rowNumber.'"]["presence"] = {"message": "שדה חובה"};');
                array_push($constraints,'constraints["time_weight_one_row_'.$rowNumber.'"] = {};');
                array_push($constraints,'constraints["time_weight_one_row_'.$rowNumber.'"]["presence"] = {"message": "שדה חובה"};');
                array_push($constraints,'constraints["symptom_date_row_'.$rowNumber.'"]= {};');
                array_push($constraints,'constraints["symptom_date_row_'.$rowNumber.'"]["presence"] = {"message": "שדה חובה"};');
                array_push($constraints,'constraints["symptom_duration_row_'.$rowNumber.'"]= {};');
                array_push($constraints,'constraints["symptom_duration_row_'.$rowNumber.'"]["presence"] = {"message": "שדה חובה"};');
                array_push($constraints,'constraints["time_const_row_'.$rowNumber.'"] = {};');
                array_push($constraints,'constraints["time_const_row_'.$rowNumber.'"]["presence"] = {"message": "שדה חובה"};');
                array_push($constraints,'constraints["time_weight_two_row_'.$rowNumber.'"] = {};');
                array_push($constraints,'constraints["time_weight_two_row_'.$rowNumber.'"]["presence"] = {"message": "שדה חובה"};');
                $counter++;
            }
        }
        else {
           /* array_push($rows, '<div class="container" id="row_1">');
            array_push($rows, "<div class='col-md-12' style='width:97%;'><table class='table phenomenon'>" .
                "<tr>" . '<td>' . xl("Side effect description") . '</td><td>' . xl("Date of the phenomenon") . '</td><td></td><td>' . xl("Duration of the phenomenon") . '</td><td ></td><td></td><td></td>' . "</tr>" .
                "<tr>" . "<td><select name='symptom_description'  class='form-control categories' ></select></td>" . "<td ><input name='symptom_date' class='form-control'  type='text'/></td>" . "<td ><select  class='form-control' name='time_weight_one'>" . $this->listToOption(['minutes', 'hours', 'days', 'weeks']) . "</select></td>" . "<td><select id='symptom_duration' name='symptom_duration' class='form-control'   >" . $this->listToOption(['time description', 'continues phenomena', 'unknown']) . "</select></td>" . "<td><input  class='form-control' type='text' name='time_const'/></td>" . "<td><select name='time_weight_two' class='form-control' >" . $this->listToOption(['minutes', 'hours', 'days', 'weeks']) . "</select></td>" . "<td><icon class='glyphicon glyphicon-remove'></icon></td>" . "</tr>" .
                "</table></div>");

            array_push($rows, "<div style='display:none' class='form-group  d-inline  anaphylaxis'><div class='col-md-5'><label >" . xl("Details for Anaphylaxis") . "</label></div><div class='col-md-1'><select name='details_for_anaphylaxis' class='details_for_anaphylaxis form-control '  ></select></div></div>");
            array_push($rows, "<div style='display:none' class='form-group  d-inline  details'><div class='col-md-3'><label >" . xl("Details") . "</label></div><div class='col-md-3'><input name='details' class='form-control'type='text'/></div></div>");
            array_push($rows, "<div style='display:none' class='form-group  d-inline  affiliation_to_vaccination'><div class='col-md-8'><label >" . xl("Affiliation_to_vaccination") . "</label></div><div class='col-md-1'><select name='affiliation_to_vaccination' class='form-control vaccines_of_today' ></select></div></div>");
            array_push($rows, "<div style='display:none' class='form-group  d-inline  documentExists'><div class='col-md-1'><input name='existing_document' class='form-control' type='checkbox'></div><div class='col-md-11'><label>" . xl("There is a document with medical diagnosis") . "</label></div></div>");
            array_push($rows, '</div>');*/
        }






         foreach($rows as $row){

             echo $row;

         }




        echo "\n\n <input class='form-control' id='add' type='button' class='btn btn-success' value='".xl('Add')."'/>";


        echo "\n\n <script>\n\n ";
        echo "\n\n  rows =". json_encode($rowsEmpty).";";
        echo "\n\n if(constraints == undefined) { \n\n constraints=[]; \n\n}\n\n"  ;
        foreach($constraints as $key=>$value){  //open constraints here when approved
           echo "\n\n".$value."\n\n";
        }
        echo " \n\n  db = TAFFY('".$sidesEffect."'); \n\n";


        echo " \n\n  anflaxis = TAFFY('".$anflaxises."'); \n\n";
        echo " \n\n  vaccinesFromDate = TAFFY(".json_encode($vaccinesFromDate)."); \n\n";
        echo "\n\n $(document).ready(function(){ \n\n";
        echo "if (window.validateForm == undefined) \n\n $.getScript( '".$GLOBALS['webroot']."/interface/modules/zend_modules/public/js/SideEffect/js/side_effect.js');";
        if($symptomsValues!="") {
            $counter = 1;
            foreach ($symptomsValuesArray as $key => $symptom) {
                $keys = array_keys((array)$symptom);
                $rowNumber = explode("row_", $keys[0])[1];

                $symptom_description = "symptom_description_row_" . $rowNumber;
                $symptom_description_value = $symptom->$symptom_description;

                $symptom_date = "symptom_date_row_" . $rowNumber;
                $symptom_date_value = $symptom->$symptom_date;

                $time_weight_one = "time_weight_one_row_" . $rowNumber;
                $time_weight_one_value = $symptom->$time_weight_one;

                $symptom_duration = "symptom_duration_row_" . $rowNumber;
                $symptom_duration_value = $symptom->$symptom_duration;

                $time_const = "time_const_row_" . $rowNumber;
                $time_const_value = $symptom->$time_const;

                $time_weight_two = "time_weight_two_row_" . $rowNumber;
                $time_weight_two_value = $symptom->$time_weight_two;

                $details_for_anaphylaxis = "details_for_anaphylaxis_row_" . $rowNumber;
                $details_for_anaphylaxis_value = implode(",",json_decode($symptom->$details_for_anaphylaxis));

                $details = "details_row_" . $rowNumber;
                $details_value = $symptom->$details;

                $affiliation_to_vaccination = "affiliation_to_vaccination_row_" . $rowNumber;
                $affiliation_to_vaccination_value = $symptom->$affiliation_to_vaccination;

                $existing_document = "existing_document_row_" . $rowNumber;
                $existing_document_value = $symptom->$existing_document;


                $rowNumber = $counter;
                $symptom_description = "symptom_description_row_" . $rowNumber;
                $symptom_date = "symptom_date_row_" . $rowNumber;
                $time_weight_one = "time_weight_one_row_" . $rowNumber;
                $symptom_duration = "symptom_duration_row_" . $rowNumber;
                $time_const = "time_const_row_" . $rowNumber;
                $time_weight_two = "time_weight_two_row_" . $rowNumber;
                $details_for_anaphylaxis = "details_for_anaphylaxis_row_" . $rowNumber;
                $details = "details_row_" . $rowNumber;
                $affiliation_to_vaccination = "affiliation_to_vaccination_row_" . $rowNumber;
                $existing_document = "existing_document_row_" . $rowNumber;

                echo "\n\n";
                echo "$(\"[name='$symptom_description']\").val('$symptom_description_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$symptom_date']\").val('$symptom_date_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$time_weight_one']\").val('$time_weight_one_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$symptom_duration']\").val('$symptom_duration_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$time_const']\").val('$time_const_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$time_weight_two']\").val('$time_weight_two_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$details_for_anaphylaxis']\").val([$details_for_anaphylaxis_value]).trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$details']\").val('$details_value').trigger('change');";
                echo "\n\n";
                echo "$(\"[name='$affiliation_to_vaccination']\").val('$affiliation_to_vaccination_value').trigger('change');";
                echo "\n\n";
               // echo "$(\"[name='$existing_document']\").val('$existing_document_value').trigger('change');";
               // echo "\n\n";


                if($existing_document_value==true){
                     echo '$("[name=\''.$existing_document.'\']").trigger("click")';
                }

                $counter++;
            }
        }

          echo   "\n\n });\n\n 


        

</script>";



    }
}

