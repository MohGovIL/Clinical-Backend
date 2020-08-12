

function beforeSubmit(formId) {
    //console.log("formId:"+formId);
    if (formId==="medical_anamnesis"){
        confirmForm(formId);
        return 1;
    }
    $('#' + formId).submit();
}

function dasAssignHideShowPsychopathy() {

    var psychopathy_hmtl='<span class="glyphicon glyphicon-menu-down" id="psychopathy_glyphicon"></span>';

    $('label[for="psychopathy"]').on('click change',dasHideShowPsychopathy);

    $('label[for="psychopathy"]').append(psychopathy_hmtl)

}

function dasChangeFocus() {

    var flag=1;

    var radio_historic_list=[
        'history_of_violence',
        'young_age_fvi',
        'relationship_instability',
        'employment_problems',
        'substance_use_problems',
        'major_mental_illness',
        'early_adaptation_problems',
        'personality_disorders',
        'prior_supervision_failure'];

    var radio_Psychopathy_list=[
        'superficial',
        'grandiose',
        'conning',
        'lack_of_remorse',
        'lack_empathy',
        'not_taking_responsibility',
        'impulsive',
        'poor_behavioural_controls',
        'lack_of_goals',
        'irresponsibility',
        'adolescence_antisocial_behavior',
        'adulthood_antisocial_behavior'];

    radio_historic_list.forEach(function (item, index){
        var temp_value=$('input[name='+item+']:checked').val();
        if  (temp_value==undefined) {
            flag=false;
        }
    });


    radio_Psychopathy_list.forEach(function (item, index) {
        var temp_value = $('input[name=' + item + ']:checked').val();
        if (temp_value == undefined) {
            flag = false;
        }
    });


    if (flag) {
        $('#clinical_group').trigger("click");
    }


}

function dasHideShowPsychopathy() {

    var radio_Psychopathy_list=[
        'superficial',
        'grandiose',
        'conning',
        'lack_of_remorse',
        'lack_empathy',
        'not_taking_responsibility',
        'impulsive',
        'poor_behavioural_controls',
        'lack_of_goals',
        'irresponsibility',
        'adolescence_antisocial_behavior',
        'adulthood_antisocial_behavior'];

    var flag=$('#psychopathy').val();


    if(flag=="1"){
        radio_Psychopathy_list.forEach(function (item, index) {
            $('#'+item).parent().parent().parent().hide();
        });

        $('#psychopathy_glyphicon').removeClass('glyphicon-menu-down');
        $('#psychopathy_glyphicon').addClass('glyphicon-menu-up');
        $('#psychopathy').val(0);
    }
    else {
        radio_Psychopathy_list.forEach(function (item, index) {
            $('#'+item).parent().parent().parent().show();
        });

        $('#psychopathy_glyphicon').removeClass('glyphicon-menu-up');
        $('#psychopathy_glyphicon').addClass('glyphicon-menu-down');
        $('#psychopathy').val(1);
    }



}

function dasSummaryScore() {

    var summary_score_sum=0;
    var completed=1;

    $('input[type=radio]').each( function () {

        var btn_name=this.name;
        var temp_value=$('input[name='+btn_name+']:checked').val();

        if  (temp_value==undefined) {
            completed=0;
        }

    });

    if (completed) {

        var total_scoring_of_historical= $('#total_scoring_of_historical_variables').val();
        var total_scoring_of_clinic=     $('#total_scoring_of_clinic_variables').val();
        var risk_management_total=       $('#risk_management_total_score').val();

        total_scoring_of_historical=total_scoring_of_historical.substr(0, 2);
        total_scoring_of_clinic=total_scoring_of_clinic.substr(0, 2);
        risk_management_total=risk_management_total.substr(0, 2);


        summary_score_sum= parseInt(total_scoring_of_historical) +  parseInt(total_scoring_of_clinic) +  parseInt(risk_management_total) ;
        $('#summary_score').val(summary_score_sum+'/40');
    }
}

function dasSummaryRiskAssessment() {

    var score =$('#summary_score').val();
    var int_score=parseInt(score);
    var cat_1_upper=21;
    var cat_2_upper=31;
    var categories = [
        'סיכון נמוך',
        'סיכון בינוני' ,
        'סיכון גבוה'
    ];
    var messages =[
        'יש לחזור על הבדיקה בעוד שנה',
        'יש להזין את תכנית ההתערבות שנקבעה בעקבות ההערכה',
        'הטופס לא הושלם במלואו, לכן יישמר במערכת ללא ציון סופי'
    ];

    //$('label[for="summary_risk_label"]').addClass( "alert-warning" );
    $('label[for="summary_risk_label"]').css("color","#8a6d3b");

    //console.log(score);

    if (score!="" &&  score!=undefined && int_score!=NaN  && int_score!=undefined){

        if (int_score<cat_1_upper) {
            $('#summary_risk_assessment').val(categories[0]);
            $('label[for="summary_risk_label"]').text(messages[0]);
        }
        else if (int_score<cat_2_upper) {
            $('#summary_risk_assessment').val(categories[1]);
            $('label[for="summary_risk_label"]').text(messages[1]);
        }
        else {
            $('#summary_risk_assessment').val(categories[2]);
            $('label[for="summary_risk_label"]').text(messages[1]);
        }

    }
    else{
        $('label[for="summary_risk_label"]').text(messages[2]);
        //$('label[for="summary_risk_label"]').css("color","red");

    }

}


function dasRadioChangeHandler(event) {

    var selected_id=this.name;

    var radio_historic_list=[
        'history_of_violence',
        'young_age_fvi',
        'relationship_instability',
        'employment_problems',
        'substance_use_problems',
        'major_mental_illness',
        'early_adaptation_problems',
        'personality_disorders',
        'prior_supervision_failure'];

    var radio_Psychopathy_list=[
        'superficial',
        'grandiose',
        'conning',
        'lack_of_remorse',
        'lack_empathy',
        'not_taking_responsibility',
        'impulsive',
        'poor_behavioural_controls',
        'lack_of_goals',
        'irresponsibility',
        'adolescence_antisocial_behavior',
        'adulthood_antisocial_behavior'];

    var radio_clinical_list=[
        'lack_of_insight',
        'negative_attitudes',
        'active_symptoms_mmi',
        'impulsivity',
        'unresponsive_to_treatment'];

    var radio_risk_list=[
        'plans_lack_feasibility',
        'exposure_to_destabilizers',
        'lack_of_personal_support',
        'noncompliance_with_ra',
        'stress'];



    if ( ($.inArray(selected_id, radio_historic_list) !== -1) ||  ($.inArray(selected_id, radio_Psychopathy_list) !== -1)  ){
        var radio_historic_sum=0;
        var radio_Psychopathy_sum=0;
        var radio_Psychopathy_score=0;
        var total_score=0;

        radio_historic_list.forEach(function (item, index){
            var temp_value=$('input[name='+item+']:checked').val();
            if  (temp_value!=undefined) {

                switch(temp_value) {
                    case "zero":
                        temp_value=0;
                        break;
                    case "one":
                        temp_value=1;
                        break;
                    case "two":
                        temp_value=2;
                        break;
                    default:
                        temp_value=0;
                }

                radio_historic_sum+=parseInt(temp_value);
            }
        });

        radio_Psychopathy_list.forEach(function (item, index){
            var temp_value=$('input[name='+item+']:checked').val();
            if  (temp_value!=undefined) {

                switch(temp_value) {
                    case "zero":
                        temp_value=0;
                        break;
                    case "one":
                        temp_value=1;
                        break;
                    case "two":
                        temp_value=2;
                        break;
                    default:
                        temp_value=0;
                }

                radio_Psychopathy_sum+=parseInt(temp_value);
            }
        });

        if (radio_Psychopathy_sum<12){
            radio_Psychopathy_score=0;
        }
        else if (radio_Psychopathy_sum <18) {
            radio_Psychopathy_score=1;
        }
        else{
            radio_Psychopathy_score=2;
        }

        total_score=radio_historic_sum+radio_Psychopathy_score;
        $('#total_scoring_of_historical_variables').val(total_score+'/20');
    }

    else if ( ($.inArray(selected_id, radio_clinical_list) !== -1) ) {

        var radio_clinical_sum=0;

        radio_clinical_list.forEach(function (item, index){
            var temp_value=$('input[name='+item+']:checked').val();
            if  (temp_value!=undefined) {

                switch(temp_value) {
                    case "zero":
                        temp_value=0;
                        break;
                    case "one":
                        temp_value=1;
                        break;
                    case "two":
                        temp_value=2;
                        break;
                    default:
                        temp_value=0;
                }

                radio_clinical_sum+=parseInt(temp_value);
            }
        });

        $('#total_scoring_of_clinic_variables').val(radio_clinical_sum+'/10');

    }

    else if ( ($.inArray(selected_id, radio_risk_list) !== -1) ) {

        var radio_risk_sum=0;

        radio_risk_list.forEach(function (item, index){
            var temp_value=$('input[name='+item+']:checked').val();
            if  (temp_value!=undefined) {

                switch(temp_value) {
                    case "zero":
                        temp_value=0;
                        break;
                    case "one":
                        temp_value=1;
                        break;
                    case "two":
                        temp_value=2;
                        break;
                    default:
                        temp_value=0;
                }

                radio_risk_sum+=parseInt(temp_value);
            }
        });

        $('#risk_management_total_score').val(radio_risk_sum+'/10');
    }

    else {}


    dasSummaryScore();
    dasSummaryRiskAssessment();

}

function dasRadioChangeEventCreator() {
    var radios = $('input[type=radio]');

    Array.prototype.forEach.call(radios, function(radio) {
        radio.addEventListener('change', dasRadioChangeHandler);
    });
}

function addNonZeroEventToElements(arr){
    $.each(arr,function(e,k){
        $("[name='moh_"+k+"']").prop("min",0);

        var elem = $("[name='moh_"+k+"']");


        if(elem.val().includes("-"))
        {
            elem.val("0");
        }


        if(Number(elem.val())<0 || Number(elem.val())=="")
        {
            elem.val("0");
        }


        $("[name='moh_"+k+"']").on("change",function(){
            if(elem.val().includes("-"))
            {
                elem.val("0");
            }


            if(Number(elem.val())<0 || Number(elem.val())=="")
            {
                elem.val("0");
            }


        })
    });
}

