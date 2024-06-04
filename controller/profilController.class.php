<?php

require_once __DIR__ . '/../model/profilservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';

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
      require_once __DIR__ . '/../view/profilpacijent.php';
    }
    else{
      $user = $ps->getProfilAdmin($oib);
      require_once __DIR__ . '/../view/profiladmin.php';
    }
}

  public function update(){
    $ls=new PacijentService();

    $list = $ls->getpacijenti();
    foreach ($list as $k=>$l) {

      if(!preg_match('/^[0-9]{9}$/', $_POST["mbo"][$k])){
        $poruka="Unesite ispravan MBO (9 znamenki).\n";
        require_once __DIR__ . '/../view/updatepacijent.php';
        return;
      }
      $l->__set('mbo',$_POST["mbo"][$k]);

        if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"][$k])){
          $poruka="Unesite ispravno ime (0-20 slova).\n";
          require_once __DIR__ . '/../view/updatepacijent.php';
          return;
        }
        $l->__set('ime',$_POST["ime"][$k]);


      if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"][$k])){
        $poruka="Unesite ispravno prezime (0-20 slova).\n";
        require_once __DIR__ . '/../view/updatepacijent.php';
        return;
      }
      $l->__set('prezime',$_POST["prezime"][$k]);

      $l->__set('datum_rodjenja',$_POST["datum_rodjenja"][$k]);

      if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"][$k])){
        $poruka="Unesite ispravnu adresu (0-30 znakova).\n";
        require_once __DIR__ . '/../view/updatepacijent.php';
        return;
      }
      $l->__set('adresa',$_POST["adresa"][$k]);

      if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"][$k])){
        $poruka="Unesite ispravno mjesto (0-20 slova).\n";
        require_once __DIR__ . '/../view/updatepacijent.php';
        return;
      }
      $l->__set('mjesto',$_POST["mjesto"][$k]);

      if(!preg_match('/^[0-9]{11}$/', $_POST["oib_lijecnika"][$k])){
        $poruka="Unesite ispravan OIB (11 znamenki).\n";
        require_once __DIR__ . '/../view/updatepacijent.php';
        return;
      }

      $d=new LijecnikService();
      if($d->getlijecnik($_POST["oib_lijecnika"][$k]))
        $l->__set('oib_lijecnika',$_POST["oib_lijecnika"][$k]);
      else{
        $poruka="Ne postoji liječnik s tim OIB-om!\n";
        require_once __DIR__ . '/../view/updatepacijent.php';
      }

      $ls->updatepacijent($l);
    }

    foreach ($_POST['brisanje'] as $i) {
      $ls->deletepacijent($i);
    }

    $poruka="Promjene uspješno spremljene!";
    require_once __DIR__ . '/../view/updatepacijent.php';
}

};

?>
