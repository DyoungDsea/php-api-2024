<?php

require_once './require.php';

require 'vendor/autoload.php'; 

// echo $jwtSecret = getenv('JWT_SECRET');
 $token =  CommonFunctions::getBearerToken();
//generate JWT secret key with base64

$rest =  json_encode($jwtHandler->validateToken($token)); 
print($rest);

die;

//TODO: POST REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = json_decode(file_get_contents("php://input"));

    $username = CommonFunctions::clean($jsonData->username); 
    $fullname = CommonFunctions::clean($jsonData->fullname); 
    $phone = CommonFunctions::clean($jsonData->phone); 
    $email = CommonFunctions::clean($jsonData->email); 

    $data = [
        "username"=>  $username,
        "fullname"=> $fullname,
        "phone"=> $phone,
        "email"=> $email 
    ];



    $token = $jwtHandler->generateToken($data);
    $response = [
        'message' => 'User was registered successfully.',
        'token' => $token,
        'user' => $data
    ];
    Response::send(200, $response);
}


 