function addMaxNumbersToInput(id,method){

    $('#'+id).on(method,function(event) {
        if ( event.keyCode == 46 || event.keyCode == 8 ) { }
        else { if (event.keyCode < 48 || event.keyCode > 57 ) {
            event.preventDefault();
        }
        }
    });

}
function addOnlyIntegers(id,method){

    $('#'+id).on(method,function(event) {
        if (  event.keyCode == 8 ) { }
        else { if (event.keyCode < 48 || event.keyCode > 57 ) {
            event.preventDefault();
        }
        }
    });

}

function addMaxLengthOfCharsToInput(id,method,maxLength){

    $('#'+id).on(method,function(event) {
        if ( event.keyCode == 46 || event.keyCode == 8 ) { return }
        if ($(this).val().length >= maxLength) {
            event.preventDefault();
        }
    });
}

function removeErrorIfDefault(target_id,select_id){

    $('#'+select_id).on("change",function(event) {

        var val =$("#"+select_id).val();

        if (val=='' || val=='not_dangerous'){
            $('#'+target_id).parent().hide();
        }
        else{
            $('#'+target_id).parent().show();
        }


    });
}

function foldDas(){

    $('label[for="psychopathy"]').trigger("click");
}

function hideInterventionPlan(){

    if (!($('#previous_intervention_plan').val().trim().length > 0)){
        $('#previous_intervention_plan').parent().hide();
    }
}




var QueryString = function () {
    // This function is anonymous, is executed immediately and
    // the return value is assigned to QueryString!
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        // If first entry with this name
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
            // If second entry with this name
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
            query_string[pair[0]] = arr;
            // If third or later entry with this name
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    }
    return query_string;
}();

function addMaxLengthOfCharsToInput2(name,method,maxLength){

    $("[name='"+name+"']").on(method,function(event) {
        if ( event.keyCode == 46 || event.keyCode == 8 ) { return }
        if ($(this).val().length >= maxLength) {
            event.preventDefault();
        }
    });
}

function addMaxLengthOfCharsToInput3(name,method,maxLength,maxValue){

    $("[name='"+name+"']").on(method,function(event) {

        if ( event.keyCode == 46 || event.keyCode == 8 ) { return }
        if ($(this).val().length > maxLength || event.target.value > maxValue ) {
            $(this).val("");
            event.preventDefault();
        }
    });
}

function addMaxNumbersToInputByName(e_name,method){

    $('[name="'+e_name+'"]').on(method,function(event) {
        if ( event.keyCode == 46 || event.keyCode == 8 ) { }
        else { if (event.keyCode < 48 || event.keyCode > 57 ) {
            event.preventDefault();
        }
        }
    });

}

function addMaxLengthOfCharsToInputByName(e_name,method,maxLength){

    $('[name="'+e_name+'"]').on(method,function(event) {
        if ( event.keyCode == 46 || event.keyCode == 8 ) { return }
        if ($(this).val().length >= maxLength) {
            event.preventDefault();
        }
    });
}


function setEmptyRadioBtnDefault(id,defaultVal){
    if (typeof $('#'+id+':checked').val() === "undefined"){
        if (defaultVal=== "No") {
            $("[name="+id+"]")[1].trigger("click");
        }
        else {
            $("[name="+id+"]").trigger("click");
        }
    }
}


function setDataFromOneControllerToAnotherController(idA,idB,e){
    $('#'+idA).on(e,function(){
        $('#'+idB).val($('#'+idA+' option:selected' ).val());
    });
}
function button_cancel() {


    // $("#button_cancel").on('click',function (e) {
    //     if(somethingChanged)
    //     {
    //         ConfirmDialog("שים לב ביצעת שינויים בטופס - האם ברצונך לשמור את השינויים שביצעת");
    //
    //     }
    //     else{
    //         top.restoreSession();
    //
    //         var closeMe=$(window.parent.parent.document).find(".ui-state-active").find(".ui-icon-close");
    //
    //         $(window.parent.parent.document).find("#ui-id-1")[0].trigger("click");
    //         closeMe.trigger("click");
    //
    //         //window.parent.location = address
    //     }
    //
    //
    // });
}


function radioDefaultNo(id){
    var no_flag=$('#'+id).is(':checked');

    if (!no_flag){
        $("[name='"+id+"'][value='No']").prop( "checked", true );
    }
}


function ConfirmDialog(message){
    $('<div></div>').appendTo('body')
        .html('<div><h6>'+message+'?</h6></div>')
        .dialog({
            modal: true, title: 'ביצעת שינויים בטופס', zIndex: 99999, autoOpen: true,
            width: 'auto', resizable: false,
            buttons: {
                כן: function () {
                    $(this).remove();
                    $("#button_submit").trigger("click");
                },
                לא: function () {

                    top.restoreSession();

                    var closeMe=$(window.parent.parent.document).find(".ui-state-active").find(".ui-icon-close");

                    $(window.parent.parent.document).find("#ui-id-1").trigger("click");
                    closeMe.trigger("click");


                    //window.parent.location = address
                }
            },
            close: function (event, ui) {
                $(this).remove();
            }
        });
};


function removeParentClass(id,className){
    $('#'+id).parent().removeClass(className);
}

function addParentClass(id,className){
    $('#'+id).parent().addClass(className);
}

function triggerChangeClick(){
    $('select').children('option').each(function() {
        if ($(this).is(':selected'))
        { $(this).trigger('change');  }
    });

    $('input[type="checkbox"]').each(function() {
        if ($(this).is(':checked') ||  $(this).attr("checked")=='checked')
        { $(this).prop('checked',true).trigger('change');  }
        else{
            $(this).trigger('change');
        }
    });

    $('input[type="radio"]').each(function() {
        if ($(this).is(':checked') ||  $(this).attr("checked")=='checked')
        {
            $(this).prop('checked',true).trigger('change');  }
        else{
            $(this).trigger('change');
        }
    });
}

function setDefaultToControl(id,value,type)
{
    switch (type)
    {
        case "input":
            if($("#"+id).val()==''){
                $("#"+id).val(value).trigger('change').trigger("click");
            }
            break;
    }

}
function changeMomentValidation(idAChanges,idBTobeValidateWithValueA,numberOf,rule,periodOfTime){




    $('#'+idAChanges).on('blur change focus',function() {



        var translate= "תאריך לא תקין";
        var objectConstraint=constraints[idBTobeValidateWithValueA];
        objectConstraint.datetime={
            dateOnly: true,
            earliest: moment.utc().add(numberOf, periodOfTime),
            message: translate

        };
        /* objectConstraint.datetime['dateOnly']=true;
         objectConstraint.datetime[rule]=  moment.utc().add(addNumberOfDays, 'months'),
         objectConstraint.datetime['message']= "  ";*/

    });
    $('#'+idAChanges).trigger('blur');
}

function somethingWasChanged(){


    $('form').change(function() {
        somethingChanged = true;
    });
}

$('document').ready(function() {

    triggerChangeClick();
    button_cancel();

    $(".autocomplete_off_calendar").each(function(){
        var element=$(this);
        element.attr('autocomplete', 'off');
        element.on("keydown paste",function(e) {
            var key=e['key'];
            if ( !(key in {'Delete':1,'Backspace':2})){
                e.preventDefault();
                //
            }

        });
    });

    if($("#vaccine_plan_info") != undefined  && $("#vaccine_plan_info").length > 0 )
        vaccinePlanInfo = $("#vaccine_plan_info").parent().find("label")[0].innerText;

    $('input,select,textarea').on("change click",function(k){
        try {
            stopDoingAjaxErrorFound = false;
            errorFound=false;
            $("#button_submit").prop("disabled", false);
            //Todo If this code is still needed se CV-1139
            /*      var id = $(this).parents('table')[0].id;
                if ($(this).parents('table').length && id.indexOf("country") >= 0) {
                    // if parent is not table
                    if (!dontShowErrors) {
                        dontDoAjax = true;
                    }
                } else {
                    dontShowErrors = false;
                    dontDoAjax = false;

                    // parent is table
                }
            */


        }
        catch(err) {

            //console.log(err.message);
        }
    });

});


function addElementOptionToList(id,value){
    $("#"+id).append("<option id='' name='"+value+"' value='"+value+"'>"+value+"</option>")
}

<!------------------------MATH FROM OPENEMR----------------------------->

function convLbtoKg(name) {
    var lb = $("#"+name).val();
    var hash_loc=lb.indexOf("#");
    if(hash_loc>=0)
    {
        var pounds=lb.substr(0,hash_loc);
        var ounces=lb.substr(hash_loc+1);
        var num=parseInt(pounds)+parseInt(ounces)/16;
        lb=num;
        $("#"+name).val(lb);
    }
    if (lb == "0") {
        $("#"+name+"").val("0");
    }
    else if (lb == parseFloat(lb)) {
        kg = lb*0.45359237;
        kg = kg.toFixed(2);
        $("#"+name+"").val(kg);
    }
    else {
        $("#"+name+"").val("");
    }

    if (name == "weight_input") {
        calculateBMI();
    }
}

function convKgtoLb(name) {
    var kg = $("#"+name+"").val();

    if (kg == "0") {
        $("#"+name).val("0");
    }
    else if (kg == parseFloat(kg)) {
        lb = kg/0.45359237;
        lb = lb.toFixed(2);
        $("#"+name).val(lb);
    }
    else {
        $("#"+name).val("");
    }

    if (name == "weight_input") {
        calculateBMI();
    }
}

function convIntoCm(name) {
    var inch = $("#"+name).val();

    if (inch == "0") {
        $("#"+name+"").val("0");
    }
    else if (inch == parseFloat(inch)) {
        cm = inch*2.54;
        cm = cm.toFixed(2);
        $("#"+name+"").val(cm);
    }
    else {
        $("#"+name+"").val("");
    }

    if (name == "height_input") {
        calculateBMI();
    }
}

