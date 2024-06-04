<?php require_once __DIR__ . '/_header.php';?>

<h1> Moj profil </h1>

<?php
    echo 'Pozdrav, ' . $ime . '!';
    echo "<h2> Ovjde možete promijeniti svoje podatke </h2>";
?>

<button class="dropbtn" onclick="window.location.href='index.php?rt=profil/podaci'">Moji podaci</button>


<?php require_once __DIR__ . '/_footer.php'; ?>
