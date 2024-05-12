<?php

require_once __DIR__ . '/../model/doktorservice.class.php';
require_once __DIR__ . '/../model/doktor.class.php';

class DoktorController{
  public function index(){
      $ls=new DoktorService();
      $list = $ls->getdoktori();
			require_once __DIR__ . '/../view/doktori.php';
	}

  public function unos(){
		  require_once __DIR__ . '/../view/newdoktor.php';
	}

	public function new(){
			$us=new DoktorService();
			$bs=new Doktor($_POST['oib'],$_POST['ime'],
        $_POST['prezime'],$_POST['id_bolnica'],$_POST['placa'],
        $_POST['podrucje'],$_POST['specijalizant']);
			$us->newdoktor($bs);

			require_once __DIR__ . '/../view/_header.php';
			echo "Doktor uspjesno dodan!";
	}
};

?>
