<?php

function clean($value){
    GLOBAL $conn;
    $value=trim($value);
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    $value=strip_tags($value);
    $value = $conn->real_escape_string($value);
    return $value;                
  }


  function formatDate($data){
    return gmdate("d M, Y", strtotime($data));
  }

  function readableDate($data){
    return gmdate("d l, Y", strtotime($data));
  }
  function formatDateTime($data){
    return gmdate("d M Y, h:i:sa", strtotime($data));
  }

  function formatDate24h($data){
    return gmdate("H:i, d-M-Y", strtotime($data));
  }

  function formatBirth($data){
    return date("d M, Y", strtotime($data));
  }

  function  fetchAssoc($data){
    return $data->fetch_assoc();
  }
  function numRows($sql){
    return $sql->num_rows;
  }

  function formatTime($data){
    return date("H:i", strtotime($data));
  }

  function runQuery($statement){
    GLOBAL $conn;
    return $conn->query($statement);
  }
  

  function addToDate($now, $howManyDays){
    $date = $now;
    $date = strtotime($date);
    $date = strtotime($howManyDays, $date); //strtotime("+7 day", $date);
    return date('Y-m-d h:i:s', $date);
  }

 
  function limitText($text,$limit, $dot='...'){
    if(str_word_count($text, 0)>$limit){
        $word = str_word_count($text,2);
        $pos=array_keys($word);
        $text=substr($text,0,$pos[$limit]). $dot;
    }
    return $text;
}


function limitTextAnchor($text,$limit,$anchor){
  if(str_word_count($text, 0)>$limit){
      $word = str_word_count($text,2);
      $pos=array_keys($word);
      $text=substr($text,0,$pos[$limit]). $anchor;
  }
  return $text;
}

  
 function selectQuery($tableName, $clause){
  return runQuery("SELECT * FROM $tableName $clause");
}

 function rowUser($tableName, $clause){
  return runQuery("SELECT * FROM $tableName $clause")->fetch_assoc();
}

 
 function formatNaira($data){
  return "&#8358; ".number_format($data,2);
}
  
  function imageUpload($fileName, $transid, $tableName, $rowName, $clause, $filePath, $x='300', $y='300',  $id=''){
    @list(, , $imtype, ) = getimagesize($fileName['tmp_name']); 
    if ($imtype == 3 or $imtype == 2 or $imtype == 1) {          
    $picid=$transid.$id; 
    $foo = new Upload($fileName);    
    if ($foo->uploaded) {   
        // save uploaded image with a new name, 
          $foo->file_new_name_body = $picid;
          $foo->image_resize = true;
          $foo->image_convert = 'png';
          $foo->image_x = $x;
          $foo->image_y = $y;
          $foo->Process($filePath);
          if ($foo->processed) {
              $foo->Clean();
          }  
  
          $picid = "$picid.png";
    } 
    runQuery("UPDATE $tableName SET $rowName$id='$picid' WHERE $clause"); 
       
    }
    
  }


  function minuteLate($normalTime, $now){
    $dateTimeObject1 = date_create($normalTime); 
    $dateTimeObject2 = date_create($now); 
        
    //? Calculating the difference between DateTime Objects
    $interval = date_diff($dateTimeObject1, $dateTimeObject2); 
    //? Printing the result in days format
    $interval->format('%R%a days');
    $min = $interval->days * 24 * 60;
    $min += $interval->h * 60;
    $min += $interval->i;
       
    return $min;
}