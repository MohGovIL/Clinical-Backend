<?php
/**
 * Created by PhpStorm.
 * User: silvers69
 * Date: 07/09/16
 * Time: 22:27
 */


namespace Formhandler\View\Helper;
use Application\Listener\Listener;
use Formhandler\Model\customDB;
use Zend\Db\Exception\ErrorException;
use Zend\View\Helper\AbstractHelper;


class CreateCustomControlFromList extends AbstractHelper
{

    protected $dbAdapter;

   protected  $translate=null;
    public function setDbAdapter($adapter){
        $this->dbAdapter=$adapter;
    }

    protected function getCustomDB(){


        $CustomDb = new CustomDb( $this->dbAdapter);
        return $CustomDb;
    }

    public function CreateSelectBoxFromList($list_name,$patient_id,$type,$replace_with_id,$id,$encounter_form_id,$table_name, $extentedReplace,$class,$valueColumn=null,$containerType=null)
    {
        $customdb =  $this->getCustomDB();


        //$fieldToSelect=$customdb->selectDataFromDB("patient_data",$patient_field,$patient_id);
        try {
            $listReturned = $customdb->getListParams($list_name);
        }
        catch(ErrorException $err)
        {
            return "<p class=' $class ' >".$err.":".$list_name."</p>";
        }


        try{
            if($table_name!=''&&$encounter_form_id!=''&&$id!='') {


                $FormData = $customdb->selectDataFromForms($table_name, $encounter_form_id, $id);
            }
            else{

                $FormData=null;
            }

            if($containerType=='billing') {
                $form_name = str_replace("form_","",$table_name);
                $billingPaymentsTickets = $customdb->getPaymentsTicketsOfTheForm([$patient_id,$form_name,$id]);
            }

        }
        catch(ErrorException $err)
        {
            return "<p class=' $class '>".$err.":".$list_name.":".$table_name."</p>";
        }

        $replace_with_id = $extentedReplace != null ? $extentedReplace : $replace_with_id;

        switch ($type){
            case "selectBox":

                if(is_null($listReturned)) {

                    $selectBox = "<select class='form-control  disabled  label-danger $class' id='" . $replace_with_id . "_list'><option style='color:white;'> MISSING LIST : " . $list_name . "</option></select>";
                }
                else {
                    $selectBox = "<select class='form-control $class ' id='" . $replace_with_id . "_list'>";
                    $selectBox .= "<option   value=''>" . $this->translate->z_xlt('choose') . "</option>";
                    foreach ($listReturned as $value) {
                        if(!is_null($FormData) && ($FormData[0][$replace_with_id]==$value['option_id'])) {
                            if($valueColumn!="")
                            {
                                $selectBox .= "<option   selected value='" . $value[$valueColumn] . "'>" . $this->translate->z_xlt($value['title']) . "</option>";
                            }
                            else {
                                $selectBox .= "<option   selected value='" . $value['option_id'] . "'>" . $this->translate->z_xlt($value['title']) . "</option>";
                            }
                        }
                        else {
                            // $selected=$value['option_id']==$fieldToSelect[$patient_field]?'selected':'';
                            $selected='';
                            if($value['is_default']==1){
                                $selected='selected';
                            }
                            if($valueColumn!="")
                            {
                                $selectBox .= "<option   value='" . $value[$valueColumn] . "' ". $selected ." >". $this->translate->z_xlt($value['title']) . "</option>";
                            }
                            else {
                                $selectBox .= "<option   value='" . $value['option_id'] . "' ". $selected .">" . $this->translate->z_xlt($value['title']) . "</option>";
                            }
                        }
                    }
                    $selectBox .= "</select>";
                }


                break;

            case "static":
                $selectBox = "<input class=' $class ' type='text' disabled='disabled' id='" . $replace_with_id . "' value='".$this->translate->z_xlt($listReturned[0]['title'])."'>";
                break;

            case "checkboxContainer":{
                $selectBox = "<div class='container col-md-12' id='".$replace_with_id."_list'>";
                foreach ($listReturned as $value) {
                 /*   $selectBox .='<div class="checkbox col-md-12" style="margin-right: 0px; margin-top: 0px;">
                            <input type="checkbox" name="'.$value['option_id'].'" value="'.$FormData[$value].'" style="border-color: rgb(170, 170, 170);">
                            <label style="margin-right: 30px;">'. $this->translate->z_xlt($value['title']).'</label></div>';
*/

                    if(!isset($FormData))
                    {
                        $valueOfTheCorrectListItemInFormData = $value['is_default']=="1"?true:false;
                    }
                    else {
                        $formdataOfTheCorrectKey = json_decode($FormData[0][$replace_with_id]);
                        $keyOfTheListOptions = $value['option_id'];
                        $valueOfTheCorrectListItemInFormData = $formdataOfTheCorrectKey->$keyOfTheListOptions;

                    }

                    $selectBox .= '<div class="checkbox col-md-6" style="margin-right: 0px; margin-top: 0px;">
                                    
                                    <input type="hidden" name="'.$value['option_id'].'" value="0" style="border-color: rgb(170, 170, 170);">
                                    <div class="col-md-4">
                                    <label style="">';
                                    if($containerType == 'billing')
                                    {
                                        //SELECT FROM PAYMENTS TABLE BY VACCINE FORM NAME -  paid / not paid
                                        $paid =  isset($billingPaymentsTickets[$value['option_id']]) && $billingPaymentsTickets[$value['option_id']]['paid']==1?xlt("Paid"):xlt("Not paid");

                                        $selectBox .= '<input type="checkbox" onchange="'."showHideData('".$value['option_id']."_paid"."',this)".'" '.($valueOfTheCorrectListItemInFormData === true ?'checked':'').' name="'.$value['option_id'].'" value="1" style="margin-right: -33px; border-color: rgb(170, 170, 170);">'. $this->translate->z_xlt($value['title']);
                                        $selectBox .= '</label>
                                    </div>
                                    <div class="col-md-8 pull-right">';

                                            $selectBox .= '<div id="'.$value['option_id'].'_paid" style=" margin-top: 3px; margin-bottom: 10px;margin: 4px -27px;font-size:10px">'.$paid.'</div>';




                                        $selectBox .='</div></div>';
                                    }
                                    else{
                                        $selectBox .='<input type="checkbox" '.($valueOfTheCorrectListItemInFormData === true ?'checked':'').' name="'.$value['option_id'].'" value="1" style="margin-right: -33px; border-color: rgb(170, 170, 170);">'. $this->translate->z_xlt($value['title']);
                                        $selectBox .= '</label>
                                    </div></div>';
                                    }

                                    

                }
                $selectBox .= "</div>";
                break;
            }
        }
        $script = '';
        switch ($type){
            case "selectBox":
            case "static":
            default:
            {
                if($selectBox!='')
                {

                    $script='<script> ';

                    $script.="\r\n";
                    $script.='$("document").ready(function() {';
                    $script.="\r\n";

                    $script.='var '.$replace_with_id.'=$("#'.$replace_with_id.'").parent();';
                    $script.="\r\n";
                    $script.='var '.$replace_with_id.'_label=$("#'.$replace_with_id.'").parent().find("label");';
                    $script.="\r\n";
                    $script.="var label=$(".$replace_with_id."_label)[0];";
                    $script.="\r\n";
                    $script.="var text_label=label.innerText";
                    $script.="\r\n";
                    $script.='var '.$replace_with_id.'_name=$("#'.$replace_with_id.'").attr("name");';
                    $script.="\r\n";
                    $script.="var value=". $replace_with_id.'.val();';
                    $script.="\r\n";
                    $script.= $replace_with_id.'.html(" ");';
                    $script.="\r\n";
                    $script.= '$('.$replace_with_id.').append(label);';
                    $script.= '$('.$replace_with_id.').find("label").text(text_label);';



                    $script.="\r\n";
                    $script.="$replace_with_id.html(label);";
                    $script.="\r\n";
                    $script.= $replace_with_id.'.append($("#'.$replace_with_id.'_list"));';
                    $script.="\r\n";
                    $script.='$("#'.$replace_with_id.'_list").prop("name",'.$replace_with_id.'_name);';
                    $script.="\r\n";
                    $script.='$("#'.$replace_with_id.'_list").prop("id","'.$replace_with_id.'");';
                    $script.="\r\n";
                    $script.='$("#'.$replace_with_id.'_list").val(value);';


                    $script.='});';
                    $script.="\r\n";
                    $script.='</script>';


                }
                break;
            }
            case "checkboxContainer":
            {
                if($selectBox!='')
                {

                    $script='<script>';

                    $script.="\r\n";
                    $script.='$("document").ready(function() {';
                    $script.="\r\n";

                    $script.='var '.$replace_with_id.'=$("#'.$replace_with_id.'").parent();';
                    $script.="\r\n";

                    $script.= '$("#'.$replace_with_id.'")'.'.hide();';
                    $script.="\r\n";
                    $script.= $replace_with_id.'.append($("#'.$replace_with_id.'_list"));';
                    $script.="\r\n";
                    $script.='});';
                    $script.="\r\n";
                    $script.=" arr_$replace_with_id=[]";
                    $script.="\r\n";

                    $script .= '$("#' . $replace_with_id . '_list").on("change",function(){
                        arr_' . $replace_with_id . '={};
                        $(this).find("input").each(function(){
                            arr_' . $replace_with_id . '[$(this)[0].name]=$(this).is(":checked");
                        });
                        $("#' . $replace_with_id . '").val(JSON.stringify(arr_' . $replace_with_id . '));
                    });';
                    $script .= "\r\n";

                    if($containerType == 'billing') {

                        $script .= ' function showHideData(id,thisObj){
                            if (thisObj.checked){
                                $("#"+id).show();
                            } 
                            else{
                                $("#"+id).hide();
                            }
                    }';
                    }

                     $script.="\r\n";
                    $script.='</script>';



            }
                break;
        }


        }
        return $selectBox."\r\n".$script;


    }
    public function __invoke($patient_id,$list_name,$type,$replace_with_id,$id,$encounter_form_id,$table_name, $extentedReplace = null,$class="",$valueColumn=null,$containerType=null)
    {
        $this->translate = new Listener();
        return $this->CreateSelectBoxFromList($list_name,$patient_id,$type,$replace_with_id,$id,$encounter_form_id,$table_name, $extentedReplace,$class,$valueColumn,$containerType);
    }
}
