<?php require_once __DIR__ . '/../navigation-bars/navigation-bar-login.php'; ?>

<?php require_once __DIR__ . '/../_header.php'; ?>

    <form action="index.php?rt=login/provjera" method="post">
        <div class="imgcontainer">
            <img src="./view/images/img_avatar2.png" alt="Avatar" class="avatar">
        </div>

        <div class="container">
            <label for="uname"><b>Korisničko ime</b></label><br>
            <input type="text" placeholder="Unesite korisničko ime" name="uname" required>
            <br>
            <label for="psw"><b>Lozinka</b></label><br>
            <input type="password" placeholder="Unesite lozinku" name="psw" required>
            <br>
            <button class="submitbtn" type="submit">Login</button>
            <br>
            <label>
                <input type="checkbox" checked="checked" name="remember"> Zapamti me
            </label>
        </div>

        <div class="container">
            <!--<button type="button" class="cancelbtn">Odustani</button>-->
            <span class="psw">Zaboravili ste <a href="index.php?rt=login/zaborav">username ili lozinku?</a></span>
        </div>
    </form>

<?php require_once __DIR__ . '/../_footer.php'; ?>
