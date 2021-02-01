<?php


class Comment
{

    public static function postComment($link, $commentBody, $postId, $userId)
    {

        if (strlen($commentBody) < 1 || strlen($commentBody) > 257) {
            die('Incorrect length!');
        }

        $userIdResult = mysqli_query($link, "SELECT id FROM posts WHERE id = '$postId'");
        if (mysqli_num_rows($userIdResult) == 0) {
            echo 'Invalid $postId';
        } else {
            mysqli_query($link, "INSERT INTO comments VALUES (id, '$commentBody', $userId, NOW(), $postId)");
        }
    }
}