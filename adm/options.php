<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");

if(!is_admin()){exit("пока пока");}


if($_GET["method_name"] == "deleteBigSlider" && is_numeric($_GET["ID"])){
    $resDb = db_delete("bigSlider", "ID=".$_GET["ID"]);
    if(!$resDb["error"]){ echo 1; }
}

