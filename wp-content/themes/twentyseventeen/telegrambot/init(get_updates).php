<?php
include('vendor/autoload.php');

include ('dataBase.php');
$db = new dataBase();

include('telegramBot.php');


//Отримуємо повідомлення
$telegramApi = new TelegramBot();

//підєднуємось до бази даних

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatId = $update["message"]["chat"]["id"];

$telegramApi->log_conversation(1, 1,1,1,1);
$telegramApi->log_conversation($, 1,1,1,1);

$updates = $telegramApi->getUpdates();
$telegramApi->log_conversation(3, 1,1,1,1);


$telegramApi->log_conversation($updates, 1,1,1,1);

//по кожному повідомленню пробігаємось
foreach ($updates as $update){

	//на кожне повідомлення відповідаємо

	if ($update->callback_query->data) {

		//повернення в голоне меню
		if ( $update->callback_query->data == 'main_menu' ) {
			//		$db->log_conversation("main_menu", $update->callback_query->message->chat->id, 1, $update->callback_query->message->date, $update->callback_query->message->message_id);
			$telegramApi->answer( $update->callback_query->message->chat->id, "main_menu" );

		}

		if ( $update->callback_query->message->text == "Виберіть дату:" or $update->callback_query->message->text == "Виберіть дату яку бажаєте скасувати:"  )
		{
			echo "відоавідь на Виберіть дату";
			if ($db->getPreviusMessage($update->callback_query->message->chat->id) == "Виберіть дату:" or $db->getPreviusMessage($update->callback_query->message->chat->id) == "Виберіть дату яку бажаєте скасувати:"){
				echo "попереднє також Виберіть дату";
				$telegramApi->answer($update->callback_query->message->chat->id, $update->callback_query->data);
			}
		}
	}
	else
	{
		$db->log_conversation($update->message->text, $update->message->chat->id, 1, $update->message->date, $update->message->message_id);
		$telegramApi->answer($update->message->chat->id, $update->message->text);
	}




}
