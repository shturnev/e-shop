<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/proverki.php");

$Admin = is_admin();
if(!$Admin){ exit("Нет прав доступа"); }

$table = "categories";
$referer = ($_POST["referer"])? $_POST["referer"]: $_SERVER["HTTP_REFERER"];


/*------------------------------
Ф-ии
-------------------------------*/
function write_to_db($method){

    global $table;

    $response = [];

    $arr = [
        "title" => proverka1($_POST["title"])
    ];

    switch ($method):
        case "add":
            $response  = db_insert($table, $arr);
        break;
        case "edit":
            if(!is_numeric($_POST["ID"])){
                $response["error"] = "Ошибка: переданы не верные параметры. Строка: ". __LINE__;
            }
            else
            {
                $response  = db_update($table, $arr, "ID = ".$_POST["ID"]);
            }

        break;

    endswitch;

    return $response;
}


/*------------------------------
Если была передана форма
-------------------------------*/
if(isset($_POST["method_name"])):
    $resWrite = write_to_db($_POST["method_name"]);
    if($resWrite["error"]){$errors[] = $resWrite["error"]; }
endif;



/*------------------------------
Если был передан GET
-------------------------------*/
if($_GET["method_name"] == "delete" && is_numeric($_GET["ID"])):
    $resDb = db_delete($table, "ID =".$_GET["ID"]);
    if($resDb["error"]){$errors[] = $resDb["error"]; }
endif;

if($_GET["method_name"] == "edit" && is_numeric($_GET["ID"])):
    $resItem = db_row("SELECT * FROM ".$table." WHERE ID=".$_GET["ID"])["item"];
    if($resItem){$resItem["meta"] = json_decode($resItem["meta"], true);}
endif;


/*------------------------------
Вывод записей
-------------------------------*/
$Items = db_select("SELECT * FROM ".$table." ORDER BY title", true)["items"];


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <title></title>
    <link rel="shortcut icon" href=""/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="all" href="../css/adm/page_settings.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>


</head>

<body>

<div class="forError"><? if($errors){var_dump($errors);} ?></div>

<a href="<? echo $referer; ?>" class="return" title="Вернуться"><i class="material-icons">&#xE31B;</i></a>
<a href="#" class="addPage">Добавить категорию</a>

<? $tmp = (!$resItem)? "hidden": null; ?>
<section class="addForm" <? echo $tmp; ?>>
    <form action="/13-shop/adm/categories.php" method="post" enctype="multipart/form-data" name="myForm" target="_self">
        <? $method = (@$resItem)? "edit": "add"; ?>
        <input type="hidden" name="method_name" value="<? echo $method; ?>" />
        <input type="hidden" name="ID" value="<? echo @$resItem["ID"]; ?>" />

        <input type="text" name="title" value="<? echo @$resItem["title"]; ?>" placeholder="title"/><br><br>

        <input name="submit" type="submit" value="готово"/>
    </form>
</section>


<? if($Items): ?>
<section class="list">
    <ul class="pageItems">
        <? foreach ($Items as $item) { ?>
        <li>
            <a href="#" class="pageItem"><? echo $item["title"] ?></a>
            <div class="settings">
                <a href="/13-shop/adm/categories.php?method_name=edit&ID=<? echo $item["ID"] ?>" class="edit">Редактировать</a>
                <a href="/13-shop/adm/categories.php?method_name=delete&ID=<? echo $item["ID"] ?>" class="delete">удалить</a>
            </div>
        </li>
        <? } ?>
    </ul>
</section>
<? endif; ?>


<script type="text/javascript" src="../js/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="../js/adm/page_settings.min.js"></script>
</body>
</html>

