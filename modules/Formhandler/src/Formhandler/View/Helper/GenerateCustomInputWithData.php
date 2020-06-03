<?php
/**
 * Created by DROR GOLAN.
 * User: drorgo
 */

namespace Formhandler\View\Helper;
use Application\Listener\Listener;
use Formhandler\Model\customDB;
use Formhandler\View\Helper\Form\TwbBundleFormStatic;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Laminas\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Laminas\Form\View\Helper\AbstractHelper;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Db\Adapter\Adapter;

class GenerateCustomInputWithData  extends AbstractHelper
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
    public function translateWord($word){
        return $this->translate->z_xlt(trim($word));

    }

    /**
     * @param $name
     * @param $listReturned
     * @param $type
     * @param $selectedValue
     * @param $add_value
     * @return string
     */
    public function CreateControlFromData($name, $listReturned, $type, $selectedValue, $add_value)
    {
        if(is_null($listReturned)) {
            switch ($type) {
                case 'static':
                    return "<dl id='" . $name . "_replace' class='form-group'>
              <dt>" . $this->translateWord(str_replace("_", " ", $name)) . ":</dt>
              <dd> </dd>
              </dl>
           ";

                    break;
                case 'hidden':
                    return "<input type='hidden' name='$name' id='" . $name . "_replace' />";
                    break;
                case 'select':
                    return "<select name='$name' id='" . $name . "_replace' ></select>";
                    break;
                case 'input':
                    return "<input  name='$name' id='" . $name . "_replace' />";
                    break;

            }
        }
        $controls='';
        switch($type) {
            case 'static':
                foreach ($listReturned as $key=>$list){
                    foreach ($list as $key=>$value) {

                        if($key!='id') {
                            $controls = "<dl id='" . $key . "_replace' class='form-group'>
              <dt>" . $this->translateWord(str_replace("_", " ", $key)) . ":</dt>
              <dd>$value</dd>
              </dl>
           ";
                        }
                    }
                }
                break;
            case 'hidden':
                foreach ($listReturned as $key=>$list){
                foreach ($list as $key=>$value) {
                    $controls = "<input type='hidden' name='" .$name. "' value='" .$value. "'>";
                    }
                }
                break;


            case 'select':
                $controls="<select name='$name' id='".$name."_replace' >";
                if(isset($selectedValue[0])) {
                    foreach ($selectedValue[0] as $key => $value1)
                    {
                        $selectedValueResult=$value1;
                    }
                }

                foreach ($listReturned as $list)

                    foreach ($list as $key=>$value) {
                        {
                            if(!empty($value) &&$value!='null' && $key!='id'){

                                if($selectedValueResult==$list['id'])
                                {
                                    $controls .= "<option selected='selected' value='" . $list['id'] . "'>" .  $value . "</option>";
                                }
                                else{
                                    $controls .= "<option value='" . $list['id'] . "'>" .  $value . "</option>";
                                }

                            }
                        }
                    }

                if(!is_null($add_value)&&$add_value!='')
                {
                    if($add_value != 'Unassigned'){
                        $controls .= "<option value='" .$add_value . "'>" .  $this->translateWord($add_value) . "</option>";
                    } else {
                        $controls .= "<option value='' ";
                        if(isset($selectedValueResult) && strlen($selectedValueResult) ==0) $controls .=  ' selected ';
                        $controls .= " >" .  $this->translateWord($add_value) . "</option>";
                    }

                }
                $controls.="</select>";
                break;


            case 'input':
                $controls="<input name='$name' id='".$name."_replace' value='";




                foreach ($listReturned as $list)
                    foreach ($list as $key=>$value) {
                        {
                            if($selectedValue)
                            {
                                if(!empty($value) &&$value!='null' && $key!='id') {


                                    $valueReplace = $value;


                                }
                            }
                            else{
                                $valueReplace ="";
                            }
                        }
                    }
                $controls.=$valueReplace. "' />";
                break;
        }
        return $controls;
    }


    /**
     * @param $id
     * @param $table / tables - support query with multi table
     * @param $fields
     * @param $idToReplace
     * @param $name
     * @param $type
     * @param null $valuesTable
     * @param null $valuefield
     * @param null $patient_id_DB
     * @param null $add_value
     * @param null $sql_where
     * @param null $valueFromList list name for case that ID saved in the DB and we need value
     * @return string
     */
    public function CreateNewInputTypeFromDB($id, $table, $fields, $idToReplace, $name, $type, $valuesTable=null, $valuefield=null, $patient_id_DB=null, $add_value=null, $sql_where=null, $valueFromList=null)
    {
        if(!is_null($sql_where))$sql_where = str_replace('$patient_id_DB',$patient_id_DB, $sql_where);
        $customdb =  $this->getCustomDB();

        $isMultiTables = count(explode(',',$table)) > 1 ? true : false;
        if($isMultiTables) {
            $listReturned=$customdb->selectDataFromDB($table,$fields,$id,$sql_where,$valueFromList);
        } else {
            $listReturned=$customdb->selectDataFromDB($table,$fields." ,id ",$id,$sql_where,$valueFromList);
        }


        if(!is_null($valuesTable) && $valuesTable!='' && !is_null($valuefield) && $valuefield!='' &&!is_null($patient_id_DB)&&$patient_id_DB!='')
            $value=$customdb->selectPatientDataFromDB($valuesTable,$valuefield,$patient_id_DB);
        if(isset($value) && !is_null($valueFromList))$value=$customdb->getValueFromList($value,$valueFromList);


         $template=$this->CreateControlFromData($name,$listReturned,$type,$value,$add_value);
        switch($type) {
            case 'static':
            case 'hidden':
        $script="
                <script>
               $('document').ready(function() {
                   var replaceThis=$('#".$idToReplace."').parent();
                   var hideThis=$('#".$idToReplace."');
                   $('#".$name."_replace').prop('class','col-md-2');
                   replaceThis.html($('#".$name."_replace'));
                   replaceThis.append(hideThis);
                   hideThis.val();
                   hideThis.hide($('#".$name."_replace').find('p').html());
                   
                });
                </script>";
                break;
            case 'select':
                $script = "
                 <script>
                   $('document').ready(function() {
                       var replaceThis=$('#" . $idToReplace . "').parent();
                        var replaceThisValue=$('#" . $idToReplace . "');
                       var replaceThisLabel=$('#" . $idToReplace . "').parent().find('label');
                       var hideThis=$('#" . $idToReplace . "');
                       $('#" . $name . "_replace').prop('class','form-control');
                       
                       replaceThis.html();
                        replaceThis.append(replaceThisLabel);
                        replaceThis.append($('#" . $name . "_replace'));
                        //$('#" . $name . "_replace').val(replaceThisValue.val());
                        //$('#" . $name . "_replace').trigger('change');
                        
                       $('#" . $name . "_replace').prop('id', '$idToReplace');
                      
                      
                       hideThis.remove();
                    });
                    </script>";
                break;
            case 'input':
                $script = "
                 <script>
                   $('document').ready(function() {
                       var replaceThis=$('#" . $idToReplace . "').parent();
                        var replaceThisValue=$('#" . $idToReplace . "');
                       var replaceThisLabel=$('#" . $idToReplace . "').parent().find('label');
                       var hideThis=$('#" . $idToReplace . "');
                       $('#" . $name . "_replace').prop('class','form-control');
                       
                       replaceThis.html();
                        replaceThis.append(replaceThisLabel);
                        replaceThis.append($('#" . $name . "_replace'));
                        //$('#" . $name . "_replace').val(replaceThisValue.val());
                        //$('#" . $name . "_replace').trigger('change');
                        
                       $('#" . $name . "_replace').prop('id', '$idToReplace');
                      
                      
                       hideThis.remove();
                    });
                    </script>";
                break;


            default:
                $script = "";
        }



        return $template.$script;




            $selectBox="<select multiple='multiple' name='$list'>";
        foreach ($listReturned as $value)
        {
            $selectBox.="<option   value='".$value['option_id']."'>".$value['title']."</option>";
        }
        $selectBox.="</select>";
        return $selectBox;
    }
    public function __invoke($id,$table,$fields,$idToReplace,$name,$type,$valuesTable=null,$valuefield=null,$patient_id_DB=null,$add_value=null,$sql_where=null,$valueFromList=null)
    {


        $this->translate = new Listener();

         $getnewInput=$this->CreateNewInputTypeFromDB($id,$table,$fields,$idToReplace,$name,$type,$valuesTable,$valuefield,$patient_id_DB,$add_value,$sql_where,$valueFromList);
        return $getnewInput;
    }
}
