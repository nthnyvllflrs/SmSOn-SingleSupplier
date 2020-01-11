<?php

function reverseGeocode($latitude, $longitude) {
    $api_key = "AIzaSyBLvHFeixDacvhmdX-L_0EoG4of6n0pM1A";
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&key='.$api_key;
    $response = json_decode(file_get_contents($url), true);
    $address = $response['results'][0]['formatted_address'];
    return $address ? $address : "Zamboanga City";
}