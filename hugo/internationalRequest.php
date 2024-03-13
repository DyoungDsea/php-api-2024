<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require("./requires.php");
require("./model/international.php");
 
// Usage:
$userid = clean($_GET['userid']);
$fetcher = new InternationalDataFetcher();
$data = $fetcher->fetchInternationalData($userid);
echo json_encode($data);
 