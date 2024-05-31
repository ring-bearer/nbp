<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/lijecnikController.class.php';
?>

<h1> Liječnici opće prakse</h1>
<table>
	<tr>
		<th>OIB</th><th>Ime</th>
		<th>Prezime</th><th>Datum rođenja</th>
		<th>Adresa ambulante</th><th>Mjesto ambulante</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('oib') . '</td>';
			echo '<td>' . $a->__get('ime') . '</td>';
			echo '<td>' . $a->__get('prezime') . '</td>';
			echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
			echo '<td>' . $a->__get('adresa_ambulante') . '</td>';
			echo '<td>' . $a->__get('mjesto_ambulante') . '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
