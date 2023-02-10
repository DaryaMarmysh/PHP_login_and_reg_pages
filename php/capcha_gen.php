<?php
session_start();
$width =200;
$height=70;
$img = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($img, rand(100, 255), rand(100, 255), rand(100, 255));
imagefill($img, 0, 0, $bg);
$linenum = rand(5, 10);
for ($i = 0; $i < $linenum; $i++) {
    $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    imageline(
        $img,
        rand(0, 10),
        rand(1, 100),
        rand(190, 200),
        rand(1, 100),
        $color
    );
}
$a_num=rand(1,5);
$b_num=rand(1,5);
$captcha_true=str_repeat('a',$a_num).str_repeat('b',$b_num);
$captcha="a$a_num+b$b_num";
imagettftext($img, 36, 0, 50, 60,  $color, "D:/OpenServer/domains/localhost/labs/lab13/ROMANTIC.ttf", $captcha);
 for ($i = 0; $i < $linenum; $i++) {
    $color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    imageline(
        $img,
        rand(0, 10),
        rand(1, 100),
        rand(190, 200),
        rand(1, 100),
        $color
    );
}
header("Content-type: image/gif");
$_SESSION['captcha_true'] = $captcha_true;
imagegif($img);
imagedestroy($img);
