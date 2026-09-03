<?php
$USERNAME =  $_SESSION['USERNAME'];
$USERTYPE = $_SESSION['USERTYPE'];

echo "<div class='lgn'><span style='margin-left:25px;'>Welcome $USERNAME</span> <span class='logOut' onclick='javascript:location.href= \"../ajax/logout.php\"'> </div>";
//echo "<a href='../main.php' target='_blank'><img src='../images/logo.png' class='logo'></a>";
echo "<img src='/support/images/logo.png' class='logo'>";
?>
<script>
    $(document).ready(function(){
        $(".logOut").hover(function(){
           $(this).addClass("logRed"); 
        },
        function(){
            $(this).removeClass("logRed"); 
        });
    });
</script>