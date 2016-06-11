<?php
require_once("DB.php");
require_once("pagination.php");


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
function products_get_1($array, $close = false)
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
    $resItems = db_select($sql, $close);
    if($resItems["error"]){
        $result["error"] = $resItems["error"];
        return $result;
    }

    //response
    $result["items"] =  $resItems["items"];
    $result["stack"] =  $resNav["stack"];

    return $result;
}

/**
 * @param $array - [<br>
 *                     <br> (int)cat_id     - id категории, не обязательный
 *                     <br> (string)search  - id категории, не обязательный
 *                     <br> (int)page       - номер активной страницы
 *                     <br> (int)limit      - сколько штук на странице
 *                     <br> (string)order_1 - название поля по которому сортировать
 *                     <br> (string)order_2 - DESC/ASC
 *                 <br>]
 * @return array
 */
function products_get_2($array, $close = false)
{
    $cat_id     = $array["cat_id"];
    $search     = $array["search"];
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
    if(is_numeric($cat_id)){ $sqlTmp = " AND cat_id = ".$cat_id; }
    $count = db_row("SELECT COUNT(*) AS n FROM products WHERE MATCH (title,text) AGAINST ('".$search."') ".$sqlTmp)["item"]["n"];
    if(!$count){
        $result["error"] = "По Вашему запросу записей не найдено";
        return $result;
    }

    //получим данные о постраничной навигации
    $arr = ["page" => $page, "limit" => $limit, "max_pages" => 3, "posts" => $count];
    $resNav = page_nav($arr);

    //Делаем выборку
    $sql = "SELECT * FROM products WHERE MATCH (title,text) AGAINST ('".$search."') ".$sqlTmp."
            ORDER BY ".$order_1."='".$order_2."'
            LIMIT ".$resNav["start"].",".$resNav["limit"];
    $resItems = db_select($sql, $close);
    if($resItems["error"]){
        $result["error"] = $resItems["error"];
        return $result;
    }

    //response
    $result["items"] =  $resItems["items"];
    $result["stack"] =  $resNav["stack"];

    return $result;
}