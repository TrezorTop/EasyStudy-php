<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Post.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Image.php';

$username = "";
$verified = False;
$isFollowing = False;

if (isset($_GET['username'])) {

    if (DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))) {

        $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['username'];
        $userId = DB::query('SELECT id FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['id'];
        $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username' => $_GET['username']))[0]['verified'];
        $followerId = Login::isLoggedIn();

        if (isset($_POST['follow'])) {
            if ($userId != $followerId) {
                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId))) {
                    if ($followerId == 1) {
                        DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid' => $userId));
                    }
                    DB::query('INSERT INTO followers VALUES (id, :userid, :followerid)', array(':userid' => $userId, ':followerid' => $followerId));
                } else {
                    echo "Already following";
                }
                $isFollowing = True;
            }
        }

        if (isset($_POST['unfollow'])) {
            if ($userId != $followerId) {
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId))) {
                    if ($followerId == 1) {
                        DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid' => $userId));
                    }
                    DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId));
                }
                $isFollowing = False;
            }

        }

        if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid' => $userId, ':followerid' => $followerId))) {
//            echo "Already following";
            $isFollowing = True;
        }

        if (isset($_POST['delete-post'])) {
            if (DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid' => $_GET['postId'], ':userid' => $followerId))) {
                DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid' => $_GET['postId'], ':userid' => $followerId));
                DB::query('DELETE FROM post_likes WHERE post_id=:postid', array(':postid' => $_GET['postId']));
                echo 'Post deleted';
            }
        }

        if (isset($_POST['post'])) {
            if ($_FILES['postimg']['size'] == 0) {
                Post::createPost($_POST['post-body'], Login::isLoggedIn(), $userId);
            } else {
                $postId = Post::createImagePost($_POST['post-body'], Login::isLoggedIn(), $userId);
                Image::uploadImage('postimg', "UPDATE posts SET postimg=:postimg WHERE id=:postid", array(':postid' => $postId));
            }
        }

        if (isset($_GET['postId']) && !isset($_POST['delete-post'])) {
            Post::likePost($_GET['postId'], $followerId);
        }

        $posts = Post::displayPosts($userId, $username, $followerId);

    } else {
        die("Username not found!");
    }

}

?>

<h1><?php echo $username ?>'s Profile <?php if ($verified) {
        echo "Verified";
    } ?></h1>

<form action="profile.php?username=<?php echo $username ?>" method="post">
    <?php
    if ($userId != $followerId && $followerId != null) {
        if ($isFollowing) {
            echo '<input type="submit" name="unfollow" value="Unfollow">';
        } else {
            echo '<input type="submit" name="follow" value="Follow">';
        }
    }
    ?>
</form>

<form action="profile.php?username=<?php echo $username ?>" method="post" enctype="multipart/form-data">
    <textarea name="post-body" cols="30" rows="8"></textarea>
    <br> Upload an image:
    <input type="file" name="postimg">
    <input type="submit" name="post" value="Post">
</form>


<div class="posts">
    <?php echo $posts; ?>
</div>