<?php require_once __DIR__ . '/_header.php';
if(!isset($prazno)){
?>

<h1>Moji pacijenti</h1>
<form action="index.php?rt=lijecnik/povijestPretraga" method="post">
<table>
	<tr>
    <th>OIB</th><th>MBO</th><th>Ime</th><th>Prezime</th>
		<th>Datum rođenja</th><th>Adresa</th><th>Mjesto</th>
  </tr>
	<?php
	$i=0;
    foreach ($list as $a){
        echo '<tr>';
		echo '<td>' . $a->__get('oib') . '</td>';
		echo '<td>' . $a->__get('mbo') . '</td>';
        echo '<td>' . $a->__get('ime') . '</td>';
		echo '<td>' . $a->__get('prezime') . '</td>';
		echo '<td>' . $a->__get('datum_rodjenja') . '</td>';
		echo '<td>' . $a->__get('adresa') . '</td>';
		echo '<td>' . $a->__get('mjesto') . '</td>';
		?>
			<input type="hidden" name="oib_pacijenta_<?php echo $i; ?>" value="<?php echo $a->__get('oib'); ?>">
			<input type="hidden" name="ime_pacijenta_<?php echo $i; ?>" value="<?php echo $a->__get('ime'); ?>">
            <td><button type="submit" name="povijest_<?php echo $i; ?>" value="<?php echo $a->__get('oib'); ?>">Povijest pretraga</button></td>
     	<?php
        echo '</tr>';
		$i++;
    }
	?>
  </table>
  </form>
<?php
}
require_once __DIR__ . '/_footer.php'; ?>
