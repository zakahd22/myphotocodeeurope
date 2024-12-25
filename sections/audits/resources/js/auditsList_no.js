$(function() {
    
    $("#pre").click(function (){
        
        var idHistory = $("#inputInterval").attr("historyid");
        
        var idPb   = $("#idPb").html();
        var stdate = $("#"+idHistory).next().attr("stdate");
        var endate = $("#"+idHistory).next().attr("endate");
        var aunum  = $("#"+idHistory).next().attr("aunum");
        var year   = $("#"+idHistory).next().attr("year");
        
        if(stdate && endate){
            $("#"+idHistory).removeClass("selected");
            $("#"+idHistory).next().addClass("selected");

            callNewAudit(idPb, stdate, endate, aunum, year);
        }
        else{
            /*mostrar alert no existeix pre*/
        }
    });

    $("#next").click(function (){
        
        var idHistory = $("#inputInterval").attr("historyid");
        
        var idPb   = $("#idPb").html();
        var stdate = $("#"+idHistory).prev().attr("stdate");
        var endate = $("#"+idHistory).prev().attr("endate");
        var aunum  = $("#"+idHistory).prev().attr("aunum");
        var year   = $("#"+idHistory).prev().attr("year");
        
        if(stdate && endate){
            $("#"+idHistory).removeClass("selected");
            $("#"+idHistory).prev().addClass("selected");

            callNewAudit(idPb, stdate, endate, aunum, year);
        }
        else{
            /*mostrar alert no existeix next*/
        }
    });
    
    $("#historyTable > .row").click(function(){
        var idPb   = $("#idPb").html();
        var stdate = $(this).attr("stdate");
        var endate = $(this).attr("endate");
        var aunum  = $(this).attr("aunum");
        var year   = $(this).attr("year");
        
        $("#historyTable > .selected").removeClass("selected");
        $(this).addClass("selected");
        callNewAudit(idPb, stdate, endate, aunum, year);
    });
    
});

function callNewAudit(idPb, stdate, endate, aunum, year){
//    
//    console.log(idPb);
//    console.log(stdate);
//    console.log(endate);
//    console.log(aunum);
//    console.log(year);
    
    $("#inputInterval").attr("historyid", year + aunum);
    $("#inputInterval").val(stdate + " - " + endate + "#" + aunum);
    
    var possHistoryTable = $("#historyTable").offset().top;
    var possRow = $('#' + year + aunum).offset().top;
    
    var posFocus = possRow - possHistoryTable;
    
    console.log(possHistoryTable);
    console.log(possRow);
    console.log(posFocus);
    
    $("#historyTable").animate({ scrollTop: posFocus }, 500);
    
    $.ajax({
            url: "sections/audits/functions/auditsManager.php?a=getAudit",
            type: 'POST',
            dataType: 'JSON',
            data:{
                idPb: idPb,
                startDate: stdate,
                endDate: endate,
                AuditsNum: aunum
            },
            success: function(data){
                console.log(data.message);
                $("#audit").html(data.message);
            },
            error: function(data){
                reject('Unknow error, try again later');
            },
        });
}


