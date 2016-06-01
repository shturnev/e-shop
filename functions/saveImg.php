<?



/**
 * Добавить одну картинку
 * @param $arr - [maw, miw, path, inputName]
 * @return array - [error, filename]
 */
function photo_add_once($arr){
    $maw        = (!is_numeric($arr["maw"]))? 800 : $arr["maw"];
    $miw        = (!is_numeric($arr["miw"]))? 800 : $arr["miw"];
    $path       = (!$arr["path"])? "FILES" : $arr["path"];
    $inputName  = (!$arr["inputName"])? "photo" : $arr["inputName"];

    $response = [];

    if($_FILES[$inputName]["error"]){
        $response["error"] = file_errors($_FILES[$inputName]["error"]) . " Файл: ".$_FILES[$inputName]["name"];
        return $response;
    }
    else
    {
        $imgInfo = getimagesize($_FILES[$inputName]["tmp_name"]);
        if(!$imgInfo){
            $response["error"] = "Ошибка: не верный формат изображения ".$_FILES[$inputName]["name"]. " На строке:".__LINE__;
            return $response;
        }
        if($imgInfo[2] > 3 or !$imgInfo[2]){
            $response["error"] = "Ошибка: не верный формат изображения ".$_FILES[$inputName]["name"]. " На строке:".__LINE__;
            return $response;
        }

        $newFileName = md5(time().rand(0,10000));
        $ext = image_type_to_extension($imgInfo[2]); //возвращает расширение файла
        if(!$ext){
            $response["error"] = "Ошибка: при получинии расширения файла ".$_FILES[$inputName]["name"]. " На строке:".__LINE__;
            return $response;
        }
        $newFileName .= $ext;


        //подготовим папку
        if(!is_dir($path."big/")){ mkdir($path."big/", 0777, true); }
        if(!is_dir($path."small/")){ mkdir($path."small/", 0777, true); }


        $res = saveImg($_FILES[$inputName]["tmp_name"], $path."big/".$newFileName, $maw);
        if(!$res){
            $response["error"] = "Ошибка: при сохранении файла ".$_FILES[$inputName]["name"]. " На строке:".__LINE__;
            return $response;
        }
        $res = saveImg($_FILES[$inputName]["tmp_name"], $path."small/".$newFileName, $miw);
        if(!$res){
            $response["error"] = "Ошибка: при сохранении файла ".$_FILES[$inputName]["name"]. " На строке:".__LINE__;
            return $response;
        }

        $response["filename"] = $newFileName;
        return $response;

    }


}


/**
 * добавить несколько фотографий
 * @param $arr - [maw, miw, path, inputName]
 * @return array -  [error[], filename[]]
 */
