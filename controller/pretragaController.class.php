<?php

require_once __DIR__ . '/../model/pretragaservice.class.php';

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
};

?>
