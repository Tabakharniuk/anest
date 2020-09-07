<?php
include 'admin/function.php';
//checkPass($_GET['pass']);

$language = "ukr";


//класси
include('vendor/autoload.php');

//База даних
include ('dataBase.php');
$db = new dataBase();
//телеграм
include('telegramBot.php');
$telegramApi = new TelegramBot();

include "admin/head.php";

if(empty($_GET['faculty'])){
	die();
}

switch ($_GET['faculty']){
	case "l":
		$f = "l";
		$faculty = "Відпрацювання(лікувальники)";
		break;
    case "cherg":
        $f = "cherg";
        $faculty = "Чергування";
        break;
    case "gurt":
        $f = "gurt";
        $faculty = "Гурток";
        break;
	case 'mk':
		$f = "mk";
		$faculty = "Міні-курси";
		break;
	case 'k':
		$f = 'k';
		$faculty = "Відпрацювання(коледж)";
		break;
	case 'f':
		$f = "f";
		$faculty = "Відпрацювання(іноземці)";
		break;
	default:
		$f = 'mk';
		$faculty = "Міні-курси";
		break;
}

?>

	<div class="container">
		<div  class="col-md-9">
		<center><h1 style="color: #0A246A"><?echo $faculty?></h1></center>
		</div>
	</div>
<?

//Редагування дати
if ($_GET['edit'])
{include 'admin/edit.php';
die;
}

checkTask($db, $telegramApi);

printTables($db,$f);

addForm();

include 'admin/fotter.php';
