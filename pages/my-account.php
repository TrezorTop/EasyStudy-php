<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

if (Login::isLoggedIn()) {
    $userId = Login::isLoggedIn();
} else {
    die('Not logged in!');
}

if (isset($_POST['upload-profile-img'])) {
    Image::uploadImage('profile-img' ,"UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid' => $userId));
}

?>

<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
    Upload a profile image:
    <input type="file" name="profile-img">
    <input type="submit" name="upload-profile-img" value="Upload Image">
</form>

