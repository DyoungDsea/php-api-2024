<?php

class Database
{
    private $pdo;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct()
    {
        $this->host =  getenv('DB_HOST');
        $this->dbname =  getenv('DB_DATABASE');
        $this->username =  getenv('DB_USERNAME');
        $this->password =  getenv('DB_PASSWORD');
        $this->pdo = $this->createPDOConnection();
    }

    private function createPDOConnection()
    {
        try {
            //  $pdo = new PDO("mysql:host=localhost;dbname=samoeihu_server", "samoeihu_server", "@admin100@");
            $pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
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
