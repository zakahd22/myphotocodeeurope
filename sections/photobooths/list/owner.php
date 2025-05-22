<?php

include '../../../sessio.php';

require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_booths');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('App_boothDongle');
$baseController->createModel('booths');
$baseController->createModel('CLD_Distributors');
$baseController->createModel('rentals');
$baseController->createModel('CLD_Incidents');

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

$USERID_filter = $USERID; 
$pbs = $baseController->App_boothsModel->getBoothsLimit($USERID_filter, $LIMIT);
echo '<script>
    document.body.classList.remove("list-view");
    localStorage.removeItem("listView");
</script>';

$pbs_no_limit = $baseController->App_boothsModel->getBooths($USERID_filter);
$totalrows = count($pbs_no_limit);
$owners = array();
array_push($owners, $USERID_filter);
if (!isset($_POST['filPage'])) {
    if (isset($_POST['fil'])) {

        $sn_f = $_POST['sn'];

        $dongle_f = $_POST['dStr'];
        $dongle_f = trim($dongle_f);

        if (strlen($dongle_f) == 4) {
            $dongle_f = substr($dongle_f, 1);
        }

        if ($_POST['idPb'] != 0 && $_POST['idPb'] != "") {
            $idPB_f = $_POST['idPb'];
        }
        if ($_POST['type'] != 0 && $_POST['type'] != "") {
            $tipo_f = $_POST['type'];
        }

        if (!empty($dongle_f)) {
            $idboo = $baseController->boothsModel->getBoothRandString($dongle_f);
            $idboo = $idboo[0]['idBooth'];
            if (!$idboo) {
                $idboo = FALSE;
            }
        }
        if (!empty($idPB_f)) {
            $idboo = $idPB_f;
        }
        utils::log("{$sn_f}, {$tipo_f}, {$stat_f}, {$idboo}, {$distributor}, {$USERID_filter}", "logasd");
        $pbs = $baseController->App_boothsModel->getPbsListFilter($sn_f, $tipo_f, $stat_f, $idboo, $distributor, $owners);
        $totalrows = count($pbs);
        utils::log("totalrows:  {$totalrows}", "logasd");
        if ($idboo === FALSE || empty($pbs)) {
            $pbs = NULL;
        }
    } else {
        $pbs = $baseController->App_boothsModel->getBoothsLimit($USERID_filter, $LIMIT);
        $pbs_no_limit = $baseController->App_boothsModel->getBooths($USERID_filter);
        $totalrows = count($pbs_no_limit);
    }
}

$html = "";

