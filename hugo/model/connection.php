<?php
class Connection
{
    private $pdo;
    private $uniqueX;
    private $trackingId;
    private $date;
    private $transid;

    public function __construct()
    {
        $this->pdo = $this->createPDOConnection();
        $this->uniqueX = "HGE" . date("Ymdhis");
        $this->trackingId = "HUE" . date("mdYhis");
        $this->date = gmdate("Y-m-d H:i:s", strtotime("+1hour"));
        $this->transid = md5(bin2hex(random_bytes(43)) . date("Ymdhis"));
    }

    private function createPDOConnection()
    { 
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=express", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    private function adminRate() 
    {        
        $stmt = $this->pdo->prepare("SELECT drate FROM admin");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin['drate'];         
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function getAdminRate()
    {
        return $this->adminRate();
    }
    public function getUniqueX()
    {
        return $this->uniqueX;
    }

    public function getTrackingId()
    {
        return $this->trackingId;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getTransid()
    {
        return $this->transid;
    }
}
