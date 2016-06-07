<?php


/**
 * Функция, которая будет подсчитывать кол-во страниц
 * @param $array - ["limit, page, posts, max_pages]
 * @return mixed
 */
function page_nav($array)
{

    $limit      = $array["limit"];
    $page       = intval($array["page"]);
    $posts      = $array["posts"];
    $max_pages  = $array["max_pages"]; //сколько штук-страниц отдать в стэк


    //проверки
    if(!$limit or !$posts) { return false;}
    if(!$max_pages) { $max_pages = 3; }


    // Находим общее число страниц
    $total = (($posts - 1) / $limit) + 1;
    $total =  intval($total);


    // Если значение $page меньше единицы или отрицательно переходим на первую страницу
    // А если слишком большое, то переходим на последнюю
    if(empty($page) or $page < 0)   { $page = 1; }
    if($page > $total)              { $page = $total; }

    // Вычисляем начиная с какого номера
    // следует выводить сообщения
    $start = $page * $limit - $limit;

    // Выбираем $limit сообщений начиная с номера $start
    if ($start < 0) {$start = 0;}


    /*
        2ая часть
    */

    // Проверяем нужны ли стрелки назад
    if ($page != 1) { $pervpage = $page - 1; }

    // Проверяем нужны ли стрелки вперед
    if ($page != $total) { $nextpage = $page + 1; }

    //находим страницу для стрелочек на самую первую (в начало)
    if($pervpage) { $beginning = 1; }



    //формируем arr для отправки
    $arr["start"] = $start;
    $arr["limit"] = $limit;

    //находим стэк
    if(!$pervpage && !$nextpage) { $arr["stack"] = false; return $arr; }

    $arr["stack"]["first"]   = $beginning;
    $arr["stack"]["last"]    = $total;
    $arr["stack"]["center"]  = $page;
    $arr["stack"]["prev"]    = $pervpage;
    $arr["stack"]["next"]    = $nextpage;
    $arr["stack"]["left"]    = array();
    $arr["stack"]["right"]   = array();

    // Находим две ближайшие станицы с обоих краев, если они есть
    for($i=$max_pages; $i>=1; --$i)
    {
        if($page - $i > 0) { $arr["stack"]["left"][] = $page - $i; }
    }


    for($i=1; $i<=$max_pages; ++$i)
    {
        if($page + $i <= $total) { $arr["stack"]["right"][] = $page + $i; }
    }


    //response
    return $arr;

}

function page_nav2($array){
    $url            = $array["url"];
    $stack          = $array["stack"];
    $firstAndLast   = $array["f_l"];

    if(!$stack){ return false; }

    if($firstAndLast)       { echo '<a href="'.$url.$stack["first"].'" class="first"><i class="material-icons">&#xE314;&#xE314;</i></a>'; }
    if($stack["prev"])      { echo '<a href="'.$url.$stack["prev"].'" class="prev"><i class="material-icons">&#xE314;</i></a>';  }

    if($stack["left"]){
        foreach ($stack["left"] as $item) {
            echo '<a href="'.$url.$item.'" class="item">'.$item.'</a>';
        }
    }


    if($stack["center"])    { echo '<span class="center">'.$stack["center"].'</span>';  }


    if($stack["right"]){
        foreach ($stack["right"] as $item) {
            echo '<a href="'.$url.$item.'" class="item">'.$item.'</a>';
        }
    }

    if($stack["next"])      { echo '<a href="'.$url.$stack["next"].'" class="next"><i class="material-icons">&#xE315;</i></a>';  }
    if($firstAndLast)       { echo '<a href="'.$url.$stack["last"].'" class="next"><i class="material-icons">&#xE315;&#xE315;</i></a>'; }


}