<?php
/**
 * Created by PhpStorm.
 * User: drorgo
 * Date: 22/09/16
 * Time: 10:49 AM
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

class GetLastPsychosocialTreatment extends AbstractHelper
{

    protected $dbAdapter;
    /* public function __construct($dbAdapter)
     {
         $string=$dbAdapter;
         $this->dbAdapter = new $dbAdapter;
         __invoke("hello");
     }*/


    public function setDbAdapter($adapter){
        $this->dbAdapter=$adapter;
    }

    protected function getCustomDB(){


        $CustomDb = new CustomDb( $this->dbAdapter);
        return $CustomDb;
    }

    public function CreateSelectBoxFromLastEncounter($table,$id,$form_id)
    {
        $customdb =  $this->getCustomDB();
        $listReturned=$customdb->selectPatientDataFromDBMaxDate($table,"*",$id,$form_id);

       /* $selectBox="<select  name='$list'>";
        if(is_null($listReturned))
            return null;
        foreach ($listReturned as $value)
        {
            $selectBox.="<option   value='".$value['option_id']."'>".$value['title']."</option>";
        }
        $selectBox.="</select>";*/
        return $listReturned;
    }

    public function CreateSelectBoxFromList($list,$name,$patient_id,$default_value)
    {
        $customdb =  $this->getCustomDB();
        $translate = new Listener();
        $listReturned=$customdb->getListParams($list);
       // $fieldToSelect=$customdb->selectMaxDataFromDB("psychosocial_treatment",$name,$patient_id);


        if(is_null($listReturned))
            return null;
        $selectBox="<select id='$name' class='form-control'  name='$name'>";

        foreach ($listReturned as $value)
        {
            if($value['title']==$default_value)
            {
                $selectBox.="<option selected  value='".$value['option_id']."'>".$translate->z_xlt($value['title'])."</option>";
            }
            else{
                $selectBox.="<option value='".$value['option_id']."'>".$translate->z_xlt($value['title'])."</option>";
            }

        }
        $selectBox.="</select>";
        return $selectBox;
    }
    public function CreateInputFromData($name,$id,$data)
    {

        $selectBox="<input id='$id' class='form-control'  name='$name' value='";

         $valueReturned=$data;

        $selectBox.=$valueReturned."' />";
        return $selectBox;
    }

    public function __invoke($id,$field_id,$form_id)
    {
        $translate = new Listener();
        $customdb =  $this->getCustomDB();
        $values=$this->CreateSelectBoxFromLastEncounter("form_psychosocial_treatment",$id,$form_id)[0];
        $current_values=$this->CreateSelectBoxFromLastEncounter("form_psychosocial_treatment",$id,($form_id+1))[0];
        if($values && $values['deleted']<1) {
            $returnedHtml = '<div dir="rtl" style="border 1px solid grey;" id=replace_me_psychosocial_treatment>';
           /* $returnedHtml .= '<dl class="dl-horizontal">
                       <div class="col-md-6" dir="rtl">

                       <dt><span class = "label label-default">'.$translate->z_xlt("Indevidual therapy").'</span></dt><dd> <input type="checkbox" disabled ' .($values["indevidual_therapy"]==1?'checked':'') . ' \></dd>
                       <dt><span class = "label label-default">'.$translate->z_xlt("Detailed treatment method").'</span></dt><dd>'. $translate->z_xlt($customdb->getValueFromList($values["detailed_treatment_method"],'moh_indevidual_therapy_list')) .'</dd>
                       <dt><span class = "label label-default">'.$translate->z_xlt("Family therapy").'</span></dt><dd><input type="checkbox"  disabled ' .($values["family_therapy"]==1?'checked':'') . ' \></dd>
                       <dt><span class = "label label-default">'.$translate->z_xlt("Other treatment").'</span></dt><dd><input type="checkbox"  disabled ' .($values["other_treatment"]==1?'checked':'') . ' \></dd>
                       </div>
                       <!---------------------------------------->
                       <div class="col-md-6">
                       <dt><span class = "label label-default">'.$translate->z_xlt("Group therapy").'</span></dt><dd>  <input type="checkbox"  disabled ' .($values["group_therapy"]==1?'checked':'') . ' \></dd>
                       <dt><span class = "label label-default">'.$translate->z_xlt("Group therapy method").'</span></dt><dd>' . $translate->z_xlt($customdb->getValueFromList($values["group_therapy_method"],'moh_group_therapy_list')) . '</dd>



                       <dt><span class = "label label-default">'.$translate->z_xlt("Family therapy method").'</span></dt><dd>' . $translate->z_xlt($customdb->getValueFromList($values["family_therapy_method"],'moh_family_therapy_list'))  . '</dd>
                       <dt><span class = "label label-default">'.$translate->z_xlt("Other therapy method").'</span></dt><dd>' . $values["other_treatment_method"] . '</dd>
                       </div>
                       </dl>';*/

            $returnedHtml .= '<table class="table table-dark   ">';
            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Indevidual therapy").'</span></td><td><input type="checkbox" disabled ' .($values["indevidual_therapy"]==1?'checked':'') . ' \></td>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Group therapy").'</span></td><td> <input type="checkbox"  disabled ' .($values["group_therapy"]==1?'checked':'') . ' \></td>';
            $returnedHtml .= '</tr>';

            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Detailed treatment method").'</span></td><td>'.$translate->z_xlt($customdb->getValueFromList($values["detailed_treatment_method"],'moh_indevidual_therapy_list')).'</td>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Group therapy method").'</span></td><td>'. $translate->z_xlt($customdb->getValueFromList($values["group_therapy_method"],'moh_group_therapy_list')) .'</td>';
            $returnedHtml .= '</tr>';

            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Family therapy").'</span></td><td><input type="checkbox"  disabled ' .($values["family_therapy"]==1?'checked':'') . ' \></td>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Other treatment").'</span></td><td><input type="checkbox"  disabled ' .($values["other_treatment"]==1?'checked':'') . ' \></td>';
            $returnedHtml .= '</tr>';

            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Other treatment").'</span></td><td><input type="checkbox"  disabled ' .($values["other_treatment"]==1?'checked':'') . ' \></td>';
            $returnedHtml .= '<td><span class = "">'.$translate->z_xlt("Other therapy method").'</span></td><td>' . $values["other_treatment_method"] . '</td>';
            $returnedHtml .= '</tr>';
            $returnedHtml .= '</table>';


            $returnedHtml .= '<table id="psychosocial_treatment_table" class="table">';
            $returnedHtml .= '<thead>';
            $returnedHtml .= '<td width="33%"><b>'.$translate->z_xlt("Goals").'</b></td>';
            $returnedHtml .= '<td width="33%"><b>'.$translate->z_xlt("Achivements").'</b></td>';
            $returnedHtml .= '<td width="33%"><b>'.$translate->z_xlt("Cooperation").'</b></td>';
            $returnedHtml .= '</thead>';
            $returnedHtml .= '<tbody>';

            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td>' . $values["goal_one"] . '</td>';
            $returnedHtml .= '<td>' . $this->CreateSelectBoxFromList("moh_achivements", "achivements_one",$id,$current_values["achivements_one"]) . '</td>';
            //$returnedHtml .= '<td><input name="cooperation_one" id="cooperation_one"  value="'..'/></td>';// . $this->CreateSelectBoxFromList("moh_achivements", "achivements_three",$id) . '</td>';
            $returnedHtml .= '<td>' . $this->CreateInputFromData("cooperation_one","cooperation_one",$current_values["cooperation_one"]).'</td>';
            $returnedHtml .= '</tr>';

            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td>' . $values["goal_two"] . '</td>';
            $returnedHtml .= '<td>' . $this->CreateSelectBoxFromList("moh_achivements", "achivements_two",$id,$current_values["achivements_two"]) . '</td>';
            //$returnedHtml .= '<td><input name="cooperation_two" id="cooperation_two" value="'.$current_values["cooperation_two"].'/></td>';// . $this->CreateSelectBoxFromList("moh_achivements", "achivements_three",$id) . '</td>';
            $returnedHtml .= '<td>'. $this->CreateInputFromData("cooperation_two","cooperation_two",$current_values["cooperation_two"]).'</td>';
            $returnedHtml .= '</tr>';

            $returnedHtml .= '<tr>';
            $returnedHtml .= '<td>' . $values["goal_three"] . '</td>';
            $returnedHtml .= '<td>' . $this->CreateSelectBoxFromList("moh_achivements", "achivements_three",$id,$current_values["achivements_three"]) . '</td>';
            // $returnedHtml .= '<td><input name="cooperation_three" id="cooperation_three" value="'.$current_values["cooperation_three"].'"/></td>';// . $this->CreateSelectBoxFromList("moh_achivements", "achivements_three",$id) . '</td>';
            $returnedHtml .= '<td>'. $this->CreateInputFromData("cooperation_three","cooperation_three",$current_values["cooperation_three"]).'</td>';

            $returnedHtml .= '</tr>';

            $returnedHtml .= '</tbody>';
            $returnedHtml .= '</table>';
            $returnedHtml .= '<label for="progress_of_treatment">'.$translate->z_xlt("Treatment\'s Progress").'</label>';
            $returnedHtml .= '<textarea class="form-control"  type="textarea" name="progress_of_treatment" id="progress_of_treatment" rows=10>'.$current_values["progress_of_treatment"].'</textarea>';
            $returnedHtml .= '</div>';

        }
        else{
            $returnedHtml =  '<div id=replace_me_psychosocial_treatment>';
            $returnedHtml .= "<label id='last_checkup_label' >".$translate->z_xlt("No last encounter was found")."</label>";
            $returnedHtml .=  '</div>';
        }


        $returnedHtml .= "\r\n"."<script>"." \r\n"." $('document').ready(function() { "."\r\n";
        $returnedHtml .="\r\n";


        $returnedHtml .="\r\n";
        $returnedHtml .= 'var replaceINThis=$("input[name=last_checkup]").closest(".form-group-TWB");';
        $returnedHtml .="\r\n";
        $returnedHtml .= 'var label=$("input[name=last_checkup]").parent().find("label");';
        $returnedHtml .="\r\n";
        $returnedHtml .= 'label.appendTo(replaceINThis)';
        $returnedHtml .="\r\n";
        $returnedHtml .="replaceINThis.html('');";
        $returnedHtml .="\r\n";
        $returnedHtml .='$("#replace_me_psychosocial_treatment").appendTo(replaceINThis)';
        $returnedHtml .="\r\n";
        $returnedHtml .= '$("#replace_me_psychosocial_treatment").attr("id","last_checkup");';
        $returnedHtml .="\r\n";
        $returnedHtml .="$('input[name=last_checkup]').remove();";

        $returnedHtml .=  '});';
        $returnedHtml .="\r\n";
        $returnedHtml .="triggerChangeClick()";
        $returnedHtml .='</script>';

        return $returnedHtml;
    }
}

