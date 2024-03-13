<?php
 
require_once './require.php';

//TODO: GET REQUEST drive_categories
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   

    //TODO: fech all vehicle category
    if(isset($_GET['Message']) AND $_GET['Message']=='category'){ 
        $distance = CommonFunctions::clean($_GET['distance']);
        $distance = str_replace(' km','',$distance);
        echo json_encode($model->category($distance));
    }
}



