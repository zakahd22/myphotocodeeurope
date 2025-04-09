
jQuery(function(){
    $("#dFrameSel").on("change", function(){
        var array = [];
        var event = $("#dFrameSel").attr("event");
        var frame = $("#dFrameSel").val();
        var array = [event, frame];
        var data = JSON.stringify(array);
       
        $.ajax({
            url: 'edit/functions/events/setFrames.php',
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

function save(id, frames){
    var array = [id, frames];
    var data = JSON.stringify(array);
    $.ajax({
        url: 'edit/functions/events/saveDCFrames.php',
        type: 'POST',
        dataType: 'html',
        data: {
            data : data,
        },
        success: function(data) {
            $("#selectedFrames").html(data);
        }
    });
    $("#selectedFrames").addClass("show");
    $("#selectedFrames").removeClass("hidden");
    return false;  
}


//function saveDCFrames(id){
//    var fr = $("#dFrame").val();
//    if (fr === ""){
//        alert("Please select a DC frame on selectbox , Thanks.");
//    } 
//    else{
//        var ajaxData = {id: id, frame : fr};
//            $.ajax({
//                url: 'edit/functions/events/saveDCFrames.php',
//                type: 'POST',
//                //Ajax events
//                success: function(data) {
//                    if (data === "OK") {
//                        closePopup();
//                        profile("events", "printPhoto", id);
//                    }
//                },
//                data: ajaxData,
//                contentType: 'application/x-www-form-urlencoded'
//            });
//    }
//}


function check(ID, valor, id_check){
    if ($("#" + id_check).hasClass("checked")) { 
        $("#" + id_check).addClass("unchecked");
        $("#" + id_check).removeClass("checked");
        var id = ID;
        var frame = valor;
        var eliminat = 1;
        var array = [id, frame, eliminat];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/saveDBDCFrames.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data,
            },
            success: function(data) {
            }
        });
        array.length=0;
//            i = i +1;
    }
    else{
        $("#" + id_check).addClass("checked");
        $("#" + id_check).removeClass("unchecked");
        var id = ID;
        var frame = valor;
        var array = [id, frame];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/saveDBDCFrames.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data,
            },
            success: function(data) {
            }
        });
        array.length=0;
    }
}

 
function cancel(id_event, frame){
    id = id_event;
    var cancel = 1;
    var eliminat = 0;
    var array = [id, frame, eliminat, cancel];
    var data = JSON.stringify(array);
    $.ajax({
        url: 'edit/functions/events/saveDBDCFrames.php',
        type: 'POST',
        dataType: 'html',
        data: {
            data : data,
        },
        success: function(data) {
        }
    });
 }

function selected_frames(id){
    showPopupv2(true);
    var array = [id];
    var data = JSON.stringify(array);
    
    $.ajax({
        url: 'edit/functions/events/selectedDCFrames.php',
        type: 'POST',
        dataType: 'html',
        data: {
            data : data,
        },
        success: function(data) {
            alert("a");
        }
    });
}


arrayShow = ["1", "4"]
function seguent(final){
    amaga1 = arrayShow[0];
    amaga2 = parseInt(arrayShow[0]) + 1;
    amaga3 = parseInt(arrayShow[0]) + 2;
    mostra1 = arrayShow[1];
    mostra2 = parseInt(arrayShow[1])+1;
    mostra3 = parseInt(arrayShow[1])+2;
    
    $("#" + amaga1).addClass("hidden");
    $("#" + amaga1).removeClass("show");
    $("#" + amaga2).addClass("hidden");
    $("#" + amaga2).removeClass("show");
    $("#" + amaga3).addClass("hidden");
    $("#" + amaga3).removeClass("show");
    
    $("#" + mostra1).addClass("show");
    $("#" + mostra1).removeClass("hidden");
    $("#" + mostra2).addClass("show");
    $("#" + mostra2).removeClass("hidden");
    $("#" + mostra3).addClass("show");
    $("#" + mostra3).removeClass("hidden");
    
    arrayShow[0] = amaga3 + 1;
    arrayShow[1] = mostra3 + 1;
    
    if(arrayShow[1] >= final){
        $("#arrow_right").addClass("hidden");
        $("#arrow_right").removeClass("show");
    }
    
    if (arrayShow[0] >= 4){
        $("#arrow_left").addClass("show");
        $("#arrow_left").removeClass("hidden");
    }; 
}

function anterior(final){
    mostra1 = arrayShow[0] - 3;
    mostra2 = parseInt(arrayShow[0]) - 2;
    mostra3 = parseInt(arrayShow[0]) - 1;
    amaga1 = arrayShow[1] - 3;
    amaga2 = parseInt(arrayShow[1]) - 2;
    amaga3 = parseInt(arrayShow[1]) - 1;
    
    $("#" + amaga1).addClass("hidden");
    $("#" + amaga1).removeClass("show");
    $("#" + amaga2).addClass("hidden");
    $("#" + amaga2).removeClass("show");
    $("#" + amaga3).addClass("hidden");
    $("#" + amaga3).removeClass("show");
    
    $("#" + mostra1).addClass("show");
    $("#" + mostra1).removeClass("hidden");
    $("#" + mostra2).addClass("show");
    $("#" + mostra2).removeClass("hidden");
    $("#" + mostra3).addClass("show");
    $("#" + mostra3).removeClass("hidden");
    
    arrayShow[0] = mostra1;
    arrayShow[1] = amaga1;
    
    if(mostra3 <= 3){
        $("#arrow_left").removeClass("show");
        $("#arrow_left").addClass("hidden");
    };
    if(arrayShow[1] <= final){
        $("#arrow_right").addClass("show");
        $("#arrow_right").removeClass("hidden");
    }
}

function deleteSelectFrames(id){
//    alert("ads");
    if (confirm('Are you sure you want to delete Selecte DC Frames?')) {
        var array = [id];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/deleteSelectFrames.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data,
            },
            success: function(data) {
                
            }
        });
        $("#selectedFrames").addClass("hidden");
        $("#selectedFrames").removeClass("show");
    }
    
}
