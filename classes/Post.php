<?php

class Post
{

    public static function createPost($link, $postBody, $loggedInUserId, $profileUserId)
    {

        if ($loggedInUserId == $profileUserId) {
            if (strlen($postBody) < 1 || strlen($postBody) > 257) {
                die('Incorrect length!');
            } else {
                mysqli_query($link, "INSERT INTO `posts` VALUES (id, '$postBody', NOW(), '$profileUserId', 0)");
            }
        } else {
            die("Incorrect User ");
        }
    }

    public static function likePost($link, $postId, $liker)
    {

        $userIdResult = mysqli_query($link, "SELECT user_id FROM post_likes WHERE post_id = '$postId' AND user_id = '$liker'");
        if (mysqli_num_rows($userIdResult) == 0) {
            mysqli_query($link, "UPDATE posts SET likes = likes + 1 WHERE id = $postId");
            mysqli_query($link, "INSERT INTO post_likes VALUES (id, '$postId', '$liker')");
        } else {
            mysqli_query($link, "UPDATE posts SET likes = likes - 1 WHERE id = $postId");
            mysqli_query($link, "DELETE FROM post_likes WHERE post_id = '$postId' and user_id = '$liker'");
        }
    }

    public static function displayPosts($link, $userId, $username, $loggedInUserId)
    {

        $dbPostsResult = mysqli_query($link, "SELECT * FROM `posts` WHERE user_id = '$userId' ORDER BY id DESC");

        $posts = "";

        foreach ($dbPostsResult as $post) {

            $postId = $post['id'];

            $postIdResult = mysqli_query($link, "SELECT post_id FROM post_likes WHERE post_id = '$postId' AND user_id = '$loggedInUserId'");

            if (mysqli_num_rows($postIdResult) == 0) {
                $posts .= htmlspecialchars($post['body']) . "
                <form action=\"profile.php?username=$username&postId=" . $post['id'] . "\" method=\"post\">
                    <input type=\"submit\" name=\"like\" value =\"Like\">
                    <span> " . $post['likes'] . " likes </span>
                </form>
                <hr><br>";
            } else {
                $posts .= htmlspecialchars($post['body']) . "
                <form action=\"profile.php?username=$username&postId=" . $post['id'] . "\" method=\"post\">
                    <input type=\"submit\" name=\"unlike\" value =\"Unlike\">
                    <span> " . $post['likes'] . " likes </span>
                </form>
                <hr><br>";
            }
        }
        return $posts;
    }

}