function photo_add_few($arr){

    $maw        = (!is_numeric($arr["maw"]))? 800 : $arr["maw"];
    $miw        = (!is_numeric($arr["miw"]))? 160 : $arr["miw"];
    $path       = (!$arr["path"])? "FILES" : $arr["path"];
    $inputName  = (!$arr["inputName"])? "photo" : $arr["inputName"];

    $response = [];


    for($i = 0; $i < count($_FILES[$inputName]["tmp_name"]); ++$i){

        if($_FILES[$inputName]["error"][$i]){
            $response[$i]["error"] = file_errors($_FILES[$inputName]["error"][$i]) . " Файл: ".$_FILES[$inputName]["name"][$i];
            continue;
        }
        else
        {
            $imgInfo = getimagesize($_FILES[$inputName]["tmp_name"][$i]);
            if(!$imgInfo){
                $response[$i]["error"] = "Ошибка: не верный формат изображения ".$_FILES[$inputName]["name"][$i]. " На строке:".__LINE__;
                continue;
            }
            if($imgInfo[2] > 3 or !$imgInfo[2]){
                $response[$i]["error"] = "Ошибка: не верный формат изображения ".$_FILES[$inputName]["name"][$i]. " На строке:".__LINE__;
                continue;
            }

            $newFileName = time().rand(0,10000).$i;
            $ext = image_type_to_extension($imgInfo[2]); //возвращает расширение файла
            if(!$ext){
                $response[$i]["error"] = "Ошибка: при получинии расширения файла ".$_FILES[$inputName]["name"][$i]. " На строке:".__LINE__;
                continue;
            }
            $newFileName .= $ext;


            //подготовим папку
            if(!is_dir($path."/big/")){ mkdir($path."/big/", 0777, true); }
            if(!is_dir($path."/small/")){ mkdir($path."/small/", 0777, true); }


            $res = saveImg($_FILES[$inputName]["tmp_name"][$i], $path."/big/".$newFileName, $maw);
            if(!$res){
                $response[$i]["error"] = "Ошибка: при сохранении файла ".$_FILES[$inputName]["name"][$i]. " На строке:".__LINE__;
                continue;
            }
            $res = saveImg($_FILES[$inputName]["tmp_name"][$i], $path."/small/".$newFileName, $miw);
            if(!$res){
                $response[$i]["error"] = "Ошибка: при сохранении файла ".$_FILES[$inputName]["name"][$i]. " На строке:".__LINE__;
                continue;
            }


            $response[$i]["filename"] = $newFileName;

        }
    }

    return $response;

}
    



/*------------------------------
Дополнительные функции
-------------------------------*/
/**
 * Функция для сохранения и уменьшения картинки
 * @param $filein - ответ от $_FILES["name"]
 * @param $fileOut - полный путь + название файла куда сохранить картинку
 */
function saveImg($filein, $fileOut, $new_w){

    $fileInfo = getimagesize($filein);

    switch ($fileInfo[2]):
        case 1:  $old = imageCreateFromGif($filein);  break;
        case 2:  $old = imageCreateFromJpeg($filein); break;
        case 3:  $old = imageCreateFromPng($filein);  break;
    endswitch;

    //узнаем нужные размеры
    switch ($fileInfo[0]):
        case $fileInfo[0] > $fileInfo[1]:  $k = $fileInfo[1] /  $fileInfo[0]; break;
        case $fileInfo[0] < $fileInfo[1]:  $k = $fileInfo[0] /  $fileInfo[1]; break;
        case $fileInfo[0] == $fileInfo[1]: $k = 1; break;
    endswitch;


    $new_w = ($fileInfo[0] > $new_w)? $new_w : $fileInfo[0];
    $new_h = $new_w * $k;


    $new   = imageCreateTrueColor($new_w, $new_h);
    imagealphablending($new, false);
    imagesavealpha($new, true);
    imageCopyResampled($new, $old, 0, 0, 0, 0, $new_w, $new_h, $fileInfo[0], $fileInfo[1]);

    switch ($fileInfo[2]):
        case 1: imagegif($new, $fileOut);  break;
        case 2: imagejpeg($new, $fileOut, 90); break;
        case 3: imagepng($new, $fileOut, 0);  break;
    endswitch;

    imageDestroy($old);
    imageDestroy($new);

    return true;
}

/**
 * Для проверки на ошибки от загрузки файлов
 * @param int $errorNum - from $_FILES["error"]
 * @return mixed
 */
function file_errors($errorNum){

    $e = [

        1 => "Размер принятого файла превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini."
        ,2 => "Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме."
        ,3 => "Загружаемый файл был получен только частично."
        ,4 => "Файл не был загружен."
        ,6 => "Отсутствует временная папка. Добавлено в PHP 5.0.3."
        ,7 => "Не удалось записать файл на диск. Добавлено в PHP 5.1.0."
        ,8 => "PHP-расширение остановило загрузку файла. PHP не предоставляет способа определить какое расширение остановило загрузку файла; в этом может помочь просмотр списка загруженных расширений из phpinfo(). Добавлено в PHP 5.2.0."

    ];

    return $e[$errorNum];
}


