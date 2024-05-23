<?php

require_once __DIR__ . '/../model/pretragaservice.class.php';

class PretragaController{
  public function index(){
      $ls=new PretragaService();
      $list = $ls->getpretrage();
			require_once __DIR__ . '/../view/pretrage.php';
	}
};

?>
