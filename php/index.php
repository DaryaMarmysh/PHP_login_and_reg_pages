<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>HTML5 Template Design</title>
    <meta content='HTML5 Template Design' name='description' />
    <meta content='width=device-width, initial-scale=1' name='viewport' />
    <link rel="stylesheet" href="../css/style.css" />
</head>
<?php
session_start();
setlocale(LC_ALL, 'ru_RU.utf8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_http_output('UTF-8');
mb_language('uni');
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Moscow');

function clearString($str)
{
    $str = trim($str);
    $str = strip_tags($str);
    $str = stripslashes($str);
    return $str;
}

if (isset($_POST['exit_form'])) {
    session_unset();
}

if (isset($_POST['form'])) {
    $nameError = $loginError = $emailError = $passwordError = $captchaError = '';
    $name = clearString($_POST['name']);
    $login = clearString($_POST['login']);
    $email = clearString($_POST['email']);
    $password = clearString($_POST['password']);
    $password_re = clearString($_POST['password_re']);
    $captcha = clearString($_POST['captcha']);

    include '../database/db.php';
    //name validate
    if ($name == '') {
        $nameError .= "Заполните поле";
    } else if ((!preg_match('/(*UTF8)^([A-zА-я]){5,}$/m', $name))) {
        $nameError .= "Поле может содержать только латинские и русские символы. Минимум 5 знаков.";
    }
    //loginvalidate
    if ($login == '') {
        $loginError .= "Заполните поле";
    } else if ((!preg_match('/(*UTF8)(?!.*(.)\1\1)(?=.+[A-z])(?=.+[А-я])(?=.+[0-9])(?=.*[_])^([A-z0-9А-я_]){18,}$/m', $login))) {
        $loginError .= "Поле должно содержать: латинские символы, русские символы, цифры, '_'. Недопускается поаторение символа более 2 раз.Минимум 18 знаков.";
    } else {

        $query = "SELECT id FROM users WHERE login='$login'";
        $result = mysqli_query($link, $query) or die("Ошибка выполнения запроса" .
            mysqli_error($link));
        if ($result) {
            $row = mysqli_fetch_row($result);
            if (!empty($row[0])) $loginError .= "Данный логин занят";
        }
    }
    //email validate
    if ($email == '') {
        $emailError .= "Заполните поле";
    } else if ((!preg_match('/[A-z0-9]+\@[A-z0-9]+\.{1}(?!.*apo$)[A-z0-9]*$/m', $email))) {
        $emailError .= "Поле может содержать латинские символы и цифры. Не допускается домен 'apo'";
    } else {

        $query = "SELECT id FROM users WHERE email='$email'";
        $result = mysqli_query($link, $query) or die("Ошибка выполнения запроса" .
            mysqli_error($link));
        if ($result) {
            $row = mysqli_fetch_row($result);
            if (!empty($row[0])) $emailError .= "Пользователь с данной почтой уже существует";
        }
    }
    //password validate
    if ($password == '') {
        $passwordError .= "Заполните поле";
    } else if (!preg_match('/(*UTF8)(?=.+[A-z])(?=.+[А-я])(?!.*_.*)^([A-zА-я]){10,}$/m', $password)) {
        $passwordError .= "Поле должно содержать: латинские символы, русские символы. Недопускается '_'.Минимум 10 знаков.";
    }
    if ($password_re == '') {
        $password_reError .= "Заполните поле";
    } else if ($password_re !== $password) {
        $password_reError .= "Пороли не совпадают";
    }
    //capcha validate 
    if ($captcha == '') {
        $captchaError .= "Заполните поле";
    } else if ($captcha !==  $_SESSION['captcha_true']) {
        $captchaError .= "Капча введена неверно";
    }

    if ($nameError . $loginError . $emailError . $passwordError . $password_reError . $captchaError == '') {
        $file = fopen("log/$login.txt", 'a+');
        $password_hesh = hash('md5', $password);
        $query = "INSERT INTO users (name, login, email, password, file)
        VALUES ('$name','$login','$email','$password_hesh','" . file_get_contents("../log/$login.txt") . "')";
        $result = mysqli_query($link, $query) or die("Ошибка " .
            mysqli_error($link));
        if ($result) {
            printLog(true, $login);
            fclose($file);
            print "<script language='Javascript' type='text/javascript'>
            alert('Вы успешно зарегистрировались! Спасибо!');
            function reload(){top.location = 'login.php'};
            reload();
            </script>";
        } else {
            print "<script language='Javascript' type='text/javascript'>
            alert('Вы не были зарегистрированы');
            </script>";
        }
    } else {
        printLog(false, $login);
    }
}
function printLog($success, $login)
{
    $currentDate = date("d.m.y");
    $currentTime = date("H:i:s");
    $file = fopen("../log/$login.txt", 'a+');
    if ($success) {
        $log = "Регистрация прошла успешно (дата: $currentDate, время: $currentTime)" . PHP_EOL;
    } else {
        $log = "Регистрация завершена ошибкой (дата: $currentDate, время: $currentTime)" . PHP_EOL;
    }
    fwrite($file, $log);
    fclose($file);
}
?>

<body>

    <?php require 'header.php' ?>
    <main>
        <form action='index.php' method="post">
            <input type="hidden" name='form' value="1" required>

            <input type="text" name="name" placeholder="ФИО" value="<?= @$name; ?> " required>
            <span class="error"><?= @$nameError; ?></span>

            <input type="text" name="login" placeholder="Логин" value="<?= @$login; ?>" required>
            <span class="error"><?= @$loginError; ?></span>

            <input type="text" name="email" placeholder="Email" value="<?= @$email; ?>" required>
            <span class="error"><?= @$emailError; ?></span>

            <input type="password" name="password" placeholder="Пароль" value="<?php echo @$password; ?>" required>
            <span class="error"><?php echo @$passwordError; ?></span>

            <input type="password" name="password_re" placeholder="Повторите пароль" value="<?= @$password_re; ?>" required>
            <span class="error"><?= @$password_reError; ?></span>

            <div class="captcha">
                <div class="captcha-img-btn">
                    <a href="javascript:void(0);" class="captcha__refresh-btn" onclick="document.getElementById('capcha-image').src='http:\/\/localhost:81/labs/project/php/capcha_gen.php?rid='+Math.random();">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path style="fill: #676767" d="M1,12A11,11
                    ,0,0,1,17.882,2.7l1.411-1.41A1,1,0,0,1,21,2V6a1,1,0,0,1-1,1H16a1,1,0,0,1-.707-1.707l1.128-1.128A8.994,
                    8.994,0,0,0,3,12a1,1,0,0,1-2,0Zm21-1a1,1,0,0,0-1,1,9.01,9.01,0,0,1-9,9,8.9,8.9,0,0,1-4.42-1.166l1.127-1.127A1,1,0,0,0,8,17H4a1,1,0,0,0-1,1v4a1,1,0,0,0,.617.924A.987.987,0,0,0,4,23a1,1,0,0,0,.707-.293L6.118,21.3A10.891,10.891,0,0,0,12,23,11.013,11.013,0,0,0,23,12,1,1,0,0,0,22,11Z" />
                        </svg>
                    </a>
                    <img src='http://localhost:81/labs/project/php/capcha_gen.php' id='capcha-image' class="captcha__image" width="120">
                    <div class="box-input captcha-input">
                        <input class="input" name="captcha" type="text" value="<?= @$captcha; ?>">
                        
                    </div>
                </div>
                <span class="error capcha-error"><?= @$captchaError; ?></span>
            </div>

            <input type="submit" value="Зарегистрироваться" class="button">
        </form>
    </main>
    <?php require 'footer.php' ?>
</body>

</html>