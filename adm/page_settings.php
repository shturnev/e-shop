<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/proverki.php");

$Admin = is_admin();
if(!$Admin){ exit("Нет прав доступа"); }

$referer = ($_POST["referer"])? $_POST["referer"]: $_SERVER["HTTP_REFERER"];



/*------------------------------
Ф-ии
-------------------------------*/
function write_to_db($method){

    $table = "page_settings";
    $response = [];

    $arr = [
        "stranica"      => proverka1($_POST["stranica"])
        ,"title"         => proverka1($_POST["title"])
        ,"btn_title"     => proverka1($_POST["btn_title"])
        ,"text"          => proverka1($_POST["text"])
        ,"meta"          => [
            "title"             => proverka1($_POST["meta"]["title"])
            ,"desc"             => proverka1($_POST["meta"]["desc"])
            ,"keywords"         => proverka1($_POST["meta"]["keywords"])
        ]

    ];

    $arr["meta"] = addslashes(json_encode($arr["meta"]));

    switch ($method):
        case "add":
            $response  = db_duplicate_update($table, [0 => $arr]);

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
    $resDb = db_delete("page_settings", "ID =".$_GET["ID"]);
    if($resDb["error"]){$errors[] = $resDb["error"]; }
endif;

if($_GET["method_name"] == "edit" && is_numeric($_GET["ID"])):
    $resItem = db_row("SELECT * FROM page_settings WHERE ID=".$_GET["ID"])["item"];
    if($resItem){$resItem["meta"] = json_decode($resItem["meta"], true);}
endif;


/*------------------------------
Вывод записей
-------------------------------*/
$Items = db_select("SELECT * FROM page_settings ORDER BY stranica", true)["items"];


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
<a href="#" class="addPage">Добавить страницу</a>

<? $tmp = (!$resItem)? "hidden": null; ?>
<section class="addForm" <? echo $tmp; ?>>
    <form action="/13-shop/adm/page_settings.php" method="post" enctype="multipart/form-data" name="myForm" target="_self">
        <? $method = (@$resItem)? "edit": "add"; ?>
        <input type="hidden" name="method_name" value="<? echo $method; ?>" />
        <input type="hidden" name="ID" value="<? echo @$resItem["ID"]; ?>" />
        <input type="hidden" name="referer" value="<? echo $referer; ?>" />

        <input type="text" name="stranica" value="<? echo @$resItem["stranica"]; ?>" placeholder="stranica"/><br><br>
        <input type="text" name="title" value="<? echo @$resItem["title"]; ?>" placeholder="title"/><br><br>
        <input type="text" name="btn_title" value="<? echo @$resItem["btn_title"]; ?>" placeholder="btn_title"/><br><br>
        <input type="text" name="meta[title]" value="<? echo @$resItem["meta"]["title"]; ?>" placeholder="meta[title]"/><br><br>
        <input type="text" name="meta[desc]" value="<? echo @$resItem["meta"]["desc"]; ?>" placeholder="meta[desc]"/><br><br>
        <input type="text" name="meta[keywords]" value="<? echo @$resItem["meta"]["keywords"]; ?>" placeholder="meta[keywords]"/><br><br>
        <textarea name="text" ><? echo @$resItem["text"]; ?></textarea><br><br>

        <input name="submit" type="submit" value="готово"/>
    </form>
</section>


<? if($Items): ?>
<section class="list">
    <ul class="pageItems">
        <? foreach ($Items as $item) { ?>
        <li>
            <a href="#" class="pageItem"><? echo $item["stranica"] ?></a>
            <div class="settings">
                <a href="/13-shop/adm/page_settings.php?method_name=edit&ID=<? echo $item["ID"] ?>" class="edit">Редактировать</a>
                <a href="/13-shop/adm/page_settings.php?method_name=delete&ID=<? echo $item["ID"] ?>" class="delete">удалить</a>
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

