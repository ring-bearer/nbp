<?php

require_once __DIR__ . '/../model/loginservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';

class LoginController{
    public function index()
    {
        require_once __DIR__ . '/../view/loginpacijent.php';
	}

    public function indexLijecnik()
    {
        require_once __DIR__ . '/../view/loginlijecnik.php';
	}

    public function indexAdmin()
    {
        require_once __DIR__ . '/../view/loginadmin.php';
	}

    public function provjeraPacijent()
    {

    //ako je korisnik vec zapamcen od prije
    if(isset($_COOKIE['oib'])){
      $oib = $_COOKIE['oib'];
      $ls = new LoginService;

      $ls->userprovjeraPacijent($oib);
      return;
    }

    // Možda se ne šalje password; u njemu smije biti bilo što.
    if( !isset( $_POST["psw"] ) )
        require_once __DIR__ . '/../view/login.php';

    // Sve je OK, provjeri jel ga ima u bazi.

    $oib = $_POST["oib"];
    $password = $_POST["psw"];

    $ls = new LoginService;

    $ls->provjeraUBaziPacijent($oib, $password);

    }

    public function provjeraLijecnik()
    {

    //ako je korisnik vec zapamcen od prije
    if(isset($_COOKIE['oib'])){
      $oib = $_COOKIE['oib'];
      $ls = new LoginService;

      $ls->userprovjeraLijecnik($oib);
      return;
    }

    // Možda se ne šalje password; u njemu smije biti bilo što.
    if( !isset( $_POST["psw"] ) )
        require_once __DIR__ . '/../view/login.php';

    // Sve je OK, provjeri jel ga ima u bazi.

    $oib = $_POST["oib"];
    $password = $_POST["psw"];

    $ls = new LoginService;

    $ls->provjeraUBaziLijecnik($oib, $password);

    }

    public function provjeraAdmin()
    {

    //ako je korisnik vec zapamcen od prije
    if(isset($_COOKIE['oib'])){
      $oib = $_COOKIE['oib'];
      $ls = new LoginService;

      $ls->userprovjeraAdmin($oib);
      return;
    }

    // Možda se ne šalje password; u njemu smije biti bilo što.
    if( !isset( $_POST["psw"] ) )
        require_once __DIR__ . '/../view/login.php';

    // Sve je OK, provjeri jel ga ima u bazi.

    $oib = $_POST["oib"];
    $password = $_POST["psw"];

    $ls = new LoginService;

    $ls->provjeraUBaziAdmin($oib, $password);

    }

    public function logout()
    {
        setcookie('oib','',time()-50);
        setcookie('ovlasti','',time()-50);
        require_once __DIR__ . '/../view/loginpacijent.php';
    }

};

?>
