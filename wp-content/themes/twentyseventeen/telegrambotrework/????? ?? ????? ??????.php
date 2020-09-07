<php

    private function anwserToStandartMessage_eng($message, $chat_id) {


    switch ($message){
    case "/start":
$keyboard = array(

"inline_keyboard" => array(
array(array("text" => "Foreing student's faculty", "callback_data" => "Foreing student's faculty")),
array(array("text" => "Медичний факультет", "callback_data" => "Медичний факультет")),

),

"resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
);
$this->sendKeyboard($chat_id, "Виберіть факультет", $keyboard);
break;
case "В головне меню":
$this->goToStart_eng($chat_id);
break;
case "Медичний факультет":
$this->goToStart_eng($chat_id);
break;
case "Скасувати реєстрацію":
$keyboard        = $this->getDatesOfRegistrationAsKeyboard($chat_id);
if (empty($keyboard)){
$this->sendMessage($chat_id, "Ви не зареєстровані на жодну з дат.");
$this->goToStart_eng($chat_id);
return;
}

$message_to_send = "Виберіть дату яку бажаєте скасувати:";

$this->sendKeyboard( $chat_id, $message_to_send, $keyboard);
break;
case "Скасувати сьогоднішню реєстрацію":
$keyboard        = $this->getDatesOfRegistrationAsKeyboard($chat_id);
if (empty($keyboard)){
$this->sendMessage($chat_id, "Ви не зареєстровані на жодну з дат.");
$this->goToStart_eng($chat_id);
return;
}

$message_to_send = "Виберіть дату яку бажаєте скасувати:";

$this->sendKeyboard( $chat_id, $message_to_send, $keyboard);
break;
case "Зареєструватись":
$keyboard = array(

"inline_keyboard" => array(
array(array("text" => "Відпрацювання(лікувальна справа)", "callback_data" => "Відпрацювання(лікувальна справа)")),
array(array("text" => "Відпрацювання(коледж)", "callback_data" => "Відпрацювання(коледж)")),
array(array("text" => "Міні-курс", "callback_data" => "Міні-курс")),

),

"resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
);
$this->sendKeyboard($chat_id, "Куди саме бажаєте зареєструватись?", $keyboard);
break;
case "Change faculty":
$this->sendMessage($chat_id, "Sorry, foreing students can`t register for now");
$this->goToStart_eng($chat_id);
break;
case "Foreing student's faculty":
$this->sendMessage($chat_id, "Sorry, foreing students can`t register for now");
$this->goToStart_eng($chat_id);
break;
case "Відпрацювання(лікувальна справа)":
$keyboard        = $this->getDatesAsKeyboard('l');
if (empty($keyboard)){
$this->sendMessage($chat_id, "Доступни дат немає");
$this->goToStart_eng($chat_id);
break;
}
$message_to_send = "Виберіть дату (лікувальна справа):";

$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
break;
case "Міні-курс":
$keyboard        = $this->getDatesAsKeyboard('mk');
if (empty($keyboard)){
$this->sendMessage($chat_id, "Доступни дат немає");
$this->goToStart_eng($chat_id);
break;
}
$message_to_send = "Виберіть міні курс:";

$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
break;
case "Вибрати іншу дату":
$keyboard        = $this->getDatesAsKeyboard('mk');
if (empty($keyboard)){
$this->sendMessage($chat_id, "Доступни дат немає");
$this->goToStart_eng($chat_id);
break;
}
$message_to_send = "Виберіть міні курс:";

$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
break;
case "Відпрацювання(коледж)":
$keyboard        = $this->getDatesAsKeyboard('k');
if (empty($keyboard)){
$this->sendMessage($chat_id, "Доступни дат немає");
$this->goToStart_eng($chat_id);
break;
}
$message_to_send = "Виберіть дату(коледж):";

$this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
break;
case "Сповістити мене про появу нових місць":
$this->subscribeToNews( $chat_id );


$this->sendMessage( $chat_id, "Гаразд! Я повідомлю Вас!" );
$this->goToStart_eng($chat_id);
break;
case "Сповістити мене про появу нових місць на міні-курси":
$this->subscribeToNews( $chat_id );


$this->sendMessage( $chat_id, "Гаразд! Я повідомлю Вас!" );
$this->goToStart_eng($chat_id);
break;
case "Перейти на сайт":
$this->sendMessageHTML($chat_id, "<a href='".$this->site."'>Анестезіологія</a>");
break;
case "":

break;
case "":

break;
default:
return true;
break;


}

}

private function anwserToNonStandartMessage_eng($message, $chat_id, $last_message)
{
echo $last_message;
echo "nonStandartMessage";
switch ($last_message) {
case "Виберіть дату(коледж):":

switch ($this->listCheckAvailable($chat_id, $message, 'k')){
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
$this->listRegister($chat_id, $message, $epoh_date, 'k');

$date = $this->date($epoh_date);
$this->sendMessage($chat_id, "Вибрана дата: ".$date);

$message_to_send = "Введіть Ваше прізвище:";

$this->sendMessage($chat_id, $message_to_send);

}


break;
case "Виберіть дату (лікувальна справа):":

switch ($this->listCheckAvailable($chat_id, $message, 'l')){
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
$this->listRegister($chat_id, $message, $epoh_date, 'l');

$date = $this->date($epoh_date);
$this->sendMessage($chat_id, "Вибрана дата: ".$date);

$message_to_send = "Введіть Ваше прізвище:";

$this->sendMessage($chat_id, $message_to_send);

}


break;
case "Виберіть міні курс:":

switch ($this->listCheckAvailable($chat_id, $message, 'mk')){
case "зареєстрований":
$this->sendMessage($chat_id, "Ви вже зареєстровані на дану дату!");
$message_to_send = "Я можу Вам ще чимось допомогти?";


$this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
break;
case "нема місць":
$this->sendMessage($chat_id, "На жаль, вільних місць на вибрану Вами дату немає.");
$keyboard_cancel = array(
"inline_keyboard" => array(
array(array(
"text" => "Сповістити мене про появу нових місць",
"callback_data" => "Сповістити мене про появу нових місць"
)),
array(array(
"text" => "Скасувати/В головне меню",
"callback_data" => "main_menu"
)))
);

$this->sendKeyboard($chat_id, "Але я можу повідомити Вас про появу нових місць.", $keyboard_cancel);
break;
default:
$epoh_date = $this->dateGetById($message)['date'];
$this->listRegister($chat_id, $message, $epoh_date, 'mk');

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
$keyboard = array(

"inline_keyboard" => array(
array(array("text" => "Переглянути списки заєстрованих", "callback_data" => "Переглянути списки заєстрованих")),
array(array("text" => "В головне меню", "callback_data" => "В головне меню")),

),

"resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
);


$this->sendKeyboard($chat_id, 'Зареєстровано!', $keyboard);
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
$this->goToStart_eng($chat_id);

}

}
