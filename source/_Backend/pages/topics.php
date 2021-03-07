<?php

include $_SERVER['DOCUMENT_ROOT'] . '/classes/DB.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Login.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Post.php';
include $_SERVER['DOCUMENT_ROOT'] . '/classes/Image.php';

if (isset($_GET['topic'])) {

    if (DB::query("SELECT topics FROM posts WHERE FIND_IN_SET(:topic, topics)", array(':topic'=>$_GET['topic']))) {

        $posts = DB::query("SELECT * FROM posts WHERE FIND_IN_SET(:topic, topics)", array(':topic'=>$_GET['topic']));

        foreach ($posts as $post) {
            echo $post['body']. "<br>";
        }

    }

}
