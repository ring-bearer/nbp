<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/bolnicaController.class.php';
?>

<h1> Bolnice</h1>
<table>
	<tr>
		<th>ID</th><th>Ime</th>
		<th>Adresa</th><th>Mjesto</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('id') . '</td>';
			echo '<td>' . $a->__get('ime') . '</td>';
			echo '<td>' . $a->__get('adresa') . '</td>';
			echo '<td>' . $a->__get('mjesto') . '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
