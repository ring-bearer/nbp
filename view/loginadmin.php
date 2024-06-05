<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Bolnice</title>
	<link rel="stylesheet" type="text/css" href='./style/style.css'>

</head>
<body>

    <div class="background-cross"></div>

    <div class="container-login-special" id="login-special">
        <form action="index.php?rt=login/provjeraAdmin" method="post">
					<div id="insideunos">
            <h1>Bolnice.hr</h1>
            <nav>
                <button class="loginpacijent" onclick="window.location.href='index.php?rt=login/index'">Pacijenti</button>
                <button class="loginpacijent" onclick="window.location.href='index.php?rt=login/indexLijecnik'">Liječnici</button>
                <button class="loginpacijent" onclick="window.location.href='index.php?rt=login/indexAdmin'">Administratori</button>
            </nav>
            <h2>Administratori</h2>
            <label for="oib"><b>OIB</b></label><br>
            <input type="text" placeholder="Unesite Vaš OIB" name="oib" required>
            <br>
            <label for="psw"><b>Lozinka</b></label><br>
            <input type="password" placeholder="Unesite lozinku" name="psw" required>
            <br>
						<p style="font-size: 15px"><?php if(isset($poruka)) echo $poruka;?></p>
            <button class="submitbtn" type="submit">Login</button>
            <br>
					</div>
        </form>
    </div>
<body>
