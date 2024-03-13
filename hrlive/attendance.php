<?php 
require("./requires.php");  

$transid = md5(bin2hex(random_bytes(30)).gmdate("Ymdhis")); 
$date = gmdate("Y-m-d H:i:s", strtotime("+1hour"));
$now = gmdate("Y-m-d", strtotime("+1hour"));

include '../image_php/class.upload.php'; 

    $companyId = clean($_POST['companyId']);
    $staffcode = clean($_POST['staffcode']);
    $lat = clean($_POST['lat']);
    $long = clean($_POST['long']);

    //** User want to click in */
    if(isset($_POST['Message']) AND $_POST['Message']=='Clock In'){
     
        //** User details */
    
        $name = clean($_POST['name']);
        $location = clean($_POST['location']); 

        $date = date("Y-m-d"); 
        $time = gmdate("H:i:s", strtotime("+1hour")); 

        //*** check company subscription */
        $sql = runQuery("SELECT fexpired FROM subscriptions WHERE account='$companyId' AND fexpired='no' ");
        if(numRows($sql)>0){

        //? *** get grace period
        $sub = runQuery("SELECT * FROM `sett_attendance` WHERE account='$companyId' ")->fetch_assoc();
        $timeInSet = $sub['dtimein'];
        $grace = $sub['dgrace'];
      
        $timex = strtotime($timeInSet); 
        $workTime = date("H:i:s", strtotime("$timeInSet +$grace minutes"));
        $workTime1 = date("H:i:s", strtotime("$timeInSet"));
 
    
            $mins = $minsLate = 0;        

            if(strtotime($time) < strtotime($timeInSet)){
                $dsignInStatus = 'early';
                $mins = minuteLate($workTime1, $time);
            }elseif(strtotime($time) >= strtotime($workTime)){
                $dsignInStatus = 'late';
                // $minsLate = $time - $workTime;
                $minsLate =  minuteLate($workTime, $time);
            }else{
                $dsignInStatus = 'base';
            }

                $sql = runQuery("INSERT INTO hr_attendance_clock SET  account='$companyId', staffcode='$staffcode', dname='$name', joblocation='$location', ddate='$date', exptimein='$timeInSet', dgrace='$grace', dstatus_signin='$dsignInStatus', min_early='$mins', min_late='$minsLate', dtimein='$time', dlong='$long', dlat='$lat' ");
                if($sql){

                    if(!empty($_FILES['img']['name'])){   
                        imageUpload($_FILES['img'], $transid, 'hr_attendance_clock', 'dclockin_img' ,"staffcode='$staffcode' AND ddate='$date'", '../userImage','400','400');
                    }
              
                } 

        
 

        }else{
                
            $data = [
                'success'=>false, 
                'type'=>'expire',
                'msg'=>'Your company subscription has expired!'
            ];
        }
    }


    

    if(isset($_POST['Message']) AND $_POST['Message']=='Clock Out'){
     
        //** User details */
        $aid = clean($_POST['aid']);

         //? *** get grace period
         $sub = runQuery("SELECT * FROM `sett_attendance` WHERE account='$companyId' ")->fetch_assoc();
         $timeInSet = $sub['dtimeout'];
         $grace = $sub['dgrace'];
       
         $time = gmdate("H:i:s", strtotime("+1hour")); 
         $workTime = date("H:i:s", strtotime("$timeInSet"));

         $mins = $minsLate = 0;        

        if(strtotime($time) >= strtotime($workTime)){
            $minsLate =  minuteLate($workTime, $time);
        }
        
        
        $sql = runQuery("UPDATE hr_attendance_clock SET  exptimeout='$timeInSet', dtimeout='$time', dlong2='$long', dlat2='$lat', dovertime='$minsLate' WHERE account='$companyId' and staffcode='$staffcode' and id='$aid' ");
        if($sql){

            if(!empty($_FILES['img']['name'])){   
                imageUpload($_FILES['img'], $transid, 'hr_attendance_clock', 'dclockout_img' ,"id='$aid'", '../userImage','400','400');
            }

        }else{

        }
    }

 
 
