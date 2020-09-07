<?php
use GuzzleHttp\Client;

class TelegramBot extends dataBase {
    protected $token = "475665632:AAFvMHWmFH9Q3WVOOVfXVgBPrZwrsUDCx_A";
    protected $updateId;
    public $site = "https://anest.e-decanat-ifnmu.site/";
    public $start_keyboard = array(

        "keyboard" => array(
            array(

                array(
                    "text" => "Зареєструватись"

                )

            ),

            array(

                array(
                    "text" => "Сповістити мене про появу нових місць на міні-курси"

                )

            ),

            array(

                array(
                    "text" => "Скасувати реєстрацію"

                )

            ),


            array(

                array(
                    "text" => "Перейти на сайт"

                )

            ),

            array(
                array(
                    "text" => "Change faculty",
                ))


        ),
        "one_time_keyboard" => true, // можно заменить на FALSE,клавиатура скроется после нажатия кнопки автоматически при True
        "resize_keyboard" => false // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
    );
    public $start_keyboard_eng = array(

        "keyboard" => array(
            array(

                array(
                    "text" => "Register"

                )

            ),


            array(

                array(
                    "text" => "Cancel registration"

                )

            ),


            array(

                array(
                    "text" => "Go to the site"

                )

            ),

            array(
                array(
                    "text" => "Вибрати інший факультет",
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

    //Клавіатури використовуються для силочки під повідомленням в головне меню
    public $keyboard_to_main_menu = array(

    "inline_keyboard" => array(
    array(array("text" => "В головне меню", "callback_data" => "В головне меню")),

    ),

    "resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
    );
    public $keyboard_to_main_menu_eng = array(

        "inline_keyboard" => array(
            array(array("text" => "To the main menu", "callback_data" => "To the main menu")),

        ),

        "resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
    );
    public $keyboard_cancel_eng = array(
        "inline_keyboard" => array(array(array(
            "text" => "Cancel/To main menu",
            "callback_data" => "main_menu_eng"
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

    public function choose_faculty($chat_id){
        $keyboard = array(

            "inline_keyboard" => array(
                array(array("text" => "Медичний факультет", "callback_data" => "Медичний факультет")),
                array(array("text" => "Foreing student*s faculty", "callback_data" => "Foreing student*s faculty")),

            ),

            "resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
        );
        $this->sendKeyboard($chat_id, "Виберіть факультет/Choose faculty", $keyboard);
    }

    public function goToStart($chat_id){
        $this->sendKeyboard($chat_id, "Ви в головному меню", $this->start_keyboard);
    }

    public function goToStart_eng($chat_id){
        $this->sendKeyboard($chat_id, "You are in the main menu.", $this->start_keyboard_eng);
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

        $GLOBALS['language'] = "ukr";
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
                $this->goToStart($chat_id);
                break;
            case "Медичний факультет":
                $this->goToStart($chat_id);
                break;
            case "Скасувати реєстрацію":
                $keyboard        = $this->getDatesOfRegistrationAsKeyboard($chat_id);
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Ви не зареєстровані на жодну з дат.");
                    $this->goToStart($chat_id);
                    return;
                }

                $message_to_send = "Виберіть дату яку бажаєте скасувати:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard);
                break;
            case "Скасувати сьогоднішню реєстрацію":
                $keyboard        = $this->getDatesOfRegistrationAsKeyboard($chat_id);
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Ви не зареєстровані на жодну з дат.");
                    $this->goToStart($chat_id);
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
                        array(array("text" => "Чергування", "callback_data" => "Чергування")),
                        array(array("text" => "Гурток", "callback_data" => "Гурток")),

                    ),

                    "resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
                );
                $this->sendKeyboard($chat_id, "Куди саме бажаєте зареєструватись?", $keyboard);
                break;
            case "Change faculty":
                $this->choose_faculty($chat_id);
                break;
            case "Foreing student's faculty":
                $this->sendMessage($chat_id, "Sorry, foreing students can`t register for now");
                $this->goToStart($chat_id);
                break;
            case "Відпрацювання(лікувальна справа)":
                $keyboard        = $this->getDatesAsKeyboard('l');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Доступни дат немає");
                    $this->goToStart($chat_id);
                    break;
                }
                $message_to_send = "Виберіть дату (лікувальна справа):";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "Міні-курс":
                $keyboard        = $this->getDatesAsKeyboard('mk');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Доступни дат немає");
                    $this->goToStart($chat_id);
                    break;
                }
                $message_to_send = "Виберіть міні курс:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "Чергування":
                $keyboard        = $this->getDatesAsKeyboard('cherg');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Доступни дат немає");
                    $this->goToStart($chat_id);
                    break;
                }
                $message_to_send = "Виберіть дату чергування:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "Гурток":
                $keyboard        = $this->getDatesAsKeyboard('gurt');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Доступни дат немає");
                    $this->goToStart($chat_id);
                    break;
                }
                $message_to_send = "Виберіть дату проведення гуртка:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "Вибрати іншу дату":
                $keyboard        = $this->getDatesAsKeyboard('mk');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Доступни дат немає");
                    $this->goToStart($chat_id);
                    break;
                }
                $message_to_send = "Виберіть міні курс:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "Відпрацювання(коледж)":
                $keyboard        = $this->getDatesAsKeyboard('k');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Доступни дат немає");
                    $this->goToStart($chat_id);
                    break;
                }
                $message_to_send = "Виберіть дату(коледж):";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "Сповістити мене про появу нових місць":
                $this->subscribeToNews( $chat_id );


