<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

?>
<h1> Vrste pretraga </h1>
<table>
	<tr>
		<th>ID</th><th>Vrsta</th>
		<th>Trajanje</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('id') . '</td>';
			echo '<td>' . $a->__get('vrsta') . '</td>';
			echo '<td>' . $a->__get('trajanje_min') . ' minuta</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
