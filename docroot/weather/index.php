<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
header('Content-Type: application/json; charset=utf-8');

require_once("./env.php");

function getWeatherData() {
    $filename = "weather-data.json";
    $now = time();
    
    $saved_json = file_get_contents($filename);
    $saved = json_decode($saved_json);
    $dif = $now - $saved->now;
    
    if($dif < 90) {
        return $saved;
    }
    
    $url = "https://api.openweathermap.org/data/3.0/onecall?lat=39.268391&lon=-76.724636&appid=$appid&units=imperial";
    $ch = curl_init();

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true, 
    ];
    
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
     
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["cURL Error" => $error];
    }
    
    curl_close($ch);
    
    $data = json_decode($response);
    $data->now = $now;
    $data->dif = $dif;
    
    file_put_contents($filename, json_encode($data));

    return $data;
}


echo json_encode(getWeatherData());