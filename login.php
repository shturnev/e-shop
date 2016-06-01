<?php
require_once("functions/DB.php");
require_once("functions/proverki.php");
require_once("functions/saveImg.php");
require_once("functions/auth.php");



if(isset($_POST["submit"])):

    switch ($_POST["method_name"]):
        case $_POST["method_name"] == "register" && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && $_POST["pass"]:

            $email  = strtolower(filter_var($_POST["email"]));
            $pass   = proverka1($_POST["pass"]);
            $nick   = proverka1($_POST["nickname"]);
            $errors = [];
            //Узанаем есть ли уже такой пользователь
                $resDb = db_select("SELECT ID FROM users WHERE email='".$email."'");
                if($resDb["items"]){
                    exit("Такой пользователь уже существует");
                }
                else{

                    //Добавим картинку
                    if($_FILES["avatar"]["tmp_name"]){
                        $resPhoto = photo_add_once(["maw" => 800, "miw" => 160, "path" => "FILES/", "inputName" => "avatar"]);
                        if($resPhoto["error"]){exit($resPhoto["error"]);}
                    }




                    $tmp  = [
                        "email"     => $email
                        ,"pass"     => md5($pass)
                        ,"nickname" => $nick
                        ,"date"     => time()
                        ,"avatar"   => @$resPhoto["filename"]
                    ];

                    $resDb = db_insert("users", $tmp, true);
                    if(!$resDb){exit("Ошибка при записи в бд. На строке:".__LINE__);}


                    setcookie("ID", $resDb["ID"], strtotime("+1 day"), "/");
                    setcookie("token", $tmp["pass"], strtotime("+1 day"), "/");


                    $goto = true;




                }




        break;
        case $_POST["method_name"] == "auth" && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && $_POST["pass"]:
            $email  = strtolower(filter_var($_POST["email"]));
            $pass   = proverka1($_POST["pass"]);

            $resDb = db_select("SELECT * FROM users WHERE email='".$email."' AND pass='".md5($pass)."'")["items"][0];
            if($resDb){
                setcookie("ID", $resDb["ID"], strtotime("+1 day"), "/");
                setcookie("token", $resDb["pass"], strtotime("+1 day"), "/");
            }

            $goto = true;


            break;
    endswitch;

endif;


/*------------------------------
Дополн ф-ии
-------------------------------*/


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <title></title>
    <link rel="shortcut icon" href=""/>
    <link rel="stylesheet" type="text/css" media="all" href="css/login.css"/>

    <script type="text/javascript" src=""></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>


</head>

<body>




<main>

    <? if(!is_auth()){ ?>
    <section class="col-1">
        <h1>Регистрация</h1>

        <div class="forForm">
            <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
                <input type="hidden" name="method_name" value="register" />

                <input type="email" name="email" value="" placeholder="Введите свой email"/><br><br>
                <input type="password" name="pass" value="" placeholder="Введите свой пароль"/><br><br>
                <input type="text" name="nickname" value="" placeholder="Введите свой nickname"/><br><br>
                <input type="file" name="avatar" value="" /><br><br>

                <input name="submit" type="submit" value="готово"/>
            </form>
        </div>
    </section>
    <section class="col-2">
        <h1>Авторизация</h1>

        <div class="forForm">
            <form action="" method="post" enctype="multipart/form-data" name="myForm" target="_self">
                <input type="hidden" name="method_name" value="auth" />

                <input type="email" name="email" value="" placeholder="Введите свой email"/><br><br>
                <input type="password" name="pass" value="" placeholder="Введите свой пароль"/><br><br>

                <input name="submit" type="submit" value="готово"/>
            </form>
        </div>
    </section>
    <? }else{ ?>



        авторизован <? var_dump(is_auth()); ?>

        <script>
            window.location = "index.php";
        </script>


    <? } ?>
</main>


</body>
</html>

