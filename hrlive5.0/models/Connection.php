<?php

class Connection
{
    private $pdo;
    private $unique; 
    private $date;

    public function __construct()
    {
        $this->pdo = $this->createPDOConnection(); 
        $this->date = gmdate("Y-m-d H:i:s", strtotime("+1hour"));
        $this->unique = md5(bin2hex(random_bytes(43)) . date("Ymdhis"));
    }

    private function createPDOConnection()
    { 
        try {
            // $pdo = new PDO("mysql:host=localhost;dbname=miner365_test", "miner365_test", "@admin100@");
            $pdo = new PDO("mysql:host=localhost;dbname=hrlive_clock", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
 

    public function getPDO(){
        return $this->pdo;
    }
   
    public function getUnique(){
        return $this->unique;
    } 

    public function getDate(){
        return $this->date;
    }

   
}
