<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require("./requires.php");
require("./model/international.php");

$id = clean($_GET['id']);  

$fetcher = new InternationalDataFetcher();
$data = $fetcher->fetchRoute($id);
echo json_encode($data);
 