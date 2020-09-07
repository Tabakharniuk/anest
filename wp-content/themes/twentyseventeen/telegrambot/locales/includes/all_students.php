<?php


function is_editor(){
	if(current_user_can(delete_pages)){
		return true;

	}else{
		return false;
	}


}


function url_for_editors_main_page()
{
	?>
	<div class="container">
	<div class="col-md-8">
		<a href="index.php?mod=1&course=5"><H3> Перейти в панель модератора</H3></a>
	</div>
	</div>
	<?php
}


function select_from_DB_with_condition($table, $column, $value_of_column){
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM students WHERE start_year = 2013";
	return mysqli_query($connection, $strSQL) or die(mysqli_error($connection));
}
function get_user_inf_by_email($email){
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM students WHERE email = '{$email}'";
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

	return mysqli_fetch_array($rrr);

}

function print_name_course_group($email){
	$user = get_user_inf_by_email($email);
	echo $user['last_name']." ".$user['first_name']." ".(2018 - $user['start_year'])." курс ".$user['stud_group']. " група";
}

function get_point_for_diplom($name, $custom_point, $status){
    if ($custom_point){
        return $custom_point;
    }else{


	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL_diplom_value = "SELECT * FROM diplom_value_measure2";
	$diplom_value_sql = mysqli_query($connection, $strSQL_diplom_value) or die(mysqli_error($connection));

	$values_array = false;
	$arrayy = array();

	while($row = mysqli_fetch_array($diplom_value_sql)) {

		if (str_replace(array(' ', '.', ':'), array('', '', ''), $row['diplom_name']) == str_replace(array(' ', '.', ':'), array('', '', ''), $name)) {
			//Значення для загального рейтингу
		    if( $row['value_general'] == 0 ){
			    $value_general = "-";
				$type_general = "-";
            } else {
				$value_general = $row['value_general'];
				$type_general = $row['type_general'];
            }

            //Значення для стипендії
			if( $row['value_stependy'] == 0 ){
				$value_stependy = "-";
				$type_stependy = "-";
			} else {
			    if ($row['value_stependy'] == 888){
			        if ($status == 1){
			        $GLOBALS["count_888"] ++;
				    if($GLOBALS["count_888"] == 1){
					    $value_stependy = 160;
				    }elseif($GLOBALS["count_888"] == 2){
					    $value_stependy = 20;
				    }elseif($GLOBALS["count_888"] >= 3){
					    $value_stependy = 20;
				    }

			        }  elseif ($status == 0 or $status == 3){
				        $value_stependy = "Бал з'явиться після розглядання документу";
                    } else {
				        $value_stependy = 0;
                    }
				    $type_stependy = $row['type_stependy'];
                }elseif($row['value_stependy'] == 999){
				    $GLOBALS["count_999"] ++;
				    if ($status == 1){
				    if($GLOBALS["count_999"] == 1){
					    $value_stependy = 160;
                    }elseif($GLOBALS["count_999"] == 2){
					    $value_stependy = 20;
                    }elseif($GLOBALS["count_999"] >= 3){
					    $value_stependy = 20;
                    }
                    }  elseif ($status == 0 or $status == 3){
                        $value_stependy = "Бал з'явиться після розглядання документу";
                    } else {
                        $value_stependy = 0;
                    }
				    $type_stependy = $row['type_stependy'];
                }else{
				$value_stependy = $row['value_stependy'];
				$type_stependy = $row['type_stependy'];
			    }
			}


		    return array ($value_general, $type_general, $value_stependy, $type_stependy);

		}
        array_push($arrayy, $row);

	}
	return array("Бал з'явиться після розглядання документу", "Тип з'явиться після розглядання документу", "Бал з'явиться після розглядання документу", "Тип з'явиться після розглядання документу");
    }
}

function get_group_from_course($course){

    $connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT DISTINCT stud_group FROM students WHERE start_year = $course ORDER BY ID";
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

	$groups = array();
	while($row = mysqli_fetch_array($rrr)) {
		array_push($groups, $row['stud_group']);

	}
    return $groups;
}

function get_emails_with_new_status($course){
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM diplom";
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

    $emails = array();
	while($row = mysqli_fetch_array($rrr)) {
        if ($row['status'] == '' or $row['status'] == 3 or $row['status'] == 0 or $row['status'] == '0'){
        array_push($emails, strtolower($row['email']));
        }
		$GLOBALS["count_888"] = 0;
		$GLOBALS["count_999"] = 0;
        if(get_point_for_diplom($row["type_of_diplom"], false, $row['status'])[0] == "Бал з'явиться після розглядання документу"){
	        array_push($emails, strtolower($row['email']));
        }
	}



	return $emails;
}

