<?php

function is_safe(string $file_path)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'http://localhost:3333/single/multipart-form');
    curl_setopt($ch, CURLOPT_POST, true);

    $file = new CURLFile($file_path);
    $post_data = array('content' => $file);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    foreach ($data['prediction'] as $prediction) {
        if (in_array($prediction['className'], ['Hentai', 'Porn', 'Sexy']) && $prediction['probability'] >= 0.5) {
            return false;
        }
    }

    return true;
}
