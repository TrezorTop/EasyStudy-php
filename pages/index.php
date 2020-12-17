<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn($link)) {
    echo "Logged In";
    echo "<br> User id is " . Login::isLoggedIn($link);
} else {
    echo "Not logged in";
}

?>