function get_student_with_new($emails, $course){
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM students WHERE start_year = $course";
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL_diplom_value = "SELECT * FROM diplom_value_measure";
	$diplom_value_sql = mysqli_query($connection, $strSQL_diplom_value) or die(mysqli_error($connection));

	$values_array = false;
	while($row = mysqli_fetch_array($diplom_value_sql)) {
		$values_array[str_replace(" ", "", trim(strtolower($row['diplom_name'])))] = $row['value'];

	}


	?>
    <div class="container">
        <div class="col-md-10">
            <h2> Студенти що потребують Вашої уваги:</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Прізвище І'мя</th>
                    <th>Група</th>
                    <th>Загальна к-ть документів</th>
                    <th>К-ть нових документів</th>
                    <th>К-ть непідтверджених документів</th>
                    <th>СРС</th>
                    <th>НДР</th>
                    <th>Загальна к-ть балів</th>
                </tr>
                </thead>



				<?php
				$number_stroke = 1;

				while($row = mysqli_fetch_array($rrr)) {

                    If (in_array(strtolower($row['email']), $emails) ){


					$line_data = get_data_for_email_in_all_student_page($row['email'], $values_array);
					$name = "<a href='index.php?mod=1&email=".$row['email']."'>". $row['last_name'] . " " . $row['first_name'] ."</a>";
					echo "<tr><th scope='row'>". $number_stroke .".</th><td>$name<td>" . $row['stud_group'] ."<td>" . $line_data['all_diplom']. " <td>" . $line_data['not_valid']. " <td>" . $line_data['new']. " <td><td></td><td>" . $line_data['sum_of_point']. "</tr>";

					$number_stroke = $number_stroke + 1;
                    }
				}

				?>
            </table>
        </div>
    </div>
	<?php

}

function print_all_student_from_course_and_course($course, $group)
{
    if ($group == false or $group == '')
    {
        return;
    }
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM students WHERE start_year = $course and stud_group = ".$group;

	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL_diplom_value = "SELECT * FROM diplom_value_measure";
	$diplom_value_sql = mysqli_query($connection, $strSQL_diplom_value) or die(mysqli_error($connection));

	$values_array = false;
	while($row = mysqli_fetch_array($diplom_value_sql)) {
		$values_array[$row['diplom_name']] = $row['value'];

	}


	?>
		<div class="container">
			<div class="col-md-10">
			<table class="table">
			    <thead>
			    <tr>
			        <th>#</th>
			        <th>Прізвище І'мя</th>
			        <th>К-ть документів</th>
			        <th>СРС</th>
			        <th>НДР</th>
                    <th>ВД</th>
                    <th>НД</th>
                    <th>ГД</th>
                    <th>СМД</th>
			    </tr>
			    </thead>



	<?php
	$number_stroke = 1;

	while($row = mysqli_fetch_array($rrr)) {

		$line_data = get_data_for_email_in_all_student_page($row['email'], $values_array);
		$name = "<a href='index.php?mod=1&email=".$row['email']."'>". $row['last_name'] . " " . $row['first_name'] ."</a>";
		echo "<tr><th scope='row'>". $number_stroke .".</th><td>$name<td>" . $line_data['all_diplom']. " <td>" . $line_data['SRS_sum']. " <td>" . $line_data['NDR_sum']. " <td>" . $line_data['VD_sum']. " <td>" . $line_data['ND_sum']. " <td>" . $line_data['GD_sum']. "<td>" . $line_data['SMD_sum']. "</tr>";

		$number_stroke = $number_stroke + 1;

	}

	?>
					</table>
				</div>
			</div>
	<?php

}

function print_all_student_from_course_and_course_for_result($course, $group)
{
	if ($group == false or $group == '')
	{
		return;
	}
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM students WHERE start_year = $course and stud_group = ".$group;
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL_diplom_value = "SELECT * FROM diplom_value_measure";
	$diplom_value_sql = mysqli_query($connection, $strSQL_diplom_value) or die(mysqli_error($connection));



	$values_array = false;
	while($row = mysqli_fetch_array($diplom_value_sql)) {
		$values_array[$row['diplom_name']] = $row['value'];

	}

				$number_stroke = 1;

				while($row = mysqli_fetch_array($rrr)) {

					$line_data = get_data_for_email_in_all_student_page($row['email'], $values_array);
					if ($line_data['all_diplom'] !=0 or $line_data['SRS_sum'] !=0 or $line_data['NDR_sum'] !=0 or $line_data['VD_sum'] !=0 or $line_data['ND_sum'] !=0 or  $line_data['GD_sum'] !=0 or $line_data['SMD_sum']){
					$name = "<a href='index.php?mod=1&email=".$row['email']."'>". $row['last_name'] . " " . $row['first_name'] ."</a>";
					echo "<tr><td>$name<td>" . $row['stud_group'] ."<td>" . $line_data['all_diplom']. "<td>" . $line_data['not_valid']. "<td>" . $line_data['new']. " <td>" . $line_data['SRS_sum']. " <td>" . $line_data['NDR_sum']. " <td>" . $line_data['VD_sum']. " <td>" . $line_data['ND_sum']. " <td>" . $line_data['GD_sum']. "<td>" . $line_data['SMD_sum']. "</tr>";
					}
					$number_stroke = $number_stroke + 1;

				}



}


