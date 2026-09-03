<?php

/*
 * Aquesta pàgina es el contador de pàgines per a les llistes. 
 * A les pàgines de llistes sels inclou i tenen de tenir una variable
 * anomanada $select_nolimit la qual tindra el numero de registres que té la consulta.
 * Les variables LIMITERPAGES , $PAGE i $LIMIT estan el fitxer /conf.php  
 */
if(!isset($totalrows) && $totalrows <= 0){
    $CLD_CON->OpenRs($select_nolimit);
    $totalrows = $CLD_CON->GetRsRows();
}

if (isset($_POST['fil']) || isset($where)) {
    if (($pages = floor($totalrows / $LIMITERPAGES) + 1) > 1) {
        echo "<div class='page-selector'><ul class='listpageSelector'>";
        echo "<li style='width:50px;'>$totalrows</li>";
        if ($PAGE > $pages) {
            $PAGE = $pages;
        }
        
        if ($PAGE > 5) {
            $x = $PAGE - 5;
        } 
        else {
            $x = 1;
        }
        if ($PAGE > $pages - 5) {
            if ($pages > 9) {
                $x = $pages - 9;
            }
            $lastpage = $pages;
        }
        else {
            if ($PAGE < 6) {
                $lastpage = 10;
            } else {
                $lastpage = $PAGE + 4;
            }
        }
        
        $l = $x * $LIMITERPAGES - $LIMITERPAGES;
        echo "<li class='pageSelectorFrist' onclick='setPageList2(\"1\",\"0,$LIMITERPAGES\" , \"$s\" )'> |&lsaquo;</li>";
        while ($x < $lastpage + 1) {
            if ($x == $PAGE) {
                echo "<li onclick='setPageList2(\"$x\",\"$l,$LIMITERPAGES\"  , \"$s\")'><b style='color:$color;'>$x</b></li>";
            } else {
                echo "<li onclick='setPageList2(\"$x\",\"$l,$LIMITERPAGES\" , \"$s\")'>$x</li>";
            }
            $x++;
            $l = $l + $LIMITERPAGES;
        }

        $p = $pages;
        $lim = $totalrows - floor($totalrows % $LIMITERPAGES);
        echo "<li class='pageSelectorLast' onclick='setPageList2(\"$p\",\"$lim,$LIMITERPAGES\" , \"$s\")'>&rsaquo;| </li>";
        echo "</ul></center></div>";
        echo "</ul></div>";
    } else {
        echo "<div class='page-selector' style='text-align:center;width:20%;'><ul class='listpageSelector'>";
        echo "<li style='width:50px;'>$totalrows</li>";
        echo "</ul>";
        echo "</div>";
    }
} else {
    if (($pages = floor($totalrows / $LIMITERPAGES) + 1) > 1) {
        echo "<div class='page-selector'><center><ul class='listpageSelector'>";
        echo "<li style='width:50px;'>$totalrows</li>";

        if ($PAGE > $pages) {
            $PAGE = $pages;
        }

        if ($PAGE > 5) {
            $x = $PAGE - 5;
        } else {
            $x = 1;
        }
        if ($PAGE > $pages - 5) {
            if ($pages > 9) {
                $x = $pages - 9;
            }
            $lastpage = $pages;
        } else {
            if ($PAGE < 6) {
                $lastpage = 10;
            } else {
                $lastpage = $PAGE + 4;
            }
        }
        $l = $x * $LIMITERPAGES - $LIMITERPAGES;
        echo "<li class='pageSelectorFrist' onclick='setPageList(\"1\",\"0,$LIMITERPAGES\" , \"$s\")'> |&lsaquo;</li>";
        while ($x < $lastpage + 1) {
            if ($x == $PAGE) {
                echo "<li onclick='setPageList(\"$x\",\"$l,$LIMITERPAGES\"  , \"$s\")'><b style='color:$color;'>$x</b></li>";
            } else {
                echo "<li onclick='setPageList(\"$x\",\"$l,$LIMITERPAGES\" , \"$s\")'>$x</li>";
            }
            $x++;
            $l = $l + $LIMITERPAGES;
        }

        $p = $pages;
        $lim = $totalrows - floor($totalrows % $LIMITERPAGES);
        echo "<li class='pageSelectorLast' onclick='setPageList(\"$p\",\"$lim,$LIMITERPAGES\" , \"$s\")'>&rsaquo;| </li>";
        echo "</ul></center></div>";
    } else {
        echo "<div class='page-selector' style='text-align:center;width:20%;'><ul class='listpageSelector'>";
        echo "<li style='width:50px;'>$totalrows</li>";
        echo "</ul>";
        echo "</div>";
    }
}
?>
