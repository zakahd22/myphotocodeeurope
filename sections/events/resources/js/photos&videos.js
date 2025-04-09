$(document).ready(function (){
    $('#downloadAll').click(function(e){
        e.stopPropagation();
        var idEvent = $('#downloadAll').attr('idEvent');
        downloadAllPhotos(idEvent);
    });
    
    $('.paginator_hover').click(function(e){
        e.stopPropagation();
        $(this).addClass('paginator_selected');
        $(this).siblings().each(function(){
            $(this).children("p").removeClass('paginator_selected');
        });
    });
});

var code_shared = "";
var type_shared = 0;
$(function() {
    
    $(".facebookUploadSDk").click(function () {
        photo = $(this).attr("PhotoUrl").split(",");
        url = $(this).attr("url")
        code_shared = $(this).attr("code");
        type_shared = $(this).attr("type_shared");
        album = 1;
        establishFacebookConect(photo, $(this).attr("fileType"), $(this).attr("hashtags"), saveStatics, album, url);
    });
});

function saveStatics(){
//    alert("Type: "+type_shared+" \n PHOTO CODE :"+code_shared);
    $.ajax({
        url: "sections/photos/functions/lookPhotos.php",
        type: 'POST',
        dataType: 'text',
        data:{f: 'saveStd', t: type_shared, c: code_shared},
        success: function(data) {
        },
        error: function(data) {
        },
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function showPopupAllPhotos(title, content){
    showPopupv2();
    if(title === 0){
        $('#title_popupV2').html("<div>Event contains too many photos</div>");
    }
    else if(title === 2){
        $('#title_popupV2').html("<div>Error has ocurred, try again later</div>");
    }
    $('#content_popupV2').html(content);
}

function saveStatics(){
//    alert("Type: "+type_shared+" \n PHOTO CODE :"+code_shared);
    $.ajax({
        url: "sections/photos/functions/lookPhotos.php",
        type: 'POST',
        dataType: 'text',
        data:{f: 'saveStd', t: type_shared, c: code_shared},
        success: function(data) {
        },
        error: function(data) {
        },
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
}

