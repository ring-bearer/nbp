<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

echo "<h1>$poruka</h1>";
?>
<table>
	<tr>
		<th>Vrsta</th>
		<th>Datum</th><th>Ime bolnice</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a[0] . '</td>';
			echo '<td>' . $a[1] . '</td>';
			echo '<td>' . $a[2]. '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
