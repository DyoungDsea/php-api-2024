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

    public static function getDateTime($hours)
    {
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
        $test = __DIR__ . "/emailController/mailTemplate.php";
        require __DIR__ . "/emailController/mailTemplateApi.php";
    }



    public static function sendMessage($to, $message)
    {
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


    public static function loadEnv($path)
    {
        if (!file_exists($path)) {
            throw new Exception("The .env file does not exist at path: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Only process lines with an equals sign
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Remove surrounding quotes if any
                $value = trim($value, '"\'');

                // Set environment variable
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    public static  function  generateJwtSecretKey()
    {
        return hash('SH256', bin2hex(random_bytes(64)));
    }

    public static  function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public static function getBearerToken()
    {
        $headers = self::getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
