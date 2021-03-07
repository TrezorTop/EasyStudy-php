<?php

class Post
{
    public static function createPost($postBody, $loggedInUserId, $profileUserId)
    {
        if (strlen($postBody) < 1 || strlen($postBody) > 257) {
            die('Incorrect length! (createPost)');
        }

        $topics = self::getTopics($postBody);

        if ($loggedInUserId == $profileUserId) {
            if (count(Notify::createNotify($postBody)) != 0) {
                foreach (Notify::createNotify($postBody) as $key => $notification) {
                    $receiver = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $key))[0]['id'];
                    $sender = $loggedInUserId;

                    if ($receiver != 0) {
                        DB::query('INSERT INTO notifications VALUE (id, :type, :receiver, :sender, :extra)', array(':type' => $notification["type"], ':receiver' => $receiver, ':sender' => $sender, ':extra' => $notification["extra"]));
                    }
                }
            }

            DB::query('INSERT INTO posts VALUES (id, :postbody, NOW(), :userid, 0, NULL, :topics)', array(':postbody' => $postBody, ':userid' => $profileUserId, ':topics' => $topics));
        } else {
            die('Incorrect user!');
        }

    }

    public static function createImagePost($postBody, $loggedInUserId, $profileUserId)
    {
        if (strlen($postBody) > 257) {
            die('Incorrect length! (createImagePost)');
        }

        $topics = self::getTopics($postBody);

        if ($loggedInUserId == $profileUserId) {
            if (count(Notify::createNotify($postBody)) != 0) {
                foreach (Notify::createNotify($postBody) as $key => $notification) {
                    $receiver = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $key))[0]['id'];
                    $sender = $loggedInUserId;

                    if ($receiver != 0) {
                        DB::query('INSERT INTO notifications VALUE (id, :type, :receiver, :sender, :extra)', array(':type' => $notification["type"], ':receiver' => $receiver, ':sender' => $sender, ':extra' => $notification["extra"]));
                    }
                }
            }

            DB::query('INSERT INTO posts VALUES (id, :postbody, NOW(), :userid, 0, NULL, :topics)', array(':postbody' => $postBody, ':userid' => $profileUserId, ':topics' => $topics));

            return DB::query('SELECT id FROM posts WHERE user_id = :userid ORDER BY ID DESC LIMIT 1;', array(':userid' => $loggedInUserId))[0]['id'];

        } else {
            die('Incorrect user!');
        }
    }

    public static function likePost($postId, $likerId)
    {
        if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postId, ':userid' => $likerId))) {
            DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid' => $postId));
            DB::query('INSERT INTO post_likes VALUES (id, :postid, :userid)', array(':postid' => $postId, ':userid' => $likerId));
//            Notify::createNotify("", $postId);
        } else {
            DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid' => $postId));
            DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $postId, ':userid' => $likerId));
        }
    }

    public static function getTopics($text)
    {
        $text = explode(" ", $text);

        $topics = "";

        foreach ($text as $word) {
            if (substr($word, 0, 1) == "#") {
                $topics .= substr($word, 1) . ",";
            }
        }

        return $topics;
    }

    public static function link_add($text)
    {
        $text = explode(" ", $text);
        $newString = "";

        foreach ($text as $word) {
            if (substr($word, 0, 1) == "@") {
                $newString .= "<a href='profile.php?username=" . substr($word, 1) . "'>" . htmlspecialchars($word) . "</a> ";
            } else if (substr($word, 0, 1) == "#") {
                $newString .= "<a href='topics.php?topic=" . substr($word, 1) . "'>" . htmlspecialchars($word) . "</a> ";
            } else {
                $newString .= htmlspecialchars($word) . " ";
            }
        }

        return $newString;
    }

    public static function displayPosts($userId, $username, $loggedInUserId)
    {
        $dbPosts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid' => $userId));
        $posts = "";

        foreach ($dbPosts as $post) {

            if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid' => $post['id'], ':userid' => $loggedInUserId))) {

                $posts .= "<img width='100px' src='" . $post['postimg'] . "'>" . self::link_add($post['body']) . "
                                <form action='profile.php?username=$username&postId=" . $post['id'] . "' method='post'>
                                        <input type='submit' name='like' value='Like'>
                                        <span>" . $post['likes'] . " likes</span>
                                ";
                if ($userId == $loggedInUserId) {
                    $posts .= "<input type='submit' name='delete-post' value='x' />";
                }
                $posts .= "
                                </form><hr /></br />
                                ";

            } else {
                $posts .= "<img width='100px' src='" . $post['postimg'] . "'>" . self::link_add($post['body']) . "
                                <form action='profile.php?username=$username&postId=" . $post['id'] . "' method='post'>
                                <input type='submit' name='unlike' value='Unlike'>
                                <span>" . $post['likes'] . " likes</span>
                                ";
                if ($userId == $loggedInUserId) {
                    $posts .= "<input type='submit' name='delete-post' value='x' />";
                }
                $posts .= "
                                </form><hr /></br />
                                ";
            }
        }
        return $posts;
    }

}