function get_data_for_email_in_all_student_page($email, $measure){
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM diplom WHERE email = '{$email}'";
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));
	$diplom_curent_student['all_diplom'] = 0;
	$diplom_curent_student['not_valid'] = 0;
	$diplom_curent_student['new'] = 0;
	$diplom_curent_student['sum_of_point'] = 0;

	$SRS_sum = 0;
	$NDR_sum = 0;
	$ND_sum = 0;
	$VD_sum = 0;
	$GD_sum = 0;
	$SMD_sum = 0;
	$GLOBALS["count_888"] = 0;
	$GLOBALS["count_999"] = 0;
	while($row = mysqli_fetch_array($rrr)) {



		$diplom_curent_student['all_diplom'] = $diplom_curent_student['all_diplom'] + 1;
		if( intval($row['status']) == 0 or intval($row['status']) == 3 ){
			$diplom_curent_student['not_valid'] = $diplom_curent_student['not_valid'] + 1;
		}
		if( intval($row['status']) == 2 ){
			$diplom_curent_student['new'] = $diplom_curent_student['new'] + 1;
		}
;

        if( $row['status'] == 1 ){
            if ($row['custom_point']){
	            $diplom_curent_student['sum_of_point'] = $diplom_curent_student['sum_of_point'] + $row['custom_point'];
	        }else{
	            $points =   get_point_for_diplom($row['type_of_diplom'], $row['custom_point'], $row['status']);
	            $value_general      = $points[0];
	            $type_general       = $points[1];
	            $value_stependy     = $points[2];
	            $type_stependy      = $points[3];


	            if($type_general == "СРС"){
		            $SRS_sum += $value_general;
	            }
	            if($type_general == "НДР"){
		            $NDR_sum += $value_general;
	            }
	            if($type_stependy == "ВД"){
		            $VD_sum += $value_stependy;
		            if($VD_sum > 200){
			            $VD_sum = 200;
		            }
	            }
	            if($type_stependy == "НД"){
		            $ND_sum += $value_stependy;
		            if($ND_sum > 200){
			            $ND_sum = 200;
		            }
	            }
	            if($type_stependy == "ГД"){
		            $GD_sum += $value_stependy;
		            if($GD_sum > 200){
			            $GD_sum = 200;
		            }
	            }
	            if($type_stependy == "СМД"){
		            $SMD_sum += $value_stependy;
		            if($SMD_sum > 200){
			            $SMD_sum = 200;
		            }
	            }

            }
			}


	}
	return array(
		"SMD_sum"           => $SMD_sum,
		"SRS_sum"           => $SRS_sum,
		"NDR_sum"           => $NDR_sum,
		"VD_sum"            => $VD_sum,
		"ND_sum"            => $ND_sum,
		"GD_sum"            => $GD_sum,
		"all_diplom" =>$diplom_curent_student['all_diplom'],
	    "not_valid"  =>$diplom_curent_student['not_valid'],
	    "new"   => $diplom_curent_student['new']
    );
}


function is_moderator_page(){
	if ( intval($_GET['mod']) == 1){
		return true;

	}else{
		false;
	}

}

function is_result_page(){
	if ( intval($_GET['result']) == 1){
		return true;

	}else{
		false;
	}

}


function current_course(){
	if ( intval($_GET['course']) >  0){
		return 2018 - intval($_GET['course']);
	}else{
		return 2018 - 5;
	}
}

