<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '0'){
	require_once __DIR__ . '/navigacija-lijecnik.php';
}
else if(isset($_COOKIE['ovlasti']) && $_COOKIE['ovlasti'] === '1'){
	require_once __DIR__ . '/navigacija-pacijent.php';
}
else{
	require_once __DIR__ . '/navigacija-admin.php';
}

?>
<h1> Pretrage </h1>
<table>
	<tr>
		<th>OIB pacijenta</th><th>Vrsta</th>
		<th>Datum</th><th>Vrijeme</th><th>ID bolnice</th>
	</tr>
	<?php
    foreach($list as $a){
			echo '<tr>';
			echo '<td>' . $a->__get('oib_pacijenta') . '</td>';
			echo '<td>' . $a->__get('vrsta') . '</td>';
			echo '<td>' . $a->__get('datum') . '</td>';
			echo '<td>' . $a->__get('vrijeme') . '</td>';
			echo '<td>' . $a->__get('id_bolnice') . '</td>';
			echo '</tr>';
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
