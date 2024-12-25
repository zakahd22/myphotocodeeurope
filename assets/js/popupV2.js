function showPopupv2(hasClose){
    $("#cover_popupV2-Off").attr('id', 'cover_popupV2');

    if(typeof hasClose == 'undefined') hasClose = true;
    
    $("#cover_popupV2").show();
    if(hasClose){
        //$('#buttons_top').html("<div class='close_popup'></div>");
        
        $(".close_popup").click(function(){
           hidePopupv2();
        });
        
        $(document).keypress(function(e){
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code === 27){
                hidePopupv2();
            }
        });

        $("#cover_popupV2").click(function(e){
            if(e.target !== this){
                return;
            }
            hidePopupv2();
        });
        setButtonsPopupv2("<button class='popup-cancel' onclick='hidePopupv2();'>Close</button>");
    }
}
function hidePopupv2(){
    $("#cover_popupV2").attr('id', 'cover_popupV2-Off');
    $('#title_popupV2').html('');
    $('#content_popupV2').html('');
    $(".swal2-validationerror").html('');
    $(".swal2-validationerror").hide();
    $(".popup-buttons").html('');
}

function getContentData(array){
    //setTitlePopupv2(array.title);
    setContentPopupv2(array.content);
    if("title" in array){
        setTitlePopupv2(array.title);
    }
    if("buttons" in array){
        setButtonsPopupv2(array.buttons);
    }
}

function setTitlePopupv2(data){
    $('#title_popupV2').html(data);
}

function setContentPopupv2(data){
    $('#content_popupV2').html(data);
}

function setButtonsPopupv2(data){
    //<input type="button" class="popup-confirm okB save" onclick="">
    $(".popup-buttons").html(data);
}