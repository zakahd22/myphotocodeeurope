var code_shared = "";
var type_shared = 0;
$(function() {
    $(".s_video3D").hide();
    $(".s_photo3D").hide(); 
    
    $("#facebook_share_photo").click(function (){
       face(); 
    });
    
    $(".facebookUploadSDk").click(function () {

        code_shared = $(this).attr("code");
        type_shared = $(this).attr("type_shared");
        establishFacebookConect($(this).attr("PhotoUrl"), $(this).attr("fileType"), $(this).attr("hashtags"), saveStatics);
        
    });
    
    $(".twitterShare").click(function () {
        code_shared = $(this).attr("code");
        type_shared = $(this).attr("type_shared");
        saveStatics();
    });
    
    $(".mail_links_share").click(function () {
        var type = $(this).attr('type');
        var url = $(this).attr('url');
        var event = $(this).attr('event');
        var code = $(this).attr('code');
        
//        showPopupEmail(type, url, event);
        swal({
            title: 'Insert your email',
            input: 'email',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: true,
            preConfirm: function(email) {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "sections/photos/functions/lookPhotos.php",
                        type: 'POST',
                        dataType: 'text',
                        data:{f: 'popupMail', t: type, u: url, e: email, i:event, c:code},
                        success: function(data) {
                            data = jQuery.parseJSON(data);
                            if(data.result == "OK"){
                                resolve(data.message);
                            }
                            else {
                                reject(data.message);
                            }
                        },
                        error: function(data) {
                            reject('Unknow error, try again later');
                        },
                        cache: false,
                        contentType: 'application/x-www-form-urlencoded'
                    });
                })
            },
            allowOutsideClick: false
            }).then(function(data) {
                swal({
                    type: 'success',
                    title: 'Success!',
                    html: data
                })
            })
        });
});

function change_background(url){
    $("body").css('background-image','url('+url+')');
}

function showPopupQuestions(popup_content){
    showPopupv2(false);
    setTitlePopupv2(popup_content.title);
    setContentPopupv2(popup_content.content);
    setButtonsPopupv2(popup_content.buttons);
//    $('#title_popupV2').html(popup_content.title);
//    $('#content_popupV2').html(popup_content.content);
}

function showPopupEmail(type, url, event, code){    
    $.ajax({
        url: 'sections/photos/functions/lookPhotos.php',
        dataType: 'JSON',
        type: 'POST',
        data: {f: 'popupView', t: type, u: url, i:event, c:code},
        success: function(array) {
            showPopupv2();
            getContentData(array);
        }
        
    });
}

function changeBackGround(color, repeat){
    
    $( "body" ).css({
      "background-color": color,
      "background-repeat": repeat
    });
}

