<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';

$tokenIsValid = False;

if (Login::isLoggedIn()) {

    if (isset($_POST['change-password-btn'])) {
        $userId = Login::isLoggedIn();
        $oldPassword = $_POST['old-password'];
        $newPassword = $_POST['new-password'];
        $newPasswordRepeat = $_POST['new-password-repeat'];

        if (password_verify($oldPassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid' => $userId))[0]['password'])) {
            if ($newPassword == $newPasswordRepeat) {
                if (strlen($newPassword) >= 6 && strlen($newPassword) <= 60) {
                    DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword' => password_hash($newPassword, PASSWORD_BCRYPT), ':userid' => $userId));
                    echo 'Password changed successfully!';
                }

            } else {
                echo 'Passwords don\'t match!';
            }

        } else {
            echo 'Incorrect old password!';
        }

    }

} else {

    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        if (DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token' => sha1($token)))) {
            $userId = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token' => sha1($token)))[0]['user_id'];
            $tokenIsValid = True;

            if (isset($_POST['change-password-btn'])) {
                $newPassword = $_POST['new-password'];
                $newPasswordRepeat = $_POST['new-password-repeat'];

                if ($newPassword == $newPasswordRepeat) {
                    if (strlen($newPassword) >= 6 && strlen($newPassword) <= 60) {
                        DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword' => password_hash($newPassword, PASSWORD_BCRYPT), ':userid' => $userId));
                        echo 'Password changed successfully!';
                        DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid' => $userId));
                    }

                } else {
                    echo 'Passwords don\'t match!';
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

<form action="<?php if (!$tokenIsValid) {
    echo "change-password.php";
} else {
    echo "change-password.php?token=$token";
} ?>" method="post">
    <?php
    if (!$tokenIsValid) {
        echo '<input type="password" name="old-password" placeholder="Current password"><br>';
    }
    ?>
    <input type="password" name="new-password" placeholder="New password"><br>
    <input type="password" name="new-password-repeat" placeholder="Repeat new password"><br>
    <input type="submit" name="change-password-btn" value="Change password">
</form>
