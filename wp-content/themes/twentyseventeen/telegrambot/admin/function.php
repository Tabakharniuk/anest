<?php
function checkPass($pass)
{
	if ($pass != '0987654321'){
		if (empty($pass)){
			include 'head.php';
			?>

			<br>
			<br>
			<br>
			<div class="container">
				<form action="admin.php" method="get">
					<div class="col-4">
						<div class="form-group">
							<label for="pwd">Пароль:</label>
							<input  class="form-control" name="pass">
						</div>

						<button type="submit" class="btn btn-default"><Увійти></Увійти></button>
					</div>
				</form>
			</div>


			</body>
			</html>

			<?
			die;
		}

		header('admin.php?pass=');
		die;

	}
}

function checkTask($db, $telegramApi)
{
	if ($_GET['del'])
	{
		echo "Запис видалено!<br>";
		echo "<a href='admin.php?pass=0987654321&recover={$_GET['del']}'>Відновити</a>";
		$telegramApi->deleteStudentFromList($_GET['del']);

	}

	if ($_GET['recover'])
	{
		echo "Відновлено!<br>";
		$telegramApi->recoverStudentFromList($_GET['recover']);

	}

	checkNew($db);

	editDate($db);

	deleteDate($db, $telegramApi);



}

function checkNew($db)
{
	if (!$_POST['new'])
	{
	    return;
	}

    if(empty($_POST['descript']) or empty($_POST['time']) or empty($_POST['max_seats']))
    {
        echo '<div class="alert alert-danger">Заповнені не всі поля!</div>';
        return;
    }

    //Конвертування в таймштамп
    $class_date = DateTime::createFromFormat('d/m/Y H:i', $_POST['time']);
    $date = $class_date->getTimestamp();


    $max_seats = intval($_POST['max_seats']);

    if ($max_seats <= 0)
    {
	    echo '<div class="alert alert-danger">Поле "Кількість місць" заповнено некоректно</div>';
	    return;
    }

    $db->datesNew($date, $_POST['descript'], $max_seats);
	echo '<div class="alert alert-success">Дату успішно добавлено!</div>';


}

function editDate($db)
{
    if(!$_POST['editDate'])
    {
        return;
    }

    $id = $_POST['editId'];
    $name = $_POST['name'];
    $max_seats = $_POST['max_seats'];

	$class_date = DateTime::createFromFormat('d/m/Y H:i', $_POST['date']);
	$date = $class_date->getTimestamp();

    if (empty($id) or empty($name) or empty($max_seats) or empty($date))
    {
	    echo '<div class="alert alert-danger">Заповнено не всі поля! <br> Зміни не збережено!</div>';
        return;
    }

    $db->dateUpdateById($id, $name, $max_seats, $date);
	echo '<div class="alert alert-success">Зміни збережено!</div>';
	return;
}

function deleteDate($db, $telegramAPI)
{
    if(empty($_GET['deleteDate']))
    {
        return;
    }

    $db-> dateDeleteById($_GET['deleteDate']);
	echo '<div class="alert alert-success">Дату Видалено!</div>';

}


function printTables($db){

	foreach ($db->getDates() as $date)
	{
		?>
		<div class="container">
		<div  class="col-md-9">
			<?php
		echo "<center><h2>". $date['name']."</h2></center>";
		echo "<center><h4 style='color: #024874'>". date("d F H:i", $date['date'])."</h4><a href='admin.php?pass={$_GET['pass']}&edit={$date['id']}'>Редагувати</a> ";

			$count = 1;

		?>
		<table class="table table-striped">
			<tr>
				<th>#<th>Прізвище<th>Ім'я<th>Курс<th>Група<td></td>
			</tr>

			<?php
			foreach ($db->getListByDate($date['id']) as $student)
			{
				echo "<tr>";
				echo "<th>$count.</th><td>{$student['ln']}<td>{$student['fn']}<td>{$student['course']}<td>{$student['sgroup']}<td><a href='admin.php?pass=0987654321&del={$student['id']}'>Видалити</a></td>";
				echo "</tr>";
				$count++;
			}

			while ($count <= $date['max_seats'])
			{
				echo "<tr>";
				echo "<th>$count.</th><td><td><td><td><td></td>";
				echo "</tr>";
				$count++;
			}
			?>
		</table>
		</div>
		</div>
		<br><br>
		<?
	}



}

function addForm()
{
?>

<div class="container">
	<div class="form-group" class="col-md-6">
		<div  class="col-md-6">

			<button type="button" class="btn btn-secondary, btn2" onclick="show()">Добавити нову дату</button>
			<script>
                function show() {
                    document.getElementById('contactform').setAttribute('class', '') }
			</script>
		</div>
	</div>
</div>


<form class="form" id="contactform" name="contact" method="post" action="admin.php?pass=0987654321" enctype="multipart/form-data">


	<div class="container">

		<div class="col-md-6">

			<div class="form-group">

				<label for="exampleTextarea">Опис:</label>
				<textarea class="form-control" id="exampleTextarea" rows="1" name="descript" required  value="<?echo $_POST['descript']?>"></textarea>
                <br>
                <label for="exampleTextarea">Кількість місць</label>
                <textarea class="form-control" id="exampleTextarea" rows="1" type="number" required name="max_seats" property="" value="<?echo $_POST['max_seats']?>"></textarea>
                <br>
				<label for="date1">Виберіть дату:</label>
			</div>
		</div>

	</div>
	<!--                початок календаря-->
	<div class="container">

		<div class='col-sm-6'>
			<div class="form-group">
				<div class='input-group date' id='datetimepicker1'>
					<input type='text' class="form-control" name="time" required  value="<?echo $_POST['time']?>">
					<span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
				</div>
			</div>
		</div>
		<script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    locale: 'uk',
                    sideBySide: true,
                    format : 'DD/MM/YYYY HH:mm'
                });
            });
		</script>

	</div>
	<!--                кінець календаря-->
	<input style="display: none" type='text' class="form-control"  name="new" value="1">





	<div class="container">
		<div class="col-md-6">

			<button type="submit" class="btn btn-primary">Підтвердити</button>
		</div>
	</div>



</form>
<?php
}


?>