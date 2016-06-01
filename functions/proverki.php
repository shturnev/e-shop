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

