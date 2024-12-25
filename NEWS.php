<div id="slider" style='width:100%;height:100%;overflow:auto;'>

    <?php
    $dir = "news/";
    $sd = scandir($dir);
    foreach ($sd as $s) {
        if ($s != "." && $s != "..") {
            if (!is_dir($dir . $s)) {
                include($dir . $s);
            }
        }
    }
    ?>   
</div>
