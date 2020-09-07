<?php $dates = $db->dateGetById($_GET['edit']);

?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<div class="container">
	<div class="col-sm-6">
		<center><h3>Редагування запису</h3></center>

		<form action="admin.php?pass=<?echo $_GET['pass']?>" method="post">
			Опис: <input class="form-control" type="text" name="name" value="<?echo $dates['name']?>"><br>
			Кількість місць: <input class="form-control" type="text" name="max_seats" value="<?echo $dates['max_seats']?>"><br>


					<div class="form-group">
						<div class='input-group date' id='datetimepicker1'>
							<input type='text' class="form-control" name="date" required  value="<?echo date('d/m/Y H:i', $dates['date']);?>">
							<span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
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
			<input style="display: none" type='text' class="form-control"  name="editDate" value="1">
			<input style="display: none" type='text' class="form-control"  name="editId" value="<?echo $dates['id']?>">


			<input type="submit" class="btn btn-primary" value=" Зберегти ">  <a href="admin.php?pass=<?echo $_GET['pass']?>" class="btn btn-warning">Скасувати</a>
		</form>
		<button class="delete btn btn-danger">Видалити</button>

		<script type="text/javascript">
            $('button.delete').click(function () {
                $.confirm({
                    title: 'Видалити дату?',
                    content: 'Ви дійсно бажаєте видалити дану дату?',
                    buttons: {
                        Видалити: function () {
                            $.alert('Confirmed!');
                            location.href = "admin.php?pass=<?echo $_GET['pass']?>&deleteDate=<?echo $_GET['edit']?>";
                        },
                        Скасувати: function () {
                            $.alert('Скасовано!');
                        }

                    }
                });
            });
		</script>
		<br>
		<br>

	</div>
</div>

<?



