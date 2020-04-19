<?php


namespace Formhandler\View\Helper;
use Application\Listener\Listener;
use Formhandler\Model\customDB;
use Pouring\Controller\PouringController;
use Zend\View\Helper\AbstractHelper;


class MaxDosageHelper extends AbstractHelper
{
    const APPROVE_MOH_METHADONE = 120;
    const MAX_METHADONE = 300;
    const MAX_SOBOKSON = 24;
    const MAX_SOBOKSON_FILM = 24;
    const MAX_SUBUTEX = 32;

    // Types of exist pills (2mg and 8mg)
    const pillBig = 8;
    const pillSmall =2;

    public function __invoke()
    {

        $this->translate = new Listener();

        $jsOutput = "<script>
                            <!---------------max dosage code--------->
                            $(window).on('load',function(){
                                $.ready.then(function(){
                                
                                    $('#_dosage_form_3').prepend('<div class=\"form-group-TWB  col-md-6\"><label>"
                . $this->translate->z_xlt('From date') . "</label><input type=\"text\" id=\"format_from_date\" readonly=\"readonly\"  value=\"\" class=\"form-control\"></div>');
                                    $('#format_from_date').val(moment($('#from_date').val()).format('DD/MM/YYYY'));
                                    
                                    $('#treatment_type').val($('#drug option:selected').text());
                                    
                                    if($('#confirm_body>option').length == 0){
                                        $('#confirm_body').append('<option val=\'\'></option>')
                                    }
                                    
                                    $('#approval_date').after('<div class=\"text-danger\" id=\"scan_remember\" style=\"display: none\"> * "
                . $this->translate->z_xlt('Reminder: permit has to scan the patient\'s file')
                . "<b></b></div>')
                                    $('#daily_dosage').after('<div class=\"text-danger\" id=\"over_dogase\" style=\"display: none\"> * "
                . $this->translate->z_xlt('The dosage is over of quantity allowed')
                . "</div>')
                                    $('#part_1').after('<div class=\"text-danger\" id=\"not_divided\" style=\"display: none\"> * "
                . $this->translate->z_xlt('Dosage entered can not be divided through the existing pills')
                . "</div>')
    
                                    
                                    var dosageText =  $('label[for=part_1]').text();
                                    var splitDosageText = dosageText.split(' ');
                                    $('label[for=part_1]').html('<span>'+splitDosageText[0] +'</span> <span id=\'A-letter\'>'+ splitDosageText[1] +'<sapn>')
                                    $('#A-letter').hide();
                                    
                                    //$('#approval_date').parent().hide();
                                    //$(\"label[for=\'approval_date\'\").hide();
                                    // $('#confirm_body').parent().hide();
                                    //$(\"label[for=\'confirm_body\'\").hide();
                                    showhidelabels();
                                    $('#daily_dosage').on('change blur keyup',function(){
                                        var isAmountCurrect = showhidelabels();
                                        if(isAmountCurrect)checkIfIsDivisible();
                                    });
                                    $('#test_vital_signs').on('change',function(){
                                        if($(this).is(':checked')){
                                                showhidelabels();
                                                }else{
                                             $('#button_submit').attr('disabled','disabled');
                                             }
                                    });
                                    $('#drug').on('change',function(){
                                        var isAmountCurrect = showhidelabels();
                                        if(isAmountCurrect)checkIfIsDivisible();
                                        $('#treatment_type').val($('#drug option:selected').text());
                                    });
                                    $('#part_1, #part_2').on('keyup', checkIfIsDivisible);
                                        
                                    if($('[name=\"split\"]:checked').val() == 'Yes'){
                                                                   constraints.part_2 = {};
                                                                  constraints.part_2.numericality =  allConstraints.IntNotZero.numericality;
                                                                  constraints.part_2.presence =  allConstraints.required.presence;
    
                                        }  else {
                                      
                                                                   constraints.part_2 = {}; 
                                        }
                                        
                                      $('[name=\"split\"]').click(function(){
                                            
                                            if($('[name=\"split\"]:checked').val() == 'Yes'){
                                                    constraints.part_2 = {};
                                                    constraints.part_2.numericality =  allConstraints.IntNotZero.numericality;
                                                    constraints.part_2.presence =  allConstraints.required.presence;
                                                    $('#A-letter').show();
    
                                            }  else {
                                                  constraints.part_2 = {};
                                                   checkIfIsDivisible();
                                                   $('#daily_dosage').trigger('focus');
                                                   $('#A-letter').hide();
                                                 
                                            }
                                            
                                        });
                               
                                
                                });
                            });
                            
                            //this function check if given weight divided to the pills
                            var weightA = " . self::pillBig. ";
                            var weightB = " . self::pillSmall. ";
                            
                            function isDivisible(amount){
                                
                                var left = amount % weightA;
                                console.log(left);
                                if(isInt(left)){
                                    left = left % weightB; 
                                    if(left == 0)return true;
                                }
                                
                                left = amount % weightB; 
                                if(left == 0)return true;
                                 
                                return false; 
                            }
                            function isInt(n) {
                               return n % 1 === 0;
                            }
                            
                            function checkIfIsDivisible(){
                                if($('#drug').val() == '" .PouringController::DRUG_ID_METHADONE ."'){
                                    $('#not_divided').hide();
                                   if($('#test_vital_signs').is(':checked')){
                                            $('#button_submit').removeAttr('disabled');
                                         }
                                    return;
                                }
                                var partA = $('#part_1').val().length  > 0 ? $('#part_1').val() : $('#daily_dosage').val();
                                // 1 always true
                                
                                var partB = $('#part_2').val().length  > 0 ? $('#part_2').val() : 0;
                               
                                if(!isDivisible(partA) || !isDivisible(partB) ){
                                    $('#not_divided').show();
                                   $('#button_submit').attr('disabled','disabled');
                                } else {
                                    $('#not_divided').hide();
                                    if($('#test_vital_signs').is(':checked')){
                                            $('#button_submit').removeAttr('disabled');
                                         }
                                }
                            }
                                                        
                            function showhidelabels(){

                             var max_dosage, need_approve;
                             var drug_type = $('#drug').val();
                                    switch (drug_type){
                                        case '" .PouringController::DRUG_ID_METHADONE ."':
                                            max_dosage = " . self::MAX_METHADONE  . "
                                            need_approve = " . self::APPROVE_MOH_METHADONE  . "
                                            break;
                                       case '" .PouringController::DRUG_ID_SOBOXON ."':
                                            max_dosage = " . self::MAX_SOBOKSON  . "
                                            break;
                                       case '" .PouringController::DRUG_ID_SOBOXON_FILM ."':
                                            max_dosage = " . self::MAX_SOBOKSON_FILM  . "
                                            break;
                                       case '" .PouringController::DRUG_ID_SUBUTEX ."':
                                            max_dosage =  " . self::MAX_SUBUTEX  . "
                                            break;
                                    }

                                    if(drug_type == '" .PouringController::DRUG_ID_METHADONE ."' && ($('#daily_dosage').val() > need_approve && $('#daily_dosage').val() <= max_dosage)){
                                        $('#approval_date').parent().show();
                                        //$(\"label[for=\'approval_date\'\").show();
                                        $('#confirm_body').parent().show();
                                        $('#confirm_body').attr('disabled',false);
                                        //$(\"label[for=\'confirm_body\'\").show();
                                       
                                        
                                        $('#scan_remember').show();
                                        addConstraints('confirm_body', allConstraints['require']);
                                        addConstraints('approval_date', allConstraints['require']);
                                      

                                    }else {
                                        $('#approval_date').parent().hide();
                                         $('#approval_date').val('');
                                        //$(\"label[for=\'approval_date\'\").hide();
                                        $('#confirm_body').parent().hide();
                                      //  $('#confirm_body').val('');
                                         $('#confirm_body').attr('disabled',true);
                                       
                                       
                                       // $(\"label[for=\'confirm_body\'\").hide();
                                        $('#scan_remember').hide();
                                        
                                        removeConstraints('confirm_body', allConstraints['require']);
                                        removeConstraints('approval_date', allConstraints['require']);
                                    }
                                    
                                    if($('#daily_dosage').val() > max_dosage){
                                        $('#over_dogase').show();
                                        $('#button_submit').attr('disabled','disabled');
                                        console.log('false');
                                        return false;
                                    } else {
                                        $('#over_dogase').hide();
                                        if($('#test_vital_signs').is(':checked')){
                                            $('#button_submit').removeAttr('disabled');
                                            }else{
                                         $('#button_submit').attr('disabled','disabled');
                                         }
                                        
                                        return true;
                                 }      
   
                            }
                            
                             
                           
                            <!---------------end of max dosage code--------->
                            </script>";

        return $jsOutput;
    }



}
