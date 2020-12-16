<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';

if (isset($_POST['create-account-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $query = "INSERT INTO users VALUES (`id`, '$username', '$password', '$email')";

    $result = mysqli_query($link, "SELECT username FROM `users` WHERE username = '$username'");


    if (mysqli_num_rows($result) < 1) {
        mysqli_query($link, $query);
    }
    else {
        echo "User already exists!";
    }


}

?>

<h1>Register</h1>
<form action="create-account.php" method="post">
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="email" name="email" placeholder="example@mail.com"><br>
    <input type="submit" name="create-account-btn" value="Create Account"><br>
</form>
