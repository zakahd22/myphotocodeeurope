var SN_input = $('input[name=serialNumber]');
var Photobooth = $('#pbListTable > .row');
var infoPB = $('#info');
var auditType = null;

var intervalHistory = "0";
var intervalNumHistory = "0";


var hStartDate = 0;
var hEndDate   = 0;
var hAuditNum  = 0;

$(document).ready(function() {
    getAllAuditsPbsListener();
    filterPhotoboothsBySN();
    setChangePBListener();
    
    setAuditsListers();
});

function setExportCsvListener() {
    $('#exportCsv').click(function(e) {
        e.preventDefault();
        
        var idPb = $(this).attr('idPb');
        var stdate = $(this).attr('stdate');
        var endate = $(this).attr('endate');
        var aunum = $(this).attr('aunum');
        var formattedStdate = encodeURIComponent(stdate);
        var formattedEndate = encodeURIComponent(endate);
        
        var url = 'sections/audits/functions/csvAudits.php?semana=' + aunum + 
                  '&primDia=' + formattedStdate + 
                  '&ultDia=' + formattedEndate + 
                  '&download=true';
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = url;
        document.body.appendChild(iframe);
    });
}

function getAllAuditsPbsListener(){
    $('#allPbs').click(function (){
        $("#pbListTable > .selected").removeClass('selected');

        hStartDate = 0;
        hEndDate   = 0;
        hAuditNum  = 0;
        
        intervalHistory = "0";
        intervalNumHistory = "0";
        
        auditType  = $("#auditSelector").val();
        idOwner        = $("#idOwner").html();
        
        getInfoPB(false, false, false, idOwner);
    });
}

function setAuditsTitleListers(){
    $("#next").click(function (){
        
        var idHistory = $("#inputInterval").attr("historyid");
        
        var idPb        = $("#idPb").html();
        var idOwner        = $("#idOwner").html();
        var stdate      = $("#"+idHistory).next().attr("stdate");
        var endate      = $("#"+idHistory).next().attr("endate");
        var aunum       = $("#"+idHistory).next().attr("aunum");
        var year        = $("#"+idHistory).next().attr("year");
        var num         = $("#"+idHistory).next().attr("num");
        var individual  = $("#"+idHistory).next().attr("individual");
        
        if(stdate && endate){
            $("#"+idHistory).removeClass("selected");
            $("#"+idHistory).next().addClass("selected");
            
            scrollToView($("#"+idHistory).parent(), num);

            callNewAudit(idPb, stdate, endate, aunum, year, individual, idOwner);
        }
        else{
            /*mostrar alert no existeix pre*/
        }
    });

    $("#pre").click(function (){
        
        var idHistory = $("#inputInterval").attr("historyid");
        
        var idPb        = $("#idPb").html();
        var idOwner        = $("#idOwner").html();
        var stdate      = $("#"+idHistory).prev().attr("stdate");
        var endate      = $("#"+idHistory).prev().attr("endate");
        var aunum       = $("#"+idHistory).prev().attr("aunum");
        var year        = $("#"+idHistory).prev().attr("year");
        var num         = $("#"+idHistory).prev().attr("num");
        var individual  = $("#"+idHistory).prev().attr("individual");
        
        if(stdate && endate){
            $("#"+idHistory).removeClass("selected");
            $("#"+idHistory).prev().addClass("selected");
            
            scrollToView($("#"+idHistory).parent(), num);
            
            callNewAudit(idPb, stdate, endate, aunum, year, individual, idOwner);
        }
        else{
            /*mostrar alert no existeix next*/
        }
    });
    
    $('#auditSelector').change(function(){
        var idPb       = parseInt($("#idPb").html().trim());
        idPb = idPb ? idPb : false;
        var idOwner       = parseInt($("#idOwner").html());
        var individual = $("#pbListTable > .selected").attr("individual");
        
        if(!idPb){
            var individual = false;
        }

        hStartDate = 0;
        hEndDate   = 0;
        hAuditNum  = 0;
        
        intervalHistory = "0";
        intervalNumHistory = "0";
        
        auditType = $(this).val();
//alert(idOwner)
        getAuditAndHistory(idPb, individual, true, idOwner);

    });
}

