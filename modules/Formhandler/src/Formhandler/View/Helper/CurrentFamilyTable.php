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

class CurrentFamilyTable  extends AbstractHelper
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

    public function CreateSelectBoxFromList($list)
    {
        $translate = new Listener();
        $customdb =  $this->getCustomDB();
        $listReturned=$customdb->getListParams($list);
        if(is_null($listReturned))
            return "<p class='text-danger'> missing list : $list</p>";
        $selectBox="<select  class='form-control'  name='$list'>";
        foreach ($listReturned as $value)
        {
            $selectBox.="<option   value='".$value['option_id']."'>".$translate->z_xlt($value['title'])."</option>";
        }
        $selectBox.="</select>";
        return $selectBox;
    }
    public function __invoke($id)
    {




        $translate = new Listener();

        $buttons='<div  id="menu_buttons_family" class="form-group" role="group">
        <div  id="button_description" class="button_description"> <label>'. $translate->z_xlt("Press here to add or remove records") . '</label></div>
        <input type="button" id="addRow_'.$id.'"  class="btn" value="+"\>
            
        <input type="button" id="deleteRow_'.$id.'"  class="btn" value="-"\>
            
        </div>
        ';




        $table = '<table class="table table-striped   dataTable " id="' .$id .'_table">';
        $table.='<thead>';

        $table.='<th>' . $translate->z_xlt('Type') .'</th>';
        $table.='<th>' .$translate->z_xlt('First and last name') .'</th>';
        $table.='<th>' . $translate->z_xlt('Year of birth') .'</th>';
        $table.='<th>' . $translate->z_xlt('Occupation') .'</th>';
        $table.='<th>' . $translate->z_xlt('Lives with patient') .'</th>';
        $table.='<th>' . $translate->z_xlt('Use of psy. materials') .'</th>';
        $table.='<th>' . $translate->z_xlt('Under treatment') .'</th>';
        $table.='<th>' . $translate->z_xlt('Comments') .'</th>';

        $table.='</thead>';
     //   $table.='<tbody><tr><td><input name="hello" id="hello"/></td><td><input name="hello3" id="hello3"/></td><td><input name="bye" id="bye"/></td><td><input name="hello34" id="hello34" /></td><td><input name="hello32" id="hello32"/></td><td><input name="hello22" id="hello22"/></td></tr>';
        $table.='</tbody>';

        /*$table.='<tfoot>';
        $table.='<th>' . $translate->z_xlt('Type') .'</th>';
        $table.='<th>' .$translate->z_xlt('First and last name') .'</th>';
        $table.='<th>' . $translate->z_xlt('Year of birth') .'</th>';
        $table.='<th>' . $translate->z_xlt('Occupation') .'</th>';
        $table.='<th>' . $translate->z_xlt('Lives with patient') .'</th>';
        $table.='<th>' . $translate->z_xlt('Use of psy. materials') .'</th>';
        $table.='<th>' . $translate->z_xlt('Under treatment') .'</th>';
        $table.='<th>' . $translate->z_xlt('Comments') .'</th>';
        $table.='</tfoot>';*/

        $table.=$buttons;
        $table.='</table>';

        $script =  '<script> function selectFromList(obj,value)
        {
          
               obj=obj.replace("value=\'"+value+"\'", "selected value=\'"+value+"\'");
                
               return obj;
    
        }
    $(document).ready(function() {
  
     
                 var t=$("#'.$id.'_table").DataTable({   "searching": false,"bLengthChange": false,  "columns": [
    { "width": "6.25%" },
    { "width": "12.5%" },
    { "width": "6.25%" },
    { "width": "12.5%" },
    { "width": "6.25%" },
    { "width": "6.25%" },
    { "width": "6.25%" },
    { "width": "12.5%" }
  ],
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
        }
    }});
                    
                 <!---------------LOAD FAMILY STATUS------------------------------->
                 
                 if($("#family_table").val()!="")
                 {
                datajson=$.parseJSON($("#family_table").val());
                
                var dataForTable=[];
                var counter=0;
                var pushThis=[];
                $.each(datajson,function(key,value){
                if(value["value"]!="")
                {
                val=value["value"];
                }
                else{
                val="";
                }
                if(counter<8){
                pushThis.push(val);
                counter++;
                }
                else{
                counter=1;
                dataForTable.push(pushThis);
                 
                pushThis=[];
                pushThis.push(val);
               
               
               
                    //
                }
                
                
                });
                dataForTable.push(pushThis);
                
                
                $.each(dataForTable,function(key,val){
                
                var moh_type_of_relative="'.$this->CreateSelectBoxFromList("moh_type_of_relative").'";
       
           
               if(val[4]=="Yes")
                    val[4]="<option value=\"Yes\" selected>'.$translate->z_xlt("Yes").'</option><option value=\"No\">'.$translate->z_xlt("No").'</option>";
                if(val[4]=="No")
                    val[4]="<option value=\"Yes\" >'.$translate->z_xlt("Yes").'</option><option value=\"No\" selected>'.$translate->z_xlt("No").'</option>";           
                

                if(val[5]=="Yes")
                    val[5]="<option value=\"Yes\" selected>'.$translate->z_xlt("Yes").'</option><option value=\"No\">'.$translate->z_xlt("No").'</option>";          
                if(val[5]=="No")
                    val[5]="<option value=\"Yes\" >'.$translate->z_xlt("Yes").'</option><option value=\"No\" selected>'.$translate->z_xlt("No").'</option>";           
                
                if(val[6]=="Yes")
                    val[6]="<option value=\"Yes\" selected>'.$translate->z_xlt("Yes").'</option><option value=\"No\">'.$translate->z_xlt("No").'</option>";            
                if(val[6]=="No")
                    val[6]="<option value=\"Yes\" >'.$translate->z_xlt("Yes").'</option><option value=\"No\" selected>'.$translate->z_xlt("No").'</option>";            
              
                  t.row.add( [  selectFromList(moh_type_of_relative,val[0]),
                               
                           
                                
                                  "<input  class=\"form-control\" name=\"fname_lname\" type=\"text\" value=\'" +val[1]+"\'/>",
                                "<input  class=\"form-control\"  name=\"birth_day\" type=\"number\" min=\"1000\" max=\"9999\" value=\'"+val[2]+"\'/>",
                                "<input  class=\"form-control\" name=\"occupation\" type=\"text\" value=\'"+val[3]+"\'/>",
                                "<select class=\"form-control\" name=\"lives_with_patient\" class=\"form-control\"  type=\"text\" >"+val[4]+"</select>",
                                "<select class=\"form-control\" name=\"psy_materials\" class=\"form-control\"  type=\"text\" >   "+val[5]+"</select>",
                                "<select class=\"form-control\" name=\"under_treatment\" class=\"form-control\"  type=\"text\" >  "+val[6]+"</select>",
                                "<input  class=\"form-control\" name=\"comments\" type=\"text\" value=\'"+val[7]+"\'/>",


                    ] ).draw(false);
                    
                    
                   
                    
                });
                
                }
            
                 <!---------------------------------------------------------------->
                 $("#'.$id.'_table_wrapper").appendTo($("#'.$id.'").parent());
               
                    
                    $("#addRow_'.$id.'").on( "click", function () {
                        var t=$("#'.$id.'_table").DataTable();
                        t.row.add( ["'.$this->CreateSelectBoxFromList("moh_type_of_relative").'",
                                    "<input  class=\"form-control\" name=\"fname_lname\" type=\"text\" />",
                                    "<input  class=\"form-control\"  name=\"birth_day\" type=\"number\" min=\"1000\" max=\"9999\" />",
                                    "<input  class=\"form-control\" name=\"occupation\" type=\"text\" />",
                                    "<select class=\"form-control\" name=\"lives_with_patient\" class=\"form-control\"  type=\"text\" >  <option value=\"Yes\">'.$translate->z_xlt("Yes").'</option><option value=\"No\">'.$translate->z_xlt("No").'</option></select>",
                                    "<select class=\"form-control\" name=\"psy_materials\" class=\"form-control\"  type=\"text\" >  <option value=\"Yes\">'.$translate->z_xlt("Yes").'</option><option value=\"No\">'.$translate->z_xlt("No").'</option></select>",
                                    "<select class=\"form-control\" name=\"under_treatment\" class=\"form-control\"  type=\"text\" >  <option value=\"Yes\">'.$translate->z_xlt("Yes").'</option><option value=\"No\">'.$translate->z_xlt("No").'</option></select>",
                                    "<input  class=\"form-control\" name=\"comments\" type=\"text\" />"
    
                        ] ).draw(false);
                         addMaxNumbersToInputByName("birth_day","keypress");
                        addMaxLengthOfCharsToInputByName("birth_day","keypress",4);
                        
                          
                        // for new form
                        $("#'.$id.'_table input, #'.$id.'_table select").on("input change",function(event){
                                            event.preventDefault();
                                            refreshFamilyJson();
                        });
                    
                   
                    });
                    
                    $("#deleteRow_'.$id.'").on("click", function (event) {
                          var t=$("#'.$id.'_table").DataTable();
                              if(t.row(".selected").length>0)
                              {
                                t.row(".selected").remove().draw(false);
                                refreshFamilyJson();
                              }
                              else{
                               event.stopImmediatePropagation();
                              var lastRow=$("#'.$id.'_table tr:last");
                              t.row(lastRow).remove().draw(false);
                              
                              refreshFamilyJson();
                              }
                                
                              
                        } );
                
                
                        $("#'.$id.'_table_wrapper tbody").on( "click", "tr", function () {
                        if ( $(this).hasClass("selected") ) {
                            $(this).removeClass("selected");
                        }
                        else {
                            t.$("tr.selected").removeClass("selected");
                            $(this).addClass("selected");
                        }
                        } );

                    
                     // for edit form
                    $("#'.$id.'_table input, #'.$id.'_table select").on("input change",function(event){
                                            event.preventDefault();
                                            refreshFamilyJson();
                    });
                        
                     console.log($("#menu_buttons_family").parent());
                     var T=$("#menu_buttons_family").detach();
                    $("#'.$id.'_table_wrapper").parent().prepend(T);
               
                    $(".dataTables_filter input").addClass("form-control");
                    $("#menu_buttons_family").insertAfter(".dataTables_filter")
                    $(".dataTables_wrapper").css("margin-top","27px");
                    });
                    
                    function refreshFamilyJson()
                    {       
                          var arrayResults = $("#'.$id.'_table").find("select, input").serializeArray();
                          $("#'.$id.'").val(arrayResults.length === 0 ? "" : JSON.stringify(arrayResults));
                    }
                   
                    </script>';

        return  "<br/>".$table."<br/>".$script;
    }
}
