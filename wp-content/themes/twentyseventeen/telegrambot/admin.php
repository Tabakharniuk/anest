<?php
include 'admin/function.php';
checkPass($_GET['pass']);



//класси
include('vendor/autoload.php');

//База даних
include ('dataBase.php');
$db = new dataBase();
//телеграм
include('telegramBot.php');
$telegramApi = new TelegramBot();

include "admin/head.php";

//Редагування дати
if ($_GET['edit'])
{include 'admin/edit.php';
die;
}

checkTask($db, $telegramApi);

printTables($db);

addForm();

include 'admin/fotter.php';