function setAuditsInfoListers(){    
    $("#historyTable > .row").click(function(){
        var idPb        = $("#idPb").html();
        var idOwner        = $("#idOwner").html();
        var stdate      = $(this).attr("stdate");
        var endate      = $(this).attr("endate");
        var aunum       = $(this).attr("aunum");
        var year        = $(this).attr("year");
        var individual  = $(this).attr("individual");
        
        $("#historyTable > .selected").removeClass("selected");
        $(this).addClass("selected");
        
        callNewAudit(idPb, stdate, endate, aunum, year, individual, idOwner);
    });
}

function setAuditsCardListers(){
    $('#gifSendingMail').hide();
    setSendAuditMail();  
    setExportCsvListener();   
}

function setAuditsListers(){
    setAuditsTitleListers();
    setAuditsInfoListers();
    setAuditsCardListers();    
}

function setChangePBListener(){
    var SN_value = '';
    var idPb = '';
    var individual = '';
    
    Photobooth.each(function(){
        $(this).click(function(){
            auditType  = $("#auditSelector").val();
        
            SN_value = $(this).attr('serialNumber');
            idPb = $(this).attr('idPb');
            idOwner = $(this).attr('idOwner');
            individual = $(this).attr('individual');
            
            $("#allPbs").removeClass("selected");
            $(this).siblings().each(function(){
                $(this).removeClass('selected');
            });
            $(this).addClass('selected');
            
            var inthistory = $("#historyTable > .selected").attr('id');
            var numhistory = $("#historyTable > .selected").attr('num');

            if(inthistory){
                intervalHistory = inthistory;
                hStartDate      = $("#"+intervalHistory).attr("stdate");
                hEndDate        = $("#"+intervalHistory).attr("endate");
                hAuditNum       = $("#"+intervalHistory).attr("aunum");
            }
            if(numhistory){intervalNumHistory = numhistory;}
    
            getInfoPB(SN_value, individual, idPb, idOwner);
        });
    });
}

function filterPhotoboothsBySN(){
    var SN_value = '';
    var last_SN_value = '';
    
    SN_input.keyup(function(){
        SN_value = $(this).val();
        searchPhotobooths(SN_value);
    });
}

function searchPhotobooths(SN_value){
//    var All_Photobooths = $('#pbListTable > .row > .text');
    var PBserialNumber = '';
    var PBrand_string = '';
    var PBidPb = '';

    Photobooth.each(function(){
        
        PBserialNumber = $(this).attr('serialNumber');
        PBrand_string = $(this).attr('rand_string');
        PBidPb = $(this).attr('idPb');
        
        if(PBserialNumber.indexOf(SN_value) >= 0 || PBrand_string.indexOf(SN_value.toUpperCase()) >= 0  || PBidPb.indexOf(SN_value) >= 0){
            $(this).removeClass('unmatch');            
            $(this).addClass('match');
        } else {
            $(this).removeClass('match');            
            $(this).addClass('unmatch');            
        }
    });
}

function getInfoPB(serialNumber, individual, idPb, idOwner){
    
    infoPB.html("");
    $('#segon').html("<div class='my-loading-segon'><img src='images/web/loading.gif' class='loading'><p style='width:100%; text-align:center; margin-left: 0px!important;'>Loading the photobooth info...</p></div>");
    $.ajax({
        url: 'sections/audits/functions/auditsManager.php?a=getPBInfo',
        dataType: 'json',
        type: 'POST',
        data: {
            serialNumber: serialNumber,
            idPb: idPb,
            idOwner: idOwner,
            individual: individual
        },
        success: function(data) {
            if(data.success !== false){
                infoPB.html(data.message);                
                getAuditAndHistory(data.success, individual, false, idOwner);
            }
        }
    });
}

