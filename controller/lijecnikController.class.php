<?php

require_once __DIR__ . '/../model/lijecnikservice.class.php';
require_once __DIR__ . '/../model/lijecnik.class.php';

class LijecnikController{
  public function index(){
      $ls=new LijecnikService();
      $list = $ls->getlijecnici();
			require_once __DIR__ . '/../view/lijecnici.php';
	}

  public function unos(){
		  require_once __DIR__ . '/../view/newlijecnik.php';
	}

	public function new(){
			$us=new LijecnikService();

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

      if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravnu adresu (0-30 znakova).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}

			if(!preg_match('/^[a-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"])){
				require_once __DIR__ . '/../view/_header.php';
				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
				require_once __DIR__ . '/../view/newlijecnik.php';
				return;
			}


			$bs=new Lijecnik($_POST['oib'],$_POST['ime'],
        $_POST['prezime'],$_POST['datum_rodjenja'],$_POST['adresa_ambulante'],
        $_POST['mjesto_ambulante']);
			$us->newlijecnik($bs);

			require_once __DIR__ . '/../view/_header.php';
			$poruka= "Liječnik uspjesno dodan!";
      require_once __DIR__ . '/../view/newlijecnik.php';
	}

  public function brisanje(){
			$ls=new LijecnikService();
			$list = $ls->getlijecnici();
			require_once __DIR__ . '/../view/deletelijecnik.php';
	}

	public function delete(){
			$ls=new LijecnikService();
			foreach ($_POST['brisanje'] as $i) {
				$ls->deletelijecnik($i);
			}
			$poruka="Brisanje uspješno!";

			$list = $ls->getlijecnici();
			require_once __DIR__ . '/../view/deletelijecnik.php';
	}
};

?>