function convCmtoIn(name) {
    var cm = $("#"+name+"").val();

    if (cm == "0") {
        $("#"+name).val("0");
    }
    else if (cm == parseFloat(cm)) {
        inch = cm/2.54;
        inch = inch.toFixed(2);
        $("#"+name).val(inch);
    }
    else {
        $("#"+name).val("");
    }

    if (name == "height_input") {
        calculateBMI();
    }
}

function convFtoC(name) {
    var Fdeg = $("#"+name).val();
    if (Fdeg == "0") {
        $("#"+name+"").val("0");
    }
    else if (Fdeg == parseFloat(Fdeg)) {
        Cdeg = (Fdeg-32)*0.5556;
        Cdeg = Cdeg.toFixed(2);
        $("#"+name+"").val(Cdeg);
    }
    else {
        $("#"+name+"").val("");
    }
}

function convCtoF(name) {
    var Cdeg = $("#"+name+"").val();
    if (Cdeg == "0") {
        $("#"+name).val("0");
    }
    else if (Cdeg == parseFloat(Cdeg)) {
        Fdeg = (Cdeg/0.5556)+32;
        Fdeg = Fdeg.toFixed(2);
        $("#"+name).val(Fdeg);
    }
    else {
        $("#"+name).val("");
    }
}

function calculateBMI() {
    var bmi = 0;
    var height = $("#height_input").val();
    var weight = $("#weight_input").val();
    if(height == 0 || weight == 0) {
        $("#BMI").val("");
    }
    else if((height == parseFloat(height)) && (weight == parseFloat(weight))) {
        bmi = weight/height/height*703;
        bmi = bmi.toFixed(1);
        $("#BMI_input").val(bmi);
    }
    else {
        $("#BMI_input").val("");
    }
}

<!------------------------------END OF MATH FROM OPENEMR-------------------------->


function tableToJson(){
    // Loop through grabbing everything
    var myRows = [];
    var $headers = $("th");
    var $rows = $("tbody tr").each(function(index) {
        $cells = $(this).find("td");
        myRows[index] = {};
        $cells.each(function(cellIndex) {
            myRows[index][$($headers[cellIndex]).html()] = $(this).html();
        });
    });
// Let's put this in the object like you want and convert to JSON (Note: jQuery will also do this for you on the Ajax request)
    var myObj = {};
    myObj.myrows = myRows;

}

function clone(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
    }
    return copy;
}


function confirmForm (formId) {
    // must have the below code in the couchdb to work
    // $(document).ready(function() {$('#medical_anamnesis :input').change(function() {$('#medical_anamnesis').addClass('changed');});});

    $('#medical_anamnesis :input').off("change");
    if (!($("#medical_anamnesis").hasClass("changed"))) {
        alert("לתשומת לבך, לא הוזנו נתונים בטופס.");
    }

    if (!$('#error_turn_off_alerts').length && $('#turn_off_alerts').is(':checked')){
        convKgtoLb('weight');
        convCmtoIn('height');

    }
    if (typeof formId !== 'undefined') {
        $('#' + formId).submit();
    }

}
function activateGuidence(){

    if(typeof  $("#countries_table_generic_table")[0]  != "undefined" ) {
        dontDoAjax = true;
        var t = $("#countries_table_generic_table").DataTable();
        if (  t.rows()[0].length == 0) {
            // $("#guidance_and_consulting_group").parent().hide()
            //$("button_continue").prop("disabled", true);
            dontDoAjax = true;
            $("#error_no_data_found_country").remove();
            if (!dontShowErrors) {
                $("#countries_table_generic_table").parent().append("<span id='error_no_data_found_country' class='error-message'>חייב להוסיף ערך בטבלה  </span>");
            }
            setTimeout(function () {
                $("#questionnaire_group").trigger("click");
            }, 1000);
            $("#button_submit").prop("disabled", true);
        } else {
            //  $("#guidance_and_consulting_group").parent().show()
            //$("button_continue").prop("disabled", false);
            dontDoAjax = false;
            $("#error_no_data_found_country").remove();
            $("#button_submit").prop("disabled", false);
        }
    }
}
function confirmFormActivator(){
    $('#medical_anamnesis :input').change(function() {$('#medical_anamnesis').addClass('changed');});
}
function validateForm(e,formId){

    dontDoAjax = false;
    if(typeof stopDoingAjaxErrorFound == "undefined")
        stopDoingAjaxErrorFound  = false;


    var form = document.getElementById(formId) ;
    if(!form)
    {
        alert("Something went wrong");
    }
    var elements = validate.collectFormValues(form);
    ////console.log(elements);
    var errors = validate(elements, constraints, {fullMessages: false});

    if (typeof  errors !== 'undefined' && errors !== '') {
        //  //console.log(errors);
        var goToFirstPane = false;
        var errorFound=false;
        for (var key in errors) {

            if (errors.hasOwnProperty(key)) {
                element = $('[name="' + key + '"]');
                if(typeof element !="undefined" && element.length > 0) {
                    closestTabPane = $("#" + element.attr("id")).closest(".tab-pane")[0].id;

                    if (closestTabPane.includes("questionnaire")) {
                        lock_guidance_and_consulting_group = false;
                        lock_questionnaire_group = true;
                        goToFirstPane = true;
                    }
                    appendError(element, key, errors[key][0]);
                    errorFound = true;

                }

                //  $("#" + $(element).closest(".tab-pane").attr("id") + "_group").trigger("click");
            }
        }

        if (errorFound){
            stopDoingAjaxErrorFound = true;
            closestTabId ="#" + $(element).closest(".tab-pane").attr("id") + "_group";

            $("#button_submit").prop("disabled", true);
            if(typeof tryingToSave == "undefined"){
                tryingToSave=false;
            }
            if(tryingToSave)
            {
                e.stopImmediatePropagation();
                e.preventDefault();
            }

            if(goToFirstPane){
                closestTabId = "questionnaire_group";
            }
            if(closestTabId.includes("questionnaire_group")) {

                e.stopImmediatePropagation();
                e.preventDefault();
                e.returnValue = false;
                tryingToSave = false;
                dontDoAjax = true;
                submitFlow = false;
                dontShowErrors = false;
                $(closestTabId).trigger("click");
                return;
            }
            else{
                tryingToSave = false;
                dontDoAjax = false;
                //console.log(dontDoAjax);
                submitFlow = true;
                dontShowErrors = false;
                errorFound=false;
                stopDoingAjaxErrorFound = false;


                $("#guidance_and_consulting_group").tab('show');
                /* $("table").each(function(ev){

                     var id = $(this)[0].id;
                     if(id!="")
                         ValidateTable(id);

                 })*/

                activateGuidence();
                $("[role='form']").trigger("after:error");
            }



            // $("#" + $(element).closest(".tab-pane").attr("id") + "_group").trigger("click");
        }
    } else {
        return true;
    }
}
function checkForDataCorrectTransfet(table,value){
    data = JSON.parse(value);
    var t =$("#"+table+"_table_wrapper").find("table").DataTable()


    var colCount = t.columns()[0].length;
    if(colCount!=$(data[0]).length){
        return false;
    }
    return true;
}
function checkIfDataWasChanged(table,value){

    data = JSON.parse(value);
    var t =$("#"+table+"_table_wrapper").find("table").DataTable()


    var colCount = t.columns()[0].length;

    var idRow=0;

    if(data.length != t.rows().data().length)
        return true;

    while(idRow<data.length+1) {
        var foundData = false;
        // $.each(data, function (idRow, valueRow) {

        for(idRowTable = 0 ; idRowTable<t.rows().data().length;idRowTable++) {
            //$.each(t.rows(), function (idRowTable,tableData) {
            var valueRow = data[idRow];
            var tableData = t.row(idRowTable).data();
            if (tableData != undefined && tableData.length > 0 ) {
                if (tableData.equals(valueRow) === true) {
                    // //console.log("Found simlar rows in table (DATA FROM AJAX) : row - "+idRow+" : " +data[idRow] );

                    data = deleteRow(data, idRow);


                    foundData = true;
                    continue;
                }





            }
            // });
        }


        if(foundData)
        {
            idRow=0;
            foundData = false;
        }
        else {
            idRow++
        }

        // });
    }

    if(data.length == 0){
        return false;
    }

    return true;
}

/*
function arrangeDataInTable(name) {






        $.each(JSON.parse(name),function(table,value) {
            data = JSON.parse(value);
            var t =$("#"+table+"_table_wrapper").find("table").DataTable()


            if (typeof data.length != "undefined" && data.length > 0) {

                if (!checkForDataCorrectTransfet(table, value)) {
                    return;
                }
                if (!checkIfDataWasChanged(table, value)) {
                    return;
                }

                t.rows().remove().draw(true);

                t.rows.add(data).draw(false);
                //  hide("#cover");
                bigAjaxIsRunning = false;
            }
            else {
                bigAjaxIsRunning = false;
                return;
            }
        });

}
*/

