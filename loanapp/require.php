<?php


header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header('Content-Type: application/json');


//TODO: INCLUDES

include_once './siteSettings.php';
include_once './model/Connection.php';
include_once './model/Commonfunctions.php';
include_once './model/QueryBuilder.php';
include_once './model/Model.php'; 
include_once './model/Helper.php'; 

//TODO: CREATE NEW OBJECT
$connection = new Connection();
$model = new Model($connection); 
$helper = new Helper($connection->getPDO());

 