<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

$tokenIsValid = False;

if (Login::isLoggedIn($link)) {
    echo "Logged In";
    echo "<br> User ID is " . Login::isLoggedIn($link);

    if (isset($_POST['change-password-btn'])) {

        $userId = Login::isLoggedIn($link);
        $oldPassword = $_POST['old-password'];
        $newPassword = $_POST['new-password'];
        $newPasswordRepeat = $_POST['new-password-repeat'];

        $passwordMysqlResult = mysqli_query($link, "SELECT password FROM `users` WHERE id = '$userId'");
        $passwordFormDB = mysqli_fetch_row($passwordMysqlResult)[0];

        if (password_verify($oldPassword, $passwordFormDB)) {

            if ($oldPassword != $newPassword) {

                if (strlen($newPassword) >= 6 && strlen($newPassword) <= 60) {

                    if ($newPassword == $newPasswordRepeat) {

                        $newPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                        mysqli_query($link, "UPDATE `users` SET password = '$newPassword' WHERE id = '$userId'");
                        echo "<br> Password changed successfully";

                    } else {
                        echo "<br> New passwords don't match";
                    }

                } else {
                    echo "<br> New password is too short or too long";
                }

            } else {
                echo "<br> Old password and new password must be different";
            }

        } else {
            echo "<br> Current password is incorrect";
        }

    }

} else {

    if (isset($_GET['token'])) {

        $token = $_GET['token'];
        $sha1token = sha1($token);

        $userIdPasswordTokensMysqlResult = mysqli_query($link, "SELECT user_id FROM password_tokens WHERE token = '$sha1token'");
        $userIdPasswordTokens = mysqli_fetch_row($userIdPasswordTokensMysqlResult)[0];

        if ($userIdPasswordTokens > 0) {

            $tokenIsValid = True;

            if (isset($_POST['change-password-btn'])) {

                $newPassword = $_POST['new-password'];
                $newPasswordRepeat = $_POST['new-password-repeat'];

                $passwordMysqlResult = mysqli_query($link, "SELECT password FROM `users` WHERE id = '$userIdPasswordTokens'");
                $passwordFormDB = mysqli_fetch_row($passwordMysqlResult)[0];

                if (strlen($newPassword) >= 6 && strlen($newPassword) <= 60) {

                    if ($newPassword == $newPasswordRepeat) {

                        $newPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                        mysqli_query($link, "UPDATE `users` SET password = '$newPassword' WHERE id = '$userIdPasswordTokens'");
                        echo "<br> Password changed successfully";

                        mysqli_query($link, "DELETE FROM `password_tokens` WHERE user_id = '$userIdPasswordTokens'");

                    } else {
                        echo "<br> New passwords don't match";
                    }

                } else {
                    echo "<br> New password is too short or too long";
                }

            }

        } else {
            die("Token invalid");
        }

    } else {
        die("Not logged in");
    }

}

?>

<h1>Change your password</h1>

<form action="<?php if (!$tokenIsValid) { echo "change-password.php"; } else { echo "change-password.php?token=$token"; } ?>" method="post">
    <?php
        if (!$tokenIsValid) {
            echo '<input type="password" name="old-password" placeholder="Current password"><br>';
        }
    ?>
    <input type="password" name="new-password" placeholder="New password"><br>
    <input type="password" name="new-password-repeat" placeholder="Repeat new password"><br>
    <input type="submit" name="change-password-btn" value="Change password">
</form>