function get_sutudent_reword_by_email($email){


?>

<div class="container">
<div class="col-md-10">
<h3></h3>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>Документ</th>
        <th>Внесено</th>
        <th>Статус</th>
        <th colspan="2">Загальний рейтинг<br>(бал/тип)</th>
        <th colspan="2">Степендіальний рейтинг<br>(бал/тип)</th>
        <th>Фото</th>

    </tr>

    </thead>

	<?php


    $connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
    $strSQL = "SELECT * FROM diplom WHERE email = '{$email}'";
    $rs = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

    $rewiew = array();
    $number_stroke = 1;
	$SRS_sum = 0;
	$NDR_sum = 0;
	$ND_sum = 0;
	$VD_sum = 0;
	$GD_sum = 0;
	$SMD_sum = 0;
	$GLOBALS["count_888"] = 0;
	$GLOBALS["count_999"] = 0;
    while($row = mysqli_fetch_array($rs)) {
	    $time_of_add = date('d.m.y', intval($row['time_of_add']));
	    $points = get_point_for_diplom($row['type_of_diplom'], $row['custom_point'], $row['status']);
	    $value_general      = $points[0];
	    $type_general       = $points[1];
	    $value_stependy     = $points[2];
	    $type_stependy      = $points[3];

	    if ($row['status'] == 1) {

		    if ( $type_general == "СРС" ) {
			    $SRS_sum += $value_general;
		    }
		    if ( $type_general == "НДР" ) {
			    $NDR_sum += $value_general;
		    }
		    if ( $type_stependy == "ВД" ) {
			    $VD_sum += $value_stependy;
			    if ( $VD_sum > 200 ) {
				    $VD_sum = 200;
			    }
		    }
		    if ( $type_stependy == "НД" ) {
			    $ND_sum += $value_stependy;
			    if ( $ND_sum > 200 ) {
				    $ND_sum = 200;
			    }
		    }
		    if ( $type_stependy == "ГД" ) {
			    $GD_sum += $value_stependy;
			    if ( $GD_sum > 200 ) {
				    $GD_sum = 200;
			    }
		    }
		    if ( $type_stependy == "СМД" ) {
			    $SMD_sum += $value_stependy;
			    if ( $SMD_sum > 200 ) {
				    $SMD_sum = 200;
			    }
		    }
	    }
	    array_push($rewiew, array(
	            "img"            => $row['img'],
	            "discript"       => $row['discript'],
	            "type_of_diplom" => $row['type_of_diplom'],
	            "status"         => $row['status'],
                "time"           => $time_of_add,

        ));
        $comment = $row['comment'];

	    $img_code = "<button type=\"button\" class=\"btn btn-default \" data-toggle=\"modal\" data-target=\"#myModa$number_stroke\">Фото</button>";
	    $comment_code = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#comment_box" onclick=\'commentFieldd("'.$comment.'")\'>Коментар</button>';


	    if(intval($row['status']) == 1){
		    $status = "<i class='fa fa-check-circle' aria-hidden='true' style='color:green'> </i>";
	    }
	    elseif (intval($row['status']) == 2){
		    $status = "<i class='fa  fa-minus-circle' aria-hidden='true' style='color:red'> </i>";
	    }
	    elseif (intval($row['status']) == 0 or intval($row['status']) == 3){
		    $status = "<i class='fa  fa-circle-o-notch' aria-hidden='true' style='color:#ffcb00'> </i>";
	    }
	    echo '<tr id="' .$row['ID'] .'" ><th  scope="row">'. $number_stroke .'.</th> <td><b>'.$row['type_of_diplom'] . "</b><br>  Опис:   " . $row['discript']. " <td>" . $time_of_add. " <td><center>$status <br> $comment_code</center><td>$value_general<td>$type_general<td>$value_stependy<td>$type_stependy<td>$img_code<td><button class='btn btn-danger btn-xs' onclick='dellete(".$row['ID'].")'>Видалити<td></tr>";

	    $number_stroke = $number_stroke + 1;
    }

    ?>




</tbody>
</table>

<i class='fa fa-check-circle' aria-hidden='true' style='color:green'> <a style="color: black">Ваш документ підтверджено</a> </i> <br>
<i class='fa  fa-minus-circle' aria-hidden='true' style='color:red'><a style="color: black"> Ваш документ НЕ підтверджено</a> </i><br>
<i class='fa  fa-circle-o-notch' aria-hidden='true' style='color:#ffcb00'><a style="color: black"> Ваш документ знаходться в черзі на розглядання</a> </i><br>

    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModa999">Підрахувати суму балів</button>

</div>
</div>
    <?
$index = 1;
foreach($rewiew as $key => $current_rewiew){


	?>
<div id="myModa<? echo $index?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img src="http://e-decanat-ifnmu.site/uploads/<?php echo $current_rewiew['img'] ?>" class="img-responsive">
            </div>
        </div>
    </div>
</div>
<?php

	$index = $index + 1;


	?>


    <div id="myModa999" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Cума балів(загальний рейтинг):</h4>
                    <i><h5>НДР(науково-дослідницька робота)  - <?php echo $NDR_sum ?></h5></i>
                    <i> <h5>СРС(суспільний рейтинг студента) - <?php echo $SRS_sum ?></h5></i>
                    <h4>Cума балів(степендіальний рейтинг):</h4>
                    <h5>ВД(видавнича діяльність) - <?php echo $VD_sum ?></h5>
                    <h5>ГД(громадська діяльність) - <?php echo $GD_sum ?></h5>
                    <h5>СМД(спортивно-мистецька діяльність) - <?php echo $SMD_sum ?></h5>
                    <h5>НД(наукова діяльність) - <?php echo $ND_sum ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div id="comment_box" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="comment_text" class="modal-body">

                </div>
            </div>
        </div>
    </div>
	<?php

}

}


