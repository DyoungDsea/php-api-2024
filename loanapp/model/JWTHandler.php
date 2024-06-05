 
<?php

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey =  getenv('JWT_SECRET');
    }

    public function generateToken($data)
    {
        $payload = [
            'iss' => 'https://samogoza.com',
            'iat' => time(),
            'exp' => time() + (60 * 60), // Token valid for 1 hour
            'data' => $data
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token)
    {
        try {

            $decoded = JWT::decode($token,  new Key($this->secretKey, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
}