/*
 *   "achivements_one": {
           "name": "achivements_one",
           "attributes": {
               "type": "text",
               "id": "achivements_one"
           },
           "options": {
               "label": "New Achivements one",
               "class": "form-control"
           }
       },
       "cooperation_one": {
           "name": "cooperation_one",
           "attributes": {
               "type": "text",
               "id": "cooperation_one"
           },
           "options": {
               "label": "New Cooperation one",
               "class": "form-control"
           }
       },
       "achivements_two": {
           "name": "achivements_two",
           "attributes": {
               "type": "text",
               "id": "achivements_two"
           },
           "options": {
               "label": "New Achivements two",
               "class": "form-control"
           }
       },
       "cooperation_two": {
           "name": "cooperation_two",
           "attributes": {
               "type": "text",
               "id": "text"
           },
           "options": {
               "label": "New Cooperation two",
               "class": "form-control"
           }
       },
       "achivements_three": {
           "name": "achivements_three",
           "attributes": {
               "type": "text",
               "id": "achivements_three"
           },
           "options": {
               "label": "New Achivements three",
               "class": "form-control"
           }
       },
       "cooperation_three": {
           "name": "cooperation_three",
           "attributes": {
               "type": "text",
               "id": "co_operation_three"
           },
           "options": {
               "label": "New Cooperation three",
               "class": "form-control"
           }
       },
"progress_of_treatment": {
           "name": "progress_of_treatment",
           "attributes": {
               "type": "text",
               "id": "progress_of_treatment"
           },
           "options": {
               "label": "Progress of treatment",
               "class": "form-control"
           }
       },

 * */
