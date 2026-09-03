jQuery(function(){
    $("#dCollagesSel").on("change", function(){
        var array = [];
        var event = $("#dCollagesSel").attr("event");
        var collage = $("#dCollagesSel").val();
        var array = [event, collage];
        var data = JSON.stringify(array);
       
        $.ajax({
            url: 'edit/functions/events/setTemplates.php',
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
        var id = ID;
        var collage = valor;
        var eliminat = 1;
        var array = [id, collage, eliminat];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/saveDBDCTemplates.php',
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
        var id = ID;
        var collage = valor;
        var array = [id, collage];
        var data = JSON.stringify(array);
        $.ajax({
            url: 'edit/functions/events/saveDBDCTemplates.php',
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

function removeTemplates(id){
    var array = [id];
    var data = JSON.stringify(array);
    if (confirm('Are you sure you want to delete DC Collage?')) {
        $.ajax({
            url: 'edit/functions/events/removeTemplates.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data : data
            },
            success: function(data) {
                $("#selectedCollage").html(data);
            }
        });
        hidePopupv2();
    }
    $("#selectedCollage").addClass("show");
    $("#selectedCollage").removeClass("hidden");
} 

function mostraTube(code) {
    openOnlyOpac();
    var video = '<iframe id="youtube-iclame" width="560" height="315" src="https://www.youtube.com/embed/' + code + '?rel=0" frameborder="0" allowfullscreen></iframe>';
    $("#close2Pop").show();
    $("#photoPop").html(video);
    $("#photoPop").fadeIn(500);
    $("#photoPop").css("display", "flex");
}

var f1 = false;
var f2 = false;
var f3 = false;
var f4 = false;
$(document).ready(function() {
    $("#fileCollage1").on("change", function() {
        uploadTemplates(1);
    });
    $("#fileCollage2").on("change", function() {
        uploadTemplates(2);
    });
    $("#fileCollage3").on("change", function() {
        uploadTemplates(3);
    });
    $("#fileCollage4").on("change", function() {
        uploadTemplates(4);
    });
});

function uploadTemplates(cl) {
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

function saveTemplates() {
    var id_title = document.getElementById("id_title").value; 
    if(id_title == 0){
        alert("select one option");
    }else{
        if (f1 && f2 && f3 && f4) {
            var ajaxData = {id_title: id_title};
            $.ajax({
                url: 'edit/functions/events/saveTemplates.php',
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
            hidePopupv2();
        } else {
            alert("Need 4 Frames");
        }
    }
}        

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