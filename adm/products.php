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
    <title>Работа с товарами</title>
    <link rel="shortcut icon" href=""/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../css/adm/page_settings.css" rel="stylesheet" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>


</head>

<body>

<div class="forError"><? if($errors){var_dump($errors);} ?></div>

<a href="<? echo $referer; ?>" class="return" title="Вернуться"><i class="material-icons">&#xE31B;</i></a>
<!--<a href="#" class="addPage">Добавить слайдер</a>-->


<section id="products">
    <a href="#" class="addPage">Добавить позицию</a>
    <section class="st-formCont" hidden>

        <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
            <input type="hidden" name="stranica" value="<? echo $_GET["stranica"]; ?>"/>

            <div class="row">
                <p>Заголовок</p>
                <input type="text" name="title" placeholder="Заголовок"/>
            </div>

            <div class="row">
                <input name="submit" type="submit" value="Добавить"/>
            </div>

        </form>
    </section>

    <div class="catBlock">
        <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">

            <h5>Выбрать категорию</h5>

            <div class="flex">
                <select name="cat_id">
                    <option value="">Lorem ipsum dolor.</option>
                    <option value="">Lorem ipsum dolor.</option>
                    <option value="">Lorem ipsum dolor.</option>
                </select>

                <input name="submit" type="submit" value="OK"/>
            </div>
        </form>
    </div>

    <ul class="listItems">
        <li>
            <div class="col-1">
                <a href="#" class="title">Lorem ipsum dolor sit.</a>
            </div>
            <div class="col-2">
                <a href="#" class="edit" title="Редактировть"><i class="material-icons">&#xE254;</i></a>
                <a href="#" class="delete" title="Удалить"><i class="material-icons">&#xE92B;</i></a>
            </div>
        </li>

    </ul>

    <section class="postrNav tCenter mt50">
        <a href="#" class="prev"><i class="material-icons">&#xE314;</i></a>
        <a href="#" class="item">22</a>
        <span class="center">1</span>
        <a href="#" class="item">2</a>
        <a href="#" class="next"><i class="material-icons">&#xE315;</i></a>
    </section>

</section>





<script type="text/javascript" src="../js/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="../js/adm/page_settings.min.js"></script>
</body>
</html>

