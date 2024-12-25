<?php
    require_once '../../../common/global.php';
    require_once G_PATH . 'common/conexio.php';
    
    //$ID = $_GET['ID'];
    $TOKEN = strval(rawurldecode($_GET['token']));
    $dateToday = utils::get_datetime();
    
    $html = <<<HTML
            <html>
                <head>
                    <title>MyPhotoCode</title>
                    <link rel='stylesheet' href="../../../includes/logincss.css" type="text/css">
                </head>
                <body>
                    <img src="../../../images/web/myphotocode.png" id ="logoMyPhotoCode" style="display:block;left: 25%;">
                    <div style="position:relative; width:50%; left:25%; margin-top:25%;">
HTML;
    $CLD_CON->OpenRs("SELECT App_email, validatedAlertEmail, token_Sec, token_SecDate, name, id FROM rentals WHERE token_Sec = '".$TOKEN."' ");
    if($CLD_CON->FetchArray()){
        $app_email = $CLD_CON->GetArrayField("App_email");
        $validate = $CLD_CON->GetArrayField("validatedAlertEmail");
        $name = $CLD_CON->GetArrayField("name");
        $ID = $CLD_CON->GetArrayField("id");
        $token_Sec = $CLD_CON->GetArrayField("token_Sec");
        $token_SecDate = $CLD_CON->GetArrayField("token_SecDate");
        if($validate != 0){
            $html .= "This email has been validated previously";
        }        
        else if($token_Sec == $TOKEN && $dateToday < $token_SecDate){
            if ($CLD_CON->Execute("UPDATE rentals SET token_Sec=null, token_SecDate=null, ValidatedAlertEmail='1' WHERE id=$ID")) {
                $html .= "This email has been successfully validated";
                
                $function = "ChgAletEmail";
                $date = date("YmdHis");
                $sg = strtoupper(sha1($date . $function . $ID . $app_email . "1" . "ARt32qX"));
                $data = "p1=$sg&p2=$date&p3=$function&p4=$ID&p5=" . urlencode($app_email) . "&p6=1";
                
                $res = utils::motor_conect("mypc_service.php", $data);

                foreach ($res as $r) {
                    utils::log($r, "logCTRLConnect", "saveAlertEmail");
                    $p = stripos($r['res'], '{');

                    utils::log("TRACE 1", "logCTRLConnect", "saveAlertEmail");

                    if ($p != false && $p < 5) {
                        $response = json_decode(substr($r['res'], $p));
                    } else {
                        $response = json_decode($r['res']);
                    }

                    utils::log("myphotocode single-response", "logCTRLConnect", "saveAlertEmail");        
                    utils::log($response, "logCTRLConnect", "saveAlertEmail");        
                }
                
            }
            else {
                $html .= "Error ocurred, email not validated";
            }
        }
        else if($dateToday > $token_SecDate) {
            $html .= "This link has expired, go MyPhotoCode and resend email";
        }
        else if($token_Sec != $TOKEN){
            $html .= "Invalid link";
        }
        else {
            $html .= "Error ocurred";
        }
    }
    else {
        $html .= "This link has expired or this email has been previously validated";
    }
    $G_PAGE = G_PAGE;
    $html .= <<<HTML
                    <br/><br/>
                    <a href="{$G_PAGE}">Go to MyPhotoCode</a>
                </div>
            </body>
        </html>
HTML;
    echo $html;
?>