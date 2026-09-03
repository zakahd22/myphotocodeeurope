//Variables :
var Menu_3 = true;
var segundos = 900;
var apartat = "";
var apartatTerceMenu = {'owner': 'info', 'photobooths': 'info', 'events': 'info', 'emails': 'QuestionsEmails', 'alerts': 'paperAlert', 'components': 'Info', 'fiproducte': 'Form', 'payxprint': 'dongles', 'manuals': 'photobooth', 'templates' : 'frame'};
var mp;
var mk;
var ajaxEv;
var backHistory = new Array();
var backSave = true;
var lastBack = 0;
var ajaxPeticion;
var request;


$(document).ready(function() {
    
    $("#upgradeAll").on("change", function() {
            $("#allowedIds").val('');
            if($("#allowedIds").prop("readonly") == true){
                $("#allowedIds").prop('readonly', false);                
                $("#allowedIds").attr('placeholder', 'Insert the ids separated by commas');
            }else{
                $("#allowedIds").prop('readonly', true);
                $("#allowedIds").attr("placeholder", "All - This will update all PBs in this version and model");
            }
        
           
        
    });
    
    
    
    setInterval(function() {
        menos1();
    }, 1000);
    
    $('body').bind('mousemove', function(e) {
        tornaAcincMinuts();
    });
    $("#slider").AnySlider();

    //Menu	Funcions de CANVIS DE COLORS ETCETC
    $(".dMenu").hover(function() {
            //alert(0);
            if (!$(this).hasClass("dMenuSelected")) {
                $(this).removeClass();
                $(this).addClass("dMenuOver");
            }
        },
        function() {
            //alert(1);
            if (!$(this).hasClass("dMenuSelected")) {
                $(this).removeClass();
                $(this).addClass("dMenu");
            }
        }
    );

    $(".menu img").click(function() {
        $(".menu img").removeClass();
        $(".menu img").addClass("dMenu");
        $(this).removeClass();
        $(this).addClass("dMenuSelected");
        $("#a1").html($(this).attr("alt"));
    });
    
    
    
    //Fi Menu
});

function funcionsDespresDelCanvi() {
    $(".regBooth").hover(function() {
        $(this).css("background-color", "orange");
    }, function() {
        $(this).css("background-color", "#2DB0E4");
    });
    $(".regEventUL").hover(function() {
        $(this).css("background-color", "orange");
    }, function() {
        $(this).css("background-color", "#6BBA70");
    });
    $(".regEventULRed").hover(function() {
        $(this).css("background-color", "orange");
    }, function() {
        $(this).css("background-color", "#A10326");
    });
    $(".regEventULAmbar").hover(function() {
        $(this).css("background-color", "orange");
    }, function() {
        $(this).css("background-color", "#FFCC33");
    });

    $(".frames").hover(function() {
        $(this).css("background-color", "orange");
        $(this).css("color", "white");
    }, function() {
        $(this).css("background-color", "white");
        $(this).css("color", "black");
    });
    $(".regOwner").hover(function() {
        $(this).css("background-color", "orange");
    }, function() {
        $(this).css("background-color", "#378CE8");
    });
    $(".regCompUL").hover(function() {
        $(this).css("background-color", "orange");
    }, function() {
        $(this).css("background-color", "#8989BA");
    });
    /*     $(".regDongleUL").hover(function() {
     $(this).css("background-color", "orange");
     }, function() {
     $(this).css("background-color", "#5C6883");
     });*/
    $(".link").hover(function() {
        $(this).addClass("link_hover");
    }, function() {
        $(this).removeClass("link_hover");
    });
    $(".link2").hover(function() {
        $(this).addClass("link_hover2");
    }, function() {
        $(this).removeClass("link_hover2");
    });
    
    $(document).ready(function() {
        $.ajaxSetup({ cache: false });
    });
}


