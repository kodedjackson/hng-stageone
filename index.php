<?php
    require 'vendor/autoload.php';


    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $apiKey = $_ENV['API_KEY'];


    // Access the visitor's name from the query parameter
    $visitor_name = isset($_GET['visitor_name']) ? $_GET['visitor_name'] : "";

    $ip = $_SERVER['REMOTE_ADDR'];
    $get_weather = "https://api.weatherapi.com/v1/current.json?key=" . $apiKey . "=" . $ip . "&aqi=yes";


    //using curl
    $ch = curl_init($get_weather);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        // Handle the cURL error (e.g., log the error or return a generic error message)
        echo json_encode(['error' => $error_msg]);
        exit();
    }

    // Close cURL handle
    curl_close($ch);

    // Decode the JSON response
    $location_data = json_decode($response, true);

    // Check for decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Handle the JSON decoding error (e.g., log the error or return a generic error message)
        echo json_encode(['error' => 'Invalid response from IP geolocation service']);
        exit();
    }

    // Return the location data as JSON

    //Access the location of the data
    $city = $location_data['location']['name'];

    //set the greetings
    $greeting = "Hello, " . $visitor_name . '! ';

    //set json response

    $formated_response = [
        'client_ip' => $ip,
        'location' => $city,
        'greeting' => $greeting . $location_data['current']['feelslike_c'] . " 11 degrees Celcius in " . $city
    ];


    return json_encode($formated_response, JSON_THROW_ON_ERROR);