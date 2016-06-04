<?php

//Функции для разного уровня экранизации

/**
 * Максимальный экран
 * @param $str
 * @return string
 */
function proverka1($str){
    $str = htmlspecialchars($str);
    $str = addslashes($str);

    return $str;
}

/**
 * Малое экранирование, для админ раздела
 * @param $str
 * @return string
 */
function proverka2($str){
//    $str = htmlspecialchars($str);
    $str = addslashes($str);

    return $str;
}

