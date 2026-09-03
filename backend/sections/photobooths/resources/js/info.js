
$(document).ready(function() {
    autocompleteOwner();
    
    prepare_page();
});

function prepare_page(){
    $("#save").click(function(){
        saveInfoPb();
    });
    $("#cancel").click(function(){
        closePopup();
    });
    
    $("#date_sold").datepicker({
        dateFormat: 'mm/dd/yy',
    });
   
    $("#datetOwner").datepicker({
        dateFormat: 'mm/dd/yy',
    });
    
    $(".delete").click(function(){
        var dongle  = $(this).attr('id');
        var pb      = $(this).attr("pb");
        var sDate   = $(this).attr("sDate");
        var fDate   = $(this).attr("fDate");
        var array   = [dongle, pb, sDate, fDate];
        deleteDonglePairing(array);
    });
    
//    $(".edit_owner").click(function (){
//        var dongle = $(this).attr('id');
//        var owner = $("#ownerNameId").val();
//        var pb = $(this).attr("pb");
//        var array = [dongle, owner, pb];
//        chOwnerDonglePairing(array);
//    });
    
//    $(".edit_distri").click(function (){
//        var dongle = $(this).attr('id');
//        var distributor = $("#distributors").val();
//        var pb = $(this).attr("pb");
//        var array = [dongle, distributor, pb];
//        chDistributorDonglePairing(array);
//    });
    
    $('#addDongleString').keypress(function(e){
        if(e.which == 13){
            $("#acceptAdd").trigger("click");
        }
    });
    
    $("#acceptAdd").click(function (){
        var idPb        = "";
        var dongleStr   = "";
        idPb      = $(this).attr('pb');
        dongleStr = $("#addDongleString").val().toUpperCase();
        
        if(dongleStr.length !== 3){
            swal(
                'Oops...',
                'Dongle string must have 3 characters',
                'error'
            );
        }
        else{
            $.ajax({
                url: 'edit/functions/photobooths/addDongleParing.php',        
                type: 'POST',
                dataType: 'text',
                data: {
                    dongleStr : dongleStr,
                    idPb : idPb
                },
                success: function(data) {
                    if(data == "Ok"){
                        hidePopupv2();
                        $( "#manufac_edit" ).trigger( "click" );
                    }
                    else{
                        swal(
                            'Oops...',
                            'This dongle string not exist',
                            'error'
                        );
                    }
                }
            });
        }
    });
    
    $.ajax({
        url: 'edit/functions/dongles/autocompleteDongles.php',
        dataType: 'json',
        type: 'POST',
        success: function(data) {
            $('#addDongleString').autocomplete({
                source: data,
                minlenght: 1,
                autoFocus: true,
                focus: function (event, ui) {
                    event.preventDefault();
                },
                select: function(event, ui) {
                    event.preventDefault();
                    $('#addDongleId').val(ui.item.value);
                    $('#addDongleString').val(ui.item.label);
                }
            });
        }
    });
    
    $("#addDongleParing").click(function(){
        $("#rowAddNew").toggle();
    });
    
    $("#cancelAdd").click(function (){
        $("#addDongleString").val("");
        $("addDongleId").val("");
        
        $("#rowAddNew").hide();
    });
}

function autocompleteOwner(){
    $.ajax({
        url: 'edit/functions/photobooths/getAutocompleteOwners.php',        
        dataType: 'json',
        type: 'POST',
        success: function(data) {
            $('#ownerName').autocomplete({
                source: data,
                minlenght: 1,
                autoFocus: true,
                focus: function (event, ui){
                    event.preventDefault();
//                    To fill the owner input with the focused value
//                    $('#ownerName').val(ui.item.label);
                },
                select: function(event, ui){
                    event.preventDefault();
                    $('#ownerNameId').val(ui.item.value);
                    $('#ownerName').val(ui.item.label);
                }
            });
        }
    });
}

function deleteDonglePairing(array){
    
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(function () {
        var postData = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/photobooths/deleteDonglePairing.php',        
            dataType: 'text',
            type: 'POST',
            data: {
                dades : postData
            },
            success: function(data) {
                hidePopupv2();
                $( "#manufac_edit" ).trigger( "click" );
            }
        });
    })
}

function chOwnerDonglePairing(array){
    var postData = JSON.stringify(array);
    $.ajax({
        url: 'edit/functions/photobooths/chOwnerDonglePairing.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
           $("#table").html(data);
           prepare_page();
        }
    });
}

function chDistributorDonglePairing(array){
    var postData = JSON.stringify(array);
    $.ajax({
        url: 'edit/functions/photobooths/chDistributorDonglePairing.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
           $("#table").html(data);
           prepare_page();
        }
    });
}

function saveInfoPb(id){
    var postData = JSON.stringify($('#pbs_info').serializeArray());
    $.ajax({
        url: 'edit/functions/photobooths/setPbInfo.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
           profile('photobooths','info', id);
        }
    });
}

function saveInfoFinancing(id){
    var postData = JSON.stringify($('#financing_info').serializeArray());
    
    $.ajax({
        url: 'edit/functions/photobooths/setPbInfo.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
           profile('financingCode');
        }
    });
}

function closePopup() {
    $("#popup").fadeOut(1000);
    $("#content-popup").fadeOut(1000);
    $(".cPopup").html("<img src='images/web/loading.gif' class='loading'>");
}

    
