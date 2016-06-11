<?php
require_once("DB.php");
require_once("proverki.php");

function is_auth(){
    $token = proverka1($_COOKIE["token"]);
    if(!is_numeric($_COOKIE["ID"]) OR !$token){return false;}

    $resDb = db_row("SELECT ID FROM users WHERE ID='".$_COOKIE["ID"]."' AND pass='".$token."'", true )["item"];
    return $resDb;
}

function is_admin($ID = null){

    if(!$ID){$ID = $_COOKIE["ID"];}

    $token = proverka1($_COOKIE["token"]);
    if(!is_numeric($_COOKIE["ID"]) OR !$token){return false;}

    $resDb = db_row("SELECT status FROM users WHERE ID='".$_COOKIE["ID"]."' AND status = 3", true)["item"];
    return $resDb;
}

function auth_exit(){

    setcookie("ID", null, strtotime("-1 hour"), "/");
    setcookie("token", null, strtotime("-1 hour"), "/");

}

