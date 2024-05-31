<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Bolnice</title>
	<link rel="stylesheet" type="text/css" href='./style/style.css'>
</head>
<body>

	<?php
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
