<?php


class Image
{

    public static function uploadImage($formName, $query, $params)
    {
        $image = base64_encode(file_get_contents($_FILES[$formName]['tmp_name']));

        $options = array('http' => array(
            'method' => "POST",
            'header' => "Authorization: Bearer 71dd64261f5e8d03a8f4a8935150aefe696980f9\n" .
                "Content-Type: application/x-www-form-urlencoded",
            'content' => $image
        ));

        $context = stream_context_create($options);

        $imgurURL = "https://api.imgur.com/3/image";

        if ($_FILES[$formName]['size'] > 52428800) {
            die('Image too big must be 50MB or less!');
        }

        $response = file_get_contents($imgurURL, false, $context);
        $response = json_decode($response);

        $preparams = array($formName => $response->data->link);

        $params = $preparams + $params;

        DB::query($query, $params);
    }

}