<?php

class DB
{
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = mysqli_connect("hellohexx.beget.tech", "hellohexx_bot", "12345678Bb", "hellohexx_bot");
    }
    public function addNote($name)
    {
        $this->mysqli->query("INSERT INTO notes(name) VALUES ($name)");
        return TRUE;
    }
    public function getNotes()
    {
        $res = mysqli_query($this->mysqli, "SELECT * FROM notes ");
        $row = mysqli_fetch_object($res);
        return json_encode($row);
    }
    /////////////////////////////////////////////////////////////////////////////////////
    public function getUserByChatId($chatId)
    {
        $res = mysqli_query($this->mysqli, "SELECT * FROM TeleBot where chatId = $chatId ");
        $row = mysqli_fetch_object($res);
        return $row;
    }
    public function SetUserName($name, $chatId)
    {
        $this->mysqli->query("UPDATE TeleBot SET Name = '$name' where chatId = $chatId");
    }
    public function SetUserPhone($phone, $chatId)
    {
        $this->mysqli->query("UPDATE TeleBot SET Name = '$phone' where chatId = $chatId");
    }
    public function GetCommandId($chatId)
    {
        $res = mysqli_query($this->mysqli, "SELECT idLastCommand FROM TeleBot where chatId = $chatId");
        $row = mysqli_fetch_object($res);
        return $row->idLastCommand;
    }
    public function SetCommand($id, $chatId)
    {
        $this->mysqli->query("UPDATE TeleBot SET idLastCommand = '$id' where chatId = $chatId");
    }
    public function AddOrder($order, $chatId)
    {
        $json = json_encode($order, JSON_UNESCAPED_UNICODE);
        $this->mysqli->query("UPDATE TeleBot SET jOrder = '$json' where chatId = $chatId");
    }
    public function GetOrder($chatId)
    {
        $res = mysqli_query($this->mysqli, "SELECT jOrder From TeleBot where chatId = $chatId");
        $row = mysqli_fetch_object($res);
        return json_decode($row->jOrder);
    }
    public function SetValue($val, $chatId)
    {
        $this->mysqli->query("UPDATE TeleBot SET Value = $val where chatId = $chatId");
    }
    public function S($chatId)
    {
        $json = json_encode(array("type" => []));
        $this->mysqli->query("UPDATE TeleBot SET jOrder = '$json',Value=0,idLastCommand = '0' where chatId = $chatId");
    }
}


$db=new DB;
$name=$_POST["name"];
$db->addNote($name);