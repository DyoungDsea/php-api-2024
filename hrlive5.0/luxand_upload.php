<?php

 // Build the POST data
 $postData = [
    'photo' => new CURLFile($imageTmpName, $imageType, $imageName),
    'gallery' => "HR-live-gallery",
];

// $postData = array(
//     "photo" => curl_file_create("photo.jpg"),
//     "gallery" => "HR-live-gallery",
// );
// Endpoint URL
$url = "https://api.luxand.cloud/photo";

// Request headers
$headers = array(
    "token: " . $apiKey,
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);


// Execute cURL session and get the response
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

 
