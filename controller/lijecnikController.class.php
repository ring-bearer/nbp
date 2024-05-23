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
			$bs=new Lijecnik($_POST['oib'],$_POST['ime'],
        $_POST['prezime'],$_POST['datum_rodjenja'],$_POST['adresa_ambulante'],
        $_POST['mjesto_ambulante']);
			$us->newlijecnik($bs);

			require_once __DIR__ . '/../view/_header.php';
			echo "Doktor uspjesno dodan!";
	}
};

?>