function funcionsMenu2() {
//Menu 2
    $(".dMenu2").hover(function() {
        if (!$(this).hasClass("dMenuSelected2")) {
            $(this).removeClass();
            $(this).addClass("dMenuOver2");
        }
    },
            function() {
                if (!$(this).hasClass("dMenuSelected2")) {
                    $(this).removeClass();
                    $(this).addClass("dMenu2");
                }
            });

    $(".menu3 img").click(function() {
        var classe = $(this).attr("class");
        if(classe == "no-menu"){
            return false;
        }
        else{
            $(".menu3 img").removeClass();
            $(".menu3 img").addClass("dMenu2");
            $(this).removeClass();
            $(this).addClass("dMenuSelected2");
        }
        


    });
    //Fi Menu2
}
function funcionsMenu3() {
//Menu 3
    $(".dMenu3").hover(function() {
        if (!$(this).hasClass("dMenuSelected3")) {
            $(this).removeClass();
            $(this).addClass("dMenuOver3");
        }
    },
            function() {
                if (!$(this).hasClass("dMenuSelected3")) {
                    $(this).removeClass();
                    $(this).addClass("dMenu3");
                }
            });

    /*	$(".menu3 div").click(function(){
     Menu_3 = false;
     $(".menu3 div").removeClass();
     $(".menu3 div").addClass("dMenu3");
     $(this).removeClass();
     $(this).addClass("dMenuSelected3");
     $("#a4").html(" - " +$(this).text());
     
     
     });*/
    //Fi Menu3
}
/*Canvis Titol2 ...*/
function title2(s, id) {
    if (s == "alerts") {
        return;
    }
    var ajaxData = {id: id, s: s};
    $.ajax({
        url: 'sections/title.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            $("#a2").html(" - " + data);


        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}


function setSectionAndSubmenu(section){
    
    stopAll();
    
    $("#menu3").html(""); $("#a2").html(""); $("#a3").html("");
    
    list(section);
    submenu(section);
    
}
//Carrega el pagina principal i el submenu del apartat escollit
function setSection(section, typeNextPage, id, trashed) {
    stopAll();

    $("#menu3").html("");
    $("#a2").html("");
    $("#a3").html("");
    
    if (typeNextPage == 1) {
        list(section);
        submenu(section, typeNextPage, id);
    }
    if (typeNextPage == 2) {
        apartat = "profile";
        
        if(typeof trashed !== 'undefined'){
            profile(section, apartatTerceMenu[section], id, '1');
            submenu(section, typeNextPage, id, '1');
        }
        else {
            profile(section, apartatTerceMenu[section], id);
            submenu(section, typeNextPage, id);
        }
    }
    //submenu(section, typeNextPage, id);
}

//Function to reload submenu and change profile, used in payxprint section
function setProfileAndSubmenu(s, p, id){
    var type = 1;
    
    profile(s, p, id);
    switch(p){
        case "dongles":
            getProfileFilter(s, p);
            type = 2;
            break;
            
        case "orders":
            getProfileFilter(s, p);
            type = 3;
            break;
        case "reports":
            type = 4;
            break;
    }
    
    submenu(s, type, id);
}

//Carrega el submenu
function submenu(section, type, id, trashed) {
    if(typeof trashed !== 'undefined'){
        var ajaxData = {menu: type, id: id, trashed: true};
    }
    else {
        var ajaxData = {menu: type, id: id};
    }
    //var ajaxData = {menu: type, id: id};
    $.ajax({
        url: 'sections/' + section + '/submenu.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {

        },
        success: function(data) {
            $(".menu3").html(data);
            funcionsMenu2();
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

//Carrega apartats del perfil
function profile(s, s2, id, trashed) {
    oldAjax();
    hideFilters();
    if (backSave) {
        lastBack++;
        backHistory[lastBack] = ["2", s, s2, id];
    }
    backSave = true;
    title2(s, id);
    $("#a3").html(" - " + s2);
    
    if(typeof trashed !== 'undefined'){
        var ajaxData = {id: id, trashed: true};
    }
    else {
        var ajaxData = {id: id};
    }
    
    request = $.ajax({
        url: 'sections/' + s + '/profile/' + s2 + '.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            //alert('sections/' + s + '/profile/' + s2 + '.php');
            loading();
        },
        success: function(data) {
            $(".contingut").html(data);
            $.removeData();
            funcionsDespresDelCanvi();
        },
//20250124PBinfo            error: function() {
        error: function(jqXHR, exception) {//20250124PBinfo
            var traceStatus = jqXHR.status;//20250124PBinfo
            var trace = exception;//20250124PBinfo
        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function list(s) {

    oldAjax();
    apartat = "list";
    if (backSave) {
        lastBack++;
        backHistory[lastBack] = ["1", s, "", ""];
    }
    backSave = true;
    getFilters(s);
    request = $.ajax({
        url: 'sections/' + s + '/list/list.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            loading();
        }
        ,
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
//20250124PBlist            error: function() {
        error: function(jqXHR, exception) {//20250124PBlist
            var traceStatus = jqXHR.status;//20250124PBlist
            var trace = exception;//20250124PBlist
        },
        // Form data
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function list2(s) {

    oldAjax();
    apartat = "list";
    if (backSave) {
        lastBack++;
        backHistory[lastBack] = ["1", s, "", ""];
    }
    backSave = true;
    getFilters(s);
    request = $.ajax({
        url: 'sections/' + s + '/list2/list.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            loading();
        }
        ,
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        // Form data
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
}

//Carrega el tercer Menu
/*function tercerMenu(s , s2 , id){
 var ajaxData = {id : id , s2:s2};
 $.ajax({
 url: 'sections/'+s+'/'+apartat+'/tercerMenu.php', 
 type: 'POST',
 beforeSend: function() {
 
 }
 ,
 success: function(data) {
 $("#menu3").html(data);
 funcionsMenu3();
 },
 error: function() {
 
 },
 data: ajaxData,
 contentType: 'application/x-www-form-urlencoded'
 });
 
 
 }*/
/*function addNew(s){
 $("#a2").html(" - New - ");
 $.ajax({
 url: 'sections/'+s+'/addNew.php', 
 type: 'POST',
 //Ajax events
 beforeSend: function() {
 loading();
 }
 ,
 success: function(data) {
 $(".contingut").html(data);
 },
 error: function() {
 
 },
 // Form data
 contentType: 'application/x-www-form-urlencoded'
 });
 }*/

function view(section, func, id){
    openPopup();
    var ajaxData = {id: id};
    $.ajax({
        url: 'sections/'+section+'/functions/'+func+'.php',
        type: 'POST',
        success: function(data) {
            $(".cPopup").html(data);
        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function edit(form, id) {
        showPopupv2(true);
        var ajaxData = {form: form, id: id};
        $.ajax({
            url: 'edit/form.php',
            type: 'POST',
            dataType: 'JSON',
            //Ajax events
            success: function(data) {
//                console.log("All Array");
//                console.log(data);
//                console.log("--------------------");
                getContentData(data);
            },
            error: function() {

            },
            // Form data
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    /*
        openPopup();
        var ajaxData = {form: form, id: id};
        $.ajax({
            url: 'edit/form.php',
            type: 'POST',
            //Ajax events
            beforeSend: function() {

            }
            ,
            success: function(data) {
                $(".cPopup").html(data);
            },
            error: function() {

            },
            // Form data
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    */
}

function addNew(form, id) {
    showPopupv2(true);
    //openPopup();
    var ajaxData = {form: form, id: id};
    $.ajax({
        url: 'addNew/form.php',
        type: 'POST',
        dataType: 'JSON',
        //Ajax events
        beforeSend: function() {

        }
        ,
        success: function(data) {
            //$(".cPopup").html(data);
            getContentData(data);
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function openOnlyOpac() {
    $("#popup").fadeIn(1000);
}
function openPopup() {
    $("#popup").fadeIn(1000);
    $("#content-popup").fadeIn(1000);
}
function loadingPopup() {
    $(".cPopup").html("<img src='images/web/loading.gif' class='loading'>");
}
function closePopup() {
    $("#popup").fadeOut(1000);
    $("#content-popup").fadeOut(1000);
    $(".cPopup").html("<img src='images/web/loading.gif' class='loading'>");
}
function loadPopUp() {
    $("#content-popup2").show();
}
function unloadingPopUp() {
    $("#content-popup2").hide();
}
// Canabia la pàgina de la llista 
function setPageList(page, limit, section) {

    var ajaxData = {page: page, limit: limit};
    $.ajax({
        url: 'sections/' + section + '/list/list.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            loading();
        }
        ,
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}
// Canabia la pàgina de la llista 
function setPageList2(page, limit, section , WH) {

    var ajaxData = {page: page, limit: limit , filPage:1};
    $.ajax({
        url: 'sections/' + section + '/list/list.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            loading();
        }
        ,
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function loading() {
    $(".contingut").html("<img src='images/web/loading.gif' class='loading'>");
}
function openQuestions() {
    $("#qText").hide();
    $("#question").removeClass("questions").addClass("questions2");
    $("#question").attr("title", "");
    $(".qst").show();
    $("#closeQ").show();
}
function closeQuestions() {
    $("#qText").show();
    $("#question").removeClass("questions2").addClass("questions");
    $("#question").attr("title", "Click to open questions.");
    $("#closeQ").hide();
    $(".qst").hide();
}
function viewPhoto(code) {
    var ajaxData = {code: code};
    $.ajax({
        url: 'sections/events/functions/getPhoto.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            openOnlyOpac();
        }
        ,
        success: function(data) {
            $("#close2Pop").show();
            $("#photoPop").html(data);
            $("#photoPop").fadeIn(500);
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function closePhoto() {
    //stop youtube videos from playing when the div is hidden
    var video = $("#youtube-iframe").attr("src");
    $("#youtube-iframe").attr("src", "");
    $("#youtube-iframe").attr("src", video);

    //close the div
    $("#close2Pop").hide();
    $("#popup").fadeOut(1000);
    $("#photoPop").fadeOut(500);
    
    //stop any other videos from playing when the div is hidden
    var id = $("#photoPop video").attr('id');
    document.getElementById(id).pause();
}

function photoClick(e){
   e.stopPropagation();
}

function photoClick(e){
   e.stopPropagation();
}

function viewVideo(code) {
    var ajaxData = {code: code};
    $.ajax({
        url: 'sections/events/functions/getVideo.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {
            openOnlyOpac();
        }
        ,
        success: function(data) {
            $("#close2Pop").show();
            $("#photoPop").html(data);
            $("#photoPop").fadeIn(500);
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function createMap(lat, lon) {
    var lc = new google.maps.LatLng(lat, lon);
    var mapOptions = {
        center: lc,
        zoom: 8,
        panControl: false,
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        overviewMapControl: false
    };
    mp = new google.maps.Map(document.getElementById("mp"), mapOptions);
    mk = new google.maps.Marker({
        position: lc,
        map: mp
    });
}
function stopAll() {
    //ajaxEv.abort();
}
function downloadZIP(id, f, e) {
    var ajaxData = {id: id, folder: f, eventID: e};
    $.ajax({
        url: 'sections/events/functions/downloadZIP.php',
        type: 'POST',
        //Ajax events
        beforeSend: function() {

        }
        ,
        success: function(data) {
            if(data.success === true){
                window.location.assign(data.url);
            }
            else{
                alert(data.error);
            }
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        dataType: 'JSON',
        contentType: 'application/x-www-form-urlencoded'
    });
}
function deleteUSB(id, f, e) {
    if (confirm('Are you sure you want to delete this USB stick?')) {
        var ajaxData = {id: id, folder: f, eventID: e};
        $.ajax({
            url: 'sections/events/functions/deleteUSB.php',
            type: 'POST',
            //Ajax events
            beforeSend: function() {

            }
            ,
            success: function(data) {
                profile("events", "photobooths", e);
            },
            error: function() {

            },
            // Form data
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}

function deletebootDCAllowed(id) {
    if (confirm('Are you sure you want to delete this Allowed Boot Upgrade?')) {
        var ajaxData = {id: id};
        $.ajax({
            url: 'sections/upgrade/functions/deleteBootDCAllowed.php',
            type: 'POST',
            //Ajax events
            beforeSend: function() {

            }
            ,
            success: function(data) {
                setSection('upgrade', 1);
            },
            error: function() {

            },
            // Form data
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}
function downloadAllPhotos(id) {
    showPopupv2(false);
    var data = {
        "title":"Compressing...",
        "content":"<div class='popup-text'>Please wait while we compress all your Photos&Videos.</div>",
        "buttons":""
    };
    getContentData(data);
    var ajaxData = {id: id};
    $.ajax({
        url: 'sections/events/functions/downloadAllPhotos.php',
        type: 'POST',
        dataType: 'JSON',
//        beforeSend: function() {
//            loading();
//        }
//        ,
        success: function(data) {
            if(data.response === 1){
                //window.open(data.content,'_blank');
                window.location.assign(data.content);
                hidePopupv2();
                profile("events" , "Photos" , id);
            }
            else{
                showPopupv2(true);
                getContentData(data);
                //showPopupAllPhotos(data.title, data.content);
            }
        },
        error: function() {

        },
        // Form data
        timeout : 180000,
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function shareAllToFacebook(album) {
    window.open(
        'assets/php/templates/facebook-album-uploader_2.php?id=' + album,
        '_blank' // <- This is what makes it open in a new window.
    );
}

function downloadAllEmails(id) {
    loading();
    var ajaxData = {id: id};
    $.ajax({
        url: 'sections/emails/functions/downloadAllEmails.php',
        type: 'POST',
        success: function(data) {
            window.location.assign(data);
            setTimeout(function(){
                setSection("emails", 1);
            }, 2000);
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function downloadAllQuestionsEmails(id) {
    loading();
    var ajaxData = {id: id};
    $.ajax({
        url: 'sections/emails/functions/downloadQuestionsEmails.php',
        type: 'POST',
        success: function(data) {
            window.location.assign(data);
            setTimeout(function() {
                setSection("emails", 2, id);
            }, 2000);
        },
        error: function() {

        },
        // Form data
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function logout() {
    window.location.href = "logout.php";
}
function openLink(link, id) {

    var l2 = link.toLowerCase();
    $("#a1").text(link);
    $(".dMenuSelected").addClass("dMenu").removeClass("dMenuSelected");
    $("#" + link).addClass("dMenuSelected").removeClass("dMenu");
    setSection(l2, 2, id);
}

function deletePrintPhoto(sw, idEvent, id2, text) {
    if (confirm('Are you sure you want to delete ' + text + '?')) {
        loading();
        var ajaxData = {s: sw, event: idEvent, id2: id2};
        $.ajax({
            url: 'sections/events/functions/deletePrintPhoto.php',
            type: 'POST',
            //Ajax events
            beforeSend: function() {

            }
            ,
            success: function(data) {
                profile("events", "printPhoto", idEvent);

            },
            error: function() {

            },
            // Form data
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}



function deleteIMGusb(apartat, eventID, usbID, id2, text, path, booth, screens) {
    var bbb= $("#SELECTEDBOOTH").val();
    if (confirm('Are you sure you want to delete ' + text + '?')) {
        loading();
        var ajaxData = {s: apartat, event: eventID, id2: id2, usbID: usbID, path: path, screens: screens};
        $.ajax({
            url: 'sections/events/functions/deleteIMGusb.php',
            type: 'POST',
            //Ajax events
            beforeSend: function() {

            }
            ,
            success: function(data) {

                profile("events", "photobooths", eventID);
                var f = path;
                setTimeout(function() {
                    $("#setString" + usbID).val("" + apartat);
                    canviaApartat2(usbID, booth, f, apartat ,bbb);
                }, 1500);


            },
            error: function() {

            },
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}
function deleteImgProfile(id) {

    if (confirm('Are you sure you want to delete Profile Image?')) {
        loading();
        var ajaxData = {id: id};
        $.ajax({
            url: 'sections/owner/functions/deleteImageProfile.php',
            type: 'POST',
            success: function(data) {
                if (data === "OK") {
                    profile("owner", "info", id);
                } else {
                    alert("Error , please try again");
                }
            },
            error: function() {

            },
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}
function back() {
    if (lastBack > 1) {
        var seguro;
        var act = backHistory[lastBack - 1];
        submenu(act[1], act[0], act[3]);
        var cap = capitaliseFirstLetter(act[1]);
        if (act[1] == "owner" && userType == 4) {
            $("#a1").html("My Profile");
        } else {
            $("#a1").html(cap);
        }
        $(".dMenuSelected").addClass("dMenu");
        $(".dMenuSelected").removeClass("dMenuSelected");
        $("#" + cap).addClass("dMenuSelected");
        $("#" + cap).removeClass("dMenu");
        if (act[0] == 1) {
            backSave = false;
            list(act[1]);
            $("#a2").html("");
            $("#a3").html("");
        }
        if (act[0] == 2) {
            backSave = false;
            profile(act[1], act[2], act[3]);
            $("#a2").html("");
            $("#a3").html("-" + capitaliseFirstLetter(act[2]));
        }
        if (act[0] == 3) {
            backSave = false;
            openLink(act[1], act[3]);
                        $("#a2").html("");
            $("#a3").html("-" + capitaliseFirstLetter(act[2]));
        }
        if (act[0] == 2) {
            setTimeout(function() {
                seguro = act[2];
                if(seguro === "Events"){
                    seguro = "Events_2";
                }
                if(seguro === "Photos"){
                    seguro = "Photos_2";
                }
                if(seguro === "PhotoBooths"){
                    seguro = "PhotoBooths_2";
                }
                $(".dMenuSelected2").addClass("dMenu2");
                $(".dMenuSelected2").removeClass("dMenuSelected2");
                $("#" + seguro).addClass("dMenuSelected2");
                $("#" + seguro).removeClass("dMenu2");
            }, 400);
            

        }

        if (act[0] == 4) {
            backSave = false;
            getFilters(act[1]);
            filters(act[1], act[3]);
              $("#a2").html("");
                            $("#a3").html("");
        }

        backHistory[lastBack] = null;
        lastBack = lastBack - 1;
    }
}

function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getFilters(section) {
    $.ajax({
        url: 'sections/' + section + '/filters.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            if (data == "") {
                hideFilters();
            } else {
                $("#filters").html(data);
                $("#filters").fadeIn(500);
            }
        },
        error: function() {
            hideFilters();
        },
        // Form data
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function getProfileFilter(section, profileVar) {
    $.ajax({
        url: 'sections/' + section + '/filters.php',
        data: "p=" + profileVar,
        type: 'POST',
        //Ajax events
        success: function(data) {
            if (data == "") {
                hideFilters();
            } else {
                $("#filters").html(data);
                $("#filters").fadeIn(500);
            }
        },
        error: function() {
            hideFilters();
        },
        // Form data
        cache: false,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function filters(section, ajaxData) {
    loading();
    if (backSave) {
        lastBack++;
        backHistory[lastBack] = ["4", section, "", ajaxData];
    }
    backSave = true;
    $.ajax({
        url: 'sections/' + section + '/list/list.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function filtersProfile(section, p, ajaxData) {
    var profileScript;
    
    loading();
    if (backSave) {
        lastBack++;
        backHistory[lastBack] = ["4", section, "", ajaxData];
    }
    backSave = true;
    switch(p){
        case 2:
            profileScript = 'dongles';
            break;
               
        case 3:
            profileScript = 'orders';
            break;
        
        case 4:
            profileScript = 'reports';
            break;
        
        default:
            break;
    }
    
    $.ajax({
        url: 'sections/' + section + '/profile/'+profileScript+'.php',
        type: 'POST',
        //Ajax events
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded',
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        }
    });
}

function filtersPage(section, ajaxData, pages) {
    loading();
    $.ajax({
        url: 'sections/' + section + '/list/list.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}


function hideFilters() {
    $("#filters").hide();
}

function goToSAT() {

}
function oldAjax() {
    if (request !== undefined && request !== null) {
        request.abort();
    }
}

function setPagePhoto(lim, p) {
    var ajaxData = {page: p, limit: lim};
    loading();

    $.ajax({
        url: 'sections/photobooths/profile/Photos.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function setPagePhoto2(lim, p) {
    var ajaxData = {page: p, limit: lim};
    $(".boxLeft").html("");
    $(".boxRight").html("");
    $("#photoBox").html("<img src='images/web/loading.gif' class='loading_'>");
  
    $.ajax({
        url: 'sections/events/profile/Photos.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            $(".contingut").html(data);
            funcionsDespresDelCanvi();
        },
        error: function() {

        },
        cache: false,
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}
function deleteEvent(id) {
    if (confirm("Are you sure you want to delete this event?")) {
        var ajaxData = {id: id};
        $.ajax({
            url: 'sections/events/functions/delete.php',
            type: 'POST',
            //Ajax events
            success: function(data) {
                if (data === "OK") {
                    setSection("events", 1, 0);
                } else {
                    $(".contingut").html(data);
                    alert("Have been a error, please try again");
                }
                // 
            },
            error: function() {

            },
            cache: false,
            data: ajaxData,
            contentType: 'application/x-www-form-urlencoded'
        });
    }
}


function menos1() {
    segundos = segundos - 1;
    if (segundos == 0) {
        logout();

    }

}
function tornaAcincMinuts() {
    segundos = 900;
}

function loadingPopup(){
    $("#contentEdit").hide();
    $("#loadingEdit").show();
}

function unloadingPopup(){
    $("#contentEdit").show();
    $("#loadingEdit").hide();
}

function descompressEvent(id , data){ 
   var ajaxData = {eventid : id , eventDate: data};
   //loading();

   $.ajax({
        url: 'edit/functions/events/descompressEvent.php',
        type: 'POST',
        //Ajax events
        success: function(data) {
            if(data !== ""){
                $("#text_recovering").hide();
                $("#roda").hide();
//              $(".contingut").prepend("<div class='inContent'><div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Warning!</strong> Something wrong happened, please contact <a href='mailto::main@dc-image.com?subject=Issue event " + id + "&body=Hi, something wrong happen with my event (Event id: " + id + ")! Can you check it please?'>main@dc-image.com</a> for technical support. (Event Id: " + id + ")</div></div>"); //old message
                $(".contingut").prepend("<div class='inContent'><div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Warning!</strong> This event is OLD. If you want to download all photos, please contact <a href='mailto::main@dc-image.com?subject=Old event Id: " + id + "&body=Hi, I tried to see/open an old event (Event id: " + id + ")! But could not open it. Can you help / check it please?'>main@dc-image.com</a></div></div>");
            }
            else{
                $("#text_recovering").hide();
                $("#roda").hide();
                profile("events" , "Photos" , id);
            }
        },
        // Form data
        data: ajaxData,
        contentType: 'application/x-www-form-urlencoded'
    });
}

function changeTextos(text, type, event) {
        var c;
        switch (type) {
            case 0:
                c = $("#text0").val();
                break;
            case 1:
                c = $("#text1").val();
                break;
            case 2:
                c = $("#text2").val();
                break;
        }
        if ($(text).val() !== c) {
            var txt = $(text).val();
            var ajaxData = {id: event, texto: txt, type: type};
            $.ajax({
                url: 'sections/events/functions/setEmailText.php',
                type: 'POST',
                success: function(data) {
                    if (data == "OK") {
                        switch (type) {
                            case 0:
                                t=c;
                                break;
                            case 1:
                                t1=c;
                                break;
                            case 2:
                                t2=c;
                                break;
                        }
                    } else {
                        error(data);
                    }
                },
                data: ajaxData,
                contentType: 'application/x-www-form-urlencoded'
            });
        }

    }



