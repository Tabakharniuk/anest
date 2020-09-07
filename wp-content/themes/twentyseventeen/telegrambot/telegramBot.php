<?php
use GuzzleHttp\Client;


class TelegramBot extends dataBase {
	protected $token = "475665632:AAFvMHWmFH9Q3WVOOVfXVgBPrZwrsUDCx_A";
	protected $updateId;
	public $site = "https://www.google.com.ua/";
	public $start_keyboard = array(

		"keyboard" => array(
			array(

				array(
					"text" => "Зареєструватись"

				)

			),
			array(
				array(
					"text" => "Сповістити мене про появу вільних місць чи нові дати для реєстрації",

				)),

			array(
				array(
					"text" => "Скасувати минулу реєстрацію",
				))


		),
		"one_time_keyboard" => true, // можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
		"resize_keyboard" => false // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
	);
	public $keyboard_cancel = array(
		"inline_keyboard" => array(array(array(
			"text" => "Скасувати/В головне меню",
			"callback_data" => "main_menu"
		)))
	);



	protected function query( $metod, $params = [] ) {
		$url = "https://api.telegram.org/bot";

		$url .= $this->token;

		$url .= "/" . $metod;

		if ( ! empty( $params ) ) {
			$url .= "?" . http_build_query( $params );
		}

		$client = new Client( [
			'base_uri' => $url
		] );

		$result = $client->request( "GET" );

		return json_decode( $result->getBody() );

	}

	public function getUpdates() {
//		$response = $this->query( 'getUpdates', [
//			'offset' => $this->updateId + 1
//		] );
//
//		if ( ! empty( $response->result ) ) {
//			$this->updateId = $response->result[ count( $response->result ) - 1 ]->update_id;
//		}
//
//		return $response->result;


		$this->log_conversation(json_decode(file_get_contents('php://input')), 1, 1,1, 1);


	}



	public function sendMessage( $chat_id, $text ) {
		$response = $this->query( "sendMessage", [
			"text"    => $text,
			'chat_id' => $chat_id
		] );

		$this->log_conversation( $text, $chat_id, false, '', '' );


		return $response;
	}

	public function sendMessageHTML( $chat_id, $text ) {
		$response = $this->query( "sendMessage", [
			"text"    => $text,
			'chat_id' => $chat_id,
			'parse_mode' => 'HTML'
		] );

		$this->log_conversation( $text, $chat_id, false, '', '' );


		return $response;
	}

	public function sendKeyboard( $chat_id, $text, $keyboard ) {

		$response = $this->query( "sendMessage", [
			"text"         => $text,
			'chat_id'      => $chat_id,
			'reply_markup' => json_encode( $keyboard )
		] );

		$this->log_conversation( $text, $chat_id, false, '', '' );

		return $response;
	}

	private function anwserToStandartMessage($message, $chat_id) {

		if ( $message == "/start" ) {
			$keyboard = array(

				"keyboard"          => array(
					array(

						array(
							"text" => "Зареєструватись"

						)

					),
					array(
						array(
							"text" => "Сповістити мене про появу вільних місць чи нові дати для реєстрації",

						)
					),

					array(
						array(
							"text" => "Скасувати минулу реєстрацію",
						)
					)


				),
				"one_time_keyboard" => true,
				// можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
				"resize_keyboard"   => false
				// можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
			);
			print "stat";
			$message_to_send = "Доброго дня! Вас вітає бот для реєстрації на Анестезіологію! Виберіть бажану дію:";

			$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );

			return;
		}
		if ( $message == "main_menu" ) {
			$keyboard = array(

				"keyboard"          => array(
					array(

						array(
							"text" => "Зареєструватись"

						)

					),
					array(
						array(
							"text" => "Сповістити мене про появу вільних місць чи нові дати для реєстрації",

						)
					),

					array(
						array(
							"text" => "Скасувати минулу реєстрацію",
						)
					)


				),
				"one_time_keyboard" => true,
				// можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
				"resize_keyboard"   => false
				// можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
			);
			print "stat";
			$message_to_send = "Ви в головному меню. Виберіть бажану дію:";

			$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );

