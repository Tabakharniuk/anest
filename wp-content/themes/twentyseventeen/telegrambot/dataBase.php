<?php

class dataBase
{
//	localhost
//	protected $host = "localhost";
//	protected $db = "anestezyology";
//	protected $user = "root";
//	protected $password = "";

//	server
	protected $host = "tests01.mysql.tools";
	protected $db = "tests01_anest";
	protected $user = "tests01_anest";
	protected $password = "zuq96ubz";

	protected $connection;
	function __construct() {
		$this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->db);
		mysqli_set_charset($this->connection,"utf8");
	}

	private function queryWithoutReturn($sql)
	{
//		echo $sql;
		$rrr = mysqli_query($this->connection, $sql) or die(mysqli_error($this->connection));
	}

	private function query($sql)
	{
//		echo $sql;
		$rrr = mysqli_query($this->connection, $sql) or die(mysqli_error($this->connection));
		return mysqli_fetch_array($rrr);
	}

	private function queryArray($sql)
	{
//		echo $sql;
		$rrr = mysqli_query($this->connection, $sql) or die(mysqli_error($this->connection));

		$array = array();
		while ($rs = mysqli_fetch_array($rrr))
		{
			array_push($array, $rs);
		}
		return $array;
	}

	public function prepareText($text){
		$search  = array('"', "'", '/', '\\');
		$replace = array('', '', '', '');
		return str_replace($search, $replace, $text);
	}

	public function getDates()
	{
		$time = strtotime(date('d-m-Y'));
		$sql = "SELECT * FROM dates WHERE date >= '{$time}' AND status = 'done' ORDER BY date ";

		$dates = array();
		foreach ($this->queryArray($sql) as $date)
		{
			array_push($dates, $date);
		}

		return $dates;


	}

	public function date($time){

		$translated = date("d F G:i", $time);
		$English_name = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$Ukrainian_name   = array("Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Січня", "Лютого", "Березеня", "Квітня", "Травня", "Червня", "Липня", "Серпня", "Вересня", "Жовтня", "Листопада", "Грудня");

		return str_replace($English_name, $Ukrainian_name, $translated);
	}

	public function getListByDate($date_id)
	{
		$sql = "SELECT * FROM list WHERE date_id = '{$date_id}' and status = 'done'";

		return $this->queryArray($sql);
	}

	public function getDatesAsKeyboard(){
		$time = time();
		$sql = "SELECT * FROM dates WHERE date > '{$time}' AND status = 'done'";

		$dates = $this->queryArray($sql);

		if (empty($dates)){
			return false;
		}
		$keyboard = array(

			"inline_keyboard" => array(


			),

			"resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
		);

		print_r($dates);

		foreach ($dates as $date)
		{
			array_push($keyboard['inline_keyboard'], array(array("text" => $this->date($date['date'])." ".$date['name'], "callback_data" => $date['id'])));
		}

		array_push($keyboard['inline_keyboard'], array(array("text" => "Скасувати/В головне меню", "callback_data" => "main_menu")));

		print_r($keyboard);
		return $keyboard;
	}

	public function getDatesOfRegistrationAsKeyboard($chat_id){
		$time = time();
		$sql = "SELECT * FROM list WHERE date > '{$time}' and chat_id = '{$chat_id}' and status = 'done' ORDER BY date";
		print $sql;

		$dates = $this->queryArray($sql);

		if (empty($dates)){
			return;
		}
		$keyboard = array(

			"inline_keyboard" => array(


			),


		);

		print_r($dates);

		foreach ($dates as $date)
		{
			array_push($keyboard['inline_keyboard'], array(array("text" => $this->date($date['date']), "callback_data" => strval($date['date_id']))));
		}

		array_push($keyboard['inline_keyboard'], array(array("text" => "Скасувати/В головне меню", "callback_data" => "main_menu")));

		print_r($keyboard);
		return $keyboard;
	}

	public function log_conversation($text, $chat_id, $user, $time, $message_id){

		$text = $this->prepareText($text);

		if(empty($user)){
			$user = 0;
		}

		if(empty($text)){
			$text = "empty";
		}

		if(empty($chat_id)){
			$chat_id = 0;
		}

		if(empty($time)){
			$time = time();
		}

		if(empty($message_id)){
			$time = 0;
		}

		//Добавлення запису про останнє повідомлення
		if ($user == false or $user == 0)
		{
			$this->queryWithoutReturn("DELETE FROM last WHERE chat_id = '{$chat_id}'");
			$this->queryWithoutReturn("INSERT INTO last (type, chat_id, time) VALUES ('{$text}', '{$chat_id}','{$time}')");


		}

		$sql = "INSERT INTO log_conversation (text, chat_id, is_user, time, message_id) VALUES ('{$text}', '{$chat_id}', '{$user}', '{$time}', '{$message_id}')";
		return $this->queryWithoutReturn($sql);
	}

	public function listRegister($chat_id, $date_id, $date)
		//Добавляє в базу даних реєстрації певну інформацію
	{
		$time = time();
		$this->queryWithoutReturn("DELETE FROM list WHERE chat_id = '{$chat_id}' and status = 'new'");
		$sql = "INSERT INTO list (chat_id, date_id, date, status, time_of_registration) VALUES ('{$chat_id}', '{$date_id}', '{$date}', 'new', '{$time}')";

		return $this->queryWithoutReturn($sql);
	}

	public function listCheckAvailable($chat_id, $date_id){
		//Провіряє чи студент зареєстрований на дану дату і чи є вільні місця

		$sql = "SELECT chat_id FROM list WHERE date_id = '{$date_id}' and status = 'done'";

		$array = $this->queryArray($sql);
		foreach ($array as $current){
			if ($current['chat_id'] == $chat_id)
			{
				return "зареєстрований";
			}
		}

		$sql = "SELECT max_seats FROM dates WHERE id = '{$date_id}'";
		$max_seat = $this->query($sql)['max_seats'];

		if ($max_seat <= count($array))
		{
			return "нема місць";
		}

		return;


	}

	public function listUpdate($chat_id, $column, $value)
		//Добавляє в базу даних реєстрації певну інформацію
	{
		$value = $this->prepareText($value);
		$sql = "UPDATE list SET ".$column." = '{$value}' WHERE chat_id = '$chat_id' and status = 'new'";

		return $this->queryWithoutReturn($sql);
	}
	public function listDelete($chat_id, $date_id)
	{
		$sql = "UPDATE list SET status = 'del' WHERE chat_id = '$chat_id' and status = 'done' and date_id = '{$date_id}'";

		return $this->queryWithoutReturn($sql);
	}

	public function listDeleteById($id)
	{
		$sql = "SELECT * FROM list WHERE id = '{$id}';";

		$this->queryWithoutReturn($sql);
		$sql = "UPDATE list SET status = 'del' WHERE id = '{$id}';";

		return $this->queryWithoutReturn($sql);
	}

	public function listRecoverById($id)
	{
		$sql = "UPDATE list SET status = 'done' WHERE id = '{$id}';";

		return $this->queryWithoutReturn($sql);
	}

	public function listSuccess($chat_id){
		$sql = "UPDATE list SET status = 'done' WHERE chat_id = '$chat_id' and status = 'new'";

		return $this->queryWithoutReturn($sql);
	}

	public function listGetChat_idById($id){
		$sql = "SELECT * FROM list WHERE id = '{$id}'";

		return $this->query($sql);
	}

	public function datesNew($date, $name, $max_seats)
	{
		$sql = "INSERT INTO dates (date, name, max_seats) VALUES ('{$date}', '{$name}', '{$max_seats}')";
		$this->queryWithoutReturn($sql);

	}

	public function dateGetById($id){
		$sql = "SELECT * FROM dates WHERE id = '{$id}'";

		return $this->query($sql);
	}

	public function dateUpdateById($id, $name, $max_seats, $date){
		$sql = "UPDATE dates SET name = '{$name}', max_seats = '{$max_seats}', date = '{$date}' WHERE id = '{$id}'";

		return $this->queryWithoutReturn($sql);
	}

	public function dateDeleteById($id)
	{
		$sql = "UPDATE dates SET status = 'del' WHERE id = '{$id}'";

		return $this->queryWithoutReturn($sql);
	}

	public function subscribeToNews($chat_id)
	{
		$this->queryWithoutReturn("DELETE FROM subscribers WHERE chat_id = '{$chat_id}'");
		$this->queryWithoutReturn("INSERT INTO subscribers (chat_id) VALUES ('{$chat_id}')");
	}

	public function getPreviusMessage($chat_id)
	{
		$sql = "SELECT type FROM last WHERE chat_id = '{$chat_id}'";

		return $this->query($sql)['type'];
	}



}