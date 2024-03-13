<?php

$subject = SITENAME." : Verify account\r\n";

$swap = array(    
    "{SITE_NAME}"=>SITENAME,
    "{SITE_ADDR}"=>SITELINK,
    "{ICON}"=>SITEICON,
    "{LOGO}"=>SITELOGO,
    "{NAME}"=>$name,
    "{USERID}"=>$code, 
    "{KEY}"=>$pass 
);


$headers  = "MIME-Version: 1.0 \r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: ".SITENAME." <".SITEINFO.">" . "\r\n";
$headers .= "Reply-To: ".SITESUPPORT."\r\n";
// $headers .= "X-Priority: 3\r\n";
// $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
//create the html message
if(file_exists($test)){
    $message = file_get_contents($test);

}else{
    die("Unable to locate file");
}


foreach(array_keys($swap) as $key){
    if(strlen($key)>2 && trim($key) !=''){
        $message = str_replace($key, $swap[$key], $message);
    }
}


mail($email,$subject,$message,$headers); 

