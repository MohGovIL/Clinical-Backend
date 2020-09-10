<?php
/**
 * Created by DROR GOLAN.
 * User: drorgo
 */

namespace ReportTool\View\Helper;


use Zend\Form\View\Helper\AbstractHelper;

class DrawTable extends AbstractHelper
{
    CONST ID='id';
    CONST COLUMNS='columns';
    CONST TITLE='title';
    CONST ROUTE='route';
    protected $dbAdapter;

    protected  $translate=null;
    public function setDbAdapter($adapter){
        $this->dbAdapter=$adapter;
    }

    protected function CollectFilters($filters) {
    $filtersEvents = [];
    foreach( $filters as $filter ){

         if ($filter->getAttribute('type') !== 'radio'){
             array_push($filtersEvents,"\t".$filter->getName().":function() { return   $('#filters-container #".$filter->getName()."').val()} \r\n");
            }
        else{
            array_push($filtersEvents,"\t". $filter->getName().": function() {return  $('#filters-container [name=\"".$filter->getName()."\"]:checked').val()} \r\n");
        }


    }


      return implode(",",$filtersEvents);
    }


    protected function getCustomDB(){


        $CustomDb = new CustomDb( $this->dbAdapter);
        return $CustomDb;
    }

    public function __invoke($data){

        $columns = $data[self::COLUMNS];
        $title = $data[self::TITLE];
        $id = $data[self::ID];
        $route = $data[self::ROUTE];


        $columnsView=[];
        $columnDefsArr=[];

        foreach($columns as $key=>$value){
            array_push($columnsView,array("name"=>$value,"data"=>$value,"title"=> xl(ucfirst(str_replace("_"," ",$value)))));
            array_push($columnDefsArr,array("className"=>$value,"targets"=>[$key]));


            //array_push($columnsView,array("name"=>$value,"data"=>$value,"title"=> xl(ucfirst(str_replace("_"," ",$value))) . "<icon class= ' header_icon glyphicon glyphicon-info-sign ' title='hey hey' ></icon>"));
        }
        $columnDefsArr= xls(json_encode($columnDefsArr));
        $columsReturn = xls(json_encode($columnsView));

        $title = "<div id='title'><h3>".xlt($title)."</h3><span id='report_info'><span></div>";

        $table = '<table class="table table-striped   dataTable " id="' .$id .'_table">';
        $table.='</table>';
        $items= $this->CollectFilters($data['filters']);

        /*$items= from: function() { return $('#from_date').val() },
                 to: function() { return $('#until_date').val() },
                 facility: function() { return $('#facility').val() },
                 destinations: function() { return $("#filters-container [name=destinations]:checked").val()*/
        $script =  '<script>


       
        function getAccessMessageWithStatus(){
            var filters = $("#filters-container").find("input,select,textarea").not(".search-txt").not(":hidden");
            var title = "";
            $.each(filters,function(){
            
                var typ =$(this)[0].type;
                var space ="\u00A0";  //"\u00A0"
                
            if (typ==="select-one"  && !$(this).parent().hasClass("SumoSelect") ){
                typ="select";
            }
                switch(typ)
                {
                  
                   
                    case   "text":
                    case   "input":
                        
                            var titleInnerContainer =$(this).parent().find("label")[0];
                            var titleInner = "";
                            
                            if(titleInnerContainer!==undefined){
                                titleInner=titleInnerContainer.innerText;
                                var value = $(this).val();
                                title += titleInner+space+value+space+space+space;
                            }
                                
                    break;
                 
                    case   "select-multiple":
                        
                                 var value = $(this).find("option:selected");
                                 var allselected="";
                                 var titleInner="";
                                 
                                value.each(function(e){
                                titleInner = $(this).closest(".form-group-TWB").find("label")[0].innerText;
                                value = $($(this)[0]).text();
                                allselected += value+", ";
                                });
                                allselected= allselected.slice(0, -2);
                                title += titleInner+space+ allselected+space+space+space; 
                           break;
                                
                    case   "select-one":
                                var value = $(this).find("option:selected").each(function(e){
                                var titleInner = $(this).closest(".form-group-TWB").find("label")[0].innerText;
                                value = $($(this)[0]).text()
                                title += titleInner+space+ value+space+space+space; 
                            });
                            
                    break;
                    case   "select":
                            var value = $(this).find(":selected").text();
                            
                            var titleInnerContainer =$(this).parent().find("label")[0];
                            var titleInner = "";
                           
                            if(titleInnerContainer!==undefined){
                                titleInner=titleInnerContainer.innerText;
                            }

                            title += titleInner+space+ value+space+space+space; 
                           
                            
                    break;
                    
                    
                    case   "radio":
                    case   "checkbox":
                        
                                var value ="";
                                if($(this)[0].checked){
                                 var titleInner = $(this).closest(".form-group-TWB").find("label")[0].innerText;
                                value = $(this).parent().text();
                                title += titleInner+space+ value+space+space+space;
                                }
                           
                    break;
                }
                
                    
                    
                
                
            });
            
            var titleDiv= $("span[id=\'report_info\']");
            titleDiv.text(title);
            titleDiv.css( "font-weight", "bold");
            titleDiv.css("font-size","16px");
            
            
            
        }    
        
        pageLength=10;
        let tableInstance=null;
        
        $(document).ready(function() {
               
        columnsFields=JSON.parse(\''.$columsReturn.'\');
        columnDefs=JSON.parse(\''.$columnDefsArr.'\');
        
        tableID = "'.$id.'_table";
        tableElm= $("#' .$id .'_table");
       
        tableElm.on("xhr.dt", function ( e, settings, json, xhr ) {
            let table =  settings.oInstance.api();
            table.columns().visible(1);
            if(json.additionalParams && json.additionalParams.hide){
                table.columns(json.additionalParams.hide).visible(0);
            }
            //console.log(json.additionalParams);
            //console.log(table);
            
        });
        tableInstance=tableElm.DataTable( {
            "pageLength": pageLength,
            "processing": true,
            "serverSide": true,
            "pagingType": "numbers",
            "fnDrawCallback": function( oSettings ) {
                adjustWidth(oSettings);
                getAccessMessageWithStatus();
                MergeGridCells();
            },
            "ajax": {
                url :"' . $route . '/getDataAjax" ,
                type:"GET",
                data: {
                    filters:{'.$items.'},
                },
                error:function(err){
                  console.log(err);
                } 
               
            },
            "columns" :JSON.parse(\''. $columsReturn.'\'),
            "columnDefs": columnDefs,
            lengthChange: false,
            searching: false,
            paging: true,
            "orderMulti": false,
            "ordering": false,
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
            }

        } ).draw(false);
        
        $(".header_icon").tooltipster();
    } );
            
            
function MergeGridCells() {
    var dimension_cells = new Array();
    var dimension_col = null;
    var columnCount = $("#'.$id.'_table tr:first th").length;
    for (dimension_col = 0; dimension_col < 2; dimension_col++) {
        // first_instance holds the first instance of identical td
        var first_instance = null;
        var rowspan = 1;
        // iterate through rows
        $("#'.$id.'_table").find(\'tr\').each(function () {

            // find the td of the correct column (determined by the dimension_col set above)
            var dimension_td = $(this).find(\'td:nth-child(\' + dimension_col + \')\');

            if (first_instance == null) {
                // must be the first row
                first_instance = dimension_td;
            } else if (dimension_td.text() == first_instance.text()) {
                // the current td is identical to the previous
                // remove the current td
                dimension_td.remove();
                ++rowspan;
                // increment the rowspan attribute of the first instance
                first_instance.attr(\'rowspan\', rowspan);
            } else {
                // this cell is different from the last
                first_instance = dimension_td;
                rowspan = 1;
            }
        });
    }
}
    
function adjustWidth(settings) {
    let dt = settings.oInstance.api();
    dt.columns.adjust();
}

   </script>';

        return  "<br/>".$title."<br/>".$table."<br/>".$script;
    }
}
