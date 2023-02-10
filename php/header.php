<?php
?>
<header>
    <p class="name"><?php echo  $_SESSION["userName"]; ?></p>
    <?php
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    $url = explode('/', $url[0]);
    $url = $url[count($url) - 1];

    if (!isset($_SESSION['userName'])) {
        if ($url !== 'login.php') {
            echo ' <form action="login.php" method="post" class="reg">
                <input class="header-btn" type="submit" value="Войти">
            </form>';
        }
        if ($url == 'login.php') {
            echo ' <form action="index.php" method="post" class="reg">
                <input class="header-btn" type="submit" value="Зарегистрироваться">
            </form>';
        }
    } else {
        echo ' <form action="index.php" method="post" class="reg">
        <input type="hidden" name="exit_form" value="1">
        <input class="header-btn" type="submit" value="Выйти">
    </form>';
    }
    ?>
</header>