function sendEmail(type, url, event){
    var emailUser = $('#email').val();
    $('#mailError').html("");
    
    $.ajax({
        url: "sections/photos/functions/lookPhotos.php",
        type: 'POST',
        dataType: 'text',
        data:{f: 'popupMail', t: type, u: url, e: emailUser, i:event},
        success: function(data) {
            $('#mailError').html(data);
        },
        error: function(data) {
            $('#mailError').html(data);
        },
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
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

function changePhoneSMS(code){ /*No es fa servir enlloc, diria*/
    $('#error_phone').hide();
    if(typeof code == undefined || code.length == 0){
        //error
        return;
    }
    phone = document.getElementById('txt').value;
    if(undefined == typeof phone || phone.length == 0){
        alert("missing phone");
        return;
    }
    pref = document.getElementById('pref').value;
    if(undefined == typeof pref || pref.length == 0){
        //error
        return;
    }
    $.ajax({
        url: 'API/photo/change_phone.php',
        contentType: 'application/json',
        type: 'PUT',
        data : JSON.stringify({code: code, phone:pref+phone}),
        success: function(){
            $('#change_phone').html('<p>Phone successfully changed!</p>');
            $('#phone_number').html(pref+phone);
        }
    }).fail( function( jqXHR, textStatus, errorThrown ) {
        if (jqXHR.status == 400 ||jqXHR.status == 500) {
            $('#error_phone').html('<p style="color:red;">An error occurred when changing the phone!</p>');
            $('#error_phone').show();
        } else {
            alert('Uncaught Error: ' + jqXHR.responseText);
        }
    });
}

/*21-D-03-Total-Share-Whatsapp*/
function avisaSMS(){
    $('#sms').show();
    $('#mail').hide();
    $('#whatsapp').hide();
    
}
//function foraLogin(){
//    $('#login').hide();
//}
/*21-D-03-Total-Share-Whatsapp*/
function avisaMail(){
    $('#mail').show();
    $('#sms').hide();
    $('#whatsapp').hide();
}
/*21-D-03-Total-Share-Whatsapp*/
function avisaWhatsapp(){
    $('#mail').hide();
    $('#sms').hide();
    $('#whatsapp').show();
}

function cancel(){
    // Restore body scroll
    $("body").css("overflow", "");
    
    $('#errorOverlay').fadeOut(200);
    $('.errorsPestanya').hide();
}

function showSuccessToast(message) {
    // Create toast if it doesn't exist
    if ($('#successToast').length === 0) {
        $('body').append('<div id="successToast" class="success-toast"></div>');
    }
    
    // Show the toast with message
    $('#successToast').html(message).fadeIn(300);
    
    // Auto-hide after 3 seconds
    setTimeout(function() {
        $('#successToast').fadeOut(300);
    }, 3000);
}

function envia(code, metode){
    var data = null;
    $('#complet').html('');
    if(typeof code == undefined || typeof metode == undefined) {
        return;
    }
    if(metode == 0) { //email
        data = createEmailDataValues(code, metode);
        /*21-D-03-Total-Share-Whatsapp*/
    } else if(metode == 1) { //sms
        data = createPhoneDataValues(code, metode);        
    } else if(metode == 3) { //whatsapp
        data = createWhatsappDataValues(code, metode);
        
    }
    if(data != null) {
        /*21-D-03-Total-Share-Whatsapp*/
        /**********************************************
         * 
         * TODO: aqui falta control de canvi de telefon si es whatsapp
         */
        var previousContactEmail = getCookie('photo_contact_email');
        var previousContactPhone = getCookie('photo_contact_phone');
        if(typeof previousContactEmail !== 'undefined' || typeof previousContactPhone !== 'undefined'){
            if (metode == 0 && previousContactEmail != "") {
                if(previousContactEmail == data[0]) {
                    sendContactSuccessCallback();
                } else {
                    updatePhotoEmailContactRequest(data[0], previousContactEmail, code);
                }
            } else if ((metode == 1 || metode == 3) && previousContactPhone  != "") {
                if(previousContactPhone == data[0]) {
                    sendContactSuccessCallback();
                } else {                    
                    updatePhotoPhoneContactRequest(data[2]+data[0], previousContactPhone, code);
                }
            } else {
                addNewContact(code, metode, data);
            }
        } else {
            addNewContact(data);
        }
    }
}

function addNewContact(code, metode, data) {
    var dataToSend = [code, metode];
    dataToSend = dataToSend.concat(data);
    setPhotoContactRequest(dataToSend);
}

function updatePhotoEmailContactRequest(email, previousEmail, code){
    if(email != null && previousEmail && code) {
        $.ajax({
            url: 'API/photo/change_email.php',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({'email':email, 'previous_email': previousEmail, 'code': code}),
            success: sendContactSuccessCallback
        }).fail( function( jqXHR, textStatus, errorThrown ) {
            $('#complet').html('<p style="color:red;">An error occurred when changing the email address!</p>');
            $('#complet').show();
        });
    }
}

function updatePhotoPhoneContactRequest(phone, previousPhone, code){
    if(phone != null && previousPhone && code) {
        $.ajax({
            url: 'API/photo/change_phone.php',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({'phone':phone, 'previous_phone': previousPhone, 'code': code}),
            success: sendContactSuccessCallback
        }).fail( function( jqXHR, textStatus, errorThrown ) {
            $('#complet').html('<p style="color:red;">An error occurred when changing the phone!</p>');
            $('#complet').show();
        });
    }
}

function sendContactSuccessCallback(){
    // Close the error popup
    cancel();
    
    // Show success toast
    showSuccessToast('Email saved! We will notify you when your photo is ready.');
}

function setPhotoContactRequest(data) {
    if(data != null) {
        $.ajax({
            url: 'sections/totalshare/gestor.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data: JSON.stringify(data),
            },
            success: sendContactSuccessCallback 
        });
    }
}

function createPhoneDataValues() {
    var dades = document.getElementById('txt').value;
    if (typeof dades == undefined || dades == "") {
        $('#complet').html('Fill in a phone number, please');
        $('#complet').css('text-color','red');
        $('#complet').show();
        return null;
    }
    var prefixNode = document.getElementById('pref');
    var prefixVallue = null;
    if(typeof prefixNode == undefined || prefixNode == null || prefixNode.value == '') {
        if($('#txt').hasClass('phoneNumberWithPrefix')) {   
            prefixVallue = dades.substring(0,1);
            dades = dades.substring(1, dades.length);
            
            
        } else {
            $('#complet').html('Fill in a phone number prefix, please');
            $('#complet').css('text-color', 'red');
            $('#complet').show();
            return null;
           
        }
    }else{
       prefixVallue = prefixNode.value;
    }
    //alert(prefixVallue);
    return [dades, "web", prefixVallue];
}

function createWhatsappDataValues() {
    var dades = document.getElementById('txtwhatsapp').value;
    if (typeof dades == undefined || dades == "") {
        $('#complet').html('Fill in a phone number, please');
        $('#complet').css('text-color','red');
        $('#complet').show();
        return null;
    }
    var prefixNode = document.getElementById('prefwhatsapp');
    var prefixVallue = null;
    if(typeof prefixNode == undefined || prefixNode == null || prefixNode.value == '') {
        if($('#txtWhatsapp').hasClass('phoneNumberWithPrefix')) {
            prefixVallue = dades.substring(0,1);
            dades = dades.substring(1, dades.length);
        } else {
            $('#complet').html('Fill in a phone number prefix, please');
            $('#complet').css('text-color', 'red');
            $('#complet').show();
            return null;
        }
    }else{
       prefixVallue = prefixNode.value;
    }     
    return [dades, "web", prefixVallue];
}

function createEmailDataValues() {
    var dades = document.getElementById('txtmail').value.trim();
    
    // Check if email is empty
    if (typeof dades == undefined || dades == "") {
        $('#complet').html('Please enter an email address');
        $('#complet').css('color', 'red');
        $('#complet').show();
        return null;
    }
    
    // Validate email format with regex
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(dades)) {
        $('#complet').html('Please enter a valid email address');
        $('#complet').css('color', 'red');
        $('#complet').show();
        return null;
    }
    
    return [dades, "web"];
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function eliminar(id) {
    aCanviar = "confirm" + id;
    toKeep = "#keep" + id;
    toDelete = "#delete" + id;
    document.getElementById(aCanviar).value = "delete";
    $(toKeep).hide();
    $(toDelete).show();
}
function guardar(id) {
    aCanviar = "confirm" + id;
    toKeep = "#keep" + id;
    toDelete = "#delete" + id;
    document.getElementById(aCanviar).value = "confirm";
    $(toKeep).show();
    $(toDelete).hide();
}
function confirma(IDs) {
    var confirms;
    for (var i = 0; i < IDs.length; i++) {
        aCanviar = "confirm" + id;
        aCanviar = document.getElementById(aCanviar).value;
        confirms.push(IDs[i], aCanviar);
    }
    var interessats = JSON.stringify(confirms);
    $.ajax({
        url: 'sections/totalshare/gestor.php',
        type: 'POST',
        dataType: 'html',
        data: {
            interessats: interessats,
        },
        success: function () {
            $('#contingutConfirma').html("Your preferences have been saved");
            $('#complet').show();
        }
    });
} 

function pregunta(code){
    $.ajax({
        url: 'sections/photos/functions/checkGestor.php',
        type: 'POST',
        dataType: 'html',
        data: {
            codi: code,
        },
        success: function (resultat) {
            $('#contingutConfirma').html(resultat);
        }
    });

}