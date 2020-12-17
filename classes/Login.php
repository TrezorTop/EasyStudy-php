<?php

include $_SERVER['DOCUMENT_ROOT'] . '/php/connect.php';


class Login
{
    public static function isLoggedIn($link)
    {
        if (isset($_COOKIE['SNID'])) {

            $token = sha1($_COOKIE['SNID']);

            $cookieMysqlResult = mysqli_query($link, "SELECT user_id FROM `login_tokens` WHERE token = '$token'");

            if (mysqli_num_rows($cookieMysqlResult) > 0) {

                $userId = mysqli_fetch_row($cookieMysqlResult)[0];

                if (isset($_COOKIE['SNID_SECOND'])) {

                    return $userId;

                } else {

                    $crypto_strong = True;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));
                    $sha1Token = sha1($token);

                    mysqli_query($link, "INSERT INTO login_tokens VALUES (id, '$sha1Token', '$userId')");

                    $oldPrimaryToken = sha1($_COOKIE['SNID']);
                    mysqli_query($link, "DELETE FROM login_tokens WHERE token = '$oldPrimaryToken'");

                    setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                    setcookie("SNID_SECOND", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

                    return $userId;

                }
            }
        }

        return false;
    }
}

