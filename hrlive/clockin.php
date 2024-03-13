<?php 
    require("./requires.php");  

    $transid = md5(bin2hex(random_bytes(30)).gmdate("Ymdhis")); 
    $date = gmdate("Y-m-d H:i:s", strtotime("+1hour"));
    $now = gmdate("Y-m-d", strtotime("+1hour"));
  
    
       //** User details */
       $companyId = clean($_POST['companyId']);
       $staffcode = clean($_POST['staffcode']); 

    //** User want to click in */
    if(isset($_POST['Message']) AND $_POST['Message']=='Recent Clock'){
 

         //? check if user have clock in today
        $sql = runQuery("SELECT staffcode FROM hr_attendance_clock WHERE account='$companyId' AND staffcode='$staffcode' AND ddate='$now' ORDER BY id DESC LIMIT 1");
        if(numRows($sql)==0){
 
            //*** check company subscription */
            $sql = runQuery("SELECT fexpired FROM subscriptions WHERE account='$companyId' AND fexpired='no'");
            if(numRows($sql)>0){

                $data = [
                    'id'=>'',
                    'success'=>true, 
                    'type'=>'allow',
                    'msg'=>''
                ];


            }else{
                    
                $data = [
                    'id'=>'',
                    'success'=>false, 
                    'type'=>'expire',
                    'msg'=>'Your company subscription has expired!'
                ];
            }
        }else{

            //?check multiple access

            $sub = runQuery("SELECT * FROM `sett_attendance` WHERE account='$companyId' ")->fetch_assoc();
            $multiple = $sub['allow_multi_clockin'];

            if($multiple =='no'){
                $data = [
                    'id'=>'',
                    'success'=>false, 
                    'type'=>'clockout',
                    'msg'=>'You\'re not allow to clockin multiple times today!'
                ];

            }else{

                 //*** check company subscription */
            $sql = runQuery("SELECT fexpired FROM subscriptions WHERE account='$companyId' AND fexpired='no'");
            if(numRows($sql)>0){

            
                $data = [
                    'id'=>'',
                    'success'=>true, 
                    'type'=>'allow',
                    'msg'=>''
                ];
    

            }else{
                    
                $data = [
                    'id'=>'',
                    'success'=>false, 
                    'type'=>'expire',
                    'msg'=>'Your company subscription has expired!'
                ];
            }

            }
                
        }
  
    }




 


    if(isset($_POST['Message']) AND $_POST['Message']=='Clock Out'){
     
       //check if user have clockin and haven't clockout
       $sql = runQuery("SELECT dclockout_img, id FROM hr_attendance_clock WHERE account='$companyId' AND staffcode='$staffcode' AND dclockout_img IS NULL ORDER BY id DESC LIMIT 1 ");

       if(numRows($sql)>0){    
        //? get table id
        $row = fetchAssoc($sql);
           //? check for multiple clock in
           $data = [
               'id'=>$row['id'], 
               'success'=>true, 
               'type'=>'allow',
               'msg'=>''
           ];

       }else{
           $data = [
            'id'=>'',
               'success'=>false, 
               'type'=>'clockout',
               'msg'=>'You haven\'t clockin today, make sure you clockin before you can clockout.'
           ];
       }


    }

 
 
echo json_encode($data);
