<?php

function copiaHistoric($CLD_CON, $now, $rao, $sql, $set) {
    
    utils::log("entra a la funcio", "logCheckGestor");

    $CLD_CON->openRS("$sql");
    utils::log("fa el cldcon $sql", "logCheckGestor");
    

    while ($CLD_CON->FetchArray()) {
        $entry = array();
        $entry['id_original'] = $CLD_CON->GetArrayField("id");
        $entry['code'] = $CLD_CON->GetArrayField("code");
        $entry['method'] = $CLD_CON->GetArrayField("method");
        $entry['contact'] = $CLD_CON->GetArrayField("contact");
        $entry['timestamp'] = $CLD_CON->GetArrayField("timestamp");
        $entry['state'] = $CLD_CON->GetArrayField("state");
        $entry['last'] = $now;
        $entry['error'] = $CLD_CON->GetArrayField("error");
        $entry['rao'] = $rao;
        $entry['versioPB'] = $CLD_CON->GetArrayField("versioPB");
        $entry['idb'] = $CLD_CON->GetArrayField("idb");
        $entry['vist'] = $CLD_CON->GetArrayField("vist");
        
        if(!$entry['versioPB']){ $entry['versioPB']="WEB";}
        
//$values = "{$entry['id_original']}, '{$entry["code"]}',{$entry["method"]},'{$entry["contact"]}','{$entry["timestamp"]}',{$entry["state"]},'{$entry["last"]}','{$entry["error"]}','{$entry["rao"]}','{$entry["versioPB"]}',{$entry["idb"]},{$entry["vist"]}";
        $sql2 = "INSERT INTO `gestor_historic` (`id_original`, `code`, `method`, `contact`, `timestamp`, `state`, `last`, `error`, `rao`, `versioPB`, `idb`, `vist`) VALUES (";
//            $sql2 = "INSERT INTO `gestor_historic` (`id_original`, `code`, `method`, `contact`, `timestamp`, `state`, `last`, `error`, `rao`, `versioPB`, `idb`, `vist`) VALUES ($values)";
        $i = 0;
        foreach ($entry as $ent) {
            $sql2 .= "'$ent'";
            if ($i + 1 < count($entry)) {
                $sql2 .= ", ";
            } else {
                $sql2 .= ")";
            }
            $i++;
        }
        if ($CLD_CON->Execute($sql2)) {
            $sql3="UPDATE `gestor` SET $set WHERE `id` = {$entry['id_original']}";
            $CLD_CON->Execute($sql3);
            return true;
        } else {
            utils::log("falla el cldcon2 $sql2", "logCheckGestor");
            return false;
        }
    }
}
