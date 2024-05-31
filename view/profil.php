<?php require_once __DIR__ . '/_header.php';

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
<h1> Moj profil </h1>

<?php
    echo 'Pozdrav ' . $ime . '!';
?>


<?php require_once __DIR__ . '/_footer.php'; ?>