function get_sutudent_reword_by_email_moderator($email) {


	?>

    <div class="container">
        <div class="col-md-10">
            <h3></h3>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Документ</th>
                    <th>Внесено</th>
                    <th>Статус</th>
                    <th colspan="2">Загальний рейтинг<br>(бал/тип)</th>
                    <th colspan="2">Степендіальний рейтинг<br>(бал/тип)</th>
                    <th></th>

                </tr>
                </thead>

				<?php


				$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
                mysqli_set_charset($connection,"utf8");

				$strSQL = "SELECT * FROM diplom WHERE email = '{$email}'";
				$rs = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));

				$rewiew = array();
				$number_stroke = 1;
				$SRS_sum = 0;
				$NDR_sum = 0;
				$ND_sum = 0;
				$VD_sum = 0;
				$GD_sum = 0;
				$SMD_sum = 0;
				$GLOBALS["count_888"] = 0;
				$GLOBALS["count_999"] = 0;
				while($row = mysqli_fetch_array($rs)) {
					$time_of_add = date('d.m.y', intval($row['time_of_add']));
					$points = get_point_for_diplom($row['type_of_diplom'], $row['custom_point'], $row['status']);
					$value_general      = $points[0];
					$type_general       = $points[1];
					$value_stependy     = $points[2];
					$type_stependy      = $points[3];
					if ($row['status'] == 1) {
						if ( $type_general == "СРС" ) {
							$SRS_sum += $value_general;
						}
						if ( $type_general == "НДР" ) {
							$NDR_sum += $value_general;
						}
						if ( $type_stependy == "ВД" ) {
							$VD_sum += $value_stependy;
							if ( $VD_sum > 200 ) {
								$VD_sum = 200;
							}
						}
						if ( $type_stependy == "НД" ) {
							$ND_sum += $value_stependy;
							if ( $ND_sum > 200 ) {
								$ND_sum = 200;
							}
						}
						if ( $type_stependy == "ГД" ) {
							$GD_sum += $value_stependy;
							if ( $GD_sum > 200 ) {
								$GD_sum = 200;
							}
						}
						if ( $type_stependy == "СМД" ) {
							$SMD_sum += $value_stependy;
							if ( $SMD_sum > 200 ) {
								$SMD_sum = 200;
							}
						}
					}
					array_push($rewiew, array(
						"img"            => $row['img'],
						"discript"       => $row['discript'],
						"type_of_diplom" => $row['type_of_diplom'],
						"status"         => $row['status'],
						"time"           => $time_of_add,
                        "ID"             => $row['ID'],
                        "comment"        => $row['comment'],
						"value_general"  => $value_general,
                        "type_general"   => $type_general,
                        "value_stependy" => $value_stependy,
                        "type_stependy"  => $type_stependy



					));

					$img_code = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModa'.$number_stroke.'">Детальніше</button>';

					if(intval($row['status']) == 1){
						$status = "<i class='fa fa-check-circle' aria-hidden='true' style='color:green'> </i>";
					}
                    elseif (intval($row['status']) == 2){
						$status = "<i class='fa  fa-minus-circle' aria-hidden='true' style='color:red'> </i>";
					}
                    elseif (intval($row['status']) == 0 or intval($row['status']) == 3){
						$status = "<i class='fa  fa-circle-o-notch' aria-hidden='true' style='color:#ffcb00'> </i>";
					}
					echo '<tr><th scope="row">'. $number_stroke .'.</th> <td><b>'.$row['type_of_diplom'] . "</b><br>  Опис:   " . $row['discript']. " <td>" . $time_of_add. " <td><center>$status</center><td>$value_general<td>$type_general<td>$value_stependy<td>$type_stependy<td>$img_code</tr>";

					$number_stroke = $number_stroke + 1;
				}
				?>


                </tbody>
            </table>
            </div>
    </div>
    <div class="container">
        <div class="col-md-6">
    <i class='fa fa-check-circle' aria-hidden='true' style='color:green'> <a style="color: black">Ваш документ підтверджено</a> </i> <br>
    <i class='fa  fa-minus-circle' aria-hidden='true' style='color:red'><a style="color: black"> Ваш документ НЕ підтверджено</a> </i><br>
    <i class='fa  fa-circle-o-notch' aria-hidden='true' style='color:#ffcb00'><a style="color: black"> Ваш документ знаходться в черзі на розглядання</a> </i><br>
        </div>
        <div class="col-md-6">
            <h4>Cума балів(загальний рейтинг):</h4>
            <i><h5>НДР(науково-дослідницька робота)  - <?php echo $NDR_sum ?></h5></i>
            <i> <h5>СРС(суспільний рейтинг студента) - <?php echo $SRS_sum ?></h5></i>
            <h4>Cума балів(степендіальний рейтинг):</h4>
            <h5>ВД(видавнича діяльність) - <?php echo $VD_sum ?></h5>
            <h5>ГД(громадська діяльність) - <?php echo $GD_sum ?></h5>
            <h5>СМД(спортивно-мистецька діяльність) - <?php echo $SMD_sum ?></h5>
            <h5>НД(наукова діяльність) - <?php echo $ND_sum ?></h5>
        </div>
    </div>


	<?php

	$index = 1;
	foreach($rewiew as $key => $current_rewiew){


		?>
        <div id="myModa<? echo $index?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <img height="250" src="http://e-decanat-ifnmu.site/uploads/<?php echo $current_rewiew['img'] ?>" class="img-responsive"><br>
                        <a><? print_name_course_group($email);?></a><br>
                        <div id="type_of_diplom<?php echo $index ?>"><b>Документ: </b> <?php echo $current_rewiew['type_of_diplom'] ?> <button onclick="editType(<?php echo $index.','.$current_rewiew['ID'].',\''.$current_rewiew['type_of_diplom'].'\'' ?>)">Редагувати</button><br></div>
                        <b>Опис: </b> <?php echo $current_rewiew['discript'] ?> <br>
                        <div id="type_general<?php echo $index ?>"><b>Загальний рейтинг(тип): </b> <?php echo $current_rewiew['type_general'] ?> </div>
                        <div id="value_general<?php echo $index ?>"><b>Загальний рейтинг(бал): </b> <?php echo $current_rewiew['value_general'] ?> </div>
                        <div id="type_stependy<?php echo $index ?>"><b>Степендіальний рейтинг(тип): </b> <?php echo $current_rewiew['type_stependy'] ?> </div>
                        <div id="value_stependy<?php echo $index ?>"><b>Степендіальний рейтинг(бал): </b> <?php echo $current_rewiew['value_stependy'] ?> </div>

                        <div id="status<?php echo $index ?>"><b>Статус: </b> <?php if(intval($current_rewiew['status']) == 1){
		                    echo "<i class='fa fa-check-circle' aria-hidden='true' style='color:green'> <a style=\"color: black\">  документ підтверджено</a> </i>";
                                        }
                                        elseif (intval($current_rewiew['status']) == 2){
	                                        echo "<i class='fa  fa-minus-circle' aria-hidden='true' style='color:red'><a style=\"color: black\">  документ НЕ підтверджено</a> </i>";
                                        }
                                        elseif (intval($current_rewiew['status']) == 0 or intval($current_rewiew['status']) == 3){
                                            echo "<i class='fa  fa-circle-o-notch' aria-hidden='true' style='color:#ffcb00'><a style=\"color: black\">  документ знаходться в черзі на розглядання</a> </i>";
                                        } ?> </div>
                        <b>Внесено: </b> <?php echo $current_rewiew['time'] ?> <br>
                        <b>Змінити статус: </b> <button type="button" class="btn btn-success" onclick="changeStatus(<?php echo $index; ?>, 1, <?php echo $current_rewiew['ID']; ?>)"><i class='fa fa-check-circle' aria-hidden='true' > </i></button> <button type="button" class="btn btn-danger" onclick="changeStatus(<?php echo $index; ?>, 2, <?php echo $current_rewiew['ID']; ?>)"><i class='fa fa-minus-circle' aria-hidden='true' > </i></button>
                                 <button type="button" class="btn btn-warning" onclick="changeStatus(<?php echo $index; ?>, 3, <?php echo $current_rewiew['ID']; ?>)"><i class='fa fa-circle-o-notch' aria-hidden='true' > </i></button> <br>
                        <div id="comment<?php echo $index ?>"><b>Коментар: </b><?php echo $current_rewiew['comment'] ?><button onclick="editComment(<?php echo $index.','.$current_rewiew['ID'].',\''.$current_rewiew['comment'].'\'' ?>)">Добавити коментар</button> </div>
                    </div>
                </div>
            </div>
        </div>
		<?php

		$index = $index + 1;

	}

}



