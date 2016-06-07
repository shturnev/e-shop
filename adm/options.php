<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/path.php");

if(!is_admin()){exit("пока пока");}


//Удалить элемент из слайдера
if($_GET["method_name"] == "deleteBigSlider" && is_numeric($_GET["ID"])){
    $resDb = db_delete("bigSlider", "ID=".$_GET["ID"]);
    if(!$resDb["error"]){ echo 1; }
}

//Сортировка
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

//Удалить товар
if($_GET["method_name"] == "deleteProduct" && is_numeric($_GET["ID"])){

    $response = [
        "error" => null
    ];

    //узнаем есть ли такая запись
    $resItem = db_row("SELECT * FROM products WHERE ID=".$_GET["ID"])["item"];
    if(!$resItem){ $response["error"] = "Ошибка такого элемента не найдено";

        if($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest') {

            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }
        else
        {
            print_r(json_encode($response)); exit();
        }

    }

    //удалим запись
    $resDb = db_delete("products", "ID=".$_GET["ID"], true);
    if($resDb["error"]){
        $response["error"] = $resDb["error"];

        if($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest') {

            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }
        else
        {
            print_r(json_encode($response)); exit();
        }
    }

    //удалим картинки
    if($resItem["photo"])
    {
        $tmp["big"]     = path_clear_path()."/FILES/products/big/".$resItem["photo"];
        $tmp["small"]   = path_clear_path()."/FILES/products/small/".$resItem["photo"];

        if(file_exists($tmp["small"])){ unlink($tmp["small"]);  }
        if(file_exists($tmp["big"])){ unlink($tmp["big"]);  }

    }

    //response
    if($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest') {

        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
    else
    {
        print_r(json_encode($response)); exit();
    }


}