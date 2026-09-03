<?php

require_once '../common/global.php';
include G_PATH . "common/general.php";
require_once G_PATH . "common/conexio.php";

$conn = getNewBdD();

function assign_rand_value($num) {

    switch ($num) {
        case 1: $rand_value = "A";
            break;
        case 2: $rand_value = "B";
            break;
        case 3: $rand_value = "C";
            break;
        case 4: $rand_value = "D";
            break;
        case 5: $rand_value = "E";
            break;
        case 6: $rand_value = "F";
            break;
        case 7: $rand_value = "G";
            break;
        case 8: $rand_value = "H";
            break;
        case 9: $rand_value = "I";
            break;
        case 10: $rand_value = "J";
            break;
        case 11: $rand_value = "K";
            break;
        case 12: $rand_value = "L";
            break;
        case 13: $rand_value = "M";
            break;
        case 14: $rand_value = "N";
            break;
        case 15: $rand_value = "O";
            break;
        case 16: $rand_value = "P";
            break;
        case 17: $rand_value = "Q";
            break;
        case 18: $rand_value = "R";
            break;
        case 19: $rand_value = "S";
            break;
        case 20: $rand_value = "T";
            break;
        case 21: $rand_value = "U";
            break;
        case 22: $rand_value = "V";
            break;
        case 23: $rand_value = "W";
            break;
        case 24: $rand_value = "X";
            break;
        case 25: $rand_value = "Y";
            break;
        case 26: $rand_value = "Z";
            break;
        case 27: $rand_value = "0";
            break;
        case 28: $rand_value = "1";
            break;
        case 29: $rand_value = "2";
            break;
        case 30: $rand_value = "3";
            break;
        case 31: $rand_value = "4";
            break;
        case 32: $rand_value = "5";
            break;
        case 33: $rand_value = "6";
            break;
        case 34: $rand_value = "7";
            break;
        case 35: $rand_value = "8";
            break;
        case 36: $rand_value = "9";
            break;
    }

    return $rand_value;
}

function get_rand_id($length) {

    if ($length > 0) {

        $rand_id = "";

        for ($i = 1; $i <= $length; $i++) {
            mt_srand((double) microtime() * 1000000);
            $num = mt_rand(1, 36);
            $rand_id .= assign_rand_value($num);
        }
    }

    return $rand_id;
}

$dongle_id = $_REQUEST['ID'];
$booth_type = $_REQUEST['T'];

if (!$dongle_id)
    die("ko#ID");
if (!$booth_type)
    die("ko#T");

$sql = "SELECT * FROM booths WHERE dongle='$dongle_id'";
$conn->OpenRs($sql);
$existingBooth = $conn->FetchRs();
$conn->CloseRs();
if ($existingBooth) {
    die("ok#" . $existingBooth['reference'] . "#" . $existingBooth['rand_string']);
} else {
    // Get the last reference for this booth type
    $type_last_reference = 0;
    $sqlAll = "SELECT * FROM booths ORDER BY id DESC";
    $conn->OpenRs($sqlAll);
    while ($row = $conn->FetchRs()) {
        $bt = substr($row['reference'], 0, 1);
        if ($bt === $booth_type && $type_last_reference === 0) {
            $type_last_reference = (int) substr($row['reference'], 1, 3);
        }
    }
    $conn->CloseRs();

    $type_last_reference++;

    // Build the booth reference, adding leading zeros if needed
    if ($type_last_reference < 10) {
        $booth_reference = $booth_type . "00" . $type_last_reference;
    } elseif ($type_last_reference < 100) {
        $booth_reference = $booth_type . "0" . $type_last_reference;
    } else {
        $booth_reference = $booth_type . $type_last_reference;
    }

    // Generate a unique random string
    do {
        if (method_exists('utils', 'get_rndm32')) {
            $booth_rand_string = utils::get_rndm32(3);
        } else {
            $booth_rand_string = get_rand_id(3);
        }
        $sqlRand = "SELECT * FROM booths WHERE rand_string='$booth_rand_string'";
        $conn->OpenRs($sqlRand);
        $found = $conn->FetchRs();
        $conn->CloseRs();
    } while ($found);

    // Insert the new booth record
    $insert_sql = "INSERT INTO booths SET 
                        dongle='$dongle_id', 
                        reference='$booth_reference', 
                        rand_string='$booth_rand_string', 
                        rental_id=1";
    if (!$conn->Execute($insert_sql)) {
        die("ko#insert");
    }

    die("ok#" . $booth_reference . "#" . $booth_rand_string);
}
?>