<?php
$host = 'localhost';
$database = 'project';
$user = 'root';
$password_db = '';
$link = mysqli_connect($host, $user, $password_db, $database) or die('Error ' . mysqli_error($link));
?>