function getAuditAndHistory(idPb, individual, change, idOwner){    
    //En el cas de info PB necessari fer-ho abans, però en el cas auditSelector.change(), es necessaria aquesta linia
    
    $('#segon').html("<div class='my-loading-segon'><img src='images/web/loading.gif' class='loading'><p style='width:100%; text-align:center; margin-left: 0px!important;'>Loading audits...</p></div>");
    $.ajax({
        url: 'sections/audits/functions/auditsManager.php?a=getAuditAndHistory',
        dataType: 'json',
        type: 'POST',
        data: {
            idPb: idPb,
            individual:      individual,
            intervalHistory: intervalHistory,
            numhistory:      intervalNumHistory,
            auditType:       auditType,
            change:          change,
            idOwner: idOwner
        },
        success: function(data) {
            if(data.success !== false){
                $('#segon').html(data.message);
                setAuditsListers();
                
                var element = $("#" + intervalHistory).parent();
                var num     = $("#" + intervalHistory).attr("num");
                scrollToView(element, num);
                
                if($("#inputInterval").val() == "ND"){
                    $("#inputInterval").val(hStartDate + " - " + hEndDate + " #" + hAuditNum);
                }
            }
        }
    });
}

function callNewAudit(idPb, stdate, endate, aunum, year, individual, idOwner){
    $("#inputInterval").attr("historyid", year + aunum);
    $("#inputInterval").val(stdate + " - " + endate + " #" + aunum);
    
    $('#csvbutton').attr("text", "#" + aunum);
    $('#csvbutton').attr("onclick", "CSV('" +aunum+"','"+ stdate+"','"+ endate+ "')");
//    $('#csvbutton').attr("onclick", "window.location.href='sections/audits/functions/csvAudits.php?semana="+aunum+"'");
    $("#audit").html("<div class='my-loading-audit'><img src='images/web/loading.gif' class='loading'><p style='width:100%; text-align:center; margin-left: 0px!important;'>Loading audit " + stdate + " - " + endate + " #" + aunum + "...</p></div>");
    $.ajax({
        url: "sections/audits/functions/auditsManager.php?a=getAudit",
        type: 'POST',
        dataType: 'JSON',
        data:{
            idPb: idPb,
            startDate: stdate,
            endDate: endate,
            AuditsNum: aunum,
            individual: individual,
            idOwner: idOwner,
        },
        success: function(data){
            $("#audit").html(data.message);
            setAuditsCardListers();
        },
        error: function(data){
            reject('Unknow error, try again later');
        },
    });
}

function scrollToView(element, num) {
    
    var position = 30 * num - 220;
    
    $("#historyTable").animate({ scrollTop: position }, 0);

    return true;
}

function setSendAuditMail(){
    var mailButton = $('#sendAuditMail');
    var mailButtonParent = mailButton.parent();
    var divContent = mailButtonParent.html();
    
    setSendAuditMailListener(mailButton, mailButtonParent, divContent);
}

function setSendAuditMailListener(mailButton, mailButtonParent, divContent){
//    alert(1);
    mailButton.click(function(e){
        e.preventDefault();
    
        var idPb    = $(this).attr('idPb');
        var stdate  = $(this).attr('stdate');
        var endate  = $(this).attr('endate');
        var aunum   = $(this).attr('aunum');
        
        mailButton.hide();
        $('#gifSendingMail').show();
        $.ajax({
            url: "sections/audits/functions/auditsManager.php?a=sendAuditMail",
            type: 'POST',
            dataType: 'JSON',
            data:{
                idPb: idPb,
                startDate: stdate,
                endDate: endate,
                AuditsNum: aunum
            },

            success: function(data){
                $('#gifSendingMail').hide();
                mailButton.show();
//                setSendAuditMailListener(mailButton, mailButtonParent, divContent);
                if(data.success){
                    swal("Success", data.message, "success");
                } else {
                    swal("Error", data.message, "error");                    
                }
            },
            error: function(data){
                $('#gifSendingMail').hide();
                mailButton.show();
//                setSendAuditMailListener(mailButton, mailButtonParent, divContent);
                swal("Error", 'Unknown error', "error");
            },
        });
    });
}
function CSV(semana, primDia, ultDia, title){
    window.open("sections/audits/functions/csvAudits.php?semana="+semana+"&primDia="+primDia+"&ultDia="+ultDia+"&title="+title, "_blank", "width=300,height=200,menubar=no");
}