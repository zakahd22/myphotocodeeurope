$(document).ready(function() {
    $("#rowAddNew").hide();
    
    $("#addnewbtn").click(function(){
        var Dongle = $("#addnewbtn").attr("dong");

        $.ajax({
            url: 'edit/functions/financingCode/getDefaultInfo.php',        
            dataType: 'json',
            type: 'POST',
            data: {
                dades : Dongle
            },
            success: function(data) {
                $("#newDateEnd").val(data[0]);
                $("#newGracePlays").val(data[1]);
                $("#newCode").val(data[2]);
                $("#newPuk").val(data[3]);
            }
        });
        
        $("#rowAddNew").show();
    });
    
    $("#cancelNew").click(function(){
        $("#newDateEnd").val("");
        $("#newGracePlays").val("");
        $("#newCode").val("");
        $("#newPuk").val("");

        $("#rowAddNew").hide();
    });
    
    $("#saveNewRow").click(function(){
        
        var Dongle      = $("#addnewbtn").attr("dong");
        var DateEnd     = $("#newDateEnd").val();
        var GracePlays  = $("#newGracePlays").val();
        var Code        = $("#newCode").val();
        var Puk         = $("#newPuk").val();
        
        var array = [Dongle, DateEnd, GracePlays, Code, Puk];
        var postData = JSON.stringify(array);
        
        console.log(postData);
        
        $.ajax({
            url: 'edit/functions/financingCode/addNew.php',        
            dataType: 'text',
            type: 'POST',
            data: {
                dades : postData
            },
            success: function(data) {
                if(data === "Ok"){
                    edit(71 , Dongle);
                }
                else{
                    alert("Insert Fail");
                }
            }
        });
        
    });
});

function saveRow(id){
    var id          = id;
    var DateEnd     = $("#DateEnd_" + id).val();
    var GracePlays  = $("#GracePlays_" + id).val();
    var Code        = $("#Code_" + id).val();
    var Puk         = $("#Puk_" + id).val();
    
//    var array = [["id",id],["DateEnd", DateEnd],["GracePlays", GracePlays],["Code", Code],["Puk", Puk]];
    var array = [id, DateEnd, GracePlays, Code, Puk];
    
    var postData = JSON.stringify(array);
    
    $.ajax({
        url: 'edit/functions/financingCode/saveRow.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
            
        }
    });
}

function saveInfoFinancing(id){    
    var postData = JSON.stringify($('#financing_info').serializeArray());
    
    $.ajax({
        url: 'edit/functions/financingCode/setFinancingInfo.php',        
        dataType: 'json',
        type: 'POST',
        data: {
            dades : postData
        },
        success: function(data) {
        }
    });
}

function deleteRow(id){
    var Dongle = $("#addnewbtn").attr("dong");
    
    $.ajax({
        url: 'edit/functions/financingCode/delRow.php',        
        dataType: 'text',
        type: 'POST',
        data: {
            dades : id
        },
        success: function(data) {
           if(data === "Ok"){
               edit(71 , Dongle);
           }
        }
    });   
}



function reloadPage(){
    setSection('financingCode', 1);
}