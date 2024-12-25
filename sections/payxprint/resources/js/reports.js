var startDate = '';
var endDate = '';
var ownerId = '';
var checkExport = '';
    
$(document).ready(function() {
    $('img').attr("draggable", "False");
    
    $("#startDate").datepicker({
        dateFormat: 'mm/dd/yy',
        minDate: '01/01/2016',
        maxDate: 0,
        onSelect: function(dateText, inst){
            $("#endDate").datepicker( "option", "minDate", dateText);
            $("#endDate").datepicker( "refresh" );
        },
    });
   
    $("#endDate").datepicker({
        dateFormat: 'mm/dd/yy',
        minDate: 0,
        maxDate: 0
    });

    autocompleteOwner();
    
    $('.okButton').click(function() {
        if(checkValues()){
            getOrders();
        }
    });
    
    $('#startDate, #endDate').each(function(){
        $(this).keypress(function(e) {
            if(e.which === 13){
                if(checkValues()){
                    getOrders();
                }
            }
        });
    });
});

function autocompleteOwner(){
    $.ajax({
        url: 'sections/payxprint/profile/reports.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            a: 'filterOwner'
        },
        success: function(data) {
            $('#ownerName').autocomplete({
                source: data,
                minlenght: 1,
                autoFocus: true,
                focus: function (event, ui) {
                    event.preventDefault();
//                    To fill the owner input with the focused value
//                    $('#ownerName').val(ui.item.label);
                },
                select: function(event, ui) {
                    event.preventDefault();
                    $('#ownerNameId').val(ui.item.value);
                    $('#ownerName').val(ui.item.label);

                    getOrders();
                }
            }).keypress(function(e){
                if(e.which === 13) {
                    if(checkValues()){
                        getOrders();
                    }
                }
            });
        }
    });
}

function getValues(){
    startDate = $("#startDate").val();
    endDate   = $("#endDate").val();
    ownerId   = $('#ownerNameId').val();
}

function checkValues(){
    var result = false;
    if(($("#startDate").val() == "") && ($("#ownerName").val() == "") && ($("#endDate").val() == "")){
        alert("First filter what you want to output");
    }
    else if($("#startDate").val() == ""){
        alert("Missing from date");
    }
    else{
        result = true;
    }
    
    return result;
}

function getOrders(){
    if(checkValues()){
        getValues();

        var postData = null;
        if($('#ownerName').val() != ''){
            postData = {a: 'getOrders', o: ownerId, sd: startDate, ed: endDate};
        }
        else{
            postData = {a: 'getOrders', sd: startDate, ed: endDate};        
        }
        $.ajax({
            url: 'sections/payxprint/profile/reports.php',
            dataType: 'text',
            type: 'POST',
            data: postData,
            success: function(data) {
                $(".filteredContent").html(data);            

                $('.importCSV').click(function(e) {
                    e.preventDefault();
                    exportCSV();
                });
            }
        });
        
        if($('#checkExport').is(':checked')){
            exportCSV();
        }
    }
}

function exportCSV(){
    var url = '';
    if($('#ownerName').val() != ''){
        url = 'sections/payxprint/profile/reports.php?a=createFile&o=' + ownerId + '&sd=' + startDate + '&ed=' + endDate
    }
    else{
        url = 'sections/payxprint/profile/reports.php?a=createFile&sd=' + startDate + '&ed=' + endDate
    }
    
    window.location.href = url;
}