			return;
		}
		if ( $message == "Зареєструватись" ) {
			$keyboard        = $this->getDatesAsKeyboard();
			$message_to_send = "Виберіть дату:";

			$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );

			return;
		}
		if ( $message == "Сповістити мене про появу вільних місць чи нові дати для реєстрації" ) {

			$this->subscribeToNews( $chat_id );


			$this->sendMessage( $chat_id, "Гаразд! Я повідомлю Вас!" );

			$message_to_send = "Я ще можу Вам чимось допомогти?";
			echo $this->start_keyboard;

			$this->sendKeyboard( $chat_id, $message_to_send, $this->start_keyboard );

			return;
		}
		if ( $message == "Скасувати минулу реєстрацію" ) {
			$keyboard        = $this->getDatesOfRegistrationAsKeyboard($chat_id);
			if (empty($keyboard)){
				$this->sendMessage($chat_id, "Ви не зареєстровані на жодну з дат.");
				$message_to_send = "Виберіть бажану дію:";

				$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
				return;
			}

			$message_to_send = "Виберіть дату яку бажаєте скасувати:";

			$this->sendKeyboard( $chat_id, $message_to_send, $keyboard);

			return;
		}
		return true;
	}

	private function anwserToNonStandartMessage($message, $chat_id, $last_message)
	{
	echo $last_message;
		echo "nonStandartMessage";
		switch ($last_message) {
			case "Виберіть дату:":
				echo "nonStandartMessage виберіть дату";

				switch ($this->listCheckAvailable($chat_id, $message)){
					case "зареєстрований":
						$this->sendMessage($chat_id, "Ви вже зареєстровані на дану дату!");
						$message_to_send = "Я можу Вам ще чимось допомогти?";


						$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
						break;
					case "нема місць":
						$this->sendMessage($chat_id, "На жаль, вільних місць на вибрану Вами дату немає.");
						$message_to_send = "Я можу Вам ще чимось допомогти?";


						$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
						break;
					default:
						$epoh_date = $this->dateGetById($message)['date'];
						$this->listRegister($chat_id, $message, $epoh_date);

						$date = $this->date($epoh_date);
						$this->sendMessage($chat_id, "Вибрана дата: ".$date);

						$message_to_send = "Введіть Ваше прізвище:";

						$this->sendMessage($chat_id, $message_to_send);

				}


				break;
			case "Введіть Ваше прізвище:":

				$this->listUpdate($chat_id, "ln", $message);
				$message_to_send = "Введіть Ваше ім*я:";



				$this->sendMessage($chat_id, $message_to_send);
				break;
			case "Введіть Ваше ім*я:":
				$this->listUpdate($chat_id, "fn", $message);
				$message_to_send = "Введіть курс:";

				$this->sendMessage($chat_id, $message_to_send);
				break;
			case "Введіть курс:":
				$this->listUpdate($chat_id, "course", $message);
				$message_to_send = "Введіть групу:";

				$this->sendMessage($chat_id, $message_to_send);
				break;
			case "Введіть групу:":
				$this->listUpdate($chat_id, "sgroup", $message);
				$this->listSuccess($chat_id);
				$this->sendMessage($chat_id, "Зареєстровано!");
				$message_to_send = "Я можу Вам ще чимось допомогти?";


				$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
				break;
			case "Виберіть дату яку бажаєте скасувати:":
				print "Виберіть дату яку бажаєте скасувати:";
				$this->listDelete($chat_id, $message);
				$date = $this->date($this->dateGetById($message)['date']);
				$this->sendMessage($chat_id, "Вибрана дата: ".$date);
				$this->sendMessage($chat_id, "Реєстрацію скасовано!");
				$message_to_send = "Я можу Вам ще чимось допомогти?";


				$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
				break;
			default:

				$this->sendMessage($chat_id, "Я не зовсім зрозумів Вас...");
				$message_to_send = "Виберіть бажану дію:";

				$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);

		}

	}

	public function answer($chat_id, $message)
	{
		//провірка чи це є простим питанням, відповідь на нього і закінчення функції.
		if(empty($this->anwserToStandartMessage($message, $chat_id)))
		{
			echo "відповідає на стандартнє";
			return;
		}

		//змінна з останнім повідомленням яке надсилалось користувачу
		$last_message = $this->getPreviusMessage($chat_id);

		$this->anwserToNonStandartMessage($message, $chat_id, $last_message);


	}

	public function deleteStudentFromList($id){
		$row_from_list = $this->listGetChat_idById($id);
		if (empty($row_from_list)){
			return;
		}

		$this->sendMessage($row_from_list['chat_id'], "Вашу Реєстрацію за датою ".$this->date($row_from_list['date'])." - скасовано адміністратором.");
		$this->sendMessage($row_from_list['chat_id'], "Будь ласка, перевірте списки реєстрації на нашому сайті:");
		$this->sendMessageHTML($row_from_list['chat_id'], "<a href='".$this->site."'>Анестезіологія</a>");

		$this->listDeleteById($id);

	}

	public function recoverStudentFromList($id){
		$row_from_list = $this->listGetChat_idById($id);
		if (empty($row_from_list)){
			return;
		}

		$this->sendMessage($row_from_list['chat_id'], "Вашу Реєстрацію за датою ".$this->date($row_from_list['date'])." - відновлено адміністратором.");
		$this->sendMessage($row_from_list['chat_id'], "Будь ласка, перевірте списки реєстрації на нашому сайті:");
		$this->sendMessageHTML($row_from_list['chat_id'], "<a href='http://google.com'>Анестезіологія</a>");

		$this->listRecoverById($id);

	}

	public function T_Date_Delete($id)
	{

	}


}