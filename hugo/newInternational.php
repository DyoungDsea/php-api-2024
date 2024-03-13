<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// require("./requires.php");
require("./model/connection.php");
require("./model/newInternationalAPI.php");

// Create an instance of the API class and call the method
$connection = new Connection(); // Create an instance of the Connection class
$orderAPI = new InternationalOrderAPI($connection); // Pass the Connection instance to the constructor
$orderAPI->createOrUpdateOrder();
