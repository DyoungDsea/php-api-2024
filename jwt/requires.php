
<?php
$code = md5(bin2hex(random_bytes(17)).date("Ymdhis")); 
$transid = md5(bin2hex(random_bytes(43)).date("Ymdhis")); 
$date = gmdate("Y-m-d H:i:s", strtotime("+1hour"));
 