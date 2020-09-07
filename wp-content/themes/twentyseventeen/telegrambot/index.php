<?php
include ('dataBase.php');

$db = new dataBase();



?>
<style>
    h2,h4{
        padding: 0.5em 0 0;
        margin: 0 0 0;
        padding: 0;
    }
</style>
<?php

foreach ($db->getDates() as $date)
{

	echo "<center><h2>". $date['name']."</h2></center>";
	echo "<center><h4 style='color: #024874'>". date("d F H:i", $date['date'])."</h4></center>";
    $count = 1;

?>

    <table class="table table-striped">
        <tr>
            <th>#<th>Прізвище<th>Ім'я<th>Курс<th>Група<th></th>
        </tr>

    <?php
    foreach ($db->getListByDate($date['id']) as $student)
    {
        echo "<tr>";
        echo "<th>$count.</th><td>{$student['ln']}<td>{$student['fn']}<td>{$student['course']}<td>{$student['sgroup']}<td></td>";
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
    <p>&nbsp;</p>
    <?


}
