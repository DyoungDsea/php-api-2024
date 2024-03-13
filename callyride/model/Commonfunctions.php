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
        return md5(bin2hex(random_bytes(43)) . date("YmdHis"));
    }


    public static function geDate($duration)
    {
        $futureTime = strtotime("+$duration ");
        return gmdate("Y-m-d H:i:s", $futureTime);
    }

    //*format dollar amount
    public static function formatDollar($data)
    {
        return "$" . number_format($data, 2);
    }

    //*format Naira amount
    public static function formatNaira($data)
    {
        return "&#8358;" . number_format($data, 2);
    }
    public static function withNaira($data)
    {
        return "₦" . number_format($data, 2);
    }

    public static function formatDate($data)
    {
        return date("d M, Y", strtotime($data));
    }


    public static function generateOrderID()
    {
        $timestamp = time();
        $randomNumber = mt_rand(1000, 9999);
        $orderID = 'ORD' . $timestamp . $randomNumber;
        return $orderID;
    }
}
