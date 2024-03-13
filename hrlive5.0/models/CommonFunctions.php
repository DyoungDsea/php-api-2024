<?php
class CommonFunctions {
    // Function to generate a random string
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    // Function to sanitize user input to prevent SQL injection
    public static function clean($value){
        $value=trim($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $value=strip_tags($value);
        return $value;                
    }

    //* Function to generate a unique identifier
    public static function generateUniqueID() {
        return md5(bin2hex(random_bytes(43)) . date("YmdHis"));
    }

    public static function getDateTime($hours) {
        $futureTime = strtotime("+$hours hours");
        return gmdate("Y-m-d H:i:s", $futureTime);
    }

    public static function getExpDate($duration) {
        $futureTime = strtotime("+$duration ");
        return gmdate("Y-m-d H:i:s", $futureTime);
    }

     //*format dollar amount
    public static function formatDollar($data){
        return "$".number_format($data,2);
    }

    //*format Naira amount
    public static function formatNaira($data){
        return "&#8358;".number_format($data,2);
    }

    public static function formatDate($data){
        return date("d M, Y", strtotime($data));
      }
  
      public static function generateLink($text) {
        $cleanedText = preg_replace('/[^\p{L}\d]+/u', '-', $text);
        $cleanedText = trim($cleanedText, '-');
        $cleanedText = preg_replace('/-+/', '-', $cleanedText);
        $cleanedText = strtolower($cleanedText);
        return $cleanedText;
    }

    public static function showAlert($message, $type = 'success') {
        $alertClass = $type === 'success' ? 'alert-success' : 'alert-danger';
        $iconClass = $type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        $alert = '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">
                    <i class="fa ' . $iconClass . '"></i> ' . $message . '
                    
                </div>';

        return $alert;
    }

    public  function removeUnnecessaryCommas($text) {
        // Use a regular expression to match commas not followed by a word character
        $cleanedText = preg_replace('/,(?!\w)/', '', $text);
        return $cleanedText;
    }

    public static function minuteRange($normalTime, $now){
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
}
