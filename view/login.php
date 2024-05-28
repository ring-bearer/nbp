<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Bolnice</title>
	<link rel="stylesheet" type="text/css" href='./style/style.css'>

</head>
<body>

    <div class="container-login">
        <form action="index.php?rt=login/provjera" method="post">

            <h1>Bolnice.hr</h1>
            <label for="oib"><b>OIB:</b></label><br>
            <input type="text" placeholder="Unesite Vaš oib" name="oib" required>
            <br>
            <label for="psw"><b>Lozinka</b></label><br>
            <input type="password" placeholder="Unesite lozinku" name="psw" required>
            <br>
            <button class="submitbtn" type="submit">Login</button>
            <br>

        </form>
    </div>

<?php require_once __DIR__ . '/_footer.php'; ?>
