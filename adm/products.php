<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/path.php");
require_once("../functions/proverki.php");
require_once("../functions/saveImg.php");
require_once("../functions/pagination.php");
require_once("../functions/product.php");

$Admin = is_admin();
if(!$Admin){ exit("Нет прав доступа"); }

$referer    = ($_POST["referer"])? $_POST["referer"]: $_SERVER["HTTP_REFERER"];
              if($_GET["referer"]){$referer = $_GET["referer"];}
$this_page  = path_withoutGet();


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
                $ID = $_POST["ID"];

                //проверим есть ли такая запись
                $pr_item = db_row("SELECT * FROM ".$table." WHERE ID=".$ID)["item"];
                if(!$pr_item){  $response["error"] = "Ошибка: переданы не верные параметры. Строка: ". __LINE__; break; }


                if($_FILES["photo"]["tmp_name"]){
                    $tmp = [
                         "maw"       => 1024
                        ,"miw"       => 200
                        ,"path"      => $path
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
//                else
//                {
//
//                }


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
    $resItem = db_row("SELECT * FROM products WHERE ID=".$_GET["ID"])["item"];




endif;


/*-----------------------------------
Вывод категорий
-----------------------------------*/
$catItems = db_select("SELECT * FROM categories ORDER BY title", true)["items"];

/*------------------------------
Вывод записей
-------------------------------*/
$arr = [
    "cat_id"    => @$_GET["cat_id"]
   ,"page"      => @$_GET["page"]
   ,"limit"     => 15
];
$resProducts = products_get_1($arr, true);




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

    <!--Добавить позицию-->
    <a href="#" class="addPage">Добавить позицию</a>
    <? $tmp = (!$resItem)? "hidden": null;  ?>
    <section class="st-formCont" <? echo $tmp; ?>>

        <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
            <? $method = ($resItem)? "edit": "add"; ?>
            <input type="hidden" name="method_name" value="<? echo $method; ?>">
            <input type="hidden" name="ID" value="<? echo $resItem["ID"]; ?>">


            <? if($resItem["photo"]){ ?>
                <img src="../FILES/products/small/<? echo $resItem["photo"] ?>" alt="" align="right" width="100">
            <? } ?>

            <div class="row">
                <p>Категория</p>
                <select name="cat_id" id="">
                    <? if($catItems){
                        foreach ($catItems as $item) {

                            $selected = ($resItem["cat_id"] == $item["ID"])? "selected": null;

                            ?>
                            <option value="<? echo $item["ID"] ?>" <? echo $selected; ?>><? echo $item["title"] ?></option>
                        <? }
                    } ?>
                </select>
            </div>

            <div class="row">
                <p>Тип</p>
                <select name="type" >
                    <?
                        $tmp = [1 => "Мужская", 2 => "Женская"];
                        foreach ($tmp as $item => $value) {
                            $selected = ($resItem["type"] == $item)? "selected": null; ?>
                            <option value="<? echo $item ?>" <? echo $selected; ?>><? echo $value ?></option>
                        <? } ?>
                </select>
            </div>

            <div class="row">
                <p>Заголовок</p>
                <input type="text" name="title" value="<? echo $resItem["title"]; ?>" placeholder="Заголовок" required/>
            </div>

            <div class="row">
                <p>Фото</p>
                <input type="file" name="photo" >
            </div>

            <div class="row">
                <p>Цена: простая / со скидкой</p>
                <input type="text" value="<? echo $resItem["price"]; ?>" name="price" placeholder="простая"/>
                <input type="text" value="<? echo $resItem["price_2"]; ?>" name="price_2" placeholder="со скидкой"/>
            </div>

            <div class="row">
                <p>Текст</p>
                <textarea name="text" class="js-ckeditor"><? echo $resItem["text"]; ?></textarea>
            </div>

            <div class="row">
                <input name="submit" type="submit" value="Добавить"/>
            </div>

        </form>
    </section>


    <!--Вывод позиций-->
    <div class="catBlock">
        <form action="<? echo $this_page ?>" method="get" enctype="multipart/form-data" name="myForm" target="_self">
            <input type="hidden" name="referer" value="<? echo $referer; ?>">
            <h5>Выбрать категорию</h5>

            <div class="flex">
                <select name="cat_id">
                    <option value="null">--//--</option>
                    <? if($catItems){
                        foreach ($catItems as $item) {
                            $selected = ($_GET["cat_id"] && $_GET["cat_id"] == $item["ID"])? "selected": null;

                            ?>
                            <option value="<? echo $item["ID"] ?>" <? echo $selected ?>><? echo $item["title"] ?></option>
                        <? }
                    } ?>
                </select>

<!--                <input name="submit" type="submit" value="OK"/>-->
                <button>ok</button>
            </div>
        </form>
    </div>

    <? if($resProducts["items"]): ?>
    <ul class="listItems">
        <? foreach ($resProducts["items"] as $item) { ?>
        <li>
            <div class="col-1">
                <img src="../FILES/products/small/<? echo $item["photo"] ?>" alt="" style="vertical-align: middle; height: 50px;" >
                <a href="#<? echo $item["ID"]; ?>" class="title"><? echo $item["title"]; ?></a>
            </div>
            <div class="col-2">
                <a href="<? echo $this_page."?method_name=edit&ID=".$item["ID"]."&referer=".$referer; ?>" class="edit" title="Редактировть"><i class="material-icons">&#xE254;</i></a>
                <a href="options.php?method_name=deleteProduct&ID=<? echo $item["ID"]; ?>" class="delete js-delete" title="Удалить"><i class="material-icons">&#xE92B;</i></a>
            </div>
        </li>
        <? } ?>
    </ul>
    <? endif; ?>




    <? if($resProducts["stack"]):

        $tmp = ($cat_id)? "cat_id=".$cat_id."&" : null;
        $tmp = ($referer)? $tmp."referer=".$referer."&" : null;

        $arrTmp = [
            "url"   => $this_page."?".$tmp."page="
           ,"stack" => $resProducts["stack"]
        ]; 
        
    ?>
    <section class="postrNav tCenter mt50 mb50" >
        <? page_nav2($arrTmp); ?>
    </section>
    <? endif; ?>

</section>





<script type="text/javascript" src="../js/jquery-2.2.4.min.js"></script>

<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../ckeditor/adapters/jquery.min.js"></script>

<script type="text/javascript" src="../js/adm/page_settings.min.js"></script>
<script type="text/javascript" src="../js/adm/forEditor.min.js"></script>
<script type="text/javascript" src="../js/adm/products.min.js"></script>
</body>
</html>

