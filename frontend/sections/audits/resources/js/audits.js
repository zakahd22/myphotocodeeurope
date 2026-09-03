var serialNumber = '';
var sn_input = 'input[name="serialNumber"]';
var formErrorDiv = '#inputErrorContent';

$(document).ready(function() {
    globalCheckboxListener();
    submitAuditsForm();
    serialNumberAutocomplete();
});

function unsetErrorSerialNumberInputListener(){
//    $('#serialNumberFailError').css('display', 'none');
    hideFormError();
    $('.serialNumberInputDiv input').unbind('click');
    $(sn_input).prop('disabled', false);
}

function setErrorSerialNumberInputListener(){
    $('.serialNumberInputDiv input').click(function(e){
        e.preventDefault();
//        $('#serialNumberFailError').css('display', 'inline');
        showFormError($('#serialNumberFailError').html());

//        setTimeout(function(){
//            $('#serialNumberFailError').css('display', 'none'); 
//        }, 2000);
        $(sn_input).prop('disabled', true);
    });
}

function disableSerialNumber(){
    serialNumber = $(sn_input).val();
    $(sn_input).val('');
    
    setErrorSerialNumberInputListener();
}

function enableSerialNumber(){
    $(sn_input).val(serialNumber);    
    
    unsetErrorSerialNumberInputListener();
}

function globalCheckboxListener(){
    var status;
    var checkbox_object = '#auditGlobal';
    
    $(checkbox_object).click(function(e){
        if(!$(e.target).closest('input[type="checkbox"]').length > 0){
            $(checkbox_object).prop('checked', !$(checkbox_object).prop('checked'));
        }
        status = $(checkbox_object).prop('checked');
        if(status === true){
            disableSerialNumber();
        } else {
            enableSerialNumber();
        }
    });
}

function hideFormError(){
    $(formErrorDiv).animate(
        { opacity:0 }, 
        2000, 
        function(){
//            $(formErrorDiv).html('');    
        }
    );
}

function showFormError(error){
    $(formErrorDiv).html(error);
    $(formErrorDiv).animate(
        { opacity:1 },
        500
    ); 
}

function checkAuditsForm(formData){
    var result = false;
    var data = "Fill the form first!";
    
    var serialNumber = "";
    var auditGlobal = false;
    var auditType = 0;
    
    var individual = false;
    
    formData.forEach(function(item, index){
        if(item.name == 'serialNumber'){
            serialNumber = item.value;
        }
        if(item.name == 'auditGlobal'){
            auditGlobal = item.value;
        }
        if(item.name == 'auditType'){
            auditType = item.value;
        }
    });
    
    if(serialNumber != "" && auditGlobal === false){
        individual = true;
        result = true;
    }
    else if(serialNumber == "" && auditGlobal == "on"){
        individual = false;
        result = true;
    }
    else {
        if(auditType != 0){
            data = 'Insert a Serial Number or select the global option!';
            result = false;
        }
    }

    if(auditType == 0 && result === true){
        data = 'Select an auditType';
        result = false;
    }
    
    if(result === true){
        data = {individual: individual, serialNumber: serialNumber, auditType: auditType};
    }
    
    return {result: result, data: data};
}

function submitAuditsForm(){
    $('.auditFormSubmit').click(function(e){
        e.preventDefault();
        var formData = $('#auditForm').serializeArray();
        var checkedData = checkAuditsForm(formData);
        if(checkedData.result !== false){
            var url = $('#auditForm').attr('url');

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'POST',
                data: checkedData.data,
//                beforeSend: function() {
//                    loading();
//                },
                success: function(result){
                    if(result.success){
                        /* Per a que recordi la secció quan es faci un back */
                        if (backSave) {
                            lastBack++;
                            backHistory[lastBack] = ["1", 'audits', "", ""];
                        }
                        backSave = true;
                        /* ====== */
                        $('.contingut').html(result.message);
                    } else {
                        showFormError(result.message);                        
                    }
                },
                error: function(){
                    showFormError('Something went wrong!');
                },
            });
        } else {
            showFormError(checkedData.data);
        }
    });
}

function serialNumberAutocomplete(){
    $.ajax({
        url: 'sections/audits/functions/auditsManager.php?a=getOwnerPBs',        
        dataType: 'json',
        type: 'POST',
        success: function(data) {
            $(sn_input).autocomplete({
                source: data.message,
                minlenght: 3,
                autoFocus: true,
            })
        }
    });
}