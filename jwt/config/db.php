<?php

class Database{
    private $hostname;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function connect(){
       
        //?check for offline server and connect db
        $localhost = array(
            '127.0.0.1',
            '::1'
        ); 

        if(in_array($_SERVER['REMOTE_ADDR'], $localhost)){
            $this->hostname="localhost";
            $this->user="root";
            $this->pass="";
            $this->dbname="express";
        }else { 
            $this->hostname="localhost";
            $this->user="blevimar_api";
            $this->pass="@@admin100@";
            $this->dbname="blevimar_api";
        }
        
        $this->conn = new mysqli($this->hostname, $this->user, $this->pass, $this->dbname);
        if($this->conn->connect_error){
            die($this->conn->connect_error);
        }else{
            return $this->conn; 
        }
    }
}

