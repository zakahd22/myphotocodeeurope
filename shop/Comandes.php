<?php
//innclude '../sessio.php';
include '../conf.php';
include '../conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
?>
<html>

    <head>
        <?php include 'head.php'; ?>
        <style>
            .comanda{
                width:90%;
                margin-left:5%;
                border: 2px solid gray;
                border-radius: 20px;
                margin-top: 2%;
                padding-bottom: 3%;
            }
            .comanda p {
                width:90%;
                margin-left:5%;
                margin-bottom:3%;
                margin-top:1%;
                font-weight: bold;
                font-size: 13pt;
            }
            .productMiniShop{
                position: relative;
                display: inline;
                float: left;
                width: 50%;
                margin-left:25%;
            }
            .imgProduct{
                position: absolute;        
                width:100%;
                top: 0;
                left: 0;
                z-index: 2;
            }
            .photoMini{
                position:relative;
                z-index:1;
            }
            .blokWhite{
                min-height: 80%;
                margin-top: 5%;
            }
        </style>
    </head>
    <body>
        <div style="height:10%;margin-left:3%;width:30%;background-color:white;display: inline;float: left;margin-bottom:30px;">
            <p>Comanda nº : <input type="text" id='cmda'> <input type='button' value='search' onClick='searchComanda();'></p> 
        </div>
        <div style="height:10%;margin-left:35%;background-color:white;display: inline;float: left;margin-bottom:30px;">
            <p>From <input type="date" id='d1'> to  <input type="date" id='d2'> <input type='button' value='search' onClick='searchComandaDates();'></p> 
        </div>
        <div class='blok blokWhite' id="c" ></div>
        <script>
            var interval;
            $(document).ready(function() {
                get_comandes();
                interval = setInterval(function() {
                    get_comandes();
                }, 300000);
            });
            function get_comandes() {

                $.ajax({
                    url: "get_comandes.php",
                    type: 'POST',
                    success: function(data) {

                        $("#c").html(data);
                        //document.getElementById('comandesOK').html = data;
                    },
                    // Form data
                    cache: false,
                    contentType: 'application/x-www-form-urlencoded'
                });

            }
            function open_Close(c) {
                $("#t" + c).slideToggle(1000);
                $("#con_add" + c).slideToggle(1000);
            }
            function print0(c) {
                var PDF = document.getElementById("iframe" + c);
                PDF.focus();
                PDF.contentWindow.print();
                /*$(".comanda").hide();
                 $(".titles").hide();
                 $("#t" + c).show();
                 $("#con_add" + c).show();
                 $("#c" + c).show();
                 print();
                 $(".comanda").show();
                 $(".titles").show();*/
                var ajaxData = {comanda: c};
                $.ajax({
                    url: 'setToPrinted.php',
                    method: 'POST',
                    success: function() {
                        get_comandes();
                    },
                    data: ajaxData
                });
                //print();
                
            }
            function searchComanda(){
                var cmd = $("#cmda").val();
                if(cmd.length >0){
                window.open("https://myphotocode.com/shop/getcomandasola.php?comanda="+cmd, "Order " + cmd , "height=1000,width=800");
                }
            }
            function searchComandaDates(){
                var d1 = $("#d1").val() +"";
                var d2 = $("#d2").val() +"";
                window.open("https://myphotocode.com/shop/getcomandesdates.php?data1="+d1+"&data2="+d2 , "Order from " + d1 + " to " + d2 , "height=1000,width=800");

            }
        </script>
    </body>

</html>