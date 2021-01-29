<?php
include('vendor/autoload.php'); //Подключаем библиотеку
use Telegram\Bot\Api;

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

$telegram = new Api('1612113022:AAHlGWbCSHQdrjPc2klvdZSxo_kZMGCChJk'); //Устанавливаем токен, полученный у BotFather
$result = $telegram->getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
$text = $result["message"]["text"]; //Текст сообщения
$chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
$name = $result["message"]["from"]["username"]; //Юзернейм пользователя
$keyboard = [["Собрать заказ"],["Настроить пользователя"], ["/help"]]; //Клавиатура
$t_keyboard = [["🟩Зеленые"], ["🟨Желтые"], ["🌷Красные"]];
$u_keyboard=[["Изменить имя"], ["Главное меню"], ["Красные"]];
$em_keyboard=[];
if ($db->GetCommandId($chat_id)=="0") {
    if ($text) {
        if ($text == "/start") {
            if ($db->getUserByChatId($chat_id)!=null) {
                $user = $db->getUserByChatId($chat_id);
                $reply = "Привет, $name! Я помогу тебе сделать заказ тюльпанов!";
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            }
            else {
                $db->addUser($chat_id);
                $db->SetUserName($name, $chat_id);
                $reply = "Привет, $name! Я помогу тебе сделать заказ тюльпанов!";
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            }
        }elseif($text == "Настроить пользователя"){
                $user = $db->getUserByChatId($chat_id);
                $reply = "Основная информация\nИмя:$user->Name\nТелефон:$user->Phone\n";
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $u_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            }elseif($text == "Изменить имя"){
                $reply = "Как вас называть?";
                $db->SetCommand("1",$chat_id);
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $em_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            }elseif($text == "Главное меню"){
                $reply = "Главное меню:";
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            }
            
            else {
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение."]);
        }
    }
}elseif( $db->GetCommandId($chat_id)=="1"){
    $db->SetUserName($text,$chat_id);
    $reply = "Ваше новое имя : $text";
    $db->SetCommand("0",$chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $u_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
//     elseif ($text == "/help") {
//         $reply = "Информация с помощью.";
//         $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply]);
//     } elseif ($text == "Собрать заказ") {
//         $reply = "Выбери цвет(а)!";
//         $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $t_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
//         $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
//     } elseif ($text == "Гифка") {
//         $url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
//         $telegram->sendDocument(['chat_id' => $chat_id, 'document' => $url, 'caption' => "Описание."]);
//     } elseif ($text == "Последние статьи") {
//         $html = simplexml_load_file('http://netology.ru/blog/rss.xml');
//         foreach ($html->channel->item as $item) {
//             $reply .= "\xE2\x9E\xA1 " . $item->title . " (<a href='" . $item->link . "'>читать</a>)\n";
//         }
//         $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply]);
//     } else {
//         $reply = "По запросу \"<b>" . $text . "</b>\" ничего не найдено.";
//         $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => $reply]);
//     }
// } else {
//     $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение."]);
// }
