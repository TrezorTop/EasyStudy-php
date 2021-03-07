<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
} else {
    echo "Not logged in";
}

echo "<h1>Notifications</h1>";

if (DB::query('SELECT * FROM notifications WHERE receiver=:userid', array(':userid' => $userId))) {
    $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid ORDER BY id DESC', array(':userid' => $userId));

    foreach ($notifications as $notification) {

        if ($notification['type'] == 1) {
            $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid' => $notification['sender']))[0]['username'];

            if ($notification['extra'] == "") {
                echo "You got a notification! ('extra' column is NULL) <hr>";
            } else {
                $extra = json_decode($notification['extra']);
                echo $senderName . "  mentioned you in a post! - " . $extra->postbody . " <hr> ";
            }

        } else if ($notification['type'] == 2) {
            $senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid' => $notification['sender']))[0]['username'];
            echo $senderName . " liked your post <hr>";
        }

    }
}