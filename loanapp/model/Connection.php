<?php

class Connection
{
    private $pdo; 

    public function __construct()
    {
        $this->pdo = $this->createPDOConnection(); 
    }

    private function createPDOConnection()
    {
        try {
            //  $pdo = new PDO("mysql:host=localhost;dbname=callyrid_server", "callyrid_server", "@admin100@");
            $pdo = new PDO("mysql:host=localhost;dbname=loanapp", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }



    public function getPDO()
    {
        return $this->pdo;
    }

   
}
