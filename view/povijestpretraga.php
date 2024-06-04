<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

?>
<h1> Povijest mojih pretraga </h1>
<table>
	<tr>
		<th>Vrsta</th>
		<th>Datum</th><th>Ime bolnice</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a['datum'] . '</td>';
			echo '<td>' . $a['vrsta'] . '</td>';
			echo '<td>' . $a['ime_bolnice']. '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
