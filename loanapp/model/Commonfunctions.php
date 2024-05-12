<?php
class CommonFunctions
{

    public static function clean($value)
    {
        $value = trim($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        return $value;
    }

    //* Function to generate a unique identifier
    public static function generateUniqueID()
    {
        return hash("SHA256", bin2hex(random_bytes(43)) . date("YmdHis"));
    }

    public static function hashPassword(string $pass)
    {
        return hash("SHA256", $pass);
    }


    public static function getDate($duration)
    {
        $futureTime = strtotime("+$duration ");
        return gmdate("Y-m-d", $futureTime);
    }

    public static function getDateTime($hours) {
        $futureTime = strtotime("+$hours hours");
        return gmdate("Y-m-d H:i:s", $futureTime);
    }
 
    public static function formatDollar($data)
    {
        return "$" . number_format($data, 2);
    }
 
    public static function formatNaira($data)
    {
        return "₦" . number_format($data, 2);
    }
 

    public static function formatDate($data)
    {
        return date("d M, Y", strtotime($data));
    }
 
    public static function formatTime($data)
    {
        return date("H:i", strtotime($data));
    }

    public static function sendMail($mailTemplate, $email, $subject)
    {        
        $test =__DIR__."/emailController/mailTemplate.php";
        require __DIR__. "/emailController/mailTemplateApi.php";
    }



    public static function sendMessage($to, $message) {
        $api_key = 'UW5b9lnzftn7QVkvqlCBTASMbErIWX01WDSzSE5sJAHGrnSd98t2KvoNDv6W';
        $sender_id = 'Samogoza';
        
        $base_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create';
        
        $data = [
            'api_token' => $api_key,
            'from' => $sender_id,
            'to' => $to,
            'body' => $message,
        ];
        
        $ch = curl_init($base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }
        
        curl_close($ch);
        
        return $response;
      }
    
}
