<?php 
include 'sessio.php';
include 'conexio.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <link href='https://fonts.googleapis.com/css?family=Quantico:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <script src='js/javascriptFunction.js'></script>
        <link rel=stylesheet href="css/style.css" type="text/css">
        <link rel="shortcut icon" href="favico.ico"/>
        <!--[if lt IE 9]>
        <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->
        
    </head>
    <body>
                <div class='header'>
            <?php
                include './php/header.php';
            ?>
            
        </div>  
            <div id='inicio'>
                 <span class='left first'>
                        <input type='button' class='back' value='BACK' onclick='to("main.php");'>
                    </span>
            
                
                <?php
                if($userType == 1){
                ?>
                <a href="./admin/newQuestion.php"><div class="largeBox" style="background-image: url('images/question2.jpg');margin-top:40px;" > <span class='title1'>ADD NEW QUESTION</span> </div></a>
                <a href="./admin/newSolution.php"><div class="largeBox" style= "background-image: url('images/solution2.jpg');"><span class='title1'>ADD NEW SOLUTION</span></div></a>
                <a href="./admin/lookQuestions.php"><div class="largeBox" style="background-image: url('images/lookQuestions.jpg');"><span class='title1'>QUESTIONS & SOLUTIONS</span></div></a>
                <a href="./admin/assignQuestions.php"><div class="largeBox" style="background-image: url('images/assignQuestion.jpg');"> <span class='title1'>ASSING QUESTIONS</span></div></a>
                
                
                <?php
                }else{
                ?>
                   <p class='title1'>You doesn't a Administrator</p>
                <?php
                }
                ?>
            </div>
       <div class='footer'></div>  
    </body>
</html>
