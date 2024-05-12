<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/doktorController.class.php';

?>
<table>
	<tr>
		<th>OIB</th><th>Ime</th>
		<th>Prezime</th><th>ID bolnice</th>
		<th>Plaća</th><th>Područje</th>
		<th>Specijalizant?</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('oib') . '</td>';
			echo '<td>' . $a->__get('ime') . '</td>';
			echo '<td>' . $a->__get('prezime') . '</td>';
			echo '<td>' . $a->__get('id_bolnica') . '</td>';
			echo '<td>' . $a->__get('placa') . '</td>';
			echo '<td>' . $a->__get('podrucje') . '</td>';
			echo '<td>' . $a->__get('specijalizant') . '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
