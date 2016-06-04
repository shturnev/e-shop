<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");

if(!is_admin()){exit("пока пока");}


if($_GET["method_name"] == "deleteBigSlider" && is_numeric($_GET["ID"])){
    $resDb = db_delete("bigSlider", "ID=".$_GET["ID"]);
    if(!$resDb["error"]){ echo 1; }
}

if($_POST["method_name"] == "sort" && $_POST["table"]){

    $response = [];
    parse_str($_POST["data"], $arr);

    if(!$arr["nomer"]){ $response["error"] = "Ошибка: отсутствуют данные"; }
    else{
        $tmp = [];
        foreach ($arr["nomer"] as $nomer => $ID) {
            $tmp[$nomer]["ID"]      = $ID;
            $tmp[$nomer]["nomer"]   = $nomer;
        }

        $resDb = db_duplicate_update($_POST["table"], $tmp, true);
        if($resDb["error"]){ $response["error"] = $resDb["error"]; }
        $response["response"] = $resDb;

    }

    //перевести json
        echo json_encode($response);
}


