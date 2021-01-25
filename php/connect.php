<?php

$host = "SocialNetwork";
$user = "root";
$password = "root";
$database = "social_network";

$link = mysqli_connect($host, $user, $password, $database);

if (!$link) {
    echo "Error: attempt to connect to MySQL failed." . PHP_EOL;
    echo "Error number: " . mysqli_connect_errno() . PHP_EOL;
    echo "Error text: " . mysqli_connect_error() . PHP_EOL;
    mysqli_close($link);
    exit;
}

//echo "Соединение с MySQL установлено!" . PHP_EOL;
//echo "Информация о сервере: " . mysqli_get_host_info($link) . PHP_EOL;

