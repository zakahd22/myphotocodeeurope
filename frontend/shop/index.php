<html>
    <head>
        <?php 
        include 'head.php'; 
        echo $styles_p;
        ?>
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&language=en"></script>

    </head>
    <body>
        <?php
        include 'header.php';
        ?>
        <div class='blok blokWhite'>
            <p> Consulta la teva comanda.</p>
            <p> <input type='text' id='order' placeholder='ORDER ID' > - <input type='password' id='cmfcode' placeholder='COMFIRMATION CODE'> <input type='button' value='SEARCH' onclick='searchOrder();'></p>
            <div id='orderContainer' style='width:100%;position:relative'></div>
        </div>
    <script>
        function searchOrder(){
            var orderID = $("#order").val();
            var cmfCode = $("#cmfcode").val();
            if(cmfCode==="" || orderID === ""){
                
            }else{
                var ajaxData = {orderID : orderID , cmfCode : cmfCode};
                $.ajax({
                    url: 'searchOrder.php',
                    type: 'POST',
                    //Ajax events
                    success: function(data) {
                        $("#orderContainer").html(data);
                    },
                    // Form data
                    cache: false,
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });
            }
            
        }
    </script>
    </body>    
    <?php include 'footer.php'; ?>
</html>
