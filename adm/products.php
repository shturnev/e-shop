<?php
require_once("../functions/DB.php");
require_once("../functions/auth.php");
require_once("../functions/path.php");
require_once("../functions/proverki.php");
require_once("../functions/saveImg.php");
require_once("../functions/pagination.php");

$Admin = is_admin();
if(!$Admin){ exit("Нет прав доступа"); }

$referer    = ($_POST["referer"])? $_POST["referer"]: $_SERVER["HTTP_REFERER"];
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


/*-----------------------------------
Вывод категорий
-----------------------------------*/
$catItems = db_select("SELECT * FROM categories ORDER BY title", true)["items"];

/*------------------------------
Вывод записей
-------------------------------*/
/**
 * @param $array - [<br>
 *                     <br> (int)cat_id     - id категории, не обязательный
 *                     <br> (int)page       - номер активной страницы
 *                     <br> (int)limit      - сколько штук на странице
 *                     <br> (string)order_1 - название поля по которому сортировать
 *                     <br> (string)order_2 - DESC/ASC
 *                 <br>]
 * @return array
 */
function products_get_1($array)
{
    $cat_id     = $array["cat_id"];
    $page       = (!is_numeric($array["page"]))? 0 : $array["page"];
    $limit      = (!is_numeric($array["limit"]))? 15 : $array["limit"];
    $order_1    = (!$array["order_1"])? "ID": $array["order_1"];
    $order_2    = ($array["order_2"] != "DESC" || $array["order_2"] != "ASC")? "ASC" : $array["order_2"];

    //---
    $result = [
        "error"  => false
        ,"items" => null
        ,"stack" => false
    ];

    //узнаем сколько всего штук в базе
    if(is_numeric($cat_id)){ $sqlTmp = " WHERE cat_id = ".$cat_id; }
    $count = db_row("SELECT COUNT(*) AS n FROM products ".$sqlTmp)["item"]["n"];
    if(!$count){
        $result["error"] = "По Вашему запросу записей не найдено";
        return $result;
    }

    //получим данные о постраничной навигации
    $arr = ["page" => $page, "limit" => $limit, "max_pages" => 3, "posts" => $count];
    $resNav = page_nav($arr);

    //Делаем выборку
    $sql = "SELECT * FROM products ".$sqlTmp." ORDER BY ".$order_1."='".$order_2."'
            LIMIT ".$resNav["start"].",".$resNav["limit"];
    $resItems = db_select($sql);
    if($resItems["error"]){
        $result["error"] = $resItems["error"];
        return $result;
    }

    //response
    $result["items"] =  $resItems["items"];
    $result["stack"] =  $resNav["stack"];

    return $result;
}

$arr = [
    "cat_id"    => @$_GET["cat_id"]
   ,"page"      => @$_GET["page"]
   ,"limit"     => 15
];

$resProducts = products_get_1($arr);




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


    <!--Вывод позиций-->
    <div class="catBlock">
        <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
            <input type="hidden" name="method_name" value="get">
            <h5>Выбрать категорию</h5>

            <div class="flex">
                <select name="cat_id">
                    <? if($catItems){
                        foreach ($catItems as $item) { ?>
                            <option value="<? echo $item["ID"] ?>"><? echo $item["title"] ?></option>
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
                <a href="#<? echo $item["ID"]; ?>" class="edit" title="Редактировть"><i class="material-icons">&#xE254;</i></a>
                <a href="options.php?method_name=deleteProduct&ID=<? echo $item["ID"]; ?>" class="delete js-delete" title="Удалить"><i class="material-icons">&#xE92B;</i></a>
            </div>
        </li>
        <? } ?>
    </ul>
    <? endif; ?>




    <? if($resProducts["stack"]):

        $tmp = ($cat_id)? "cat_id=".$cat_id."&" : null;
        
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

