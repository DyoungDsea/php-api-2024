
<?php 
require("./requires.php");  

 $companycode = clean($_POST['companycode']); 
 $staffcode = clean($_POST['staffcode']); 


$sql = runQuery("SELECT account, staffcode, dtitle, dname, jobtitle, joblocation FROM hr_staffdirectory WHERE account='$companycode' AND staffcode='$staffcode' AND deactivated='no' ");

if(numRows($sql)>0){  
    $res = [];
    while($row=fetchAssoc($sql)){
            $res = $row;
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