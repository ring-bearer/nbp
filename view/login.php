<?php require_once __DIR__ . '/../_header.php'; ?>

    <form action="index.php?rt=login/provjera" method="post">

        <div class="container">
            <label for="uname"><b>Korisničko ime</b></label><br>
            <input type="text" placeholder="Unesite korisničko ime" name="uname" required>
            <br>
            <label for="psw"><b>Lozinka</b></label><br>
            <input type="password" placeholder="Unesite lozinku" name="psw" required>
            <br>
            <button class="submitbtn" type="submit">Login</button>
            <br>
        </div>

    </form>

<?php require_once __DIR__ . '/../_footer.php'; ?>
