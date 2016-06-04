<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/path.php");
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

    $table = "products";
    $path  = path_clear_path()."/FILES/products/";

    $response = [];

    //проверки
    if(!is_numeric($_POST["cat_id"])){ $response["error"][] = "Ошибка: не выбрана категории. на строке:".__LINE__; }
    if(!$_POST["type"]){ $response["error"][] = "Ошибка: не выбран тип товара. на строке:".__LINE__; }

    $arr = [
        "title"         => proverka1($_POST["title"])
        ,"date"         => time()
        ,"cat_id"       => $_POST["cat_id"]
        ,"type"         => $_POST["type"]
        ,"text"         => proverka2($_POST["text"])
        ,"price"        => proverka1($_POST["price"])
        ,"price_2"      => proverka1($_POST["price_2"])
    ];


    switch ($_POST["method_name"]):
        case "add":
            if($response["error"]){ break; }

            if($_FILES["photo"]["tmp_name"]){
                $tmp = [
                     "maw"       => 1024
                    ,"miw"       => 200
                    ,"path"      => $path
                    ,"inputName" => "photo"
                ];

                $resPhoto = photo_add_once($tmp);
                if($resPhoto["filename"]){ $arr["photo"] = $resPhoto["filename"]; }
                else
                {
                    $response["error"][] = $resPhoto["error"];
                }

            }

            $response["db"]  = db_insert($table, $arr);

            break;
        case "edit":
            if($response["error"]){ break; }


            if(!is_numeric($_POST["ID"])){
                $response["error"] = "Ошибка: переданы не верные параметры. Строка: ". __LINE__; break;
            }
            else
            {
                //проверим есть ли такая запись
                $pr_item = db_row("SELECT * FROM ".$table." WHERE ID=".$ID)["item"];
                if(!$pr_item){  $response["error"] = "Ошибка: переданы не верные параметры. Строка: ". __LINE__; break; }


                if($_FILES["photo"]["tmp_name"]){
                    $tmp = [
                        "maw"        => 1024
                        ,"miw"       => 200
                        ,"path"      => "FILES/products"
                        ,"inputName" => "photo"
                    ];

                    $resPhoto = photo_add_once($tmp);
                    if($resPhoto["filename"]){

                        $arr["photo"] = $resPhoto["filename"];

                        //удалим старую фотографию
                        if($pr_item["photo"])
                        {
                            if(file_exists($path."big/".$pr_item["photo"])){ unlink($path."big/".$pr_item["photo"]); }
                            if(file_exists($path."small/".$pr_item["photo"])){ unlink($path."small/".$pr_item["photo"]); }
                        }

                    }
                    else
                    {
                        $response["error"][] = $resPhoto["error"];
                    }

                }


                $response  = db_update($table, $arr, "ID = ".$_POST["ID"]);
            }

            break;

    endswitch;





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


/*-----------------------------------
Вывод категорий
-----------------------------------*/
$catItems = db_select("SELECT * FROM categories ORDER BY title", true)["items"];


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

<div class="forError"><? if($response["error"]){var_dump($response["error"]);} ?></div>

<a href="<? echo $referer; ?>" class="return" title="Вернуться"><i class="material-icons">&#xE31B;</i></a>
<!--<a href="#" class="addPage">Добавить слайдер</a>-->


<section id="products">
    <a href="#" class="addPage">Добавить позицию</a>
    <section class="st-formCont" hidden>

        <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
            <input type="hidden" name="method_name" value="add">

            <div class="row">
                <p>Категория</p>
                <select name="cat_id" id="">
                    <? if($catItems){
                        foreach ($catItems as $item) { ?>
                            <option value="<? echo $item["ID"] ?>"><? echo $item["title"] ?></option>
                        <? }
                    } ?>
                </select>
            </div>

            <div class="row">
                <p>Тип</p>
                <select name="type" >
                    <option value="1">Мужская</option>
                    <option value="2">Женская</option>
                </select>
            </div>

            <div class="row">
                <p>Заголовок</p>
                <input type="text" name="title" placeholder="Заголовок" required/>
            </div>

            <div class="row">
                <p>Фото</p>
                <input type="file" name="photo" >
            </div>

            <div class="row">
                <p>Цена: простая / со скидкой</p>
                <input type="text" name="price" placeholder="простая"/>
                <input type="text" name="price_1" placeholder="со скидкой"/>
            </div>

            <div class="row">
                <p>Текст</p>
                <textarea name="text" class="js-ckeditor"></textarea>
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

<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../ckeditor/adapters/jquery.min.js"></script>

<script type="text/javascript" src="../js/adm/page_settings.min.js"></script>
<script type="text/javascript" src="../js/adm/forEditor.min.js"></script>
</body>
</html>

