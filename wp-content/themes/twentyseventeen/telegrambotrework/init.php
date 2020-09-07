<?php
include('vendor/autoload.php');

include ('dataBase.php');
$db = new dataBase();
$language = "ukr";
include('telegramBot.php');


//Отримуємо повідомлення
$telegramApi = new TelegramBot();

//підєднуємось до бази даних

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatId = $update["message"]["chat"]["id"];

$telegramApi->log_conversation(1, 1,1,1,1);
$telegramApi->log_conversation($chatId, 1,1,1,1);

$telegramApi->log_conversation(3, 1,1,1,1);



//по кожному повідомленню пробігаємось


	//на кожне повідомлення відповідаємо

	if ($update["callback_query"]["data"]) {

		$telegramApi->answer($update["callback_query"]["message"]["chat"]["id"], $update["callback_query"]["data"]);
	}
	else
	{
		$db->log_conversation($update["message"]["text"], $update["message"]["chat"]["id"], 1, $update["message"]["date"], $update["message"]["message_id"]);
		$telegramApi->answer($update["message"]["chat"]["id"], $update["message"]["text"]);
	}





