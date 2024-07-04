<?php

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header('Content-Type: application/json');


//TODO: INCLUDES
 
include_once __DIR__.'/siteSettings.php';
include_once __DIR__.'/model/Database.php';
include_once __DIR__.'/model/Commonfunctions.php';
include_once __DIR__.'/model/EmailSender.php';
include_once __DIR__.'/model/QueryBuilder.php';
include_once __DIR__.'/model/Model.php'; 
include_once __DIR__.'/model/Helper.php'; 
require_once __DIR__.'/model/JWTHandler.php';
require_once __DIR__.'/model/Response.php';
require_once __DIR__.'/vendor/autoload.php'; 
//TODO: LOAD ENV FILE
CommonFunctions::loadEnv(__DIR__ . '/model/.env');

//TODO: CREATE NEW OBJECT
$db = new Database();
$model = new Model($db); 
$helper = new Helper($db->getPDO());
$jwtHandler = new JWTHandler();


// echo CommonFunctions::hashPassword('youngsea23');
 