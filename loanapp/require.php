<?php


header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header('Content-Type: application/json');


//TODO: INCLUDES
 
include_once __DIR__.'/siteSettings.php';
include_once __DIR__.'/model/Connection.php';
include_once __DIR__.'/model/Commonfunctions.php';
include_once __DIR__.'/model/QueryBuilder.php';
include_once __DIR__.'/model/Model.php'; 
include_once __DIR__.'/model/Helper.php'; 
require_once __DIR__.'/model/JWTHandler.php';
require_once __DIR__.'/model/Response.php';

//TODO: LOAD ENV FILE
CommonFunctions::loadEnv(__DIR__ . '/model/.env');

//TODO: CREATE NEW OBJECT
$connection = new Connection();
$model = new Model($connection); 
$helper = new Helper($connection->getPDO());
$jwtHandler = new JWTHandler();
 