                $this->sendMessage( $chat_id, "Гаразд! Я повідомлю Вас!" );
                $this->goToStart($chat_id);
                break;
            case "Сповістити мене про появу нових місць на міні-курси":
                $this->subscribeToNews( $chat_id );


                $this->sendMessage( $chat_id, "Гаразд! Я повідомлю Вас!" );
                $this->goToStart($chat_id);
                break;
            case "Перейти на сайт":
                $this->sendMessageHTML($chat_id, "<a href='".$this->site."'>Анестезіологія</a>");
                break;
            case "Переглянути списки заєстрованих":
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

    private function anwserToNonStandartMessage($message, $chat_id, $last_message)
    {
        $GLOBALS['language'] = "ukr";

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
            case "Виберіть дату чергування:":

                switch ($this->listCheckAvailable($chat_id, $message, 'cherg')){
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
                        $this->listRegister($chat_id, $message, $epoh_date, 'cherg');

                        $date = $this->date($epoh_date);
                        $this->sendMessage($chat_id, "Вибрана дата: ".$date);

                        $message_to_send = "Введіть Ваше прізвище:";

                        $this->sendMessage($chat_id, $message_to_send);

                }


                break;
            case "Виберіть дату проведення гуртка:":

                switch ($this->listCheckAvailable($chat_id, $message, 'gurt')){
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
                        $this->listRegister($chat_id, $message, $epoh_date, 'gurt');

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
                $this->goToStart($chat_id);

        }

    }

    private function anwserToStandartMessage_eng($message, $chat_id) {

        $GLOBALS['language'] = "eng";
        switch ($message){
            case "To the main menu":
                $this->goToStart_eng($chat_id);
                break;
            case "Foreing student's faculty":
                $this->goToStart_eng($chat_id);
                break;
            case "Cancel registration":
                $keyboard        = $this->getDatesOfRegistrationAsKeyboard_eng($chat_id);
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "Ви не зареєстровані на жодну з дат.");
                    $this->goToStart_eng($chat_id);
                    return;
                }

                $message_to_send = "Select the date you want to cancel:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard);
                break;
            case "Вибрати інший факультет":
                $this->choose_faculty($chat_id);
                break;
            case "Foreing student*s faculty":
                $this->goToStart_eng($chat_id);
                break;
            case "Register":
                $keyboard        = $this->getDatesAsKeyboard_eng('f');
                if (empty($keyboard)){
                    $this->sendMessage($chat_id, "No free places in the list for chosen date.");
                    $this->goToStart_eng($chat_id);
                    break;
                }
                $message_to_send = "Select a date:";

                $this->sendKeyboard( $chat_id, $message_to_send, $keyboard );
                break;
            case "See the list of registered":
                $this->sendMessageHTML($chat_id, "<a href='".$this->site."'>Anesteziology</a>");
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
        $GLOBALS['language'] = "eng";

        switch ($last_message) {
            case "Select a date:":

                switch ($this->listCheckAvailable($chat_id, $message, 'f')){
                    case "зареєстрований":
                        $this->sendMessage($chat_id, "You are already registered for this date!");
                        $message_to_send = "Can I help with something?";


                        $this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard_eng);
                        break;
                    case "нема місць":
                        $this->sendMessage($chat_id, "Unfortunately, there are no free place for your chosen date.");
                        $message_to_send = "Can I help with something?";


                        $this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard_eng);
                        break;
                    default:
                        $epoh_date = $this->dateGetById($message)['date'];
                        $this->listRegister($chat_id, $message, $epoh_date, 'l');

                        $date = $this->date($epoh_date);
                        $this->sendMessage($chat_id, "Chosen date: ".$date);

                        $message_to_send = "Enter your Last name:";

