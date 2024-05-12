<?php

$headers = '';
$headers .= "Reply-To: The Sender <support@zamogoza.com>\r\n";
$headers .= "Return-Path: The Sender <support@zamogoza.com>\r\n"; 
$headers .= "From: Zamogoza LTD <samoeihu@premium250.web-hosting.com>" . "\r\n";
$headers .= "Organization: Zamogoza LTD\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";
$headers .= "X-Priority: 3\r\n";
$headers .= "X-Mailer: PHP" . phpversion() . "\r\n";

$swap = array(
    "{SITE_ADDR}" => SITELINK,
    "{SITETITLE}" => SITETITLE,
    "{ICON}" => SITEICON,
    "{LOGO}" => SITELOGO,
    "{TEMPLATE}" => $mailTemplate,
);

 

//create the html message
if (file_exists($test)) {
    $message = file_get_contents($test);
} else {
    die("Unable to locate file");
}


foreach (array_keys($swap) as $key) {
    if (strlen($key) > 2 && trim($key) != '') {
        $message = str_replace($key, $swap[$key], $message);
    }
}


mail($email, $subject, $message, $headers);
