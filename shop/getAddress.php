<?php
require_once '../common/global.php';
require_once G_PATH . 'common/conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
?>
<html>
    <head>
        <?php include 'head.php'; ?>
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&language=en"></script>
        <script type="text/javascript">
            function setColor(nCode, info) {
                $("#color").val(nCode);
                $(".color-box").removeClass("yellowBorder");
                $("#" + nCode).addClass("yellowBorder");
            }
            function toPay() {
                $(".tbl_datos input").removeClass('borderError');
                $(".errorp").text("*");
                $(".errorp").val("*");
                var x = comprobaAddress();
                var y = comprobarContacte();
                if (x && y) {
                    $("#addressForm").submit();
                }
            }




            function comprobaAddress() {
                var x = true;
                var street = $("#street").val() + "";
                if (street.length === 0) {
                    $("#street").addClass('borderError');
                    $("#err_street").text("Street is required field.");
                    x = false;
                }
                var nn = $("#num").val() + "";
                if (nn.length === 0) {
                    $("#num").addClass('borderError');
                    $("#err_number").text("Number is a required field.");
                    x = false;
                }
                var zip2 = $("#zip").val();
                if (zip2.length === 0) {
                    $("#zip").addClass('borderError');
                    $("#err_zip").text("ZIP code is a required field.");
                    x = false;
                }
                var city = $("#city").val();
                if (city.length === 0) {
                    $("#city2").addClass('borderError');
                    $("#err_city").text("City is a required feild.");
                    x = false;
                }
                var state = $("#state").val();
                if (state.length === 0) {
                    $("#state2").addClass('borderError');
                    $("#err_state").text("State is a required field.");
                    x = false;
                }
                return x;
            }
            function comprobarContacte() {
                var x = true;
                var nam = $("#name_Contact").val();
                if (nam.length === 0) {
                    $("#name_Contact").addClass('borderError');
                    $("#err_name").text("Name is a required field.");
                    x = false;
                }
                var lastnam = $("#last_Contact").val();
                if (lastnam.length === 0) {
                    $("#last_Contact").addClass('borderError');
                    $("#err_lname").text("Last Name is a required field.");
                    x = false;
                }
                var phone = $("#phone").val();
                if (phone.length === 0) {
                    $("#phone").addClass('borderError');
                    $("#err_phone").text("Phone is a required field.");
                    x = false;
                } else {
                    if (phone.length !== 10) {
                        $("#phone").addClass('borderError');
                        $("#err_phone").text("The phone is composed of 10 numbers");
                        x = false;
                    } else {
                        if (!(isInt(phone))) {
                            $("#phone").addClass('borderError');
                            $("#err_phone").text("The phone is composed of 10 numbers2");
                            x = false;
                        }
                    }
                }
                var mail = $("#mail_Contact").val();
                if (mail.length === 0) {
                    $("#mail_Contact").addClass('borderError');
                    $("#err_email").text("E-mail is a required field.");
                    $("#err_email2").text("E-mail is a required field.");
                    x = false;
                } else {
                    if (!validateEmail(mail)) {
                        $("#mail_Contact").addClass('borderError');
                        $("#err_email").text("E-mail is not correct");
                        $("#err_email2").text("E-mail is not correct");
                        x = false;
                    } else {
                        var mail2 = $("#mail_Contact2").val();
                        if (mail2 !== mail) {
                            $("#mail_Contact").addClass('borderError');
                            $("#mail_Contact2").addClass('borderError');
                            $("#err_email").text("The E-mails do not match");
                            $("#err_email2").text("The E-mails do not match");
                            x = false;
                        }
                    }
                }
                return x;
            }

            function getInfoZip() {
                var zip = $("#zip").val();
                if (zip !== "") {
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({'address': zip}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            geocoder.geocode({'latLng': results[0].geometry.location}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    if (results[1]) {
                                        var loc = getCityState(results);
                                        if (loc.co === "United States") {
                                            $("#city").val(loc.c);
                                            $("#city2").val(loc.c);
                                            $("#state").val(loc.s);
                                            $("#state2").val(loc.s);
                                            $("#country").html(loc.co);
                                            $("#err_zip").text("*");
                                        } else {
                                            $("input").blur();
                                            $("#city").val("");
                                            $("#city2").val("");
                                            $("#state").val("");
                                            $("#state2").val("");
                                            $("#country").html("United States");
                                            $("#zip").val("");
                                            $("#err_zip").text("The ZIP code " + zip + " are not in United States!");
                                        }
                                    } else {
                                        $("input").blur();
                                        $("#city").val("");
                                        $("#city2").val("");
                                        $("#state").val("");
                                        $("#state2").val("");
                                        $("#country").html("United States");
                                        $("#zip").val("");
                                        $("#err_zip").text("The ZIP code " + zip + " no exist!");

                                    }
                                } else {
                                    $("input").blur();
                                    $("#city").val("");
                                    $("#city2").val("");
                                    $("#state").val("");
                                    $("#state2").val("");
                                    $("#country").html("United States");
                                    $("#zip").val("");
                                    $("#err_zip").text("The ZIP code " + zip + " no exist!");
                                }
                            });
                        } else {
                            $("input").blur();
                            $("#city").val("");
                            $("#city2").val("");
                            $("#state").val("");
                            $("#state2").val("");
                            $("#country").html("United States");
                            $("#zip").val("");
                            $("#err_zip").text("The ZIP code " + zip + " no exist!");

                        }
                    });
                }
            }
            function getCityState(results)
            {
                var a = results[0].address_components;
                var city, state, country;
                for (i = 0; i < a.length; ++i)
                {
                    var t = a[i].types;
                    if (compIsType(t, 'administrative_area_level_1'))
                        state = a[i].long_name; //store the state
                    else if (compIsType(t, 'locality'))
                        city = a[i].long_name; //store the city
                    else if (compIsType(t, 'country'))
                        country = a[i].long_name;
                }

                var results = {c: city, s: state, co: country};
                return results;
            }

            function compIsType(t, s) {
                for (z = 0; z < t.length; ++z)
                    if (t[z] == s)
                        return true;
                return false;
            }
            function validateEmail(email) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }
            function isInt(n) {
                return n % 1 === 0;
            }
        </script>
    </head>
    <body>
        <?php
        include 'header.php';
        $photo = $_SESSION['photo'];

        echo "<div class='blok blokWhite'>";
        if (isset($_POST['sub'])) {
            $id = $_SESSION['product_id'];
            $code = $_SESSION['product_code'];
            // $id = $_POST['productID'];
            //$code = $_POST['productCode'];
            $CLD_CON->OpenRs("SELECT * FROM SHP_products WHERE code='$code' AND id=$id");
            if ($CLD_CON->FetchArray()) {
                $nom = $CLD_CON->GetArrayField("name");
                $shop = $CLD_CON->GetArrayField("shop");
                $preu = $CLD_CON->GetArrayField("preu");
                $styles_p = $CLD_CON->GetArrayField("style");
                $qty = $_POST['qty'];
                echo "<style>$styles_p</style>";

                $CLD_CON2->OpenRs("SELECT currency , impostos FROM SHP_Shops WHERE id=$shop");
                if ($CLD_CON2->FetchArray()) {
                    $impost = $CLD_CON2->GetArrayField("impostos");
                    $currency = $CLD_CON2->GetArrayField("currency");
                }
                $d = date("Y-m-d H:i:s");
                $comanda = $CLD_CON3->ExecuteInsert("INSERT INTO SHP_Comandes (n2,impostos,estat,currency,shop,fecha) VALUES(1,$impost,0 ,'$currency' ,$shop , '$d')");
                $p_cm = $CLD_CON3->Execute("INSERT INTO SHP_Comandes_Products (comanda , producte , qty , preu , photoCode) VALUES($comanda , $id , $qty , $preu , '$photo')");

                $image = $_POST['productIMG'];
                $CLD_CON2->OpenRs("SELECT * FROM SHP_caracteristiques WHERE producte=$id");

                $selectedOptions .= "<p> Product : $nom</p>";
                while ($CLD_CON2->FetchArray()) {
                    $option_id = $CLD_CON2->GetArrayField("id");
                    $option_name = $CLD_CON2->GetArrayField("nom");
                    $option_code = $CLD_CON2->GetArrayField("code");
                    $selectedOptions .= "<p>$option_name - " . $_POST[$option_code] . "</p>";
                    $CLD_CON3->Execute("INSERT INTO SHP_cm_pr_ch (id_cp , caract , valor , codevalor) VALUES($p_cm , $option_id , '$option_name' , '" . $_POST[$option_code] . "')");
                }
                $selectedOptions .= "<p> Quantity :" . $qty . "</p>";


                echo "<div class='imgContainer'>";
                echo "<img  src='$image' class='taza'>";
                $CLD_CON2->OpenRs("SELECT e.id , e.start_date FROM photos p LEFT JOIN events e ON p.event_id=e.id WHERE p.code='$photo'");
                if ($CLD_CON2->FetchArray()) {
                    $date_e = $CLD_CON2->GetArrayField("start_date");
                    $id_e = $CLD_CON2->GetArrayField("id");
                    $img_url = "events/$date_e$id_e/$photo.jpg";
                }
                echo "<div class='imgTransform'>";
                echo "<img  src='$img_url'>";
                echo "</div>";
                echo "<div class='WhiteDiv'></div>";
                echo "<div class='WhiteDivLateral'></div>";
                echo "</div>";


                echo "<div  class='contentDiv'>";
                //echo $selectedOptions;
                echo "<form method='POST' action='toPay.php' id='addressForm'>";
                echo "<input type='hidden' name='image_p' value='$image'>";
                echo "<input type='hidden' name='comanda' value='$comanda'>";
                echo "<div class='totalDiv'>";
                echo "<h3>Contact</h3>";
                echo "</div>";
                echo "<div class='formDiv halfDiv'>";
                echo "<p><input type='text' id='name_Contact' name='name_Contact' placeholder='Name'></p><p class='errorp' id='err_name'>*</p>";
                echo "</div>";
                echo "<div class='formDiv halfDiv'>";
                echo "<p><input type='text' id='last_Contact' name='last_Contact' placeholder='Last Name'></p><p class='errorp' id='err_lname'>*</p>";
                echo "</div>";

                echo "<div class='formDiv halfDiv'>";
                echo "<p> <input type='text' id='mail_Contact' name='mail_Contact' placeholder='E-mail' ></p><p class='errorp' id='err_email'>*</p>";
                echo "</div>";
                echo "<div class='formDiv halfDiv'>";
                echo "<p> <input type='text' id='mail_Contact2' name='mail_Contact2' placeholder='Repeat E-mail' ></p><p class='errorp' id='err_email2'>*</p>";
                echo "</div>";
                echo "<div class='formDiv halfDiv'>";
                echo "<p><input type='text' id='phone' name='phone' placeholder='Phone'></p><p class='errorp' id='err_phone'>*</p></p>";
                echo "</div>";
                echo "<div class='formDiv totalDiv'>";
                echo "<h3>Address</h3>";
                echo "</div>";
                echo "<div class='formDiv thirdDiv'>";
                echo "<p><input type='text' id='street' name='street' placeholder='Street'></p><p class='errorp' id='err_street'>*</p>";
                echo "</div>";
                echo "<div class='formDiv oneDiv'>";
                echo "<p><input type='text' id='num' name='num'   placeholder='Number'></p><p class='errorp' id='err_number'>*</p>";
                echo "</div>";
                echo "<div class='formDiv halfDiv'>";
                echo "<p> <input type='text' id='zip'  name='zip' onblur='getInfoZip();' placeholder='ZIP CODE'> </p><p class='errorp' id='err_zip'>*</p>";
                echo "</div>";
                echo "<div class='formDiv halfDiv'>";
                echo "<p> <input type='text' id='city2'  placeholder='City Name' disabled><input type='hidden' name='city' id='city'> </p><p class='errorp' id='err_city'>*</p>";
                echo "</div>";
                echo "<div class='formDiv totalDiv'>";
                echo "<p><input type='text' id='state2'  placeholder='State Name' disabled><input type='hidden' name='state' id='state'></p><p class='errorp' id='err_state'>*</p>";
                echo "</div>";
                echo "<div class='formDiv totalDiv'>";
                echo "<p>Country : Only in United States of America </p>";
                echo "</div>";
                //echo "<table style='width:60%;'>";

                /*
                  echo "<tr><td colspan=2><h3>Address Contact</h3></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>Name</p></td><td class='tbl_datos'><p><input type='text' id='name_Contact' name='name_Contact' placeholder="Name"></p><p class='errorp' id='err_name'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>Last Name</p></td><td class='tbl_datos'><p> <input type='text' id='last_Contact' name='last_Contact'></p><p class='errorp' id='err_lname'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>Phone</p></td><td class='tbl_datos'><p> <input type='text' id='phone' name='phone'></p><p class='errorp' id='err_phone'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>E-mail</p></td><td class='tbl_datos'><p> <input type='text' id='mail_Contact' name='mail_Contact' ></p><p class='errorp' id='err_email'>*</p></td></tr>";

                  echo "<tr><td colspan=2><h3 style='margin-top:30px;'>Address</h3></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>Street</p></td><td class='tbl_datos'><p><input type='text' id='street' name='street'></p><p class='errorp' id='err_street'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>Number</p></td><td class='tbl_datos'><p><input type='text' id='num' name='num'style='width:60px;'></p><p class='errorp' id='err_number'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>ZIP code</p></td><td class='tbl_datos'><p> <input type='text' id='zip'  name='zip' onblur='getInfoZip();'> </p><p class='errorp' id='err_zip'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>City</p></td><td class='tbl_datos'><p> <input type='text' id='city' name='city'> </p><p class='errorp' id='err_city'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>State</p></td><td class='tbl_datos'><p> <input type='text' id='state' name='state'></p><p class='errorp' id='err_state'>*</p></td></tr>";
                  echo "<tr><td class='tbl_tit'><p>Country</p></td><td class='tbl_datos'><p> <span id='country'> United States of America</span></p></td></tr>";
                  echo "</table>"; */
                echo "<p><input type='button' onclick='toPay()' value='To Pay' class='nextButton'></p>";
                echo "</form>";
                echo "</div>";
            } else {
                echo "Error 2";
            }
        } else {
            echo "Error";
        }
        echo "</div>";
        ?>
    </body>
    <?php include 'footer.php'; ?>
</html>


