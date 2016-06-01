<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/proverki.php");
require_once("../functions/saveImg.php");

$Admin = is_admin();
if(!$Admin){ exit("Нет прав доступа"); }

$referer = ($_POST["referer"])? $_POST["referer"]: $_SERVER["HTTP_REFERER"];



/*------------------------------
Ф-ии
-------------------------------*/

/*------------------------------
Если была передана форма
-------------------------------*/
if(isset($_POST["submit"])):

    $arr = [
        "maw"       => 1000
       ,"miw"       => 160
       ,"path"      => "../FILES/forSlider"
       ,"inputName" => "photo"

    ];

    $resAdd = photo_add_few($arr);
    $resAdd = array_column($resAdd, "filename");

    if(count($resAdd))
    {
        $arr = [];
        foreach ($resAdd as $item) {
            $arr[] = [
              "stranica" => $_POST["stranica"]
              ,"photo"   => $item
            ];
        }

        //пишем в базу
        $resDb = db_duplicate_update("bigSlider", $arr);

    }





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
$Items = db_select("SELECT * FROM bigSlider ORDER BY ID DESC", true)["items"];


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <title>Работа со слайдером</title>
    <link rel="shortcut icon" href=""/>
    <link rel="stylesheet" type="text/css" media="all" href="../css/adm/page_settings.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>


</head>

<body>

<div class="forError"><? if($errors){var_dump($errors);} ?></div>

<a href="<? echo $referer; ?>" class="return">Вернуться</a>
<a href="#" class="addPage">Добавить слайдер</a>

<section class="addItems">
    <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
        <input type="hidden" name="stranica" value="<? echo $_GET["stranica"]; ?>"/>
        <input type="file" name="photo[]" multiple />

        <input name="submit" type="submit" value="готово"/>
    </form>
</section>

<ul class="listItems">
    <li>
        <a href="#" style="background-image:url();"></a>
    </li>
</ul>





<script type="text/javascript" src="../js/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="../js/adm/page_settings.min.js"></script>
</body>
</html>

