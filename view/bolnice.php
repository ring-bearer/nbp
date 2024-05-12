<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/bolnicaController.class.php';

?>
<table>
	<tr>
		<th>ID</th><th>Ime</th>
		<th>GPS koordinate</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('id') . '</td>';
			echo '<td>' . $a->__get('ime') . '</td>';
			echo '<td>' . $a->__get('zemlj_sirina') . ', ' . $a->__get('zemlj_duzina') . '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
