<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (!Login::isLoggedIn()) {
    die("Not logged in");
}

if (isset($_POST['confirm-btn'])) {
    if (isset($_POST['logout-all-devices'])) {
        DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid' => Login::isLoggedIn()));
    } else {
        if (isset($_COOKIE['SNID'])) {
            DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token' => sha1($_COOKIE['SNID'])));
        } else {
            setcookie('SNID', '1', time() - 3600);
            setcookie('SNID_SECOND', '1', time() - 3600);
        }

    }

}

?>

<h1>Logout of your account?</h1>
<p>Are you sure you would like to logout?</p>

<form action="logout.php" method="post">
    <input type="checkbox" name="logout-all-devices"> Logout of all devices? <br>
    <input type="submit" name="confirm-btn" value="Confirm">
</form>
