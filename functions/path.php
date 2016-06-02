<?php

/**
 * Вывод чистого uri пути
 * @return string
 */
function path_clear_path(){
    return strstr(__DIR__, "\\functions", true);
}

/**
 * Вывод читого URL
 * @return string
 */
function path_clear_url(){
    return "http://". $_SERVER["HTTP_HOST"];
}

/**
 * Вывод адреса страницы без GET
 * @return string
 */
function path_withoutGet(){
    return  strstr(path_clear_url().$_SERVER["REQUEST_URI"], "?", true);
}


