<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Bolnice</title>
	<link rel="stylesheet" type="text/css" href='./style/style.css'>

</head>
<body>

    <form action="index.php?rt=login/provjera" method="post">

        <div class="container">
            <label for="oib"><b>OIB:</b></label><br>
            <input type="text" placeholder="Unesite korisničko ime" name="oib" required>
            <br>
            <label for="psw"><b>Lozinka</b></label><br>
            <input type="password" placeholder="Unesite lozinku" name="psw" required>
            <br>
            <button class="submitbtn" type="submit">Login</button>
            <br>
        </div>

    </form>

<?php require_once __DIR__ . '/_footer.php'; ?>
