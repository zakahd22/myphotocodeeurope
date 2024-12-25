<?php
include './conf.php';
include './conexio.php';
?>
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
        <img src="images/logo.png" id ="logoMyPhotoCode" style='margin-top:5%;margin-bottom:3%;'>
        <div class="page2" id="pageLogin" >
            <div class="register">
                <div class='box2'>
                    <u>Company</u>
                    <p>
                        <label for="companyName">Name*</label>
                        <input type='text' name='companyName' id='companyName' style='float:right;'>
                    </p><p>
                        <label for="address"> Address*</label>
                        <input type='text' name='address' id='address' style='float:right;'>
                    </p><p>
                        <label for="num"> Num</label>
                        <input type='text' name='num' id='num' style='width: 20%;margin-left: 16.5%;margin-right: 12%;'>
                        <label for="zip"> Zip Code*</label>
                        <input type='text' name='zip' id='zip' style='width: 20%;margin-left: 3.5%;'>
                    </p><p>
                        <label for="city"> City*</label>
                        <input type='text' name='city' id='city' style='float:right;'>
                    </p><p>                 
                        <label for="state"> State*</label>
                        <input type='text' name='state' id='state' style='float:right;'>
                    </p><p>
                        <label for="country" > Country*</label>
                        <input type='text' name='country' id='country' style='float:right;'>
                    </p>
                </div>
                <div class='box2'>
                    <u>Your PhotoBooth</u>
                    <p>
                        <label for="model" style='line-height:48px;'>Model*</label>
                        <select name='model' id='model' style='float: right;width: 328px;position: relative;color:black;'>
                            <option value='0'>---</option>
                            <?php
                            $CLD_CON->OpenRs("SELECT * FROM CLD_boothTypes");

                            while ($CLD_CON->FetchArray()) {
                                $name = $CLD_CON->GetArrayField("name");
                                echo "<option value='$name'>$name</option>";
                            }
                            ?>
                        </select>
                    </p>
                    <p>
                        <label for="serialnumber"> Serialnumber* </label>
                        <input type='text' name='serialnumber' id='sn' style='float:right;'>       
                    </p>
                </div>
                <div class='box2' style='margin-top: 80px;z-index: 2;padding-top: 0px;'>
                    <u>Person Contact</u>
                    <p>
                        <label for="ownerName">Name*</label>
                        <input type='text' name='ownerName' id='ownerName' style='float:right;'>
                    </p><p>
                        <label for="ownerName2"> Last Name*</label>
                        <input type='text' name='ownerName2' id='ownerName2' style='float:right;'>
                    </p><p>
                        <label for="email">E-mail*</label>
                        <input type='text' name='email' id='email' style='float:right;'>
                    </p><p>
                        <label for="pho"> Phone*</label>
                        <input type='text' name='pho' id='pho' style='float:right;'>                  
                    </p>
                </div>
                <div class='box2'>
                    <p>Click on a register and send the request by a register . You will receive the username and password in the next week.</p>
                    <input type='button' class='register_button' value='REGISTER' onclick='register();'>
                </div>

                <img src='images/loading.gif' id='loading' style='position: relative;height: 125px;margin: auto;top: -125px;display: none;'>
            </div>
            <p style='width: 100%;text-align: center;' id ='error'></p>
        </div>
    </body> 
    <script>
                        function register() {
                            loading();
                            var name = $("#companyName").val();
                            var address = $("#address").val();
                            var num = $("#num").val();
                            var city = $("#city").val();
                            var state = $("#state").val();
                            var country = $("#country").val();
                            var zip = $("#zip").val();

                            var ownerName = $("#ownerName").val();
                            var lastName = $("#ownerName2").val();
                            var email = $("#email").val();
                            var phone = $("#pho").val();

                            var model = $("#model").val();
                            var serialnumber = $("#sn").val();


                            if (name.length < 1) {
                                unloading();
                                $("#error").html("Company Name is empty.");
                                return;
                            }
                            if (address.length < 1) {
                                unloading();
                                $("#error").html("Address is empty.");
                                return;
                            }
                            if (zip.length < 1) {
                                unloading();
                                $("#error").html("Zip code is empty.");
                                return;
                            }
                            if (city.length < 1) {
                                unloading();
                                $("#error").html("City is empty.");
                                return;
                            }
                            if (state.length < 1) {
                                unloading();
                                $("#error").html("State is empty.");
                                return;
                            }
                            if (country.length < 1) {
                                unloading();
                                $("#error").html("Country is empty.");
                                return;
                            }

                            if (ownerName.length < 1) {
                                unloading();
                                $("#error").html("Person Contact Name is empty.");
                                return;
                            }

                            if (lastName.length < 1) {
                                unloading();
                                $("#error").html("Person Contact Last Name is empty.");
                                return;
                            }
                            if (email.length < 1) {
                                unloading();
                                $("#error").html("Person Contact e-mail is empty.");
                                return;
                            }
                            if (validarEmail(email)) {
                                unloading();
                                $("#error").html("The e-mail is not correct");
                                return;
                            }
                            if (phone.length < 1) {
                                unloading();
                                $("#error").html("Number Phone  is empty.");
                                return;
                            }
                            /*    if (validatePhone(phone)) {
                             $("#error").html("Phone is not correct.");
                             return;
                             }*/
                            if (model === "0") {
                                unloading();
                                $("#error").html("Select PhotoBooth model please");
                                return;
                            }
                            if (serialnumber.length < 1) {
                                unloading();
                                $("#error").html("PhotoBooth serial number is empty");
                                return;
                            }


                            $("#error").html("");
var ajaxData = {name: name, address: address, num: num, zip: zip, city: city, state: state, country: country, oName: ownerName, lastName: lastName, phone: phone, email: email, model: model, sn: serialnumber};

                            
                            $.ajax({
                                url:'registerAction.php',
                                type: 'POST',
                                success: function(data) {                                    
                                    if(data ==="OK"){
                                       $(".register").html("<p style='padding:60px;text-align:center;'>La solucitud de registre ha estat enviada , d'aqui un parell de ¿dies? ens posarem em contacte em voste per comunicar-li el resultat. Salutacions!</p><p><a href='./main.php'>go Back</a>");
                                       
                                    }else{
                                          $("#error").html(data);
                                    unloading();
                                    }
                                },                               
                                cache: false,
                                data: ajaxData,
                                contentType: 'application/x-www-form-urlencoded'
                            });


                        }
                        function loading() {
                            $(".box2").hide();
                            $("#loading").show();
                        }
                        function unloading() {
                            $(".box2").show();
                            $("#loading").hide();
                        }
                        /*                    function validatePhone(ph) {
                         var pattern = /^\(\d{3}\)\s*\d{3}(?:-|\s*)\d{4}$/;
                         return pattern.test(ph);
                         // string looks like a good (US) phone number with optional area code, space or dash in the middle
                         }*/

                        function validateEmail(email) {
                            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                            return re.test(email);
                        }

    </script>
</html>