<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once './models/Connection.php';
require_once './models/CommonFunctions.php';
require_once './models/Model.php';
require_once './models/QueryBuilder.php';

$connection = new Connection();
$model = new Model($connection);
$query = new QueryBuilder();

$apiKey = "d6921c11cef94d9891763d9a6c74c13c";