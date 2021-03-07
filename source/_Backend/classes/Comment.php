<?php


class Comment
{
    public static function postComment($commentBody, $postId, $userId)
    {
        if (strlen($commentBody) < 1 || strlen($commentBody) > 257) {
            die('Incorrect length!');
        }

        if (!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid' => $postId))) {
            echo 'Invalid post ID';
        } else {
            DB::query('INSERT INTO comments VALUES (id, :comment, :userid, NOW(), :postid)', array(':comment' => $commentBody, ':userid' => $userId, ':postid' => $postId));
        }
    }

    public static function displayComments($postId)
    {
        $comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id = :postid AND comments.user_id = users.id', array(':postid' => $postId));

        foreach ($comments as $comment) {
            echo $comment['comment'] . " ~ " . $comment['username'] . '<hr>';
        }
    }
}