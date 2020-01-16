<?php

function reverseGeocode($latitude, $longitude) {
    $api_key = "AIzaSyBLvHFeixDacvhmdX-L_0EoG4of6n0pM1A";
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&key='.$api_key;
    $response = json_decode(file_get_contents($url), true);
    $address = $response['results'][0]['formatted_address'];
    return $address ? $address : "Zamboanga City";
}

function iTextMo($number, $message){
    $url = 'https://www.itexmo.com/php_api/api.php';
    $itexmo = array('1' => $number, '2' => $message, '3' => env('ITEXTMO_API_KEY', null));
    $param = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($itexmo),
        ),
    );
    $context  = stream_context_create($param);
    return file_get_contents($url, false, $context);
}