if ($pbs === FALSE || empty($pbs)) {
    $html .= "No results found";
} else {
    $html .= "<link rel='stylesheet' href='sections/photobooths/resources/css/photobooths.css' type='text/css'>";
    $html .= "<div id='positional_div'></div>";
    $html .= "<button id='toggle-list-view' onclick='toggleListView()'>List View</button>"; 
    $html .= "<div id='booth-container'>"; 

    foreach ($pbs as $pb) {
        $idBooth = $pb["idBooth"];
        $sn = $pb["serialnumber"];
        $char = $pb["type"];
        $typeID = $pb["CLD_idType"];
        $pbname = $pb["name"]; //20150709pbname
        $location = $pb["location"];

        if (!empty($typeID)) {
            $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeName($typeID);
            if ($boothTypes) $typeName = $boothTypes[0]["name"];
        } else {
            $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeByChar($char);
            if ($boothTypes) {
                $typeName = $boothTypes[0]["name"];
                $typeName = $boothTypes[0]["id"];
            }
        }
        $boothDongleLmit = $baseController->App_boothDongleModel->boothDongleLmit($idBooth, 1);
        if ($boothDongleLmit) {
            $dongleID = $boothDongleLmit[0]["idDongle"];
            $booth = $baseController->boothsModel->getBoothsByDongle($dongleID);
            //if($booth) $r_string = " - " . $char . $a[0]["rand_string"];
            if ($booth) $r_string = " - " . $char . $booth[0]["rand_string"];
        } else {
            $r_string = "";
        }

        $html .= "<div class='regBooth grid-item' onclick='setSection(\"photobooths\" ,2 ,$idBooth)'>";
        $html .= "<div class='imgListBooth'>";
        if (empty($typeID)) {
            $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img''>";
        } else {
            if (file_exists(G_PATH . "/images/web/pb/$typeID.png")) {
                $html .= "<img src='images/web/pb/$typeID.png' class='pbs_img''>";
            } else {
                $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img'>";
            }
        }
        $html .= "</div>";
        $html .= "<div class='infoListBooth'>";
        $html .= "<p>S/N : $sn $r_string</p>";
        $html .= "<p>Type : $typeName</p>";
        $html .= "<p>Name : $pbname</p>"; //20150709pbname
        $html .= "<p>Location : $location</p>";
        $html .= "</div>";
        $html .= "</div>";

        //20250124PBlist        $available = $CLD_CON->FetchArray();

    }
    $html .= "</div>"; 

    echo $html;

    echo '<style>




.list-view .regBooth {
    display: flex;
    align-items: center;
    text-align: left;
    margin-bottom: -5px; 
    width: 100%;
    padding: 5px; /* Reduced padding for smaller list items */
    font-size: 0.9em; /* Reduced font size */
}
.list-view .infoListBooth {
    margin-top: 190px;
}

.regBooth_small:hover {
    background-color: orange;
    cursor: pointer;
}


.list-view .imgListBooth {
  
    width: 105px; /* Reduced image size */
}

.list-view .pbs_img {
    max-width: 40px; /* Reduced image size */
    height: auto;
}

#toggle-list-view {
    top: -10px;       /* ajusta verticalmente */
    right: 20px;     /* ajusta horizontalmente */
    padding: 10px 16px;
    background: linear-gradient(135deg, #4CAF50, #45A049);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease, transform 0.2s ease;
    z-index: 10;     /* para que esté encima */
}

#toggle-list-view:hover {
    background: linear-gradient(135deg, #45A049, #388E3C);
    transform: scale(1.05);
}


</style>';

    echo '<script>
let allDataLoaded = false;
let originalHTML = "";
let originalPageSelector = "";

function toggleListView() {
    const toggleButton = document.getElementById("toggle-list-view");
    const isListView = document.body.classList.toggle("list-view");
    localStorage.setItem("listView", isListView ? "1" : "0");

    if (isListView) {
        toggleButton.textContent = "Grid View";
        originalHTML = document.getElementById("booth-container").innerHTML;

        const pageSelector = document.querySelector(".page-selector");
        if (pageSelector) {
            originalPageSelector = pageSelector.outerHTML;
            pageSelector.remove();
        }

        const infos = document.querySelectorAll(".infoListBooth, .infoListBoothAlt");
        infos.forEach(info => {
            if (info.classList.contains("infoListBooth")) {
                info.classList.remove("infoListBooth");
                info.classList.add("infoListBoothAlt");
            }
        });

        const booths = document.querySelectorAll(".regBooth.grid-item");
        booths.forEach(booth => booth.remove());

        loadAllData();
    } else {
        toggleButton.textContent = "Toggle List";
        document.getElementById("booth-container").innerHTML = originalHTML;

        const infos = document.querySelectorAll(".infoListBoothAlt");
        infos.forEach(info => {
            info.classList.remove("infoListBoothAlt");
            info.classList.add("infoListBooth");
        });

        const booths = document.querySelectorAll(".regBooth");
        booths.forEach(booth => {
            booth.classList.add("grid-item");
            booth.classList.remove("regBooth_small");
        });

        if (originalPageSelector) {
            document.getElementById("booth-container").insertAdjacentHTML("afterend", originalPageSelector);
        }
        const restoredBooths = document.querySelectorAll(".regBooth.grid-item");
        restoredBooths.forEach(booth => {
            booth.addEventListener("mouseover", () => booth.style.backgroundColor = "orange");
            booth.addEventListener("mouseout", () => booth.style.backgroundColor = "");
        });
    }
}

function loadAllData() {
    fetch("sections/photobooths/list/load_all_booths.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("booth-container").innerHTML = data;
            const booths = document.querySelectorAll(".regBooth");
            booths.forEach(booth => {
                booth.classList.remove("grid-item");
                booth.classList.add("regBooth_small");
            });

            const infos = document.querySelectorAll(".infoListBooth, .infoListBoothAlt");
            infos.forEach(info => {
                info.style.display = "grid";
                info.style.gridTemplateColumns = "200px 200px 200px 750px";
                info.style.columnGap = "40px";
                info.querySelectorAll("p").forEach(p => {
                    p.style.display = "block";
                    p.style.margin = "1";
                });
            });

            const pageSelector = document.querySelector(".page-selector");
            if (pageSelector) pageSelector.remove();

            const toggleButton = document.getElementById("toggle-list-view");
            if (toggleButton) {
                toggleButton.style.display = "inline-block";
            }

            allDataLoaded = true;
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

</script>';

}
//else{
//    echo "<br /><center><b> You don't have any photobooth available </b></center>";
//}

$s = "photobooths";
$color = "#5882FA";
include '../../pagescount.php';