function add_reward_form($email){
	?>
	<div class="container">
    <div class="form-group" class="col-md-6">
        <div  class="col-md-6">

            <button type="button" class="btn btn-secondary, btn2" onclick="function () {
              document.getElementById('contactform').setAttribute('class', '')
            }">Добавити новий документ</button>
        </div>
    </div>
</div>


<!--<form class="form" id="contactform" name="contact" method="post" action="index.php?add=1&email=--><?php //echo $email; ?><!--" enctype="multipart/form-data">-->
<!---->
<!--    <div class="container">-->
<!--        <div class="form-group" class="col-md-9">-->
<!--    <H3 STYLE="color: red;"><center>Подача документів завершена! <br> Подача документів за даний семестр розпочненться 15 лютого!</center></H3>-->
<!--        </div>-->
<!--    </div>-->
<!--</form>-->
<!--    --><?php //return ?>

	<div class="container">
	<div class="form-group" class="col-md-9">
		<div  class="col-md-9">
		<label for="exampleSelect1">Виберіть тип документу</label>
		<select class="form-control" id="exampleSelect1" name="diplom">
			<option></option>
            <?php foreach(get_types_of_diploms() as $key => $current_diplom){
                echo "<option>$current_diplom</option>";} ?>

		</select>
	</div>
	</div>
	</div>


	<div class="container">
		<div class="col-md-9">

			<div class="form-group" >

				<label for="exampleTextarea">Опис</label>
				<textarea class="form-control" id="exampleTextarea" rows="1" name="descript" placeholder="Наприклад: 'Олімпіада з Внутрішньої медицини'"></textarea>


			</div>
		</div>
	</div>



	<div class="container">
		<div class="col-md-9">
			<div class="form-group">
				<label>Завантажте фото документу</label>
				<div class="input-group">
            <span class="input-group-btn">
                <span class="btn btn-default btn-file">
                            Вибрати фото… <input type="file" id="imgInp" name="img" accept="image/jpeg,image/png">
                </span>
            </span>
					<input type="text" class="form-control" readonly >
				</div>
				<img id='img-upload'/>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="col-md-6">

		<button type="submit" class="btn btn-primary">Підтвердити</button>
	</div>
	</div>



