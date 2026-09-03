<?php

/* De moment test
 */

if(isset($_POST['p1'])) $p1 = "-".$_POST['p1']; else $p1 = "";

if (($_FILES["myFileUp"]["error"] > 0) && ($_FILES["myFileUp"]["error"] != 4))
    {
		switch($_FILES["myFileUp"]["error"]){
			case 1:
		echo "ko#1#Error: the uploaded file exceeds the 10M directive.";
			break;
			case 2:
		echo "ko#2#Error: the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
			break;
			case 3:
		echo "ko#3#Error: the uploaded file was only partially uploaded.";
			break;
			default:
		echo "ko#0#Error: " . $_FILES["myFileUp"]["error"];
			break;
		}
    }
    else
    {
      //control de cap fitxer
      if($_FILES["myFileUp"]["error"] == 4){//si no ha pujat cap fitxer, mostrem text inicial
		echo "ko#4#No file has been selected, please try again.";

      }
      else{//cal gestionar el fitxer pujat i mostrar-lo
          $fitxerOk = true;
        $ContentType = $_FILES['myFileUp']['type'];
        switch($ContentType){
         case "application/octet-stream":
                $extensio=".jpg";
                break;
         case "image/pjpeg":
                $extensio=".jpg";
                break;
         case "image/jpeg":
                $extensio=".jpg";
                break;
         default:
             $fitxerOk = false;
		echo "ko#100#Error: Invalid file type" . $ContentType ;
         }
         if($fitxerOk){
            
           // $nomFitxer = "./files/myFile$nFitxer$extensio";
            $nomFitxer = "./files/myFile$p1$extensio";
            $tmpFitxer = $_FILES["myFileUp"]["tmp_name"];
                if(!move_uploaded_file($_FILES["myFileUp"]["tmp_name"], $nomFitxer)){
                        echo "ko#101#Error: can't move de file: " . $_FILES["myFileUp"]["tmp_name"];
                       // return;
                }
                else{//ok
//20131004 INICI
                    //20131004 echo "<p>Uploaded file:</p>";
                    //20131004 echo "<img src='$nomFitxer?imgp=".rand()."' width='200' />";
                    echo "ok#File $nomFitxer uploaded successfully.</p>";
//					if($nFitxer < 5){//mxim 6 per sessi
//							echo "<p>Need to upload more photos?</p>";
//					}
////20131004 FINAL
//					
//                    $_SESSION['nFile'] = $nFitxer + 1;//millor incrementem un cop s'ha pujat correctament
                }
         }

      }//end else de if($_FILES["fileUpload"]["error"] != 4){

    }//end del else if (($_FILES["fileUpload"]["error"]

?>
