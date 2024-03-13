<?php

$params = array(
    "uuid" => $uuid,
);
// Endpoint URL
$url = "https://api.luxand.cloud/photo/" . $params["uuid"] . "";

// Request headers
$headers = array(
    "token: " . $apiKey,
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


// Execute cURL session and get the response
$res = curl_exec($ch);

// Close cURL session
curl_close($ch);

 