<?php require_once __DIR__ . '/_header.php';
require_once __DIR__ . '/../controller/pretragaController.class.php';

echo "<h1>$poruka</h1>";
?>
<table>
	<tr>
		<?php
			if($_COOKIE['ovlasti']==='2'){
				echo "<th>Pacijent</th>";
			}
		?>
		<th>Datum</th>
		<th>Vrsta</th><th>Ime bolnice</th>
	</tr>
	<?php
		$i=0;
    foreach($list as $a){
			foreach ($a as $k){
			echo '<tr>';
			if($_COOKIE['ovlasti']==='2'){
				echo '<td>' . $pac[$i]->__get('prezime') . ', ' . $pac[$i]->__get('ime') . '</td>';
			}
			echo '<td>' . $k[0] . '</td>';
			echo '<td>' . $k[1] . '</td>';
			echo '<td>' . $k[2]. '</td>';
			echo '</tr>';
		}
		$i++;
   }?>
</table>

<?php require_once __DIR__ . '/_footer.php'; ?>
