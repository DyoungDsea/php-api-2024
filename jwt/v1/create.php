<?php
//? include headers
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset = UTF-8");
header("Access-Control-Allow-Methods:POST");

require '../config/db.php';
require '../classes/user.php';
require '../../_functions.php';
require '../requires.php';

//? create db object
$db = new Database();
$conn = $db->connect();

//? create user object
$user = new User($conn);

if($_SERVER['REQUEST_METHOD']==="POST"){

    $data = json_decode(file_get_contents("php://input"));
    print_r($data);
    die;

    $user->name = clean($data->name);
    $user->username = clean($data->username);
    $user->phone = clean($data->phone);
    $user->email = clean($data->email);
    $user->pass = md5(clean($data->pass));
    $user->code = $code;
    $user->date = $date;


    if($user->createUser()){

    }



}
