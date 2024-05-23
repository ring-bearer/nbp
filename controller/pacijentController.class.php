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
			$bs=new Pacijent($_POST['oib'],$_POST['mbo'],
					$_POST['ime'],$_POST['prezime'],
					$_POST['datum_rodjenja'],$_POST['adresa'],
					$_POST['mjesto'],$_POST['oib_lijecnika']);
			$us->newpacijent($bs);

			require_once __DIR__ . '/../view/_header.php';
			echo "Pacijent uspjesno dodan!";
	}

};

?>