function arrangeDataInTable(name){

    var equaleArr = false;

    // //console.log("NAME : " +JSON.parse(name))
    $.each(JSON.parse(name),function(table,value) {

        if(table.includes("recommended_vaccines_table_generic")) {
//        //console.log(table+":"+value.toString());
            if (!checkForDataCorrectTransfet(table, value)) {
                return false;
            }
            if (!checkIfDataWasChanged(table, value)) {
                return false;
            }
        }

    });



    var jsonStringCountries = $("#countries_table_generic").val();
    var vaccinesProgram = $("#vaccines_program").val();
    if(jsonStringCountries) {
        var countriesArray = JSON.parse(jsonStringCountries);
        var noCountrieSelected = false;


        $.each(countriesArray, function (idx, elem) {

            if (idx == 0 && (elem[idx].value == undefined || elem[idx].value == "")) {
                noCountrieSelected = true;
            }
        });


        if (countriesArray.length ==0 ||  countriesArray == '' || noCountrieSelected) {

            //          $("#vaccines_briefing_table_generic_table").DataTable().clear().draw(false);
            //          $("#recommended_vaccines_table_generic_table").DataTable().clear().draw(false);
            //          $("#malaria_prevention_treatment_table_generic_table").DataTable().clear().draw(false);


            $("#questionnaire_group").trigger("click");
            return;
        }
    }

    form_name  = $("form")[0].name=='vaccine_advisor';

    if((vaccinesProgram == "" && form_name == "vaccination_counseling_for_health_professions")  || (!jsonStringCountries && form_name == 'vaccine_advisor'))
    {
        return;
    }

    if(dontDoAjax) {


        $('table').each(function(e){


            var id=this.id;
            if(id!='') {
                $(this).find('input,textarea,select').each(function(ev){

                    var idElement=$(this).attr("id");
                    if($(this).is(":checked")) {
                        if($(this).attr("onclick")!="" && $(this).attr("onclick") != undefined)
                        {
                            var val = $("#"+idElement)[0].value;
                            $("#"+idElement)[0].onclick();
                            $("#"+idElement)[0].onclick();
                            $("#"+idElement)[0].value = val;
                        }

                    }

                    if($(this).val()!="")
                    {
                        if($(this).attr("onchange")!="" && $(this).attr("onchange") != undefined)
                        {
                            var val = $("#"+idElement)[0].value;
                            $("#"+idElement)[0].onchange();
                            $("#"+idElement)[0].value = val;
                        }
                    }


                });

            }

        });


        if(typeof submitFlow != "undefined" &&  submitFlow && !dontShowErrors) {

            ValidateTable(id);

        }
        // $($("#" + $(element).closest(".tab-pane").attr("id") + "_group").parent().parent().closest("ul")[0].children[0]).find("a").trigger("click");
        //$("#" + $(element).closest(".tab-pane").attr("id") + "_group").trigger("click");
        return;
    }

    $('form').on("submit",function( event ) {

        $.each(JSON.parse(name),function(table,value) {
            window["refreshTableJson_"+table]();
        });

    });
    $.each(JSON.parse(name),function(table,value){

//        //console.log(table+":"+value.toString());




        if($("#"+table+"_table_wrapper").find("table").length) {

           /* if(table.includes("recommended_vaccines_table_generic")) {
                if (!checkForDataCorrectTransfet(table, value)) {
                    return;
                }
                if (!checkIfDataWasChanged(table, value)) {
                    return;
                }
            }
*/

            var t =$("#"+table+"_table_wrapper").find("table").DataTable()


            var colCount = t.columns()[0].length;


            data = JSON.parse(value);




            if(typeof data != "undefined") {


                var iDx = -1;
                $.each(t.column(0).context[0].aoColumns, function (column) {
                    if (data.length>0 && data[0].length != colCount) {
                        tableSettings = data.splice(0, 1)[0];


                    }
                    if (typeof tableSettings != "undefined" && this.sTitle == tableSettings['column-name']) {
                        // this.sClass = tableSettings['class'];
                        // //console.log(this.sTitle);
                        iDx = this.idx;
                    }
                    else{
                        this.sClass = " btn-sm text-muted ";
                    }

                });
                /*  var equaleArr = false;
                  $.each(data,function(idRow,valueRow){


                          if (t.row(idRow).data() != undefined &&t.row(idRow).data().length > 0  && t.row(idRow).data().equals(valueRow) === true)
                          {
                              equaleArr = true;
                          }

                  });*/

                if (typeof data.length != "undefined" &&  data.length > 0 ) {
                    t.rows().remove().draw(true);

                    t.rows.add(data).draw(true);

                    //  hide("#cover");
                    bigAjaxIsRunning  = false;

                    $("#recommended_vaccines_table_generic_table").find("tr td:nth-child(10)").each(function(){$(this).hide()});
                    $("#recommended_vaccines_table_generic_table").find("tr th:nth-child(10)").each(function(){$(this).hide()});
                }
                else{
                    t.rows().remove().draw(true);
                    bigAjaxIsRunning  = false;
                    return;
                }


                $("#"+table+"_table_wrapper input[type=checkbox]").each(function(e){
                    if($(this).is(":checked")) {
                        var val = $(this).val();
                        $(this).trigger("click");
                        $(this).trigger("click");
                        $(this).val(val);
                    }
                    else{

                        $(this).closest("tr").find("input,select").each(function(){
                            var name= $(this)[0].name;
                            if(!name.includes("comment") && !name.includes("guidelines") && !name.includes("directives") ) {
                                if ($(this)[0].type != "checkbox") {
                                    $(this).prop("disabled", true);
                                }
                            }

                        });
                    }
                });



                var id = $(this)[0].id;
                if(id!="")
                    ValidateTable(id);


                if(table.indexOf('malaria')>-1){

                    if(data.length>0){

                        $("[id^='malaria_prevention_treatment']").parent().parent().each(function(ev){
                            var idA =$(this).attr('id');
                            if ( idA != undefined && idA.indexOf('guidence_and_consulting') )
                                $('#'+idA).show();
                        });



                        //$("#"+table+"_table_wrapper").parent().parent().show();
                    }
                    else{
                        $("[id^='malaria_prevention_treatment']").parent().parent().each(function(ev){
                            var idA =$(this).attr('id');
                            if ( idA != undefined && idA.indexOf('guidence_and_consulting') )
                                $('#'+idA).hide();
                        });
                    }
                }


            }
        }

        if($("#addRow_recommended_tests_table_generic") != undefined)
        {
            adjustSelect2("#recommended_tests_table_generic_table_wrapper", true);
            $("#addRow_recommended_tests_table_generic").on("click",function(ev){

                ev.preventDefault();
                ev.stopImmediatePropagation();
                var testNameID = $('#recommended_tests_table_generic_table tr:last').find("[id^='test_name']")[0].name;

                constraints[testNameID] = allConstraints['Required'];
                var advised =  $('#recommended_tests_table_generic_table tr:last').find("[id^='advised']");
                if(!advised.is(":checked"))
                    advised.trigger("click");

               // adjustSelect2("#recommended_tests_table_generic_table_wrapper", true);
            });
        }



        if($("#addRow_recommended_vaccines_table_generic") != undefined)
        {
            adjustSelect2("#recommended_vaccines_table_generic_table_wrapper", true);

            $("#addRow_recommended_vaccines_table_generic").on("click",function(ev){

                ev.preventDefault();
                ev.stopImmediatePropagation();
                var vaccineNameID = $('#recommended_vaccines_table_generic_table tr:last').find("[id^='vaccine_name']")[0].name;

                constraints[vaccineNameID] = allConstraints['Required'];
                var advised = $('#recommended_vaccines_table_generic_table tr:last').find("[id^='advised']");
                if(!advised.is(":checked"))
                    advised.trigger("click");

        //        adjustSelect2("#recommended_vaccines_table_generic_table_wrapper", true);
            });
        }


        $(document).ready(function(){
            /* if(table.indexOf('country')>-1){

             var t=table.DataTable();
             t.on("dt.draw",function(e){
             dontDoAjax = false;
             });
             }*/

            $('input,select,textarea').on("change click",function(k){
                try {
                    tableCheckedArray=[];
                    if($("#button_submit").prop("disabled")) {
                        $("#button_submit").prop("disabled", false);

                        var id = $(this).parents('table')[0].id;
                        if ($(this).parents('table').length && id.indexOf("country") >= 0) {
                            // if parent is not table
                            if (!dontShowErrors) {
                                dontDoAjax = true;
                            }
                        } else {
                            dontShowErrors = false;
                            dontDoAjax = false;

                            // parent is table
                        }

                    }

                }
                catch(err) {

                    //console.log(err.message);
                }
            });

        });

      //  ValidateTable(table+"_table");
    });


  //  debugger;
    $("table").each(function(){
        if($(this)[0].id!="")
            ValidateTable($(this)[0].id);
    })
}




function appendError(input, id, message) {


    //bind hide function on focus/select again




    if(document.getElementById('error_' + id) == undefined) {
        $(input).each(function(){

            var input_type=$(this).attr('type');

            if(input_type!='hidden')
                if(input_type!='checkbox' && input_type!='radio' && input_type !== undefined) {
                    $(this).after("<span id='error_" + id + "' class='error-message'>" + message + "</span>");
                }
                else{
                    if( input_type=='radio'){

                        var container=$(this).parent().parent();
                        var elements_in_container=container.find("span[id^=error_]").length;

                        if (elements_in_container === 0){

                            container.append("<span id='error_" + id + "' class='error-message'>" + message + "</span>");
                        }
                    }else{
                        $(this).parent().append("<span id='error_" + id + "' class='error-message'>" + message + "</span>");
                    }

                }

        })

    }
    else{
        $(document.getElementById('error_' + id)).show();
        $(document.getElementById('error_' + id)).text(message);
    }

    $(input).addClass('error-border');

    if($(input)[0].type=="select-one"){
        var borderSelect2 = $(input).parent().find("span.select2-selection.select2-selection--single");
        if(typeof  borderSelect2 !="undefined")
        {
            borderSelect2.addClass("error-border");
        }
    }
    if($('#error_' + id).length == 1) {
        ////console.log("scrolling ui_addons");
        $('#error_' + id).parent().get(0).scrollIntoView();
    }

    $(input).on("change select keypress input blur", function () {
        hideErrors(this, id);
    });



}
function hideErrors2(input, id) {
    $(input).removeClass('error-border');
    $("#error_" + id).remove();

    if($(input)[0].type=="select-one"){
        var borderSelect2 = $(input).parent().find("span.select2-selection.select2-selection--single");
        if(typeof  borderSelect2 !="undefined")
        {
            borderSelect2.removeClass('error-border');
        }
    }

}
function hideErrors(input, id){
    $(input).removeClass('error-border');
    $("#error_" + id).text('');

    var parent_div = $(input).parents('div.tab');
    if($(parent_div).is('div')) {
        var div_id = $(parent_div).attr('id');
        var type_tab = div_id.substr(4);
        $('a#header_tab_'+type_tab).css('color', 'black');
    }

    if($(input)[0].type=="select-one"){
        var borderSelect2 = $(input).parent().find("span.select2-selection.select2-selection--single");
        if(typeof  borderSelect2 !="undefined")
        {
            borderSelect2.removeClass('error-border');
        }
    }
}

