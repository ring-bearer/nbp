<?php

require_once __DIR__ . '/../model/pretragaservice.class.php';
require_once __DIR__ . '/../model/pacijentservice.class.php';

class PretragaController{
  public function index(){
      $ls=new PretragaService();
      $list = $ls->getpretrage();
			require_once __DIR__ . '/../view/pretrage.php';
	}

  public function povijest(){
      $ls=new PretragaService();
      $list = $ls->povijestpretraga($_COOKIE['oib']);
      require_once __DIR__ . '/../view/povijestpretraga.php';
	}

  public function unos(){
		  require_once __DIR__ . '/../view/newpretraga.php';
	}

  public function new(){
    $ls=new PacijentService();
    $pac = $ls->getpacijent($_COOKIE['oib']);
    $oib_lijecnika=$pac->__get('oib_lijecnika');
    $ps=new PretragaService();
    $ps->newzahtjev($_COOKIE['oib'],$oib_lijecnika,$_POST['zahtjev']);
    $poruka="Zahtjev uspješno poslan!";
		require_once __DIR__ . '/../view/newpretraga.php';
	}


};

?>
