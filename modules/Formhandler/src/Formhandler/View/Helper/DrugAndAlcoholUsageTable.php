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

class DrugAndAlcoholUsageTable  extends AbstractHelper
{

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

    public function CreateSelectBoxFromList($list,$selected=null)
    {
        $translate = new Listener();

        $customdb =  $this->getCustomDB();
        $listReturned=$customdb->getListParams($list);
        if(is_null($listReturned))
            return "<p class='text-danger'> missing list : $list</p>";


        $selectBox="<select   class='form-control' name='$list'>";
        foreach ($listReturned as $value)
        {
            if($selected==$value['title']){
                $selectBox .= "<option   selected value='" . $value['option_id'] . "'>". $translate->z_xlt($value['title']) . "</option>";
            }
            else {
                $selectBox .= "<option   value='" . $value['option_id'] . "'>". $translate->z_xlt($value['title']) . "</option>";
            }
        }
        $selectBox.="</select>";
        return $selectBox;
    }
    public function __invoke($id)
    {




        $translate = new Listener();

        $buttons='<div  id="menu_buttons_drug" class="form-group" role="group">
        <div  id="button_description" class="button_description"> <label>'. $translate->z_xlt("Press here to add or remove records") . '</label></div>
        <input type="button" id="addRow_'.$id.'"  class="btn" value="+"\>
            
        <input type="button" id="deleteRow_'.$id.'"  class="btn" value="-"\>
            
        </div>
        ';




        $table = '<table class="table table-striped   dataTable " id="' .$id .'_table">';
        $table.='<thead>';

        $table.='<th>' . $translate->z_xlt('type') .'</th>';
        $table.='<th>' .$translate->z_xlt('started at age') .'</th>';
        $table.='<th>' . $translate->z_xlt('usage ') .'</th>';
        $table.='<th>' . $translate->z_xlt('frequency') .'</th>';
        $table.='<th>' . $translate->z_xlt('frequency for a day') .'</th>';
        $table.='<th>' . $translate->z_xlt('last used at') .'</th>';

        $table.='</thead>';
     //   $table.='<tbody><tr><td><input name="hello" id="hello"/></td><td><input name="hello3" id="hello3"/></td><td><input name="bye" id="bye"/></td><td><input name="hello34" id="hello34" /></td><td><input name="hello32" id="hello32"/></td><td><input name="hello22" id="hello22"/></td></tr>';
        $table.='</tbody>';

        /*$table.='<tfoot>';
        $table.='<th>' . $translate->z_xlt('type') .'</th>';
        $table.='<th>' .$translate->z_xlt('started at age') .'</th>';
        $table.='<th>' . $translate->z_xlt('usage ') .'</th>';
        $table.='<th>' . $translate->z_xlt('frequency') .'</th>';
        $table.='<th>' . $translate->z_xlt('frequency for a day') .'</th>';
        $table.='<th>' . $translate->z_xlt('last used at') .'</th>';
        $table.='</tfoot>';*/

        $table.=$buttons;
        $table.='</table>';




        $script =  '<script>


$(document).ready(function() {
    function selectFromList(obj,value)
    {
        
           obj=obj.replace("value=\'"+value+"\'", "selected value=\'"+value+"\'");
            
           return obj;

    }
     $("#drug_and_alcohol_usage").hide();
    
    
                 var t=$("#'.$id.'_table").DataTable({ 
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
                        }
                    },
                 "bLengthChange": false,  "searching": false ,columns:[ 
                 {"width" : "25%"},
                 {"width" :"10%" },
                 {"width" : "15%"},
                 {"width" : "10%"},
                 {"width" : "10%"},
                 {"width" : "35%"} ]});
                 if($("#drug_and_alcohol_usage").val()!="")
                 {
                datajson=$.parseJSON($("#drug_and_alcohol_usage").val());
                
                var dataForTable=[];
                var counter=0;
                var pushThis=[];
                $.each(datajson,function(key,value){
                    if(counter<=5){
                         pushThis.push(value["value"]);
                         counter++;
                    }
                    else{
                        counter=1;
                        dataForTable.push(pushThis);
                        pushThis=[];
                        pushThis.push(value["value"]);
                    }    
                
                });
                dataForTable.push(pushThis);
                
                $.each(dataForTable,function(key,val){
                
                var moh_materials_type="'.$this->CreateSelectBoxFromList("moh_materials_type").'";
                var moh_way_of_use="'.$this->CreateSelectBoxFromList("moh_way_of_use").'";
                
                  t.row.add( [  selectFromList(moh_materials_type,val[0]),
                                "<input  class=\'form-control\' name=\'started_at_age\' value=\'"+val[1]+"\'/>",
                                selectFromList( moh_way_of_use,val[2]),
                                "<input  class=\'form-control\' name=\'usage_type\' value=\'"+val[3]+"\'/>",
                                "<input  class=\'form-control\' name=\'frequency_for_a_day\' value=\'"+val[4]+"\'/>",
                                "<input  class=\'form-control\' name=\'last_used_at\' value=\'"+val[5]+"\'/>",
                                
                                

                    ] ).draw(false);
                    
                    
                   
                    
                });
                
                }
                 
                 
                 
                 $("#'.$id.'_table_wrapper").appendTo($("#'.$id.'").parent());
               
                    
                    $("#addRow_'.$id.'").on( "click", function (event) {
                    event.stopPropagation();
                    var t=$("#'.$id.'_table").DataTable();
                    t.row.add( ["'.$this->CreateSelectBoxFromList("moh_materials_type").'",
                                "<input  class=\"form-control\" name=\"started_at_age\" />",
                                "'.$this->CreateSelectBoxFromList("moh_way_of_use").'",
                                "<input  class=\"form-control\" name=\"usage_type\" />",
                                "<input  class=\"form-control\" name=\"frequency_for_a_day\" />",
                                "<input  class=\"form-control\" name=\"last_used_at\" />"

                    ] ).draw(false);
                    
  
                     // for new form
                     $("#'.$id.'_table input, #'.$id.'_table select").on("change input",function(event){
                                event.preventDefault();
                                refreshAlcoholJson();
                   });
                    
                    
  
                });
                    
                    
                $("#deleteRow_'.$id.'").on("click", function (event) {
                      var t=$("#'.$id.'_table").DataTable();
                          if(t.row(".selected").length>0)
                          {
                            t.row(".selected").remove().draw(false);
                            refreshAlcoholJson();
                          }
                          else{
                           event.stopImmediatePropagation();
                          var lastRow=$("#'.$id.'_table tr:last");
                          t.row(lastRow).remove().draw(false);
                          
                          refreshAlcoholJson();
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
                    $("#'.$id.'_table input, #'.$id.'_table select").on("change input",function(event){
                                            event.preventDefault();
                                            refreshAlcoholJson();
                   });
                   
                   
                     console.log($("#menu_buttons_drug").parent());
                     var T=$("#menu_buttons_drug").detach();
                    $("#'.$id.'_table_wrapper").parent().prepend(T);
               
                    $(".dataTables_filter input").addClass("form-control");
                    $("#menu_buttons_drug").insertAfter(".dataTables_filter")
                    $(".dataTables_wrapper").css("margin-top","27px");
                    });

                    function refreshAlcoholJson()
                    {   
                          var arrayResults = $("#'.$id.'_table").find("select, input").serializeArray();
                          $("#'.$id.'").val(arrayResults.length === 0 ? "" : JSON.stringify(arrayResults));
                    }
                    </script>';

        return  "<br/>".$table."<br/>".$script;
    }
}
