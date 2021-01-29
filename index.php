<?php
include('vendor/autoload.php'); //Подключаем библиотеку
use Telegram\Bot\Api;

class DB
{
    private $mysqli;

    public function __construct($chatId)
    {
        $this->mysqli = mysqli_connect("hellohexx.beget.tech", "hellohexx_bot", "12345678Bb", "hellohexx_bot");
        if ($chatId != null) {
            $this->mysqli->query("INSERT INTO TeleBot(chatId) VALUES ($chatId)");
        }
    }
    public function addUser($chatId)
    {
        $arr = array("type" => []);
        $json = json_encode($arr);
        $this->mysqli->query("INSERT INTO TeleBot(chatId,jOrder) VALUES ($chatId,'$json')");
        return TRUE;
    }
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

$db = new DB(null);

$telegram = new Api('1612113022:AAHlGWbCSHQdrjPc2klvdZSxo_kZMGCChJk'); //Устанавливаем токен, полученный у BotFather
$result = $telegram->getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
$text = $result["message"]["text"]; //Текст сообщения
$chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
$name = $result["message"]["from"]["username"]; //Юзернейм пользователя
$keyboard = [["Собрать заказ"], ["Настроить пользователя"], ["/help"]]; //Клавиатура
$t_keyboard = [["🟩Зеленые"], ["🟨Желтые"], ["🌷Красные"], ["Выбрать несколько"]];
$tm_keyboard = [["🟩Зеленые"], ["🟨Желтые"], ["🌷Красные"], ["Закончить"]];
$u_keyboard = [["Изменить имя"], ["Главное меню"]];
$em_keyboard = [];
$s_keyboard = [["/start"]];
$command = $db->GetCommandId($chat_id);

if ($text == "/s") {
    //$arr = array("type" => []);
    $db->S($chat_id);
    //$db->SetCommand("0", $chat_id);
    $reply = "Состояние сброшено!";
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $s_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
    exit;
}

if (($command == "0") || ($command == null)) {
    if ($text) {
        if ($text == "/start") {
            if ($db->getUserByChatId($chat_id) != null) {
                $db->S($chat_id);
                $user = $db->getUserByChatId($chat_id);
                $reply = "Привет, $name! Я помогу тебе сделать заказ тюльпанов!";
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            } else {
                $db->addUser($chat_id);
                $db->SetUserName($name, $chat_id);
                $reply = "Привет, $name! Я помогу тебе сделать заказ тюльпанов!";
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            }
        } elseif ($text == "Настроить пользователя") {
            $user = $db->getUserByChatId($chat_id);
            $reply = "Основная информация\nИмя:$user->Name\nТелефон:$user->Phone\n";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $u_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } elseif ($text == "Изменить имя") {
            $reply = "Как вас называть?";
            $db->SetCommand("1", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $em_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } elseif ($text == "Главное меню") {
            $reply = "Главное меню:";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } elseif ($text == "Собрать заказ") {
            $reply = "Выберите цвет.\nЕсли вам нужно несколько - нажмите соответствующую кнопку.";
            $db->SetCommand("3", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $t_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } else {
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение."]);
        }
    }
} elseif ($command == "1") {
    $db->SetUserName($text, $chat_id);
    $reply = "Ваше новое имя : $text";
    $db->SetCommand("0", $chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $u_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
} elseif (($command == "3") || ($command == "31")) {
    if ($text == "Выбрать несколько") {
        $reply = "Выберите цвета:";
        $db->SetCommand("31", $chat_id);
        $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
        $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
    } elseif ($text == "🟩Зеленые") {
        $order = $db->GetOrder($chat_id);
        array_push($order->type, "Зеленые");
        $db->AddOrder($order, $chat_id);
        if ($command == "3") {
            $reply = "Введите количество:";
            $db->SetCommand("4", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } else {
            $reply = "Добавлен зеленый!:";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        }
    } elseif ($text == "🟨Желтые") {
        $order = $db->GetOrder($chat_id);
        array_push($order->type, "Желтые");
        $db->AddOrder($order, $chat_id);
        if ($command == "3") {
            $reply = "Введите количество:";
            $db->SetCommand("4", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } else {
            $reply = "Добавлен желтый!:";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        }
    } elseif ($text == "🌷Красные") {
        $order = $db->GetOrder($chat_id);
        array_push($order->type, "Красные");
        $db->AddOrder($order, $chat_id);
        if ($command == "3") {
            $reply = "Введите количество:";
            $db->SetCommand("4", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } else {
            $reply = "Добавлен Красный!:";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        }
    } elseif ($text == "Закончить") {
        $reply = "Введите количество:";
        $db->SetCommand("4", $chat_id);
        $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
        $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
    }
} elseif ($command == "4") {
    $db->SetValue($text, $chat_id);
    $reply = "Заказ сформирован!";
    $db->SetCommand("0", $chat_id);
    $user = $db->getUserByChatId($chat_id);
    $reply_t="";
    foreach ($db->GetOrder($chatId) as $key) {
        if($key==1){
            $reply_t.="Зеленые ";
        }
        if($key==2){
            $reply_t.="Желтые ";
        }
        if($key==3){
            $reply_t.="Красные ";
        }
    }
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $s_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram
   ->setAsyncRequest(true)
   ->sendMessage(['chat_id' => "980196074", 'text' => "Новый заказ!\n Имя:$user->Name\nЗаказ: $user->jOrder\nКоличество:$user->value", 'reply_markup' => $reply_markup]);
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Заказ получен!\n", 'reply_markup' => $reply_markup]);
    //$telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
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
