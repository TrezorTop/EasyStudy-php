<?php

class Login
{
    public static function isLoggedIn()
    {
        if (isset($_COOKIE['SNID'])) {
            if (DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($_COOKIE['SNID'])))) {
                $userId = DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token' => sha1($_COOKIE['SNID'])))[0]['user_id'];

                if (isset($_COOKIE['SNID_SECOND'])) {
                    return $userId;
                } else {
                    $crypto_strong = True;
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $crypto_strong));

                    DB::query('INSERT INTO login_tokens VALUES (id, :token, :user_id)', array(':token' => sha1($token), ':user_id' => $userId));
                    DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token' => sha1($_COOKIE['SNID'])));

                    setcookie("SNID", $token, time() + 60 * 60 * 24 * 14, '/', NULL, NULL, TRUE);
                    setcookie("SNID_SECOND", '1', time() + 60 * 60 * 24 * 6, '/', NULL, NULL, TRUE);

                    return $userId;

                }
            }
        }

        return false;
    }
}

