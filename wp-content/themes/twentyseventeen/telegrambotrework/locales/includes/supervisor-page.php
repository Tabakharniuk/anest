<?php

if (is_result_page() and !check_email()){
	if(is_editor()){
		?>

		<?php
		$current_user       =   wp_get_current_user();
		$email              =   $current_user->user_email;
		$curent_course =    2018 - $_GET['course'];



		?>

        <div class="container">
            <div class="col-md-10">
                <table  border="1">
                    <thead>
                    <tr>

                        <th>Прізвище І'мя</th>
                        <th>Група</th>
                        <th>К-ть всіх документів</th>
                        <th>К-ть непідтверджених документів</th>
                        <th>К-ть нових документів</th>
                        <th>СРС</th>
                        <th>НДР</th>
                        <th>ВД</th>
                        <th>НД</th>
                        <th>ГД</th>
                        <th>СМД</th>
                    </tr>
                    </thead>



					<?php

					foreach(get_group_from_course($curent_course) as $key => $current_group){
						if ($current_group != "10а" and $current_group != "10б" and $current_group != "10в" and $current_group != "10A" and $current_group != "10Б" and $current_group != "10В" and $current_group != "10А"){


							print_all_student_from_course_and_course_for_result($curent_course, $current_group);
						}
					}
					?>
                </table>
            </div>
        </div>
		<?php
	}
	die;
}

if($_GET['course']){
    head();
	$curent_course = 2018 - $_GET['course'];
	?>
    <div class="container">
        <div class="col-md-8">
            <h4><a href="index.php?result=1&course=<?php echo $_GET['course'] ?>">Згенерувати результат</a></h4>
        </div>
    </div>
	<?
	get_student_with_new(get_emails_with_new_status($curent_course),$curent_course );

	foreach(get_group_from_course($curent_course) as $key => $current_group){
		if ($current_group != "10а" and $current_group != "10б" and $current_group != "10в"){

			echo "<div class=\"container\">
                    <div class=\"col-md-10\"><h1> Група: $current_group</h1></div></div>";

			print_all_student_from_course_and_course($curent_course, $current_group);
		}}
	footer();
	die;
}

if($_GET['mod'] == 1 and check_email()){
    head();

//	url_for_editors_main_page();
	$email = check_email();
	?>

	<div class="container">
		<div class="col-md-8">
			<h3><b><?php print_name_course_group( $email ); ?></b></h3>

		</div>
	</div>

	<?php
	get_sutudent_reword_by_email_moderator( $email );
	echo '<br>';
	add_reward_form( $email );
	footer();
	die;
}


head();
?>

	<div class="container">
		<div class="col-md-8">
			<h3><a href="index.php?course=1">1 курс</a></h3>
			<h3><a href="index.php?course=2">2 курс</a></h3>
			<h3><a href="index.php?course=3">3 курс</a></h3>
			<h3><a href="index.php?course=4">4 курс</a></h3>
			<h3><a href="index.php?course=5">5 курс</a></h3>
			<h3><a href="index.php?course=6">6 курс</a></h3>

		</div>
	</div>
<?
footer();
die;