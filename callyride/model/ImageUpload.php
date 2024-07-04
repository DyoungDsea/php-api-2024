<?php


use Intervention\Image\ImageManagerStatic as Image;

class ImageUploader {
    private $uploadDir;

    public function __construct($uploadDir) {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
    }

    public function uploadImage($file, $id='', $width=800, $height=null) {
        // Process the uploaded file
        $uploadedFile = $file;
        $filename = $uploadedFile['name'];
        $basename = basename($filename);
        $extension = pathinfo($basename, PATHINFO_EXTENSION);
        
        // Generate a unique filename
        $rename = md5(time() . rand(12345, 67890)).$id . '.' . $extension;
        $tmpName = $uploadedFile['tmp_name'];
        
        // Use Intervention Image to handle the image upload
        $img = Image::make($tmpName);
         // Resize the image
         $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $img->save($this->uploadDir . $rename);
        $pathSave = $this->uploadDir . $rename;
        
        return $pathSave;
    }
}

 
?>