</form>
<?php
}

function add_reward_php() {
	$current_user  = wp_get_current_user();
	$email         = $current_user->user_email;
	$time          = strval( strtotime( 'Now' ) );
	$new_file_name = "$email$time";
	if ( $_REQUEST['diplom'] ) {
		$upload_allow = 1;
		$message      = "";
		if ( ! $_POST['diplom'] ) {
			$upload_allow = 0;
			$message      = '<strong>Заповніть,</strong> будь ласка, всі поля.';

		}
//		if ( ! $_POST['descript'] ) {
//			$upload_allow = 0;
//			$message      = '<strong>Заповніть,</strong> будь ласка, всі поля.';
//
//		}


//        if( !$_GET['img'] ){
//	        $upload_allow = 0;
//
//        }
//		if ( ! $_POST['descript'] ) {
//			$upload_allow = 0;
//			$message      = '<strong>Заповніть,</strong> будь ласка, всі поля.';
//
//		}

		$upload_allow_img = 0;
		$filetypes        = array( 'image/jpeg', 'image/gif', 'image/png' );
		foreach ( $filetypes as $x => $check_filetype ) {
			if ( $check_filetype = $_FILES['img']['type'] ) {
				$upload_allow_img = 1;
			}
		}

		if ( $upload_allow_img != 1 ) {
			$upload_allow = 0;
			$message      = 'Вибрано<strong> некоректне фото.</strong> Будь ласка, виберіть інше.';

		}

		if ( ! $_FILES['img']['type'] ) {
			if ( $_REQUEST['diplom'] == "староста академічної групи" or $_REQUEST['diplom'] == "староста курсу" or $_REQUEST['diplom'] == "староста потоку" ) {
				$upload_allow_img = 0;
				$upload_allow     = 1;


			}
		}


		if ( $upload_allow == 0 ) {
			echo '<div class="alert alert-danger">' . $message . '</div>';
		}


		if ( $upload_allow == 1 ) {


			if ( $_GET['email'] ) {
				$email = $_GET['email'];
			} else {
				$current_user = wp_get_current_user();
				$email        = $current_user->user_email;
			}
			if ( $upload_allow_img == 1 ) {


				$img = $_FILES['img']['name'];
				$img = "$new_file_name$img";
			} else {
				$img = "image_not_difined.jpg";
			}
			$type_of_diplom = $_POST['diplom'];
			$discript       = $_POST['descript'];
			$discript       = str_replace( array( '"', "'" ), array( '', '' ), $discript );
			$curent_time    = strtotime( 'now' );


			$connection = mysqli_connect( 'tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan' );
			mysqli_set_charset( $connection, "utf8" );
			$strSQL = "INSERT INTO diplom (email, type_of_diplom, discript, img, time_of_add) VALUES('{$email}', '{$type_of_diplom}', '{$discript}', '{$img}', '{$curent_time}')";
			mysqli_query( $connection, $strSQL ) or die( mysqli_error( $connection ) );
			if ( $upload_allow_img == 1 ) {
				$target_dir  = "/home/tests01/e-decanat-ifnmu.site/www/uploads//";
				$target_file = $target_dir . basename( $_FILES['img']['tmp_name'] );

				//		echo $target_file;
				$uploadOk      = 1;
				$imageFileType = pathinfo( $target_file, PATHINFO_EXTENSION );
				// Check if image file is a actual image or fake image
				if ( isset( $_POST["submit"] ) ) {
					$check = getimagesize( $_FILES["fileToUpload"]["tmp_name"] );
					if ( $check !== false ) {
						echo "File is an image - " . $check["mime"] . ".";
						$uploadOk = 1;
					} else {
						echo "File is not an image.";
						$uploadOk = 0;
					}
				}
				//		echo "<br>";
				$filename = $img;
				move_uploaded_file( $_FILES['img']['tmp_name'], "$target_dir$filename" );

				smart_resize_image( "$target_dir$filename", false, 2000, 2000, true, 'file', true, false, 60 );


			}


			//		echo "<pre>";
			//		print_r( $_POST );
			//		print_r( $_FILES );
		}
	}
	if ( is_editor() ) {
		header( 'Location: index.php?mod=1&email=' . $email );
	} else {
	header( 'Location: index.php' );
	}
	die();
}

