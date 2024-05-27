<?php

require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';

class PacijentController{
	public function index(){
		$ls=new PacijentService();
		$list = $ls->getpacijenti();
	 	require_once __DIR__ . '/../view/pacijenti.php';
	}

	public function unos(){
		  require_once __DIR__ . '/../view/newpacijent.php';
	}

	public function new(){
			$us=new PacijentService();

			if(!preg_match('/^[0-9]{11}$/', $_POST["oib"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravan OIB (11 znamenki).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[0-9]{9}$/', $_POST["mbo"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravan MBO (9 znamenki).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno ime (0-20 slova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno prezime (0-20 slova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
				require_once __DIR__ . '/../view/newpacijent.php';
				return;
			}

			$bs=new Pacijent($_POST['oib'],$_POST['mbo'],
					$_POST['ime'],$_POST['prezime'],
					$_POST['datum_rodjenja'],$_POST['adresa'],
					$_POST['mjesto'],$_POST['oib_lijecnika']);
			$poruka=$us->newpacijent($bs);

			require_once __DIR__ . '/../view/_header.php';
			require_once __DIR__ . '/../view/newpacijent.php';
	}

	public function promjena(){
			$ls=new PacijentService();
			$list = $ls->getpacijenti();
			require_once __DIR__ . '/../view/updatepacijent.php';
	}

	public function update(){
			$ls=new PacijentService();

			$list = $ls->getpacijenti();
      foreach ($list as $k=>$l) {

				if(!preg_match('/^[0-9]{9}$/', $_POST["mbo"][$k])){
					require_once __DIR__ . '/../view/_header.php';
					$poruka="Unesite ispravan MBO (9 znamenki).\n";
					require_once __DIR__ . '/../view/updatepacijent.php';
					return;
				}
				$l->__set('mbo',$_POST["mbo"][$k]);

          if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["ime"][$k])){
    				require_once __DIR__ . '/../view/_header.php';
    				$poruka="Unesite ispravno ime (0-20 slova).\n";
    				require_once __DIR__ . '/../view/updatepacijent.php';
    				return;
    			}
          $l->__set('ime',$_POST["ime"][$k]);


  			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["prezime"][$k])){
  				require_once __DIR__ . '/../view/_header.php';
  				$poruka="Unesite ispravno prezime (0-20 slova).\n";
  				require_once __DIR__ . '/../view/updatepacijent.php';
  				return;
  			}
        $l->__set('prezime',$_POST["prezime"][$k]);

        $l->__set('datum_rodjenja',$_POST["datum_rodjenja"][$k]);

        if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"][$k])){
  				require_once __DIR__ . '/../view/_header.php';
  				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
  				require_once __DIR__ . '/../view/updatepacijent.php';
  				return;
  			}
        $l->__set('adresa',$_POST["adresa"][$k]);

  			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"][$k])){
  				require_once __DIR__ . '/../view/_header.php';
  				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
  				require_once __DIR__ . '/../view/updatepacijent.php';
  				return;
  			}
        $l->__set('mjesto',$_POST["mjesto"][$k]);

				if(!preg_match('/^[0-9]{11}$/', $_POST["oib_lijecnika"][$k])){
					require_once __DIR__ . '/../view/_header.php';
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
