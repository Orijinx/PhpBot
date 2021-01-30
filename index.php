<?php
if($_SERVER['REQUEST_URI'] === '/'){
    return require "./tulips-bot.php";
}
elseif($_SERVER['REQUEST_URI'] === '/contacts')
    echo 'Контакты';
else
    echo 'Ошибка 404';