                        $this->sendMessage($chat_id, $message_to_send);

                }


                break;
            case "Enter your Last name:":

                $this->listUpdate($chat_id, "ln", $message);
                $message_to_send = "Enter your First name:";



                $this->sendMessage($chat_id, $message_to_send);
                break;
            case "Enter your First name:":
                $this->listUpdate($chat_id, "fn", $message);
                $message_to_send = "Enter your course:";

                $this->sendMessage($chat_id, $message_to_send);
                break;
            case "Enter your course:":
                $this->listUpdate($chat_id, "course", $message);
                $message_to_send = "Enter your group:";

                $this->sendMessage($chat_id, $message_to_send);
                break;
            case "Enter your group:":
                $this->listUpdate($chat_id, "sgroup", $message);
                $this->listSuccess($chat_id);
                $keyboard = array(

                    "inline_keyboard" => array(
                        array(array("text" => "See the list of registered", "callback_data" => "See the list of registered")),
                        array(array("text" => "To the main menu", "callback_data" => "To the main menu")),

                    ),

                    "resize_keyboard" => true // можно заменить на FALSE, клавиатура будет использовать компактный размер автоматически при True
                );


                $this->sendKeyboard($chat_id, 'Registered!', $keyboard);
                break;
            case "Select the date you want to cancel:":
                $this->listDelete($chat_id, $message);
                $date = $this->date($this->dateGetById($message)['date']);
                $this->sendMessage($chat_id, "Selected date: ".$date);
                $this->sendMessage($chat_id, "Registration canceled!");
                $message_to_send = "Can I help with something?";


                $this->sendKeyboard($chat_id, $message_to_send, $this->start_keyboard);
                break;
            default:
                $this->sendMessage($chat_id, "I do not quite understand you...");
                $this->goToStart_eng($chat_id);

        }

    }

    public function answer($chat_id, $message)
    {
        //провірка чи це є простим питанням, відповідь на нього і закінчення функції.
        if(empty($this->anwserToStandartMessage($message, $chat_id)))
        {
            return;
        }
        if(empty($this->anwserToStandartMessage_eng($message, $chat_id)))
        {
            return;
        }

        //змінна з останнім повідомленням яке надсилалось користувачу
        $last_message = $this->getPreviusMessage($chat_id)['type'];

        //провірка мови і використання відповідної функції, з переданням до неї попереднього ввідправлееного повідомлення
        // за вмовчання виконується українська
        switch ($this->getPreviusMessage($chat_id)['language']){
            case 'ukr':
                $this->anwserToNonStandartMessage($message, $chat_id, $last_message);
                break;
            case 'eng':
                $this->anwserToNonStandartMessage_eng($message, $chat_id, $last_message);
                break;
            default:
                $this->anwserToNonStandartMessage($message, $chat_id, $last_message);
        }



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

    public function send_notification_for_today(){
        foreach ($this->get_list_to_sendNotification_on_the_day_of_register() as $value){
            switch ($value['faculty']){
                case "f":
                    $GLOBALS['language'] = 'eng';
                    $name_of_course_or_revork = $this->dateGetById($value['date_id']);
                    $this->sendMessage($value['chat_id'], "Hello, ".$value['fn']." ".$value['ln']."! You are registered to ".$name_of_course_or_revork.", what will happen today about ".date("G:i", $value['date']).".");
                    $this->sendMessage($value['chat_id'], "If your plans have changed and you can not come today, then it is possible to cancel the registration.");
                    $this->sendMessage($value['chat_id'], "To do this, in the main menu, select \"Cancel registration\", then select the date you want.");
                    $this->sendKeyboard($value['chat_id'], "Pay attention! Students who will be registered and will not come to work - for them registration will no longer be available", $this->keyboard_to_main_menu_eng);
                    break;
                default:
                    $GLOBALS['language'] = 'ukr';
                    $name_of_course_or_revork = $this->dateGetById($value['date_id']);
                    $this->sendMessage($value['chat_id'], "Доброго дня, ".$value['fn']." ".$value['ln']."! Ви є зареєстровані на ".$name_of_course_or_revork.", що відбудеться сьогодні о ".date("G:i", $value['date']).".");
                    $this->sendMessage($value['chat_id'], "Якщо Ваші плани змінились і Ви не зможете прийти сьогодні, тоді є можливіть скасувати реєстрацію.");
                    $this->sendMessage($value['chat_id'], "Для цього у головному меню виберіть пункт \"Cкасувати реєстрацію\", потім виберіть потрібну вам дату.");
                    $this->sendKeyboard($value['chat_id'], "Зверніть увагу! Студенти які будуть зареєстровані і не прийдуть на відпрацювання - для них реєстрація більше доступна не буде", $this->keyboard_to_main_menu);
                    break;

            }
        }
    }
}