<?php
include('vendor/autoload.php');

//База даних
include ('dataBase.php');
$db = new dataBase();
//телеграм
include('telegramBot.php');
$telegramApi = new TelegramBot();

$language = "ukr";
$telegramApi->send_notification_for_today();