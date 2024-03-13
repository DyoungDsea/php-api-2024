<?php
include './_functions.php';

$time = gmdate("H:i:s", strtotime("+1hour")); 

$timeInSet = '8:00:00';
$grace = '5';
$timeSet = date("H:i:s", strtotime("8:00:00 +5 minutes"));

// echo $timeInSet;

echo "$time | $timeSet";

// echo minuteLate("12:30:00", '12:48:00');