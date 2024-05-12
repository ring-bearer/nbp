<?php

require_once __DIR__ . '/../model/bolnicaservice.class.php';

class BolnicaController{
  public function index(){
      $ls=new BolnicaService();
      $list = $ls->getbolnice();
			require_once __DIR__ . '/../view/bolnice.php';
	}
};

?>
