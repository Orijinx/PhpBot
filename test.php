<?php
class DB
{
    private $mysqli;

    public function __construct($chatId)
    {
        $this->mysqli = mysqli_connect("hellohexx.beget.tech", "hellohexx_bot", "12345678Bb", "hellohexx_bot");
        if($chatId != null){
        $this->mysqli->query("INSERT INTO TeleBot(chatId) VALUES ($chatId)");
        }
    }
    public function addUser($chatId){
        $this->mysqli->query("INSERT INTO TeleBot(chatId) VALUES ($chatId)");
        return TRUE;
    }
    public function getUserByChatId($chatId){
        $res = mysqli_query($this->mysqli, "SELECT * FROM TeleBot where chatId = $chatId ");
        $row = mysqli_fetch_object($res);
        return $row;
    }
    public function SetUserName($name,$chatId){
        $this->mysqli->query("UPDATE TeleBot SET Name = '$name' where chatId = $chatId");
    }
    public function SetUserPhone($phone,$chatId){
        $this->mysqli->query("UPDATE TeleBot SET Name = '$phone' where chatId = $chatId");
    }
    public function GetCommandId($chatId){
        $res = mysqli_query($this->mysqli, "SELECT idLastCommand FROM TeleBot where chatId = $chatId");
        $row = mysqli_fetch_object($res);
        return $row->idLastCommand;
    }
    public function SetCommand($id,$chatId){
        $this->mysqli->query("UPDATE TeleBot SET idLastCommand = '$id' where chatId = $chatId");    
    }
}

$db = new DB(null);



var_dump($db->GetCommandId("980196074"));
if($db->GetCommandId("980196074")=="0"){
    echo "null";
}