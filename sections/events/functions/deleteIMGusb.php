<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$section = $_POST['s'];
$id2 = $_POST['id2'];
$folder = $_POST['path'];
$f = "../../../usbs/";
$s = $_POST['screens'];
$letras = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l');


switch ($section) {
    case 1://WELCOMES
        if ($s == 1) {
            unlink($f . $folder . "/PhotoIdUpload/Welcome/Random/" . $id2 . ".jpg");
            unlink($f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $id2 . ".jpg");
        } else {
            $x = 1;
            while ($x <= $s) {
                unlink($f . $folder . "/PhotoIdUpload/Welcome/Random/" . $id2 . $letras[$x] . ".jpg");
                unlink($f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $id2 . $letras[$x] . ".jpg");
                $x++;
            }
        }

        $x = 1;
        $y = $id2 + 1;


        while ($y <= 20) {
            if ($s == 1) {
                if (file_exists($f . $folder . "/PhotoIdUpload/Welcome/Random/" . $y . ".jpg")) {
                    rename($f . $folder . "/PhotoIdUpload/Welcome/Random/" . $y . ".jpg", $f . $folder . "/PhotoIdUpload/Welcome/Random/" . $id2 . ".jpg");
                } if (file_exists($f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $y . ".jpg")) {
                    rename($f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $y . ".jpg", $f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $id2 . ".jpg");
                }
               
            } else {
                while ($x <= $s) {
                    if (file_exists($f . $folder . "/PhotoIdUpload/Welcome/Random/" . $y . $letras[$x] . ".jpg")) {
                        rename($f . $folder . "/PhotoIdUpload/Welcome/Random/" . $y . $letras[$x] . ".jpg", $f . $folder . "/PhotoIdUpload/Welcome/Random/" . $id2 . $letras[$x] . ".jpg");
                    }
                    if (file_exists($f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $y . $letras[$x] . ".jpg")) {
                        rename($f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $y . $letras[$x] . ".jpg", $f . $folder . "/PhotoIdUpload/Welcome/Custom/" . $id2 . $letras[$x] . ".jpg");
                    }
                    $x++;
                }
            }
            $x = 1;
            $y++;
            $id2++;
        }

        echo "Welcomes has deleted Succesfully";

        break;
         case 2://BYES
        if ($s == 1) {
            unlink($f . $folder . "/PhotoIdUpload/Bye/Random/" . $id2 . ".jpg");
            unlink($f . $folder . "/PhotoIdUpload/Bye/Custom/" . $id2 . ".jpg");
        } else {
            $x = 1;
            while ($x <= $s) {
                unlink($f . $folder . "/PhotoIdUpload/Bye/Random/" . $id2 . $letras[$x] . ".jpg");
                unlink($f . $folder . "/PhotoIdUpload/Bye/Custom/" . $id2 . $letras[$x] . ".jpg");
                $x++;
            }
        }

        $x = 1;
        $y = $id2 + 1;


        while ($y <= 20) {
            if ($s == 1) {
                if (file_exists($f . $folder . "/PhotoIdUpload/Bye/Random/" . $y . ".jpg")) {
                    rename($f . $folder . "/PhotoIdUpload/Bye/Random/" . $y . ".jpg", $f . $folder . "/PhotoIdUpload/Bye/Random/" . $id2 . ".jpg");
                } if (file_exists($f . $folder . "/PhotoIdUpload/Bye/Custom/" . $y . ".jpg")) {
                    rename($f . $folder . "/PhotoIdUpload/Bye/Custom/" . $y . ".jpg", $f . $folder . "/PhotoIdUpload/Bye/Custom/" . $id2 . ".jpg");
                }
               
            } else {
                while ($x <= $s) {
                    if (file_exists($f . $folder . "/PhotoIdUpload/Bye/Random/" . $y . $letras[$x] . ".jpg")) {
                        rename($f . $folder . "/PhotoIdUpload/Bye/Random/" . $y . $letras[$x] . ".jpg", $f . $folder . "/PhotoIdUpload/Bye/Random/" . $id2 . $letras[$x] . ".jpg");
                    }
                    if (file_exists($f . $folder . "/PhotoIdUpload/Bye/Custom/" . $y . $letras[$x] . ".jpg")) {
                        rename($f . $folder . "/PhotoIdUpload/Bye/Custom/" . $y . $letras[$x] . ".jpg", $f . $folder . "/PhotoIdUpload/Bye/Custom/" . $id2 . $letras[$x] . ".jpg");
                    }
                    $x++;
                }
            }
            $x = 1;
            $y++;
            $id2++;
        }

        echo "Byes has deleted Succesfully";

        break;
        case 3:
        unlink($f . $folder . "/PhotoIdEvents/CustomShots/$id2.jpg");

        $c= $id2+1;
        while($id2<25){
            if(file_exists($f . $folder . "/PhotoIdEvents/CustomShots/$c.jpg")){
                rename($f . $folder . "/PhotoIdEvents/CustomShots/$c.jpg", $f . $folder . "/PhotoIdEvents/CustomShots/$id2.jpg");              
            }
            $c++;
            $id2++;
            
        }
        echo "Custom Shot has deleted Succesfully";
        break;
        case 4:
            unlink($f . $folder . "/PhotoIdUpload/BGmusic.mp3");
                    echo "Background Music has deleted Succesfully";
        break;
        case 5:
            unlink($f . $folder . "/PhotoIdEvents/Wedding/Header/1.jpg");
                    echo "Header Banner has deleted Succesfully";
        break;
}

?>
