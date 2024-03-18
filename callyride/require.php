<?php


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


//TODO: INCLUDES
include_once './model/Connection.php';
include_once './model/Commonfunctions.php';
include_once './model/QueryBuilder.php';
include_once './model/Model.php';
include_once './model/ModelDriver.php';
include_once './model/Helper.php';//

//TODO: CREATE NEW OBJECT
$connection = new Connection();
$model = new Model($connection);
$modelDriver = new ModelDriver($connection);

 