<?php
include 'vendor/autoload.php'; //Подключаем библиотеку
use Telegram\Bot\Api;

class SendMessage
{
    public static function Send($message)
    {
        // сюда нужно вписать токен вашего бота
        define('TELEGRAM_TOKEN', '1612113022:AAHlGWbCSHQdrjPc2klvdZSxo_kZMGCChJk');

        // сюда нужно вписать ваш внутренний айдишник
        define('TELEGRAM_CHATID', '441239846');


        $ch = curl_init();
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendMessage',
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => array(
                    'chat_id' => TELEGRAM_CHATID,
                    'text' => $message,
                ),
            )
        );
        curl_exec($ch);
    }
}
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
        return true;
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
        $this->mysqli->query("UPDATE TeleBot SET Phone = '$phone' where chatId = $chatId");
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
$keyboard = [["Собрать заказ"], ["Настроить пользователя"]]; //Клавиатура
$t_keyboard = [["🟩Зеленые"], ["🟨Желтые"], ["🌷Красные"], ["Выбрать несколько"]];
$tm_keyboard = [["🟩Зеленые"], ["🟨Желтые"], ["🌷Красные"], ["Закончить"]];
$u_keyboard = [["Изменить имя"], ["Изменить телефон"], ["Главное меню"]];
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

if ($text == "/start") {
    if ($db->getUserByChatId($chat_id) != null) {
        $db->S($chat_id);
        $user = $db->getUserByChatId($chat_id);
        $reply = "Привет, $user->Name! Я помогу тебе сделать заказ тюльпанов!";
        $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
        $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
    } else {
        $db->addUser($chat_id);
        $db->SetUserName($name, $chat_id);
        $reply = "Привет, $name! Ты здесь впервые? Я помогу тебе сделать заказ тюльпанов!\nНо перед этим, рекомендую настроить пользователя!";
        $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
        $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
    }
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
        } elseif ($text == "Изменить телефон") {
            $reply = "Оставьте ваш контактный телефон для связи:";
            $db->SetCommand("2", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $em_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => []]);
        } elseif ($text == "Главное меню") {
            $reply = "Главное меню:";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } elseif ($text == "Собрать заказ") {
            $user = $db->getUserByChatId($chat_id);
            if (($user->Phone == null) || ($user->Name == null)) {
                $reply = 'Перед оформлением заказа, пожалуйста, заполните контактную информацию в разделе "Настройки пользователя"!';
                $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
                $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
                exit;
            }
            $reply = "Выберите цвет.\nЕсли вам нужно несколько - нажмите соответствующую кнопку.";
            $db->SetCommand("3", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $t_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } else {
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение."]);
        }
    }
} elseif (($command == "1") || ($command == "12")) {
    $db->SetUserName($text, $chat_id);
    $reply = "Ваше новое имя : $text";
    $db->SetCommand("0", $chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $u_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
} elseif (($command == "2")) {
    if (is_numeric($text)) {
        $db->SetUserPhone($text, $chat_id);
        $reply = "Ваше телефон : $text";
        $db->SetCommand("0", $chat_id);
    } else {
        $reply = "Номер телефона должен содержать только числа!";
    }
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
        if (in_array("Зеленые", $order->type)) {
            $reply = "Вы уже выбрали данный цвет!";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            exit;
        }
        array_push($order->type, "Зеленые");
        $db->AddOrder($order, $chat_id);
        if ($command == "3") {
            $reply = "Введите количество:";
            $db->SetCommand("4", $chat_id);
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        } else {
            $ntm_key_board;
            $reply = "Добавлен зеленый!:";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
        }
    } elseif ($text == "🟨Желтые") {
        $order = $db->GetOrder($chat_id);
        if (in_array("Желтые", $order->type)) {
            $reply = "Вы уже выбрали данный цвет!";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            exit;
        }
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
        if (in_array("Красные", $order->type)) {
            $reply = "Вы уже выбрали данный цвет!";
            $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $tm_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
            $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
            exit;
        }
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
} elseif (($command == "4")) {
    $db->SetValue($text, $chat_id);
    $reply = "Заказ сформирован!";
    $db->SetCommand("0", $chat_id);
    $user = $db->getUserByChatId($chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $s_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram
        ->setAsyncRequest(true)
        ->sendMessage(['chat_id' => "441239846", 'text' => "Новый заказ!\n Имя:$user->Name\nЗаказ: $user->jOrder\nКоличество:$user->Value", 'reply_markup' => $reply_markup]);
    $telegram
        ->setAsyncRequest(true)
        ->sendMessage(['chat_id' => "620096189", 'text' => "Новый заказ!\n Имя:$user->Name\nЗаказ: $user->jOrder\nКоличество:$user->Value", 'reply_markup' => $reply_markup]);
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Заказ получен!\n", 'reply_markup' => $reply_markup]);
    //$telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
 } 
//elseif (($command == "5")) {
//     $db->SetValue($text, $chat_id);
//     $reply = "Заказ сформирован!";
//     $db->SetCommand("0", $chat_id);
//     $user = $db->getUserByChatId($chat_id);
//     $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $s_keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
//     SendMessage::Send("Новый заказ!\n Имя:$user->Name\nЗаказ: $user->jOrder\nКоличество:$user->Value");
//     $telegram
//         ->setAsyncRequest(true)
//         ->sendMessage(['chat_id' => "441239846", 'text' => "Новый заказ!\n Имя:$user->Name\nЗаказ: $user->jOrder\nКоличество:$user->Value", 'reply_markup' => $reply_markup]);
//     $telegram
//         ->setAsyncRequest(true)
//         ->sendMessage(['chat_id' => "620096189", 'text' => "Новый заказ!\n Имя:$user->Name\nЗаказ: $user->jOrder\nКоличество:$user->Value", 'reply_markup' => $reply_markup]);
//     $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Заказ получен!\n", 'reply_markup' => $reply_markup]);
//     //$telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup]);
// }

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
