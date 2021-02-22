<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
} else {
    echo "Not logged in";
}

if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid' => $userId))) {
    $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid' => $userId));

    foreach ($notifications as $notification) {
        echo $notification['type'];
    }
}