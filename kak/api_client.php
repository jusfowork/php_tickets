<?php

function sendPostRequest($url, $data) {
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // Debugging: Check if response is valid JSON
    if ($response === FALSE) {
        throw new Exception("Error: Failed to get a response from API.");
    }

    // Logging the raw response for debugging
    file_put_contents('api_response.log', "URL: $url\nResponse:\n$response\n", FILE_APPEND);

    // Attempt to decode the JSON response
    $decoded_response = json_decode($response, true);

    // Check if decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding JSON response: " . json_last_error_msg() . "\nRaw response: $response");
    }

    return $decoded_response;
}
