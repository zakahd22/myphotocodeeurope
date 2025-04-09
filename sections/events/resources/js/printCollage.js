
jQuery(function(){
    
    $("#dEventSel").on("change", function(){
        var id_event = $("#dEventSel").val();
        $("#dCollagesSelTem").on("change", function(){
            var array = [];
            var event = $("#dCollagesSelTem").attr("event");
            var collage = $("#dCollagesSelTem").val();
            var array = [event, collage, id_event];
            var data = JSON.stringify(array);

            $.ajax({
                url: 'edit/functions/events/setCollages.php',
                dataType: 'html',
                type: 'POST',
                data: {
                    dades : data
                },
                success: function(data) {
                    $("#contentDcFrames").html(data)
                }
            });
        });
    });
    $("#dCollagesSel").on("change", function(){
       
        var array = [];
        var event = $("#dCollagesSel").attr("event");
        var collage = $("#dCollagesSel").val();
        var array = [event, collage];
        var data = JSON.stringify(array);

        $.ajax({
            url: 'edit/functions/events/setCollages.php',
            dataType: 'html',
            type: 'POST',
            data: {
                dades : data
            },
            success: function(data) {
                $("#contentDcFrames").html(data)
            }
        });
    });
});

function check(ID, valor, id_check){
    if ($("#" + id_check).hasClass("checked")) { 
        $("#" + id_check).addClass("unchecked");
        $("#" + id_check).removeClass("checked");
        var ID_E = $("#dEventSel").val();
        var id = ID;
        var collage = valor;
        var cancel = 0;
        var eliminat = 1;
        var array = [id, collage, eliminat, cancel, ID_E];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/saveDBDCColages.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data,
            },
            success: function(data) {
                    $("#marcat").html(data)
            }
        });
    }
    else{
        $("#" + id_check).addClass("checked");
        $("#" + id_check).removeClass("unchecked");
        var ID_E = $("#dEventSel").val();
        var id = ID;
        var collage = valor;
        var cancel = 0;
        var eliminat = 0;
        var array = [id, collage, eliminat, cancel, ID_E];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/saveDBDCColages.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data,
            },
            success: function(data) {
                    $("#marcat").html(data)
            }
        });
        array.length=0;
    }
}

function cancel_collage(id_event, collage){
    var ID_E = $("#dEventSel").val();
    var id = id_event;
    var cancel = 1;
    var eliminat = 0;
    var array = [id, collage, eliminat, cancel, ID_E];
    var data = JSON.stringify(array);
    $.ajax({
        url: 'edit/functions/events/saveDBDCColages.php',
        type: 'POST',
        dataType: 'html',
        data: {
            data : data,
        },
        success: function(data) {
                $("#marcat").html(data)
        }
    });
}
 
function save_collage(id){
    var ID_E = $("#dEventSel").val();
    var array = [id, ID_E];
    var data = JSON.stringify(array);
    $.ajax({
        url: 'edit/functions/events/saveDCCollage.php',
        type: 'POST',
        dataType: 'html',
        data: {
            data : data
        },
        success: function(data) {
            $("#selectedCollage").html(data);
        }
    });
    $("#selectedCollage").addClass("show");
    $("#selectedCollage").removeClass("hidden");
} 

nou_num = 0;
arrayShow = ["1", "2"];
function seguent(final){
    
    amaga = arrayShow[0];
    mostra = arrayShow[1];
    
    $("#" + amaga).addClass("hidden");
    $("#" + amaga).removeClass("show");
    
    $("#" + mostra).addClass("show");
    $("#" + mostra).removeClass("hidden");
    
    arrayShow[0] = arrayShow[1];
    arrayShow[1] = parseInt(arrayShow[1]) + 1;
    
    if(arrayShow[0] == final){
        $("#arrow_right").addClass("hidden");
        $("#arrow_right").removeClass("show");
    }
    
    if (arrayShow[0] >= 1){
        $("#arrow_left").addClass("show");
        $("#arrow_left").removeClass("hidden");
    }; 
}

function anterior(final){
    
    mostra = parseInt(arrayShow[0])-1;
    amaga = parseInt(arrayShow[1])-1;
    
    $("#" + amaga).addClass("hidden");
    $("#" + amaga).removeClass("show");
    
    $("#" + mostra).addClass("show");
    $("#" + mostra).removeClass("hidden");
    
    arrayShow[0] = parseInt(arrayShow[0])-1;
    arrayShow[1] = parseInt(arrayShow[1])-1;
    
    if(mostra == 1){
        $("#arrow_left").removeClass("show");
        $("#arrow_left").addClass("hidden");
    };
    if(amaga <= final){
        $("#arrow_right").addClass("show");
        $("#arrow_right").removeClass("hidden");
    }
}

function deleteSelectCollages(id){
    if (confirm('Are you sure you want to delete Selecte DC Collages?')) {
        var array = [id];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/deleteSelectCollages.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data,
            },
            success: function(data) {  
            }
        });
        $("#selectedCollage").addClass("hidden");
        $("#selectedCollage").removeClass("show");
    }
   
}

$(document).ready(function() {
    $("#fileCollage1").on("change", function() {
        uploadCollages(1);
    });
    $("#fileCollage2").on("change", function() {
        uploadCollages(2);
    });
    $("#fileCollage3").on("change", function() {
        uploadCollages(3);
    });
    $("#fileCollage4").on("change", function() {
        uploadCollages(4);
    });
});

function uploadCollages(cl) {
    if ($("#fileCollage" + cl).val() === "") {
    } else {
        $("#collageForm" + cl).ajaxForm({
            success: function(e) {
                if (e === "ERROR") {
                    alert("Error");
                } else {
                    $("#fP" + cl).html("<img src='printPhoto/tmp/" + e + "' style='width:100%; height:100%;'>");
                    $("#fP" + cl).show(500);
                    switch (cl) {
                        case 1:
                            f1 = true;
                            break;
                        case 2:
                            f2 = true;
                            break;
                        case 3:
                            f3 = true;
                            break;
                        case 4:
                            f4 = true;
                            break;
                    }
                }
            },
            error: function(e) {
            }
        });
        $("#collageForm" + cl).submit();

    }
}

function saveCollages($ID) {
    var ajaxData = {id: id};
    $.ajax({
        url: 'edit/functions/events/saveCollages.php',
        type: 'POST',
        success: function(data) {
                if (data === "OK") {
                    closePopup();
                    profile("events", "printPhoto", id);
                }
        },
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}
