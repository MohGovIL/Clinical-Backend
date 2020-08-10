

/* on submit function*/

$(window).on('load',function() {




// Before using it we must add the parse and format functions
// Here is a sample implementation using moment.js
    validate.extend(validate.validators.datetime, {
        // The value is guaranteed not to be null or undefined but otherwise it
        // could be anything.
        parse: function(value, options) {
            var format = (typeof options.format !== 'undefined') ? options.format : 'DD/MM/YYYY';
            return (moment.utc(value, format));
        },
        // Input is a unix timestamp
        format: function(value, options) {
            var format = (typeof options.format !== 'undefined') ? options.format : 'DD/MM/YYYY';
            return moment.utc(value).format(format);
        }
    });



    if(typeof formButton !== 'undefined' && formButton) {

        $('#' + formButton).click(function (e) {

            $('#' + formButton).attr('disabled', true);

            if (formButton != undefined && formId != undefined) {

                if (constraints != undefined) {


                    e.preventDefault();

                    var elements = validate.collectFormValues(document.getElementById(formId));
                    //console.log(elements);
                    var errors = validate(elements, constraints, {fullMessages: false});

                    if (typeof  errors !== 'undefined' && errors !== '') {
                        //  console.log(errors);

                        var errorFound=false;
                        for (var key in errors) {

                            if (errors.hasOwnProperty(key)) {
                                element = $('[name="' + key + '"]');


                                appendError(element, key, errors[key][0]);
                                errorFound=true;


                                $("#" + $(element).closest(".tab-pane").attr("id") + "_group").click()
                            }
                        }

                        if (errorFound){
                            $("[role='form']").trigger( "after:error" );
                        }
                    } else {
                        if (typeof beforeSubmit === "function") {
                            beforeSubmit(formId);
                        }
                        else{
                            $('#' + formId).submit();
                        }
                    }

                }
                else {
                    if (typeof beforeSubmit === "function") {
                        beforeSubmit(formId);
                    }
                    else{
                        $('#' + formId).submit();
                    }

                }
            }
            else {
                if (typeof beforeSubmit === "function") {
                    beforeSubmit(formId);
                }
                else{
                    $('#' + formId).submit();
                }
            }
        });
    }



    $("[role='form']").on( "after:error", function( event ) {

        if (typeof afterError!= "undefined"){
            afterError(event);
        }

    });


    function appendError(input, id, message) {


        //bind hide function on focus/select again

        if(document.getElementById('error_' + id) == undefined) {
            $(input).each(function(){

                var input_type=$(this).attr('type');

                if(input_type!='hidden')
                    if(input_type!='checkbox' && input_type!='radio' && input_type !== undefined ) {
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
        $(input).addClass('error-border');
        if($('#error_' + id).length == 1) {
            //console.log("scrolling custom_validate");
            $('#error_' + id).parent().get(0).scrollIntoView();
        }

        $(input).on("change select keypress input blur", function () {
            hideErrors(this, id);
        });

    }

    /*
     * hide error message
     * @param element
     * */
    function hideErrors(input, id) {

        $(input).removeClass('error-border');
        $("#error_" + id).remove();
        $('#' + formButton).removeAttr('disabled');
    }



})





function addConstraints(element_id,constraint)
{
    var exists=false;




    $.each(constraints,function(key,value){
        if(key==element_id){
            exists=true;
        }

    });

    if(!exists) {
        constraints[element_id] = constraint;
    } else {
        $.each(constraint, function(key, value){
            constraints[element_id][key] = value;
        })
    }
    var branch='';
    $.each(constraints,function(key,value){
        if(key==element_id){
                branch=this;
        }
    });

    if(branch!='')
    {
        $.extend( branch, constraint );

    }
}
function removeConstraints(element_id,constraint)
{
    var exists=false;
    $.each(constraints,function(key,value){
        if(key==element_id){
            exists=true;
        }

    });
    if(!exists) {
       return;
    }
    var branch='';
    $.each(constraints,function(key,value){
        if(key==element_id){
            branch=this;
        }
    });

    if(branch!='')
    {
        $.each(branch,function(key,value){
            delete (branch[key]);

            $("#"+element_id).removeClass('error-border');
            $("#error_" + element_id).remove();

        });

    }
}
