/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var x2;
var popupStatus = 0;

function getSerialize(formulari) {
    var str = $(formulari).serialize();
    return str;
}

function ajax(url, div) {
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            var x = xmlhttp.responseText;
            if (x == "OK") {
                $(".content-popup").hide(500);
                $(".popup").hide(500);
            } else {
                error(x);
            }
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);

}
function ajax_popup(url, div) {
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            var x = xmlhttp.responseText;
            $("." + div).html(x);
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);

}
function login() {
    var user = $("#user").val();
    var pass = $("#pswd").val();
    var ajaxData = {pswd: pass, username: user};
    $.ajax({
        
        url: 'login.php',
        type: 'POST',
        //Ajax events
        success: function (data) {
            var url = data.trim();
            if (url == 'main.php') {
                window.location.assign(url);
            } else {
                $("#login-error").html(data);
            }


        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });

}
function lookPhoto(sino) {
    if ($("#photocode").val() == "") {
        error("PLEASE, WRITE YOUR PHOTO CODE");
    } else {
        var url = 'sections/photos/functions/lookPhotos.php?' + $("#photoCodeForm").serialize();
        if (sino !== undefined) {
            if (sino == 1) {
                url = 'sections/photos/functions/lookPhotos.php?' + $("#photoCodeForm").serialize() + "&qr=1";
            } else {
                url = 'sections/photos/functions/lookPhotos.php?' + $("#photoCodeForm").serialize() + "&v=" + sino;
            }
        }

        $.ajax({
            url: url, //Server script to process data
            type: "POST",
            success: function (data) {
                if (data.indexOf("#Error") >= 0) {
                    x2 = data.split("#Error");
                    var i;
                    var alerts = false;
                    for (i = 0; i < x2.length; i++) {
                        if (x2[i] == " 00: Confirm Alert") {
                            var alerts = true;
                        }
                    }
                    if (alerts == false) {
                        error(x2[1]);
                    } else {

                        code = $("#photocode").val();
                        pregunta(code);
                        $('#contingutConfirma').height(300);
                        toConfirmAlert();
                    }
                } else {
                    $("#pagePHOTO").html(data);
                    toPhotoCode();
                    /*/SI es iphone o ipad
                     var isiPad = navigator.userAgent.match(/iPad/i) != null;
                     var ua = navigator.userAgent;
                     var isiPad = /iPad/i.test(ua) || /iPhone OS 3_1_2/i.test(ua) || /iPhone OS 3_2_2/i.test(ua);
                     if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
                     if(confirm("Would you like to download the MyPhotoCodeApp")){
                     location.href= 'https://itunes.apple.com/app/id736602319';
                     }
                     }
                     if(navigator.userAgent.match(/Android/i)) {
                     if(confirm("Would you like to download the MyPhotoCodeApp")){
                     window.location.href="market://details?id=com.myphotocode";
                     }
                     }*/

                    // si es iphone o ipad fi
                }
            },
            error: function (data) {
                alert("Failed");
            }
        });
    }
}
function lookPhoto2(code) {
    $("#AllPhotoDiv").html("<img src='images/web/loadingGif.gif' style='width:30%;margin-left:35%;margin-top:10%;'>");
    $.ajax({
//        url: 'sections/photos/functions/lookPhotos.php?photocode=' + code+"&p2=1", //Server script to process data
        url: 'sections/photos/functions/lookPhotos.php?photocode=' + code + '&p2=1', //Server script to process data
        type: 'POST',
        data: {f: "changePublicPhoto"},
        success: function (data) {
            console.log("TRACE 0");
            console.log(data);
            if (data.indexOf("#Error:") >= 0) {
                console.log("TRACE 3");
                x2 = data.split("#Error:");
                error(x2[1]);
            } else {
                console.log("TRACE 1");
                $("#photo_content").html(data);
                console.log("TRACE 2");
                closeAllPhotosPopup();
            }

        },
        error: function (data) {

        }
    });
}



function lookAllPhotos() {
    $(".allPhotos").slideDown(1000);
    $(".background-photo").slideUp(1000);
    $(".bSp").hide();
}
function toLoginCode() {
    $('.login').show();
    $("#pageLogin").animate({width: '100%'}, 1000);
    $("#pageCode").animate({width: '0%'}, 850);
    $("#code-error").html("");
    $("#login-error").html("");
    amaga = setInterval(amagaL,1001);
    function amagaL(){
        $('.login1').hide();
    }
    clearInterval(amaga);
//    if(("#pageLogin").width='100%'){        
//        $('#pageCode').hide();
//    }

}
function toCodeLogin() {
    $('.login1').show();
    $("#pageCode").animate({width: '100%'}, 1000);
    $("#pageLogin").animate({width: '0%'}, 850);
    $("#code-error").html("");
    $("#login-error").html("");
    amaga = setInterval(amagaPC,1001);
    function amagaPC(){
        $('.login').hide();
    }
    clearInterval(amaga);
//    if(("#pageLogin{").width='100%'){        
//        $('#pageLogin').hide();
//    }
}
function toPhotoCode() {
   
    $("#pagePHOTO").animate({width: '101%'}, 1000);
    $("#pageCode").animate({width: '0%'}, 850);
    $(".banner").show();
    $("#backoption").show();
    $("#errors").html("");
    $("#code-error").html("");
    $("#login-error").html("");
    $("#logoMyPhotoCode").fadeOut(500);
    
}
function toCodePhoto() {
//    $("#pageCode").animate({width: '100%'}, 1000);
//    $("#pagePHOTO").animate({width: '0%'}, 850);
//    $(".banner").hide();
//    $("#backoption").hide();
//    $("#likeFB").hide();
//    $(".content-popup").hide(500);
//    $(".popup").hide(500);
//    $("#errors").html("");
//    $("#code-error").html("");
//    $("#login-error").html("");
//    $("#shopAll").hide();
//    front = true;
//    $(".bSp").hide();
//    setTimeout(function() {
//        $("#logoMyPhotoCode").fadeIn(500);
//    }, 750);
   
    
    //location.reload
    
    //no volem un reload, no té sentit mantenir el photocode perque tornarà on som
    window.location.href = 'https://www.myphotocode.com';
}


function loadPopup()
{

    if (popupStatus == 0)
    {

        var windowWidth = $(document).width();
        var windowHeight = $(document).height();
        var popupWaitWidth = $("#popupWait").width();
        var popupWaitHeight = $("#popupWait").height();

        $("#backgroundPopup").css({
            "width": "100%",
            "height": windowHeight
        });

        $("#backgroundPopup").css({"opacity": "0.9"});
        $("#backgroundPopup").fadeIn({duration: 200});

        var scrollTop = $(document).scrollTop();

        $("#popupWait").css({
            "position": "absolute",
            "top": "50%",
            "left": "50%",
            "margin-left": popupWaitWidth / -2,
            "margin-top": (popupWaitHeight / -2) + scrollTop
        });

        $("#popupWait").fadeIn({duration: 200});

        popupStatus = 1;

    }

}


function disablePopup()
{

    if (popupStatus == 1)
    {
        $("#backgroundPopup").fadeOut({duration: 200});
        $("#popup").fadeOut({duration: 200});
        $(".content-popup2").hide();
        popupStatus = 0;
    }

}


function centerPopup() {
    //var windowWidth = $(document).width();
    //var windowHeight = $(document).height();
    var popupWidth = $("#popup").width();
    var popupHeight = $("#popup").height();

    var scrollTop = $(document).scrollTop();

    $("#popup").css({
        "position": "absolute",
        "top": "50%",
        "left": "50%",
        "margin-left": popupWidth / -2,
        "margin-top": (popupHeight / -2) + scrollTop
    });

    $("#popupWait").hide();
    $("#popup").fadeIn({duration: 200});

}

function startPopup(content, id) {
    var file;
    switch (content) {
        case "video" :
            file = "popup_video.php?id=" + id;
            break;

        case "video3D":
            file = "popup_video.php?id=" + id + "&d3=1";
            break;

        case "email" :
            file = "popup_email.php?id=" + id;
            break;

        case "email2":
            file = "popup_email.php?id=" + id + "&video=1";
            break;

        case "email3D" :
            file = "popup_email.php?id=" + id + "&D3=1";
            break;

        case "emailv3d" :
            file = "popup_email.php?id=" + id + "&v3d=1";
            break;

        case "emailGIF" :
            file = "popup_email.php?id=" + id + "&gif=1";
            break;
    }

    popupOpen2("assets/php/templates/" + file);
    /*$("#popup").load("https://www.myphotocode.com/assets/php/templates/" + file, function() {
     centerPopup();
     });*/

}

/*
 * Funció comentada per canvi d'enviament, tot esta a lookPhoto
 * 
 * 
 
 function startPopup(content, id){
 alert("HERE");
 var file;
 switch (content){
 case "video" :
 file = "popup_video.php?id=" + id;
 break;
 
 case "video3D":
 file = "popup_video.php?id=" + id + "&d3=1";
 break;
 
 case "email" :
 file = "popup_email.php?id=" + id;
 break;
 
 case "email2":
 file = "popup_email.php?id=" + id + "&video=1";
 break;
 
 case "email3D" :
 file = "popup_email.php?id=" + id + "&D3=1";
 break;    
 
 case "emailv3d" :
 file = "popup_email.php?id=" + id + "&v3d=1";
 break;
 
 case "emailGIF" :
 file = "popup_email.php?id=" + id + "&gif=1";
 break;
 }
 
 popupOpen2("assets/php/templates/" + file);
 /*$("#popup").load("https://www.myphotocode.com/assets/php/templates/" + file, function() {
 centerPopup();
 });*/

//}
function askQuestions() {
    var form = getSerialize("#EventQuestions");
//    var url = "includes/ajax/funcions/askQuestions.php?" + form;
//    ajax(url, "errors");
    $.ajax({
        url: "includes/ajax/funcions/askQuestions.php?" + form,
        dataType: 'text',
        type: 'GET',
        success: function (data) {
            if (data == "OK") {
                hidePopupv2();
            } else {
                alert(data);
            }
        }
    });
}


function error(text) {
    $("#pError").html(text);

    // Prevent body scroll on mobile (without breaking layout)
    $("body").css("overflow", "hidden");
    
    $("#errorOverlay").fadeIn(300);
    $("#pestError").css("display", "block");

//    setTimeout(function () {
//        errorClose();
//    }, 4000);

}
function errorClose() {

    $("#pestError").animate({
        padding: "0px"

    }, 500, function () {
        $("#pestError").slideUp(500);

        $("#pError").html("");
    });

}

function popupOpen2(url) {
    ajax_popup(url, "content-popup");
    $(".popup").slideDown(500);
    $(".content-popup").slideDown(500);
    $(".popup-close").show();
    popupStatus = 1;
}
function popupOpen2(url) {
    ajax_popup(url, "content-popup2");
    $(".popup").slideDown(500);
    $(".content-popup2").slideDown(500);
    $(".popup-close").show();
    popupStatus = 1;
}

function popupClose() {
    $(".content-popup").slideUp(50);
    $(".content-popup2").slideUp(50);
    $(".popup").slideUp(500);
    $(".popup-close").hide();
    popupStatus = 0;
}

function forgotPass() {
    $(".info").hide();
    var username = $("#forgot").val();

    if (username === "") {
        error("Complete the new username field.");
    } else {
        $.ajax({
            url: 'support/ajax/forgot_password.php?username=' + username, //Server script to process data
            type: 'POST',
            success: function (data) {
                if (data.indexOf("#Error:") >= 0) {
                    x2 = data.split("#Error:");
                    forgotError(x2[1]);
                } else {
                    forgotError(data);
                }
            },
            error: function (data) {
                error(data);
            }
        });
    }
}

//function forgotPass(){
////    alert("123");
//    $(".info").hide();
//    var username = $("#forgot").val();
//    if(username === ""){
//        alert('not username');
//    }else{
//        var array = [username];
//        var data = JSON.stringify(array);
//        $.ajax({
//            url: 'support/ajax/forgot_password_n.php',
//            type: 'POST',
//            dataType: 'html',
//            data: {
//                data : data,
//            },
//            success: function(data) {
//                alert(data);
//            }
//        });
//    }
//}

function forgotError(text) {
    $(".info").html(text);
    $(".info").fadeIn(500);

}

//function restartPass(){
//    $.ajax({
//        url: 'restartpass2.php', //Server script to process data
////            type: 'POST',
////            dataType: 'html',
////            data: {
////                data : data
////            },
//            success: function (data) {
//                alert(3);
//////                if (data.indexOf("#Error:") >= 0) {
//////                    x2 = data.split("#Error:");
//////                    forgotError(x2[1]);
//////                } else {
//////                    forgotError(data);
//////                }
//            },
//    })
//}
//function restartPass() {
//    var pass = $("#pass").val();
//    var passR = $("#passR").val();
//    if (pass === "") {
//        forgotError("Complete the new password field.");
//        return;
//    }
//    if (passR === "") {
//        forgotError("Complete the repeat password field.");
//        return;
//    }
//    if (passR === pass) {
//        var code = $("#code").val();
//        var array = [code, pass];
//        var data = JSON.stringify(array);
//        $.ajax({
//            url: 'restartpass.php', //Server script to process data
//            type: 'POST',
//            dataType: 'html',
//            data: {
//                data : data
//            },
//            success: function (data) {
////                forgotError("password restart successful");
//                $("div").removeClass("info").addClass("info_restart");
//                forgotError("password restart successful");
////                if (data.indexOf("#Error:") >= 0) {
////                    x2 = data.split("#Error:");
////                    forgotError(x2[1]);
////                } else {
////                    forgotError(data);
////                }
//            },
//            error: function (data) {
//                error(data);
//            }
//        });
//    } else {
//        forgotError("The passwords do not match.");
//        return;
//    }
////
//}
//
//
//
//
//
//function tw(typeInfo) {
//    $.ajax({
//        url: "sections/photos/functions/twitter.php?typeInfo=" + typeInfo, //Server script to process data
//        type: 'POST',
//        success: function(data) {
//            var url = "";
//            if (typeInfo === 5) {
//                url = $("#photoTwitter").val();
//            }
//            if (typeInfo === 8) {
//                url = $("#videoTwitter").val();
//            }
//            window.open(url, '_blank');
//        },
//        error: function(data) {
//            error(data);
//        }
//    });
//}

var front = true;
var seguro = 1;
function setToFront() {
    if (seguro == 1) {
        $("#f3d").hide();
        seguro = 0;
        if (front) {
            $("#no3dphoto").animate({
                left: "100px",
                top: "-20px"

            }, 1000);

            $("#d3photo").animate({
                left: "-100px",
                top: "-0px"

            }, 1000);

            $("#band").animate({
                left: "100px",
                top: "-20px"
            }, 1000);

            $("#band3d").animate({
                left: "-100px",
                top: "-0px"
            }, 1000);

            setTimeout(function () {
                $("#band").css("z-index", "9");
                $("#no3dphoto").css("z-index", "8");
                $("#band3d").css("z-index", "11");
                $("#d3photo").css("z-index", "10");
            }, 1000);
            $("#sharePhoto2D").hide();
            $("#shareVideo2D").hide();
            $("#sharePhoto3D").fadeIn(1000);
            $("#shareVideo3D").fadeIn(1000);
            $("#sharePhoto3D").css("display", "");
            $("#shareVideo3D").css("display", "");




            $("#no3dphoto").animate({
                left: "-12%",
                top: "0px"
            }, 1000);

            $("#d3photo").animate({
                left: "0px",
                top: "20px"
            }, 1000);
            $("#band").animate({
                left: "-12%",
                top: "0px"
            }, 1000);

            $("#band3d").animate({
                left: "0px",
                top: "20px"
            }, 1000);
            //classe 3D
            $(".banner3D").css({
                margin: "0px 0px 0px -137px"
            });

            front = false;
        } else {


            $("#d3photo").animate({
                left: "100px",
                top: "-20px"

            }, 1000);

            $("#no3dphoto").animate({
                left: "-100px",
                top: "-0px"

            }, 1000);

            $("#band").animate({
                left: "-100px",
                top: "-0px"

            }, 1000);

            $("#band3d").animate({
                left: "100px",
                top: "-20px"

            }, 1000);

            setTimeout(function () {
                $("#band3d").css("z-index", "9");
                $("#d3photo").css("z-index", "8");

                $("#band").css("z-index", "11");
                $("#no3dphoto").css("z-index", "10");

            }, 1000);
            $("#shareVideo3D").hide();
            $("#sharePhoto3D").hide();
            $("#shareVideo2D").fadeIn(1000);
            $("#sharePhoto2D").fadeIn(1000);
            $("#sharePhoto2D").css("display", "");
            $("#shareVideo2D").css("display", "");

            $("#d3photo").animate({
                left: "-12%",
                top: "0px"
            }, 1000);

            $("#no3dphoto").animate({
                left: "0px",
                top: "20px"
            }, 1000);
            $("#band").animate({
                left: "0px",
                top: "20px"
            }, 1000);
            $("#band3d").animate({
                left: "-12%",
                top: "0px"
            }, 1000);

            //classe 2D
            $(".banner3D").css({
                margin: "70px 0px 0px -137px"
            });

            front = true;
        }

        setTimeout(function () {
            seguro = 1;
            $("#f3d").fadeIn(1500);
        }, 2000);

    }
}

function openPhotosAllPhotos(event) {
    $(".popup").slideDown(500);
    $("#AllPhotoDiv").html("<img src='images/web/loadingGif.gif' style='width:30%;margin-left:35%;margin-top:10%;'>");
    $("#AllPhotoDiv").removeAttr("style");
    var ajaxData = {event: event};
    $.ajax({
        url: 'sections/photos/functions/getAllPhotos.php',
        type: 'POST',
        //Ajax events
        success: function (data) {
            $("#AllPhotoDiv").html(data);
        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });

}
function closeAllPhotosPopup() {
    $("#AllPhotoDiv").html("");
    $("#AllPhotoDiv").hide();
    $(".popup").slideUp(500);
}

// Noves funcions per confirmar avisos 06/02/2019 ##############################

function toConfirmAlert() {
    $("#pageAlerts").animate({width: '100%'}, 1000);
    $("#pageCode").animate({width: '0%'}, 850);
    $("#code-error").html("");
    $("#login-error").html("");
    $("contingutConfirma").width("400");
    $("contingutConfirma").height("300");

}
function backToStart() {
    $("#pageCode").animate({width: '100%'}, 1000);
    $("#pageAlerts").animate({width: '0%'}, 850);
    $("#code-error").html("");
    $("#login-error").html("");
    $("contingutConfirma").width("400");
    $("contingutConfirma").animate({height: '300'}, 1000);

}

function avisaSMS(code) {
var contingut ="<center>"+code+"</center><br/><table border=0 width='100%'><tr><td style='padding-left: 20;'>Introduce your country code:</td></tr><tr><td align='center'><input type='text' id='pref' value='+'></td></tr><tr><td style='padding-left: 20;'>Introduce your phone number:</td></tr><tr><td align='center'><input type='tel' id='txt'></td></tr></table><br/><input type='submit' onclick='envia("+'"'+code+'"'+", 1);' id='enviamail' value='Send'>";

$("#contingutConfirma").html(contingut);
}
/*21-D-03-Total-Share-Whatsapp*/
function avisaWhatsapp(code) {
var contingut ="<center>"+code+"</center><br/><table border=0 width='100%'><tr><td style='padding-left: 20;'>Introduce your country code:</td></tr><tr><td align='center'><input type='text' id='pref' value='+'></td></tr><tr><td style='padding-left: 20;'>Introduce your phone number:</td></tr><tr><td align='center'><input type='tel' id='txt'></td></tr></table><br/><input type='submit' onclick='envia("+'"'+code+'"'+", 1);' id='enviamail' value='Send'>";

$("#contingutConfirma").html(contingut);
}

//function foraLogin(){
//    $('#login').hide();
//}
function avisaMail(code) {

var contingut ="<center>"+code+"</center><br/><table border=0 width='100%'><tr><td style='padding-left: 20;'>Introduce your e-mail:</td></tr><tr><td align='center'><input type='email' id='txtmail'></td></tr></table><br/><input type='submit' onclick='envia("+'"'+code+'"'+", 0);' id='enviamail' value='Send'>";

$("#contingutConfirma").html(contingut);
$("#contingutConfirma").height('220');

}
function cancel() {
    $('.errorsPestanya').hide();
}

function envia(code, metode) {
    var missatges = "";
    if (metode == 0) {
        if (document.getElementById('txtmail').value == "") {
            $('#txtmail').css({
                'border-color': "crimson",
                'border-width': 'thick'});
            missatges += "You must enter a valid e-mail address";
        } else {
            $('#txtmail').css({
                'border-color': "initial",
                'border-width': '2px'});
            dades = document.getElementById('txtmail').value;
            var array = [code, metode, dades, "WEB"];
        }
    } else {
        /*21-D-03-Total-Share-Whatsapp*/
        if (metode === 1 || metode === 3) {
            if (document.getElementById('txt').value === "") {
                $('#txt').css({
                    'border-color': "crimson",
                    'border-width': 'thick'});
                missatges = "You must enter a valid phone number";
            } else {
                $('#txt').css({
                    'border-color': "initial",
                    'border-width': '2px'});
                dades = document.getElementById('txt').value;
            }
            
            var preftext=document.getElementById('pref').value;
            if (preftext == "" || preftext == "+") {
                $('#pref').css({
                    'border-color': "crimson",
                    'border-width': 'thick'});
//                alert(missatges);
                missatges = "You must enter a valid country-code";
//                alert(missatges);
            } else {
                $('#pref').css({
                    'border-color': "initial",
                    'border-width': '2px'});
                pref = preftext;
            }
            dades.replace(/\s/g, "");
            pref.replace(/\s/g, "");
            var array = [code, metode, dades, "WEB", pref];
        }
    } 
   // missatges = toString(missatges);
    if (missatges != "") {
        error(missatges);
        return false;
    }
//    dades = document.getElementById('txt').value;
//    if (dades == "") {
//        dades = document.getElementById('txtmail').value;
//        var array = [code, metode, dades, "web"];
//    } else {
//        pref = document.getElementById('pref').value;
//        var array = [code, metode, dades, "web", pref];
//    }
    var data = JSON.stringify(array);
    if (missatges == "") {
        $.ajax({
            url: 'sections/totalshare/functions/insertFromWeb.php',
            type: 'POST',
            dataType: 'html',
            data: {
                data: data
            },
            success: function () {
                var contingut = "<center>Your preferences have been saved</center><div class='botons aball'><input type='button' onclick='backToStart();' value='Back'></div>";
                $('#contingutConfirma').html(contingut);
                $('#contingutConfirma').height("100");
            }
        });
    } else {
        error(missatges);
    }
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
    var confirms = new Array();
    for (var i = 0; i < IDs.length; i++) {
        aCanviar = "confirm" + IDs[i];
        aCanviar = document.getElementById(aCanviar).value;
        confirms.push(IDs[i], aCanviar);
    }
    var interessats = JSON.stringify(confirms);
    $.ajax({
        url: 'sections/totalshare/functions/updateFromWeb.php',
        type: 'POST',
        dataType: 'html',
        data: {
            interessats: interessats,
        },
        success: function () {
            var contingut="<center>Your preferences have been saved</center><div class='botons aball'><input type='button' onclick='backToStart();' value='Back'></div>";
            $('#contingutConfirma').html(contingut);
            $('#contingutConfirma').height(100);
            //$('#complet').show();
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

// Noves funcions per confirmar avisos 06/02/2019 ##############################