function sortSelect(selElem) {
    if(selElem == undefined)
        return;

    var tmpAry = new Array();
    for (var i=0;i<$("#"+selElem)[0].options.length;i++) {
        tmpAry[i] = new Array();
        tmpAry[i][0] = $("#"+selElem)[0].options[i].text;
        tmpAry[i][1] = $("#"+selElem)[0].options[i].value;
        tmpAry[i][2] = $("#"+selElem)[0].options[i].disabled;
        tmpAry[i][3] = $("#"+selElem)[0].options[i].selected;

        //   //console.log(tmpAry[i])
        //  //console.log("\n\n--------------------------------\n\n");
    }
    var elementPop = tmpAry.shift();
    tmpAry.sort();

    while ($("#"+selElem)[0].options.length > 0) {
        $("#"+selElem)[0].options[0] = null;
    }
    tmpAry.unshift(elementPop);
    for (var i=0;i<tmpAry.length;i++) {
        var op = new Option(tmpAry[i][0], tmpAry[i][1], tmpAry[i][2], tmpAry[i][3]);
        $("#"+selElem)[0].options[i] = op;

    }
    return;
}


tableCheckedArray=[];

function ValidateTable(tableId)
{
    if(typeof(tableId)=="undefined"){
        return;
    }
    var table_name=$("#"+tableId).closest("table")[0].id;

   /* var myJsonString = JSON.stringify(tableCheckedArray);

    if(myJsonString.includes(table_name))
    {

        return;
    }*/

    tryingToSave=false;
    tableCheckedArray.push(table_name);

    //$(table_name).css("opacity",0.1);

    if(!$("#"+tableId).is("table"))
    {

        return;
    }
    if(typeof stopDoingAjaxErrorFoundInTable =="undefined"){
        stopDoingAjaxErrorFoundInTable  =   false;

    }

    if(typeof stopDoingAjaxErrorFoundInTableCounter =="undefined"){

        stopDoingAjaxErrorFoundInTableCounter = 0 ;
    }
    stopDoingAjaxErrorFoundInTableCounter++;
    var elements =  validate.collectFormValues($("#"+tableId));
    ////console.log(elements);
    var errorsReturned = validate(elements, constraints, {fullMessages: false});

    var errors =[]
    $.each(elements,function(key,value){


        if(errorsReturned[key]){
            errors[key]=errorsReturned[key];
        }

    })
    var errorsFound=false;
    if (typeof  errors !== 'undefined' && errors !== '') {
        //  //console.log(errors);

        for (var key in errors) {

            if (errors.hasOwnProperty(key)) {
                element = $('[name="' + key + '"]');

                ////console.log("Alert found : " +  key);

                appendError(element, key, errors[key][0]);

                errorsFound =true;
                //         $($("#" + $(element).closest(".tab-pane").attr("id") + "_group").parent().parent().closest("ul")[0].children[0]).find("a").trigger("click");

                //
            }
        }
    }

    if(errorsFound) {
        stopDoingAjaxErrorFoundInTable = true;
        closestTabId ="#" + $(element).closest(".tab-pane").attr("id") + "_group";


        submitFlow = true;
        dontDoAjax = false;
        dontShowErrors = false;
        if(closestTabId.includes("questionnaire_group")) {
            submitFlow = false;
            dontDoAjax = true;

            $(closestTabId).trigger("click");
        }




    }
    //console.log(dontDoAjax + ":" + "3")
    // $(table_name).css("opacity",1);
}
function isFunction(functionToCheck) {
    return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

// Show an element
var show = function (elem) {


    elem = $(elem)[0];
    elem.style.display = 'block';

}

// Hide an element
var hide = function (elem) {
    setTimeout(function(){
        elem = $(elem)[0];
        elem.style.display = 'none';
    }, 1000);


}

function adjustSelect2(table, jsSort){
    if (typeof jsSort === 'undefined') {
        jsSort = true;
    }
    $(table+" .selectbox2").each(function(ev){

        if(! $(this).hasClass("as-label")) {
            $(this).select2({width: '185px'});
            if(jsSort == true){
                sortSelect($(this).attr("id"));
            }
          //  $(this).trigger("change");
        }

    });

}
function ajaxApiCallBody(url,formId,callThisFunction,additionalData,onlyThisData,event,async)
{
    var asyncType=true;
    if(typeof  async !=="undefined"){
        asyncType = async ;
    }

    if(typeof bigAjaxIsRunning == "undefined")
    {
        bigAjaxIsRunning = true;
    }

    if(!url.includes('check-if-something-was-changed')) {
        var urlCondition=!url.includes('get-advising-data')&& !url.includes('get-commercial-names-list') &&  !url.includes("get-risk-icon");
        if (dontDoAjax || (bigAjaxIsRunning && urlCondition)) {
            hide("#cover");
            return new Array();
        }
    }




    var stop = false;
    var formJson = { };
    $.each($('form').find("input,select").attr("readonly",false).serializeArray(), function() {
        formJson[this.name] = this.value;
    });

    var tUrl =(window.location.origin+window.location.pathname)
    tUrl.split("public")[0]+"public"+url
    $("#"+formId).find("input,select").attr("readonly",true);


    var apiResult = false;
    var data="";
    if(onlyThisData=="" || onlyThisData == undefined){
        data={data:formJson,blobData:additionalData};
    }else{
        data= onlyThisData;
    }


//console.log ("async : "+asyncType+"\r\n url: "+url);
    $.ajax({
        url:  window.location.origin+BASE_PATH+url,
        async:asyncType,
        cache:true,
        type: 'POST',
        data:data,
        dataType:'json'}).done(function(result){
        if(result.code == 0){
            // updateFrame();
            stop=false;


            if(isFunction(callThisFunction)){
                callThisFunction(JSON.stringify(result.output));
            }

            $("#"+formId).find("input,select").attr("readonly",false);
            apiResult = result.output;

        }
        else{
            alert("error"+result.code);
            stop=true;
        }
        if(stop)
        {
            event.preventDefault();
            event.stopPropagation();
            apiResult = false;
            hide("#cover");
            return false;
        }

//                var formJson = $("#"+formId).serializeArray();

    })


    /*if(asyncType == true){
        while(!apiResult) {
            setTimeout(function (ev) {

                //console.log("waiting for "+url);
            }, 1000);
        }
    }*/



    // hide("#cover");
    return apiResult;

}


function callAjaxFunctionWithoutClick(url,id,formId,callThisFunction,additionalData,onlyThisData,async)
{
    var formName =   $("form").attr("name");

    additionalData['form_name']=formName;

    if(typeof tryingToSave != "undefined"   && tryingToSave)
    {
        return;
    }

    table_recommended_vaccines_table_generic_table = $('#recommended_vaccines_table_generic_table').DataTable();
    table_recommended_vaccines_table_generic_table.order( [ 9, 'asc' ] ).draw(true);
    $('#recommended_vaccines_table_generic_table').find("tbody th:nth-child(9)").each(function(){$(this).remove()});

    return ajaxApiCallBody(url,formId,callThisFunction,additionalData,onlyThisData,null,true);



}

function callAjaxFunction(url,id,formId,callThisFunction,additionalData,onlyThisData,async)
{
    var formName =   $("form").attr("name");

    additionalData['form_name']=formName;


    if(typeof tryingToSave != "undefined"   && tryingToSave)
    {
        return;
    }
    if (id !="" && typeof id != "undefined" &&  id != "null"){
        $("#"+id).trigger("click",function(e){

            // $("#"+id).disabled=true;
            var apiResult = ajaxApiCallBody(url,formId,callThisFunction,additionalData,onlyThisData,e,async);
            /*if(typeof apiResult != "undefined" && apiResult){
             // $("#"+id).disabled=false;

             }
             else{

             e.preventDefault();
             e.stopPropagation();
             // $("#"+id).disabled=false;
             }*/
        });
    }
    else{

        return ajaxApiCallBody(url,formId,callThisFunction,additionalData,onlyThisData,null,async);
    }


}


function updateFrame(){
    this.location.reload();
}

function getAllCommercialNames(){

    return callAjaxFunction("/advisor-api/get-commercial-names-list","","vaccine_advisor","",{"vaccineId":"all"},"",false);

}

function getCommercialName(vaccineId){

    var CommercialNameobj = undefined;
    $.each(AllCommertialNames,function(key,value){
        if (value.vaccineId==vaccineId){


            if (CommercialNameobj===undefined){
                CommercialNameobj = {}
            }
            CommercialNameobj[key]={
                "value":value.value,
                "id":value.id
            }


        }
    });
    return CommercialNameobj;

}

function getCommertialNamesList(self,dontClick){

    if(window["refreshTableJson_malaria_prevention_treatment_table_generic"]!==undefined){
        window["refreshTableJson_malaria_prevention_treatment_table_generic"]();
    }

    if(window["refreshTableJson_recommended_vaccines_table_generic"]!==undefined){
        window["refreshTableJson_recommended_vaccines_table_generic"]();
    }

    if(window["refreshTableJson_vaccines_briefing_table_generic"]!==undefined){
        window["refreshTableJson_vaccines_briefing_table_generic"]();
    }

    if(bigAjaxIsRunning)
        return;
    /* if(typeof dontClick == "undefined"){
         $(this).trigger("click");
     }

     if(typeof dontClick == "undefined"){
         $(this).trigger("click");
     }*/
    var vaccine_id = $(self).closest("tr").find("[id^=vaccine_name]").val();

    var isChecked = false;
    var myelement = $(self).closest("tr").find("[id^=advised]");
    if( myelement.length >0) {
        isChecked = myelement[0].hasAttribute('checked') || myelement[0].checked;
    }else{
        isChecked=undefined;
    }
    var advised = isChecked;


    if(vaccine_id=="" || !advised || advised=="undefined")
        return;
    ////console.log(id);

    var fillThisSelectBox = $(self).closest("tr").find("td [id^=commercial_name]");
    var commertial_name = fillThisSelectBox.val();
    fillThisSelectBox.find("option").each(function(ev){
        $(this).remove()
    });


    if(vaccine_id == undefined || vaccine_id == "" ||  vaccine_id=="on")
        vaccine_id = 0;



    if(vaccine_id == 0 || !advised){

        var option = document.createElement("option")
        option.text =""; //"לא נמצאו מרווחים";
        option.value = "";

        fillThisSelectBox[0].add(option);
        return;
    }

    /*
    var additionalData ={"vaccineId":vaccine_id};
    var intervals=callAjaxFunction("/advisor-api/get-commercial-names-list","","vaccine_advisor","","",additionalData,false);
    */
    var intervals=getCommercialName(vaccine_id);


    if ( typeof intervals == "undefined" || intervals=="false" || intervals=="" || intervals.length==0){

        var option = document.createElement("option");
        option.text = "לחיסון לא קיימות אצוות פעילות";
        option.value = "";

        fillThisSelectBox[0].add(option);


    }else {


        /*
         ערכים לבחירה: שמות מסחריים של החיסון שנבחר, שיש להם לפחות אצווה אחת פעילה בטבלת האצוות. אם יש רק אפשרות אחת לבחירה אז יש להציג אותה בשדה כברירת מחדל.
         אם אין אצווה פעילה לשם המסחרי יש להציג הודעה מתחת לשדה: "לחיסון לא קיימות אצוות פעילות"
         * */

        if(Object.size(intervals) > 1) {
            var option = document.createElement("option");
            option.text = "בחר";
            option.value = "";
            fillThisSelectBox[0].add(option);
        }



        $.each(intervals,function(key,value){

            var option = document.createElement("option");
            option.text = value.value;
            option.value = value.id;
            fillThisSelectBox[0].add(option);
        });
        fillThisSelectBox.val(commertial_name);

        if(Object.size(intervals) == 1){
            fillThisSelectBox[0].firstElementChild.selected=true;
        }


    }

}


Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

window['getIntervals']=[];

function getTimeDiffVaccList(self,dontClick){

    /* if(typeof dontClick == "undefined"){
         $(this).trigger("click");
     }

     $(this).trigger("click");*/

    if(window["refreshTableJson_malaria_prevention_treatment_table_generic"]!==undefined){
        window["refreshTableJson_malaria_prevention_treatment_table_generic"]();
    }

    if(window["refreshTableJson_recommended_vaccines_table_generic"]!==undefined){
        window["refreshTableJson_recommended_vaccines_table_generic"]();
    }

    if(window["refreshTableJson_vaccines_briefing_table_generic"]!==undefined){
        window["refreshTableJson_vaccines_briefing_table_generic"]();
    }




    if(bigAjaxIsRunning)
        return;
    var dosage = $(self).closest("tr").find("[id^=dosage_amount]").val();
    var vaccine_id = $(self).closest("tr").find("[id^=vaccine_name]").val();


    var isChecked2 = false;
    var myelement2 = $(self).closest("tr").find("[id^=advised]");
    if( myelement2.length >0) {
        isChecked2 = myelement2[0].hasAttribute('checked') || myelement2[0].checked;
    }else{
        isChecked2=undefined;
    }
    var advised = isChecked2;


    if(vaccine_id=="" || dosage =="" || !advised || advised=="undefined") {
        return;
    }

    if(dosage<2) {
        $(self).closest("tr").find("td [id^=dosage_interval]").val("");
        return;
    }
    ////console.log(id);

    var fillThisSelectBox = $(self).closest("tr").find("td [id^=dosage_interval]");
    var dosageValue = fillThisSelectBox.val();

    //var dosageValue = "";
    var dosageIntervalId = fillThisSelectBox.attr("id");

    var recommended_vaccines_table_generic=$("#recommended_vaccines_table_generic").val();
    var recVacTabGen=[];

    try {
        recVacTabGen=JSON.parse(recommended_vaccines_table_generic);
    } catch (e) {
        recVacTabGen=[];
    }
    for (var i = 0; i < recVacTabGen.length; i++){
        if (recVacTabGen[i][5]["name"] == dosageIntervalId && dosageValue!=null &&  dosageValue.length == 0){
            if( $("#hidden_"+dosageIntervalId).length === 0){
                $(fillThisSelectBox).parent().append("<input type='hidden' id='hidden_"+dosageIntervalId+"' name='hidden_"+dosageIntervalId+"' value='"+ recVacTabGen[i][5]["value"] +"'>");
            }
            if(recVacTabGen[i][5]["value"] !== null) {
                dosageValue = recVacTabGen[i][5]["value"];
            }
        }
    }
    if(dosageValue!=null &&  dosageValue.length === 0){
        dosageValue = $("#hidden_"+dosageIntervalId).val();
    }

    fillThisSelectBox.find("option").each(function(ev){
        $(this).remove();
    });


    if(vaccine_id == undefined || vaccine_id == "" ||  vaccine_id=="on")
        vaccine_id = 0;

    if(dosage == undefined || dosage == "" || dosage=="on" )
        dosage = 0;

    if(dosage==0 || vaccine_id == 0 || (!advised && dosage <1)){

        var option = document.createElement("option");
        option.text =""; //"לא נמצאו מרווחים";
        option.value = "";

        fillThisSelectBox[0].add(option);
        return;
    }


    var additionalData ={"vaccineId":vaccine_id,"dosage":dosage};

    var intervals = "";




    if(dosage>1) {

        if(window['getIntervals'][vaccine_id] === undefined) {
            window['getIntervals'][vaccine_id]=[];

        }



        if(typeof window['getIntervals'][vaccine_id][dosage] == "undefined")
        {

            intervals = callAjaxFunction("/advisor-api/get-Intervals-list", "", "vaccine_advisor", "", "", additionalData, false);
            window['getIntervals'][vaccine_id][dosage] = intervals;

        }
        else{
            intervals=window['getIntervals'][vaccine_id][dosage];
        }
    }




    if ( typeof intervals == "undefined" || intervals=="false" || intervals=="" || intervals.length==0){

        var option = document.createElement("option");
        if(dosage>1)
        {
            option.text ="בחר";
        } //"לא נמצאו מרווחים";
        else{
            option.text ="";
        }
        option.value = "";

        fillThisSelectBox[0].add(option);


    }else {


        //אם כמות המנות לחיסון היא 0 או 1 אז לא להציג ערך בשדה.
        // אם הוגדרו 2 מנות ומעלה: ב"מ: 'בחר'.


        if(intervals.length > 1) {
            var option = document.createElement("option");
            option.text = "בחר";
            option.value = "";
            fillThisSelectBox[0].add(option);

        }


        $.each(intervals,function(key,value){

            var option = document.createElement("option");
            option.text = value.value;
            option.value = value.id;
            fillThisSelectBox[0].add(option);

        });
        fillThisSelectBox[0].selected=true;
        fillThisSelectBox.focus();
        $(self).closest("tr").find("[id^=dosage_amount]").focus();
        if(dosageValue!="") {
            fillThisSelectBox.find("option").each(function(){

                if($(this).val()==dosageValue){
                    fillThisSelectBox.val(dosageValue);
                    fillThisSelectBox.prop("selected",true);
                }
            })


        }





    }



}

AllAlerts = null;
function checkIfAllisChecked(){
    if(!checkIfAllisCheckedFlag){
        var allRadio=$("input[type='radio'][id]");
        var allischecked=true;
        allRadio.each(function( index ) {
            var name=$(this).attr('name');
            var element=$("input[name='"+name+"']");
            if(!(element.is(':checked'))) {
                allischecked=false;
                return false;
            }
        });
        if (allischecked){
            AllAlerts=callAjaxFunction("/advisor-api/get-risk-icon","",formId,"",{"vaccine_id":"all", "formId": formId},"",false);
        }
    }
}

function getVaccineAlertsById(vaccineId){
    return CommercialNameobj=AllAlerts["vaccines"][vaccineId];
}

function getGroupsAlertsById(vaccineId){
    var groupAlerts = AllAlerts["groups"][vaccineId];
    return groupAlerts;
}
function getTestAlertsById(testId){
    var testAlerts = AllAlerts["tests"][testId];
    return testAlerts;
}

function checkforWarningSign(self, cssClass){

    if(bigAjaxIsRunning)
        return;
    var id =$(self).val();
    ////console.log(id);
    var vaccine_id={"vaccine_id":id};


    /*
    var advised =  $(self).closest("tr").find("[id^=advised]") &&   $(self).closest("tr").find("[id^=advised]").length >0 ?  $(self).closest("tr").find("[id^=advised]")[0].checked:false;
    if(vaccine_id == "" || (!advised && !$(self).hasClass("as-label")))
    {
        var nam = $(self).closest("tr").find("[id^='recommended_vaccines_table_generic_icon']");
        removeWarningSign(nam);
        return;
    }
    var info=callAjaxFunction("/advisor-api/get-risk-icon","","vaccine_advisor","",vaccine_id,"",false);
    */

    var info=getVaccineAlertsById(id);
    if ( typeof info != "undefined" && info!="false" && info!=""){
        createWarningSign(self, info, cssClass, 2, 'vaccines-icon');
    }else {
        removeWarningSign(self, 2, 'vaccines-icon');
    }

    var groupsInfo=getGroupsAlertsById(id);
    if ( typeof groupsInfo != "undefined" && groupsInfo!="false" && groupsInfo!=""){
        createWarningSign(self, groupsInfo, '', 3, 'groups-icon');
    }else {
        removeWarningSign(self, 3, 'groups-icon');
    }


}

function checkforTestWarningSign(self, cssClass){

    if(bigAjaxIsRunning)
        return;
    var id =$(self).val();
    console.log(id);

    var info=getTestAlertsById(id);
    if ( typeof info != "undefined" && info!="false" && info!=""){
        createWarningSign(self, info, cssClass, 2, 'vaccines-icon');
    }else {
        removeWarningSign(self, 2, 'vaccines-icon');
    }
}

function removeThisRow(tableID,row){
    $(row).find('input, select, textarea').each(function(ev){
        removeConstraintsFromList($(this)[0].id, allConstraints['Required']);
    });
    var t=$("#"+tableID).dataTable();

    var lastIndex = tableID.lastIndexOf("_table");

    tableID = tableID.substring(0, lastIndex);


    if(tableID.includes("countries")) {

        undoCountries = t.fnGetData(row);
    }
    //row.remove();
    //var index = row[0].rownIndex;

    t.fnDeleteRow(row);//.fnDraw(false);




    window["refreshTableJson_"+tableID]();


    if(tableID.includes("countries")) {


        CheckIfSomethingWasChanged("שים לב, מחיקת היעד עלולה לגרום למחיקת נתוני חיסונים מומלצים/טיפול מונע למלריה שהומלצו ע`י המערכת. האם לבצע את השינוי?", $("#" + tableID));
    }

}

function createWarningSign(self, title, cssClass, whichTd, whichIcon){

    var tdcontainer=$(self).parent().parent().find('td:eq(' + whichTd + ')');

    title=title.replace(/&#x00A;/g,String.fromCharCode(10));
    title=title.replace(/&#x00A;/g,String.fromCharCode(10));
    var p = tdcontainer.find('.' + whichIcon).next("p");
    p.attr({
        "class" : typeof cssClass === 'undefined' || cssClass == 'none' ? '' : cssClass,
        "data-toggle" : "tool-tip",
        "title" : title
    });

    //tdcontainer[0].title = title;
    p.find('span').attr({
        "class" : "fa fa-exclamation-triangle"
    });
}


function removeWarningSign(self, whichTd, whichIcon){

    var tdcontainer=$(self).parent().parent().find('td:eq(' + whichTd + ')');

    var pdiv= tdcontainer.find('.' + whichIcon).next("p");
    var spandiv=pdiv.find('span');

    pdiv.removeClass('alert-warning-extended');
    pdiv.removeData('data-toggle'); //tool-tip
    pdiv.attr("title" , "");

    spandiv.removeClass('fa fa-exclamation-triangle');

}
function removeConstraintsFromList(element_id,constraint)
{
    var exists=false;
    dontDoAjax = false;
    $.each(constraints,function(key,value){
        if(key==element_id){
            delete (constraints[key]);
            $("#"+key).removeClass('error-border');
            $("#error_" + key).remove();
        }

    });



}

function EnableElement(runConstraint,idToChange,obj,constraint)
{

    var commertialName= $(obj).closest("tr").find("[id^="+idToChange+"]");


    if(commertialName.hasClass("as-label"))
        return;


    var id = null;


    if( commertialName.is( "option" ) ) {
        id = (commertialName.parent())[0].id;
    }
    else{
        id = commertialName[0].id;
    }



    if(runConstraint != undefined &&  runConstraint) {



        if($(commertialName).attr("disabled") == "disabled") {


            $(commertialName[0]).prop("disabled", false);
            commertialName.trigger("change");
            commertialName.trigger("click");


            addConstraints(id, constraint);
        }

    }
    else{
       // //console.log($(commertialName[0]).attr("disabled"));
        if($(commertialName).attr("disabled") != "undefined") {

            $(commertialName[0]).prop("disabled", true);

            // if(commertialName.find("option").length>0){
            //  commertialName.find("option").each(function(ev){$(this).remove();})
            // var option = document.createElement("option");
            // option.text = "בחר";
            // option.value = "";
            // commertialName[0].add(option);

            //}
            if (commertialName[0].type == "number") {
                if (commertialName[0].value == "") {
                    commertialName[0].value = 0;
                }


            }
            commertialName.trigger("change");
            commertialName.trigger("click");
            removeConstraintsFromList(id, constraint);

            /*var elementName ="";
            var element_name = commertialName[0].id.split("_");
            $.each(element_name,function(place,val){

                if(isNaN(parseInt(val)))
                {
                    elementName+=val;
                    elementName+="_";
                }

            });
            elementName = elementName.slice(0, -1)


            switch(elementName){
                case "commercial_name":
                //    commertialName.find("option").each(function(ev){ $(this).remove();})
                    break;
                case "dosage_amount":
                    commertialName[0].value = 0;
                    break;
                case "dosage_interval":
               //     commertialName.find("option").each(function(ev){ $(this).remove();})
                    break;
            }

            commertialName.trigger("change");
            commertialName.trigger("click");
            removeConstraintsFromList(id, constraint);
    */
        }
    }

    if(typeof submitFlow != "undefined" &&  submitFlow && !dontShowErrors) {
        ValidateTable(id);

    }

}

function ClearRow(checked,commertialName){

    if(checked) {

        delete(constraints[$(this)[0].id]);

        var element_name = $(commertialName).closest("tr").find("input , textarea, select");
        $.each(element_name, function (place, val) {

            var elementName ="";
            var valId = val.id.split("_");

            delete(constraints[val.id]);

            $.each(valId,function(p,v){
                if (isNaN(parseInt(v))) {
                    elementName += v;
                    elementName += "_";
                }
            })

            elementName = elementName.slice(0, -1);


            switch (elementName) {

                case "dosage_interval":
                case "commercial_name":
                    $(val).find("option").each(function(ev){ $(this).remove();})
                    $(val).append("<option selected value=''></option>").trigger("change");
                    break;
                case "dosage_amount":
                    val.value = 0;
                    break;
                case "checklist_time" :
                case "test_name" :
                    $(val).val("");
                    break;
            }

        });

    }
}
function AddConstraintsToTableElementAfterCheck(runConstraint,id,constraint,tableId,valueToCheck){

    var isChecked3 = false;
    var myelement3 =  $("#"+id).closest("tr").find("[id^=advised]");
    if( myelement3.length >0) {
        isChecked3 = myelement3[0].hasAttribute('checked') || myelement3[0].checked;
    }else{
        isChecked3=undefined;
    }
    var advised = isChecked3;


    if(typeof advised != "undefined" &&  !advised){
        $("#"+id).prop("disabled",true);
        $("#"+id).trigger("change");
        $("#" + id).removeClass('error-border');
        $("#error_" + id).text('');
        return;
    }

    if(runConstraint != undefined &&  runConstraint) {

        if($("#"+id).val() =="" || $("#"+id).val() ==valueToCheck) {

            addConstraints(id, constraint);
            $("#"+id).trigger("change");
        }
    }
    else{
        if($("#"+id).val() =="" || $("#"+id).val() ==valueToCheck) {
            removeConstraintsFromList(id, constraint);
            $("#"+id).trigger("change");
            $("#" + id).removeClass('error-border');
            $("#error_" + id).text('');
        }

    }

    /* if(typeof submitFlow != "undefined" &&  submitFlow && !dontShowErrors) {
     ValidateTable(tableId);

     }*/
}

function AddConstraintsToTableElement(runConstraint,id,constraint,tableId){


    var isChecked4 = false;
    var myelement4 = $("#"+id).closest("tr").find("[id^=advised]");
    if( myelement4.length >0) {
        isChecked4 = myelement4[0].hasAttribute('checked') || myelement4[0].checked;
    }else{
        isChecked4=undefined;
    }
    var advised = isChecked4;


    if(typeof advised != "undefined" && !advised){
        $("#"+id).prop("disabled",true);
        $("#"+id).removeClass('error-border');
        $("#error_" + id).text('');

        return;
    }

    if(runConstraint != undefined &&  runConstraint) {
        addConstraints(id, constraint);
    }
    else{
        removeConstraintsFromList(id, constraint);
        $("#"+id).removeClass('error-border');
        $("#error_" + id).text('');


    }

    /* if(typeof submitFlow != "undefined" &&  submitFlow && !dontShowErrors) {
         ValidateTable(tableId);

     }*/
}
function checkThisValue(self,CheckHere){


    /*  var   arrCountries = [];


     $("#"+CheckHere+"_table_wrapper select").each(function(val){

     if(arrCountries.indexOf($(this).val())>-1){
     // alert("already exists" +  $(this).val());
     $(this).addClass('label-danger');
     }
     else{
     $(this).removeClass('label-danger');
     arrCountries.push($(this).val());
     }

     });
     */



}

function approveOrNotPopup(self){

    var modal='<div id="MyModal" class="modal fade">';
    modal+=' <div class="modal-dialog">';
    modal+='<div class="modal-content">';
    modal+=' <div class="modal-header">';
    modal+='  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    modal+='<h4 class="modal-title">Modal title</h4>';
    modal+='</div>';
    modal+=' <div class="modal-body">';
    modal+='  <p>One fine body&hellip;</p>';
    modal+=' </div>';
    modal+='<div class="modal-footer">';
    modal+=' <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
    modal+='   <button type="button" class="btn btn-primary">Save changes</button>';
    modal+=' </div>';
    modal+=' </div>';
    modal+=' </div>';
    modal+='</div>';



}


function placeContinueToGuidenceAndConsulting(id){

    var element=$("#"+id).parent();
    element.removeClass("col-md-12");
    element.addClass("col-md-3");
    element.addClass("pull-left");


    element.clone().appendTo( ".formSumbitButtonsHere" );

    element.remove();

}
function moveToCancelSave(id){

    var element=$("#"+id).parent();
    element.clone().appendTo( ".formSumbitButtonsHere" );
    element.remove();
    $("#"+id).parent().css('display','inline-block');
}


function addSpaceToHtmlText(myClass){

    var element=$("."+myClass);
    element.prepend("&nbsp;&nbsp;");
    var html=  element.html();
    element.html("<span class='form_improved_text' style='font-family: initial' >"+html+"</span>");
}

function onlyPositive(e){
    if(!((e.keyCode > 95 && e.keyCode < 106)
        || (e.keyCode > 47 && e.keyCode < 58)
        || e.keyCode == 8)) {
        e.preventDefault();
        return false;
    }
}




function limitTableAddRow(self,id,limit){


    var tableRows = $("#"+id).find("tr[role='row']").length;
    var btnId=$(self)[0]['id'];

    if (tableRows>=limit){
        $("#"+btnId).prop('disabled', true);
    }else{
        $("#"+btnId).prop('disabled', false);
    }





}
function CheckIfSomethingWasChanged(message,self){


    /*   if(typeof $(self).closest("table")[0] != "undefined" && $(self).closest("table")[0].id=="countries_table_generic_table")
       {

           if($(self)[0].type.includes("select"))
           {
               var selected = $(self)[0].selectedIndex;
               if($(self).find("option")[selected].value=="")
                   return;
           }

           if(typeof loading == "undefined"){
              loading = false;
               return;
           }

           //if($(self).closest("tbody").find("tr").length == 1)
             //  return;

       }*/

    /*if(typeof $(self).closest("table")[0] != "undefined" && $(self).closest("table")[0].id=="countries_table_generic_table")
        refreshTableJson_countries_table_generic();

    var info=ajaxApiCallBody("/advisor-api/check-if-something-was-changed","vaccine_advisor",null,false,"",null,false);
    //var info=callAjaxFunction("/advisor-api/check-if-something-was-changed","","vaccine_advisor","","",true,true);
    if ( typeof info != "undefined" && info!="false" && info!="" && info.changes == true){
       return  ConfirmDialog2(message,self);
    }else {
      return true;//   $("#"+formId).find("input,select").attr("readonly",false);
    }
*/

}
function CheckIfValueCanBeSelected(element){

    var selectorArr = element.name.split("_");
    var selector = "";
    var elementNewValue = element.value;
    var elementId = element.id;
    counter = 0;
    counterNumber = 0;
    selectorArr.forEach(function(key){

        if(!isNaN(key)){
            counterNumber=counter;
        }
        if(counterNumber == 0)
            counter++;
    });

    for(i=0;i<counterNumber;i++){
        selector+=selectorArr[i]+"_";
    }
    var arrToCheck = [];

    $("[id^="+selector+"]").each(function(k){
        id = $(this).attr('id');
        value =  $(this).val();
        arrToCheck[id]=value;
    });
    var message = "";
    var tableName = ($(element).closest("table"))[0].id;
    if(tableName.includes("recommended_vaccines") || tableName.includes("refuses_to_accept") ){
        message =  "החיסון שנבחר כבר קיים בטבלה";
    }
    if(tableName.includes("countries")){
        message =  "מדינה זו כבר נבחרה עבור התדריך";
    }
    if(tableName.includes("recommended_tests_table")){
        message =  "הבדיקה שנבחרה כבר קיימת בטבלה";
    }

    for(var key in arrToCheck){
        value = arrToCheck[key];
        if(elementNewValue != "" && key != elementId && elementNewValue == value){
            VaccinationFound(function() {
                $("#"+elementId).val('').trigger("change");
            },message);

        }
    }


}


function VaccinationFound(callback,message) {
    alert(message);
    callback();
}


/*jQuery.loadScript = function (url, callback) {
 jQuery.ajax({
 url: url,
 dataType: 'script',
 success: callback,
 async: true
 });
 }*/

var JavaScript = {
    load: function(src, callback) {
        var script = document.createElement('script'),
            loaded;
        script.setAttribute('src', src);
        if (callback) {
            script.onreadystatechange = script.onload = function() {
                if (!loaded) {
                    callback();
                }
                loaded = true;
            };
        }
        document.getElementsByTagName('body')[0].appendChild(script);
    }
};

if (!String.prototype.includes) {
    String.prototype.includes = function(search, start) {
        'use strict';
        if (typeof start !== 'number') {
            start = 0;
        }

        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}



function TriggerElementWith(runConstraint,idToChange,obj,typeOfTrigger){

    var changeMe= $(obj).closest("tr").find("[id^="+idToChange+"]");
    var id = changeMe[0].id;
    if(runConstraint != undefined &&  runConstraint) {
        $("#"+id).trigger(typeOfTrigger);
    }


}

// Warn if overriding existing method
if(Array.prototype.equals)
    console.warn("Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you've got double inclusions in your code.");
// attach the .equals method to Array's prototype to call it on any array
Array.prototype.equals = function (array) {
    // if the other array is a falsy value, return
    if (!array)
        return false;

    // compare lengths - can save a lot of time
    if (this.length != array.length)
        return false;

    for (var i = 0, l=this.length; i < l; i++) {
        // Check if we have nested arrays
        if (this[i] instanceof Array && array[i] instanceof Array) {
            // recurse into the nested arrays
            if (!this[i].equals(array[i]))
                return false;
        }
        else if (this[i] != array[i]) {
            // Warning - two different object instances will never be equal: {x:20} != {x:20}
            return false;
        }
    }
    return true;
}
// Hide method from for-in loops
Object.defineProperty(Array.prototype, "equals", {enumerable: false});
//addRow_countries_table_generic
//countries_table_generic
function deleteRow(arr, row) {
    arr = arr.slice(0); // make copy
    arr.splice(row - 1, 1);
    return arr;
}
function sleep(milliseconds) {

    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};



function setNewAdvisedProgram(label){
    var spacealign = "   ";
    label = label.replaceAll("\"","").replace("\\","\"");
    $("#vpi").remove();
    if(label!="") {
        $("#vaccine_plan_info").parent().show();
        $("#vaccine_plan_info").parent().find("a").after("<div id='vpi'><span class=''>"+spacealign+vaccinePlanInfo + label+"</span><div>");
    }
    else{
        $("#vaccine_plan_info").parent().show();
        $("#vaccine_plan_info").parent().find("a").after("<div id='vpi'<span class='' >"+spacealign+ vaccinePlanInfo+"</span><div>");
    }

}


function changeEnableStateOfElement(status,obj){
    if(status) {
        $(obj).prop("disabled", false);
    }
    else{
        $(obj).prop("disabled", true);
    }

}

function limitTextInput(element,limit){

    if (! element instanceof jQuery){
        element=$(element);
    }
    if (element!==null && element!==undefined && element instanceof jQuery  && limit>0 ){

        element.on("keydown paste",function(e) {

            var elementText =element.val();
            var length=elementText.length;
            var key=e['key'];   //ArrowRight Backspace ArrowLeft
            var actionType=e['type'];
            var pasted="";

            if (length>=limit && actionType!=="paste" && !(key in {'ArrowRight':1,'Backspace':2,'ArrowLeft':3,'Delete':4,'Control':5,'Alt':6})){
                e.preventDefault();
                //
            }else{
                if (actionType==="paste"){

                    o_event=e.originalEvent;
                    if (o_event.clipboardData && o_event.clipboardData.getData) {
                        pasted=o_event.clipboardData.getData('Text');
                    }
                    else if (window.clipboardData && window.clipboardData.getData) { // IE
                        pasted = window.clipboardData.getData('Text');
                    }

                    //pasted=e.originalEvent.clipboardData.getData('Text');
                    if ((length+pasted.length) >limit){
                        e.preventDefault();
                    }
                }
            }

        });
    }
}
function CreateInSelectBoxOptionsFromNames(triggerName,inController,fromControllers){
    // boxOptionsCounter="";
    $("[name ='"+triggerName+"']").on("click",function(){

        $("[name='"+inController+"']").find("option").each(function(ev){
            $(this).remove();
        });
        var optStart = "<option value=''>בחר</option>";
        $("#"+inController).append(optStart);

        $.each(fromControllers,function(index){
            // if(boxOptionsCounter>17)
            //    boxOptionsCounter =1;
            $("[name='"+this+"']").on("change",function(){//when every on of the items change we need to change the items in the selectbox
                var findLabel= $(this).parent().find("label").text();
                var findValue= $(this).val();
                var findId= $(this)[0].id;
                var found = false;
                $("[id='"+inController+"']").find("option").each(function(ev){

                    if($(this)[0].id==findId+"_temp"){
                        found=true;
                        if(findValue=="") {
                            $(this).remove();
                        }
                        else{
                            findLabelOne=findLabel.split("כמה פעמים הואשמת ב:")[0];
                            findLabelTwo=findLabel.split("כמה פעמים הואשמת ב:")[1];
                            if(findLabelTwo!=undefined) {
                                findLabel = findLabelTwo;
                            }
                            else{
                                findLabel = findLabelOne;
                            }
                            $(this).val(findValue);
                        }
                    }
                });
                if(!found){
                    if(findLabel !='' && findValue!='') {
                        findLabelOne=findLabel.split("כמה פעמים הואשמת ב:")[0];
                        findLabelTwo=findLabel.split("כמה פעמים הואשמת ב:")[1];
                        if(findLabelTwo!=undefined) {
                            findLabel = findLabelTwo;
                        }
                        else{
                            findLabel = findLabelOne;
                        }
                        var opt = "<option value='" + findValue + "' id='"+findId+"_temp'>"+findLabel+"</option>";
                        $("#" + inController).append(opt);
                    }
                }
            });



            var title = $("[name='"+fromControllers[index]+"']").parent().find("label").text();
            titleOne=title.split("כמה פעמים הואשמת ב:")[0];
            titleTwo=title.split("כמה פעמים הואשמת ב:")[1];
            if(titleTwo!=undefined) {
                title = titleTwo;
            }
            else{
                title = titleOne;
            }

            var value = $("[name='"+fromControllers[index]+"']").val();
            var findId= $("[name='"+fromControllers[index]+"']")[0].id;


            if(title !='' && value!='') {
                var opt = "<option value='" + value + "' id='"+findId+"_temp'>"+title+"</option>";
                $("#" + inController).append(opt);
            }

        });
    });

}



function checkYear(element,limit,checkBox) {

    if(checkBox==undefined)
        checkBox=true;
    element.on("change",function(e){

        if(checkBox)
        {
            var checkbox=element.parent().parent().find("input[type='checkbox']")

            var checked = checkbox[0].checked || checkbox[0].hasAttribute('checked');
            if(!checked)
                return;
        }

        var year = element.val();

        if(!$.isNumeric(year))
        {

            element.val("");
            alert("יש להקליד ערך מספרי");
            return;
        }
        var currentYear = (new Date()).getFullYear()
        var answer;
        if ( $.isNumeric(year) && year>=limit && year<=currentYear) {

        } else {
            element.val("");
            alert("יש להזין שנה  הגדולה משנת הלידה של המטופל וקטנה או שווה לשנה נוכחית")
        }

    });
}

$(document).ready(function() {
    $('.tooltipster').tooltipster();
});
