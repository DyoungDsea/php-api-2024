<?php 
    //check whether on offline or online
    $localhost = array(
        '127.0.0.1',
        '::1'
    );

    if(in_array($_SERVER['REMOTE_ADDR'], $localhost)){
        // $conn=new mysqli("localhost","root","","hrlive");
        $conn=new mysqli("localhost","root","","express");
    }else { 
        $conn=new mysqli("localhost","hrlivese_eyn","@Projects234@","hrlivese_eyn");
    }

    if($conn->connect_error){
        die($conn->connect_error);
    }

date_default_timezone_set("Africa/Lagos");
?>