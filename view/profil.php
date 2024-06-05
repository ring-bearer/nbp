<?php require_once __DIR__ . '/_header.php';?>

<h1> Moj profil </h1>

<?php
    echo '<p id="gore"> Pozdrav, ' . $ime . '!<br>';
    echo "<br>Ovdje možete promijeniti svoje podatke.<br></p>";
?>

<button class="dropbtn" onclick="window.location.href='index.php?rt=profil/podaci'">Moji podaci</button>


<?php require_once __DIR__ . '/_footer.php'; ?>
