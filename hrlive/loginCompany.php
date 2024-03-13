
<?php 
require("./requires.php");  

 $companycode = clean($_POST['companyId']); 


$sql = runQuery("SELECT account, demail, dname, dlogo, fexpired FROM subscriptions WHERE account='$companycode'");

if(numRows($sql)>0){  
    $res = [];
    while($row=fetchAssoc($sql)){
        if($row['fexpired']=='no'){
            $res = $row;
        }else{
            $res = 'expired';
        }
    }
    $data = [
        'success'=>true,
        'user'=>$res,
        'msg'=>'Success'
    ];
    
}else{  
    $data = [
        'success'=>false,
        'msg'=>'no'
    ];
}
 
echo json_encode($data);