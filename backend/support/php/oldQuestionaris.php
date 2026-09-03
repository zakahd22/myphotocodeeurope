
<!DOCTYPE html>
<html>
    <?php
    include '../sessio.php';
    include '../conexio.php';
    ?>
    <head>
        <link href='https://fonts.googleapis.com/css?family=Quintessential|Kelly+Slab|Oleo+Script' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <script src='../js/javascriptFunction.js'></script>
        <link rel=stylesheet href="../css/style.css" type="text/css">
                <link rel="shortcut icon" href="../favico.ico"/>
        <!--[if lt IE 9]>
        <script src="https://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->

    </head>
    <body>
               <div class='header'>
            <?php
                include '../php/header.php';
            ?>
            
        </div>  
        <span  class='right' style='position:absolute;margin-bottom:10px;'>
            <input type='button' onclick='filters();' value='FILTERS'>
        </span>
        <div id='barralateralF' class="barraRight">
            <span class='left' style='position:absolute;margin-bottom:10px;'>
                <input type='button' class='back' value='SEARCH' onclick='filtersProblems();'>
            </span>
            <span id='closeMiniPopup' onclick='openQorSListNoHtml();'>&nbsp;X&nbsp;</span>
            <div id='barralateralContent'>
                <p>&nbsp;</p>
                <hr>
                <p class='title2'>Dates Interval</p>  
                <p class='text'> Start date :<input type="text" id='date1' class='first' style='width:40%;'></p>  
                <p class='text'> End date :<input type="text" id='date2' class='first' style='width:40%;'></p>
                <hr>
                <?php if ($_SESSION['USERID'] == 9999991) { ?>
                    <p class='title2'>Owner</p>

                    <select size='4' id ='ownerFilter'class='first' style='width:95%;' onchange='setPhotoboothsFilters();'>
                        <option value ='0' selected>--- None ---</option>
                        <?php
                        $CLD_CON->OpenRs("SELECT id , name FROM rentals ORDER BY name");
                        while ($CLD_CON->FetchArray()) {
                            $ownerId = $CLD_CON->GetArrayField("id");
                            $ownerName = $CLD_CON->GetArrayField("name");
                            echo "<option value='$ownerId'> $ownerName </option>";
                        }
                        ?>
                    </select>
                    <hr>
                <?php } ?>
                <p class='title2' >Photobooth Type</p>

                <select size='4' id ='typeFilter' class='first' style='width:95%;' onchange='setPhotoboothsFilters();'>
                    <option value ='0' selected>--- None ---</option>
                    <?php
                    if ($_SESSION['USERID'] == 9999991) {
                        $CLD_CON->OpenRs("SELECT b.name , b.char  FROM booth_types b ORDER BY  b.name");
                    } else {
                        $where = " WHERE bo.owner = " . $_SESSION['USERID'];
                        $CLD_CON->OpenRs("SELECT b.name , b.char  FROM booth_types b RIGHT JOIN App_booths bo ON b.char = bo.type $where GROUP BY bo.type ORDER BY  b.name ");
                    }
                    while ($CLD_CON->FetchArray()) {
                        $boothChar = $CLD_CON->GetArrayField("char");
                        $typeBoothName = $CLD_CON->GetArrayField("name");
                        echo "<option value='$boothChar'> $typeBoothName </option>";
                    }
                    ?>
                </select>

                <hr>
                <p class='title2'>Photobooth</p>

                <select size='4' class='first' id='photoboothsFilters' style='width:95%;'>
                    <option value ='0' selected>--- None ---</option>
                    <?php
                    if ($_SESSION['USERID'] == 9999991) {
                        $CLD_CON->OpenRs("SELECT b.name bName , bt.name btName , b.idBooth FROM App_booths b LEFT JOIN booth_types bt  ON b.type = bt.char ORDER BY  bt.name , b.name");
                    } else {
                        $where = " WHERE b.owner = " . $_SESSION['USERID'];
                        $CLD_CON->OpenRs("SELECT b.name bName , bt.name btName , b.idBooth FROM App_booths b LEFT JOIN booth_types bt  ON b.type = bt.char $where ORDER BY  bt.name , b.name");
                    }

                    while ($CLD_CON->FetchArray()) {
                        $boothId = $CLD_CON->GetArrayField("idBooth");
                        $boothName = $CLD_CON->GetArrayField("bName");
                        $typeName = $CLD_CON->GetArrayField("btName");
                        echo "<option value='$boothId'> $typeName - $boothName </option>";
                    }
                    ?>
                </select>


            </div>

        </div>
        <div id='minipopup'><span id='closeMiniPopup' onclick='closeMiniPopup();'>&nbsp;X&nbsp;</span><div id='contentMiniPopup'></div></div>
        <div id='inicio'>
            <span class='left' style='position:absolute;margin-bottom:10px;'>
                <input type='button' class='back' value='BACK' onclick='to("../main.php");'>
            </span>

            <div class="solvedUnsovedBox" style='margin-top:4%;'> 
                UNSOLVED PROBLEMS 
                <div class='lista' id='unsolvedList'>

                </div>

            </div>
            <div class="solvedUnsovedBox"  style='margin-top:4%;'> 
                SOLVED PROBLEMS
                <div class='lista' id='solvedList'>

                </div>
            </div>
        </div>
        <div class='footer'></div>  
    </body>
    <script>
                $(document).ready(function() {
                    getunsolvedList();
                    getsolvedList();
                    $("#date1").datepicker({
                        inline: true,
                        showOtherMonths: true,
                        altFormat: "dd M yy",
                        dateFormat: "yy-mm-dd"});

                    $("#date2").datepicker({
                        inline: true,
                        showOtherMonths: true,
                        altFormat: "dd M yy",
                        dateFormat: "yy-mm-dd"});
                });

    </script>
</html>