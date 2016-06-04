<?php

$DB_CONNECT_SETTINGS = ["host" => "127.0.0.1", "user" => "root", "pass" => "", "dbName" => "e-shop1"];

$DB = new mysqli(
         $DB_CONNECT_SETTINGS["host"]
        ,$DB_CONNECT_SETTINGS["user"]
        ,$DB_CONNECT_SETTINGS["pass"]
        ,$DB_CONNECT_SETTINGS["dbName"]
);

if($DB->connect_error){ exit("Ошибка: при подключении к БД. " .$DB->connect_error . ". На строке:". __LINE__ ); }


/**
 * Универсальная ф-ия, выполняющая запрос к БД
 * @param $sql
 * @return array
 */
function db_query($sql){

    global $DB, $DB_CONNECT_SETTINGS;

    if(!@$DB->ping()){
        $DB->connect(
             $DB_CONNECT_SETTINGS["host"]
            ,$DB_CONNECT_SETTINGS["user"]
            ,$DB_CONNECT_SETTINGS["pass"]
            ,$DB_CONNECT_SETTINGS["dbName"]
        );

        if($DB->connect_error){ exit("Ошибка: при подключении к БД. " .$DB->connect_error . ". На строке:". __LINE__ ); }
    }

    $resQuery = $DB->query($sql);

    $arr = [
        "error"  => $DB->error
       ,"result" => $resQuery
    ];

    return $arr;
}


/**
 * Вставить в таблицу одиночную запись
 * @param $table - название таблицы
 * @param $array - массив с данными для вставки, должен совпадать с структурой указонной таблицы
 * @param bool|false $close
 */
function db_insert($table, $array, $close = false){

    if(!$array or ! $table){ exit("Ошибка: не верные параметры. На строке:". __LINE__ ); }
    global $DB;

    $sql = "INSERT INTO ".$table." (".implode(",", array_keys($array)).") VALUES ('".implode("','", array_values($array))."')";
    $res = db_query($sql);

    $res["ID"] = $DB->insert_id;

    if($close){ $DB->close(); }
    return $res;
}

/**
 * Обновить записи в бд.
 * @param $table - название таблицы
 * @param $array - массив данных, должен быть === структуре $table
 * @param $where - условие WHERE
 * @param bool|false $close - следует ли закрывать соединение с БД
 * @return array
 */
function db_update($table, $array, $where, $close = false){

    if(!$array or ! $table){ exit("Ошибка: не верные параметры. На строке:". __LINE__ ); }
    global $DB;

    $keys = array_keys($array);
    $vals = array_values($array);
    $tmp  = [];

    for ($i = 0; $i < count($keys); $i++):
        $tmp[] = $keys[$i] . "='".$vals[$i]."'";
    endfor;

    $sql = "UPDATE ".$table." SET ".implode(",", $tmp)." WHERE ".$where;
    $res = db_query($sql);

    if($close){ $DB->close(); }

    return $res;
}


/**
 * Удалить записи из БД
 * @param $table - название таблицы
 * @param $where - условие WHERE
 * @param bool|false $close - следует ли закрывать соединение с БД
 * @return array
 */
function db_delete($table, $where, $close = false){

    if(!$where or ! $table){ exit("Ошибка: не верные параметры. На строке:". __LINE__ ); }
    global $DB;

    $sql = "DELETE FROM ".$table." WHERE ".$where;
    $res = db_query($sql);

    if($close){ $DB->close(); }

    return $res;
}

/**
 * Универсальный select метод, для выборки из БД
 * @param $sql - полный SELECT запрос
 * @param bool|false $close - следует ли закрыть бд
 * @return array
 */
function db_select($sql, $close = false){

    if(!$sql){ exit("Ошибка: не верные параметры. На строке:". __LINE__ ); }
    global $DB;

    $res = db_query($sql);
    if($res["error"] or !$res["result"]->num_rows){
        if($close){ $DB->close(); }
        return $res;
    }

    $row    = null;

    while($row = $res["result"]->fetch_assoc()){
        $res["items"][] = $row;
    }

    if($close){ $DB->close(); }

    return $res;
}


/**
 * Select для одной записи
 * @param $sql
 * @param bool|false $close
 * @return array - ["error | item]
 */
function db_row($sql, $close = false){
    if(!$sql){ exit("Ошибка: не верные параметры. На строке:". __LINE__ ); }
    global $DB;

    $res = db_query($sql);
    if($res["error"] or !$res["result"]->num_rows){
        if($close){ $DB->close(); }
        return $res;
    }

    $res["item"] = $res["result"]->fetch_assoc();
    if($close){ $DB->close(); }

    return $res;

}

/**
 * @param $table
 * @param $array - многомерный ассоциативный массив $arr[0][0]
 * @param bool|false $close
 * @return array
 */
function db_duplicate_update($table, $array, $close = false){

    if(!$array or ! $table){ exit("Ошибка: не верные параметры. На строке:". __LINE__ ); }
    global $DB;

    $keys    = array_keys($array[0]);
    $values  = [];
    foreach($array as $item){
        $values[] = "('".implode("','", array_values($item))."')";
    }

    foreach ($keys as $key) {
        $values2[] =  $key."=VALUES(".$key.")";
    }


    $sql = "INSERT INTO ".$table." (".implode(",", $keys).") VALUES ".implode(",", $values) . " ON DUPLICATE KEY UPDATE " .implode(",",$values2);
    $res = db_query($sql);

    $res["ID"] = $DB->insert_id;

    if($close){ $DB->close(); }
    return $res;




    /*
     *
     * INSERT INTO table (a,b,c) VALUES (1,2,3),(1,2,3) ON DUPLICATE KEY UPDATE a= VALUES(a), b = VALUES(b), c = VALUES(c);
  ;*/
}




?>