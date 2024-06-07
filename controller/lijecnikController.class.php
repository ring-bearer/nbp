<?php

require_once __DIR__ . '/../model/lijecnikservice.class.php';
require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/zahtjevservice.class.php';
require_once __DIR__ . '/../model/bolnicaservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';
require_once __DIR__ . '/../model/zahtjev.class.php';

class LijecnikController{
  public function index(){
      $ls=new LijecnikService();
      $list = $ls->getlijecnici();
      $bs=new BolnicaService();
      $bolnice=array();
      foreach ($list as $a){
        $id=$a->__get('id_bolnice');
        $bolnice[]=$bs->getbolnica($id);
      }
			require_once __DIR__ . '/../view/lijecnici.php';
	}

  public function mojipacijenti(){
    $ls=new PacijentService();
    $list = $ls->getmojipacijenti($_COOKIE['oib']);
    	require_once __DIR__ . '/../view/mojipacijenti.php';
  }

  public function unos(){
      $bs=new BolnicaService();
      $list=$bs->getbolnice();
		  require_once __DIR__ . '/../view/newlijecnik.php';
	}

	public function new(){
			$us=new LijecnikService();
      $bs=new BolnicaService();
      $list=$bs->getbolnice();

      if(!preg_match('/^[0-9]{11}$/', $_POST["oib"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravan OIB (11 znamenki).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno ime (0-20 slova).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno prezime (0-20 slova).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}

      if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}

			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}

			$bs=new Lijecnik($_POST['oib'],$_POST['ime'],
        $_POST['prezime'],$_POST['datum_rodjenja'],$_POST['adresa_ambulante'],
        $_POST['mjesto_ambulante'],$_POST["bolnica"]);
			$us->newlijecnik($bs);

			require_once __DIR__ . '/../view/_header.php';
			$poruka= "Liječnik uspjesno dodan!";
      require_once __DIR__ . '/../view/newlijecnik.php';
	}

  public function promjena(){
			$ls=new LijecnikService();
			$list = $ls->getlijecnici();
      $bs=new BolnicaService();
			$bolnice = $bs->getbolnice();
			require_once __DIR__ . '/../view/updatelijecnik.php';
	}

	public function update(){
			$ls=new LijecnikService();

			$list = $ls->getlijecnici();
      foreach ($list as $k=>$l) {
          if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"][$k])){
    				require_once __DIR__ . '/../view/_header.php';
    				$poruka="Unesite ispravno ime (0-20 slova).\n";
    				require_once __DIR__ . '/../view/updatelijecnik.php';
    				return;
    			}
          $l->__set('ime',$_POST["ime"][$k]);


  			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"][$k])){
  				require_once __DIR__ . '/../view/_header.php';
  				$poruka="Unesite ispravno prezime (0-20 slova).\n";
  				require_once __DIR__ . '/../view/updatelijecnik.php';
  				return;
  			}
        $l->__set('prezime',$_POST["prezime"][$k]);

        $l->__set('datum_rodjenja',$_POST["datum_rodjenja"][$k]);

        if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa_ambulante"][$k])){
  				require_once __DIR__ . '/../view/_header.php';
  				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
  				require_once __DIR__ . '/../view/updatelijecnik.php';
  				return;
  			}
        $l->__set('adresa_ambulante',$_POST["adresa_ambulante"][$k]);

  			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto_ambulante"][$k])){
  				require_once __DIR__ . '/../view/_header.php';
  				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
  				require_once __DIR__ . '/../view/updatelijecnik.php';
  				return;
  			}
        $l->__set('mjesto_ambulante',$_POST["mjesto_ambulante"][$k]);

        if($_POST["mjesto_ambulante"][$k]!=0)
          $l->__set('id_bolnice',$_POST["bolnica"][$k]);
        $ls->updatelijecnik($l);
      }

			foreach ($_POST['brisanje'] as $i) {
				$ls->deletelijecnik($i);
			}

			$poruka="Promjene uspješno spremljene!";
			require_once __DIR__ . '/../view/updatelijecnik.php';
	}
};

?>
