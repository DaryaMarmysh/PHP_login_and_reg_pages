<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
    <title>Авторизация</title>
</head>
<?php
session_start();
function clearString($str)
{
    $str = trim($str);
    $str = strip_tags($str);
    $str = stripslashes($str);
    return $str;
}
$loginError =  '';
$userExists = '';
if (isset($_POST['form'])) {
    $login = clearString($_POST["login"]);
    $password = clearString($_POST["password"]);
    include '../database/db.php';
    $query = "SELECT id FROM users WHERE login='$login'";
    $result = mysqli_query($link, $query) or die("Ошибка выполнения запроса" .
        mysqli_error($link));
    if ($result) {
        $row = mysqli_fetch_row($result);
        if (empty($row[0])) $loginError .= "Данный логин не зарегистрирован";
    }
    if ($loginError == '') {
        $passwordQuery = "SELECT password FROM users WHERE login='$login'";
        $passwordResult = mysqli_query($link, $passwordQuery) or die("Ошибка
        выполнения запроса" . mysqli_error($link));
        if ($passwordResult) {
            $passworsRow = mysqli_fetch_row($passwordResult);
            if (md5($password) == $passworsRow[0]) {
                $userExists = true;
            } else {
                $userExists = false;
                print "<script language='Javascript' type='text/javascript'>
        alert('Такого пользователя не существует!');
        </script>";
            }
        }
    }
    if ($userExists) {
        $nameQuery = "SELECT name FROM users WHERE login='$login'";
        $nameResult = mysqli_query($link, $nameQuery) or die("Ошибка выполнения запроса" . mysqli_error($link));
        if ($nameResult) {
            $nameRow = mysqli_fetch_row($nameResult);
            $_SESSION["userName"] = $nameRow[0];
        }
        
      print "<script language='Javascript' type='text/javascript'>
        alert(`Вы успешно вошли в аккаунт!`);
        function reload(){top.location = './main_load.php'};
        reload();
        </script>";
    }
}

?>

<body>
    <?php require './header.php' ?>

    <main>
        <form action="login.php" method="post" class="form">

            <input class="input" name="login" type="text" placeholder="Логин" required>
            <span class="error"><?= @$loginError; ?></span>
            <input class="input" name="password" type="password" placeholder="Пароль" required>
            <span class="error"><?= @$passwordError; ?></span>
            <input type="hidden" name="form" value="5">
            <input type="submit" class="button" value="Войти">
            
        </form>
        <form action="index.php" method="post" class='reg'>
                <input class="registrate-input" type="submit" value="Зарегистрироваться">
            </form>
        </div>

    </main>

    <?php require 'footer.php' ?>
</body>

</html>