function smart_resize_image($file,
	$string             = null,
	$width              = 0,
	$height             = 0,
	$proportional       = false,
	$output             = 'file',
	$delete_original    = true,
	$use_linux_commands = false,
	$quality = 100
) {

	/**
	 * easy image resize function
	 * @param  $file - file name to resize
	 * @param  $string - The image data, as a string
	 * @param  $width - new image width
	 * @param  $height - new image height
	 * @param  $proportional - keep image proportional, default is no
	 * @param  $output - name of the new file (include path if needed)
	 * @param  $delete_original - if true the original image will be deleted
	 * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
	 * @param  $quality - enter 1-100 (100 is best quality) default is 100
	 * @return boolean|resource
	 */

	if ( $height <= 0 && $width <= 0 ) return false;
	if ( $file === null && $string === null ) return false;

	# Setting defaults and meta
	$info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
	$image                        = '';
	$final_width                  = 0;
	$final_height                 = 0;
	list($width_old, $height_old) = $info;
	$cropHeight = $cropWidth = 0;

	# Calculating proportionality
	if ($proportional) {
		if      ($width  == 0)  $factor = $height/$height_old;
        elseif  ($height == 0)  $factor = $width/$width_old;
		else                    $factor = min( $width / $width_old, $height / $height_old );

		$final_width  = round( $width_old * $factor );
		$final_height = round( $height_old * $factor );
	}
	else {
		$final_width = ( $width <= 0 ) ? $width_old : $width;
		$final_height = ( $height <= 0 ) ? $height_old : $height;
		$widthX = $width_old / $width;
		$heightX = $height_old / $height;

		$x = min($widthX, $heightX);
		$cropWidth = ($width_old - $width * $x) / 2;
		$cropHeight = ($height_old - $height * $x) / 2;
	}

	# Loading image to memory according to type
	switch ( $info[2] ) {
		case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
		case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
		case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
		default: return false;
	}


	# This is the resizing/resampling/transparency-preserving magic
	$image_resized = imagecreatetruecolor( $final_width, $final_height );
	if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
		$transparency = imagecolortransparent($image);
		$palletsize = imagecolorstotal($image);

		if ($transparency >= 0 && $transparency < $palletsize) {
			$transparent_color  = imagecolorsforindex($image, $transparency);
			$transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
			imagefill($image_resized, 0, 0, $transparency);
			imagecolortransparent($image_resized, $transparency);
		}
        elseif ($info[2] == IMAGETYPE_PNG) {
			imagealphablending($image_resized, false);
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
			imagefill($image_resized, 0, 0, $color);
			imagesavealpha($image_resized, true);
		}
	}
	imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


	# Taking care of original, if needed
	if ( $delete_original ) {
		if ( $use_linux_commands ) exec('rm '.$file);
		else @unlink($file);
	}

	# Preparing a method of providing result
	switch ( strtolower($output) ) {
		case 'browser':
			$mime = image_type_to_mime_type($info[2]);
			header("Content-type: $mime");
			$output = NULL;
			break;
		case 'file':
			$output = $file;
			break;
		case 'return':
			return $image_resized;
			break;
		default:
			break;
	}

	# Writing image according to type to the output destination and image quality
	switch ( $info[2] ) {
		case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
		case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
		case IMAGETYPE_PNG:
			$quality = 9 - (int)((0.9*$quality)/10.0);
			imagepng($image_resized, $output, $quality);
			break;
		default: return false;
	}

	return true;
}



function check_email(){
	if($_GET['email']){
		return $_GET['email'];
	}
	else false;
}

function change_status(){
    if(!is_editor()){
	    header('Location: index.php');
    }
    $id = $_GET['id'];
    $new_status = $_GET['new_status'];
    $email = $_GET['email'];

    if ($email == false){
	    header('Location: index.php');
    }

    if (!$id){
	    header('Location: index.php?mod=1&email='.$email);
    }

	$connection = mysqli_connect( 'tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL     = "UPDATE diplom SET status=$new_status WHERE id=$id";
	mysqli_query( $connection, $strSQL ) or die( mysqli_error( $connection ) );
	header('Location: index.php?mod=1&email='.$email);
}

function get_types_of_diploms(){
	$connection = mysqli_connect('tests01.mysql.tools', 'tests01_edecan', 'cgrj42r5', 'tests01_edecan');
	mysqli_set_charset($connection,"utf8");
	$strSQL = "SELECT * FROM diplom_value_measure2";
	$rrr = mysqli_query($connection, $strSQL) or die(mysqli_error($connection));
    $all_diploms = array();
	while($row = mysqli_fetch_array($rrr)) {
		array_push($all_diploms,$row['diplom_name']);
		}


	return $all_diploms;
}

function is_supervisor_decan(){
	$current_user       =   wp_get_current_user();
	$email              =   $current_user->user_email;

    if(get_user_inf_by_email($email)['start_year'] == 1111){
        return true;
    }else{
        return false;
    }

}

