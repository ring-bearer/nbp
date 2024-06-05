<?php

require_once __DIR__ . '/../model/profilservice.class.php';
require_once __DIR__ . '/../model/lijecnikservice.class.php';
require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/adminservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';
require_once __DIR__ . '/../model/admin.class.php';

class ProfilController{
  public function index(){
      
        $oib = $_COOKIE['oib'];
        $ovlasti = $_COOKIE['ovlasti'];

        //echo $ovlasti;

        $ps = new ProfilService;

        $ime = $ps->dohvatiUsera($oib, $ovlasti);

        require_once __DIR__ . '/../view/profil.php';

	}

  public function podaci(){

    $oib = $_COOKIE['oib'];
    $ovlasti = $_COOKIE['ovlasti'];

    $ps=new ProfilService();

    if($ovlasti === '0'){
      $user = $ps->getProfilLijecnik($oib);
      require_once __DIR__ . '/../view/profillijecnik.php';
    }else if($ovlasti === '1'){
      $user = $ps->getProfilPacijent($oib);
      $oib_lijecnik = $user->__get('oib_lijecnika');
      $doktor = $ps->getProfilLijecnik($oib_lijecnik);
      require_once __DIR__ . '/../view/profilpacijent.php';
    }
    else{
      $user = $ps->getProfilAdmin($oib);
      require_once __DIR__ . '/../view/profiladmin.php';
    }
}

  public function updateLijecnik(){

    $oib = $_COOKIE['oib'];

    $ls=new ProfilService();

    $user = $ls->getProfilLijecnik($oib);

    if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"])){
      require_once __DIR__ . '/../view/_header.php';
      $poruka="Unesite ispravno ime (0-20 slova).\n";
      require_once __DIR__ . '/../view/profillijecnik.php';
      return;
    }
    $user->__set('ime',$_POST["ime"]);


    if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"])){
      require_once __DIR__ . '/../view/_header.php';
      $poruka="Unesite ispravno prezime (0-20 slova).\n";
      require_once __DIR__ . '/../view/profillijecnik.php';
      return;
    }
    $user->__set('prezime',$_POST["prezime"]);

    $user->__set('datum_rodjenja',$_POST["datum_rodjenja"]);

    $ls2 = new LijecnikService;
    $ls2->updatelijecnik($user);

  $poruka="Promjene uspješno spremljene!";
  require_once __DIR__ . '/../view/profillijecnik.php';
}

public function updatePacijent(){

  $oib = $_COOKIE['oib'];

  $ls=new ProfilService();

  $user = $ls->getProfilPacijent($oib);

  if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"])){
    $poruka="Unesite ispravno ime (0-20 slova).\n";
    require_once __DIR__ . '/../view/profilpacijent.php';
    return;
  }
  $user->__set('ime',$_POST["ime"]);

  if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"])){
    $poruka="Unesite ispravno prezime (0-20 slova).\n";
    require_once __DIR__ . '/../view/profilpacijent.php';
    return;
  }
  $user->__set('prezime',$_POST["prezime"]);

  $user->__set('datum_rodjenja',$_POST["datum_rodjenja"]);

  if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"])){
    $poruka="Unesite ispravnu adresu (0-30 znakova).\n";
    require_once __DIR__ . '/../view/profilpacijent.php';
    return;
  }
  $user->__set('adresa',$_POST["adresa"]);

  if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"])){
    $poruka="Unesite ispravno mjesto (0-20 slova).\n";
    require_once __DIR__ . '/../view/profilpacijent.php';
    return;
  }
  $user->__set('mjesto',$_POST["mjesto"]);

  $oib_lijecnik = $user->__get('oib_lijecnika');
  $doktor = $ls->getProfilLijecnik($oib_lijecnik);

  $ls2 = new PacijentService;
  $ls2->updatepacijent($user);

  $poruka="Promjene uspješno spremljene!";
  require_once __DIR__ . '/../view/profilpacijent.php';
}

public function updateAdmin(){

  $oib = $_COOKIE['oib'];

  $ls=new ProfilService();

  $user = $ls->getProfilAdmin($oib);

  if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"])){
    $poruka="Unesite ispravno ime (0-20 slova).\n";
    require_once __DIR__ . '/../view/profiladmin.php';
    return;
  }
  $user->__set('ime',$_POST["ime"]);

  if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"])){
    $poruka="Unesite ispravno prezime (0-20 slova).\n";
    require_once __DIR__ . '/../view/profiladmin.php';
    return;
  }
  $user->__set('prezime',$_POST["prezime"]);

  $ls2 = new AdminService;
  $ls2->updateadmin($user);

  $poruka="Promjene uspješno spremljene!";
  require_once __DIR__ . '/../view/profiladmin.php';
}

};

?>
