<?php

if ($foo->uploaded) {   
   // save uploaded image with a new name,
   // resized to 100px wide
   $foo->file_new_name_body = $picid;
   $foo->image_resize = true;
   $foo->image_convert = 'jpg';
   $foo->image_ratio_x = true;
   $foo->image_ratio_y = true;
   $foo->Process($folderName);
   if ($foo->processed) {
     $foo->Clean();
   }  
}
?>