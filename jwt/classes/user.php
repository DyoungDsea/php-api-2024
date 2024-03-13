<?php

class User{
    public $name; 
    public $username;
    public  $phone;
    public $email;
    public $pass;
    public $code;
    public $date;

    private $tableName;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->tableName = "dlogin";
    } 
    
    public function createUser(){
        $inner = $this->conn->query("INSERT INTO $this->tableName SET userid='$this->code', username='$this->username', demail='$this->email', dfname='$this->name', dphone='$this->phone', dpass = '$this->pass', ddate='$this->date'");
        if($inner){
     
          
          $user = $this->conn->query("SELECT id FROM $this->tableName WHERE userid='$this->code' AND demail='$this->email'")->fetch_assoc();
          $myId = $user['id'].rand(5466372,8987655);

          $this->conn->query("UPDATE $this->tableName SET drefCode='$myId' WHERE userid='$this->code' AND demail='$this->email'");

          $flop = $this->conn->query("SELECT userid, username, drefCode, dfname, demail, dphone, dwallet, dpass, ddob, dstatus, ddate FROM $this->tableName WHERE userid='$this->code' ");
        
            $row=fetchAssoc($flop);   
            $data = [
                'success'=>true,
                'user'=>$row,
                'msg'=>'Success'
            ];
            
     
        }
    }
}

