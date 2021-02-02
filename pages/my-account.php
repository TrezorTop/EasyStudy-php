<?php

if (isset($_POST['upload-profile-img'])) {

    $image = base64_encode(file_get_contents($_FILES['profile-img']['tmp_name']));

    $options = array('http' => array(
        'method' => "POST",
        'header' => "Authorization: Bearer 71dd64261f5e8d03a8f4a8935150aefe696980f9\n" .
            "Content-Type: application/x-www-form-urlencoded",
        'content' => $image
    ));

    $context = stream_context_create($options);

    $imgurURL = "https://api.imgur.com/3/image";

    $response = file_get_contents($imgurURL, false, $context);
}

?>

<h1>My Account</h1>
<form action="my-account.php" method="post" enctype="multipart/form-data">
    Upload a profile image:
    <input type="file" name="profile-img">
    <input type="submit" name="upload-profile-img" value="Upload Image">
</form>

