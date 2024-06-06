<?php

require_once __DIR__ . '/../model/pacijentservice.class.php';
require_once __DIR__ . '/../model/bolnicaservice.class.php';
require_once __DIR__ . '/../model/pacijent.class.php';
require_once __DIR__ . '/../model/bolnica.class.php';

class BolnicaController{
	public function index(){
		$ls=new BolnicaService();
		$list = $ls->getbolnice();
	 	require_once __DIR__ . '/../view/bolnice.php';
	}

	public function susjedi(){
		$ls=new BolnicaService();
		$arr=array();
		$sus=array();
		$l = $ls->getbolnica($_POST['susjedi']);
		$arr = $ls->getsusjedi($_POST['susjedi']);
		foreach($arr as $b){
			$nov=$ls->getbolnica($b);
			$sus[]=$nov;
		}
	 	require_once __DIR__ . '/../view/susjedi.php';
	}


	  public function unos(){
			  require_once __DIR__ . '/../view/newbolnica.php';
		}

		// maknuli smo opciju dodavanja novih bolnica
		public function new(){
				$us=new BolnicaService();

				if(!preg_match('/^[\s"a-zA-ZčćšđžČĆŠĐŽ-]{0,100}$/', $_POST["ime"])){
					require_once __DIR__ . '/../view/_header.php';
					$poruka="Unesite ispravno ime (0-100 slova).\n";
					require_once __DIR__ . '/../view/newbolnica.php';
					return;
				}

	      if(!preg_match('/^[\.\/\sa-zA-ZčćšđžČĆŠĐŽ0-9]{0,30}$/', $_POST["adresa"])){
					require_once __DIR__ . '/../view/_header.php';
					$poruka="Unesite ispravnu adresu (0-50 znakova).\n";
					require_once __DIR__ . '/../view/newbolnica.php';
					return;
				}

				if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"])){
					require_once __DIR__ . '/../view/_header.php';
					$poruka="Unesite ispravno mjesto (0-20 slova).\n";
					require_once __DIR__ . '/../view/newbolnica.php';
					return;
				}

				$bs=new Bolnica(0,$_POST['ime'],
	        $_POST['adresa'], $_POST['mjesto']);
				$noviid=$us->newbolnica($bs);
				$bs->__set('id',$noviid);

				$poruka= "Bolnica uspjesno dodana!";
	      require_once __DIR__ . '/../view/newbolnica.php';
		}

		public function promjena(){
				$ls=new BolnicaService();
				$list = $ls->getbolnice();
				require_once __DIR__ . '/../view/updatebolnica.php';
		}

		public function update(){
				$ls=new BolnicaService();

				$list = $ls->getbolnice();
	      foreach ($list as $k=>$l) {
				
	          if(!preg_match('/^[\s"a-zA-ZčćšđžČĆŠĐŽ\-.,()\'’]{1,100}$/', $_POST["ime"][$k])){
	    				require_once __DIR__ . '/../view/_header.php';
	    				$poruka="Unesite ispravno ime (0-100 slova).\n" . $k;
	    				require_once __DIR__ . '/../view/updatebolnica.php';
	    				return;
	    			}
	          $l->__set('ime',$_POST["ime"][$k]);

	        if(!preg_match('/^[\s\.\/a-zA-ZčćšđžČĆŠĐŽ0-9]{0,50}$/', $_POST["adresa"][$k])){
	  				require_once __DIR__ . '/../view/_header.php';
	  				$poruka="Unesite ispravnu adresu (0-50 znakova).\n";
	  				require_once __DIR__ . '/../view/updatebolnica.php';
	  				return;
	  			}
	        $l->__set('adresa',$_POST["adresa"][$k]);

	  			if(!preg_match('/^[\sa-zA-ZčćšđžČĆŠĐŽ-]{0,20}$/', $_POST["mjesto"][$k])){
	  				require_once __DIR__ . '/../view/_header.php';
	  				$poruka="Unesite ispravno mjesto (0-20 slova).\n";
	  				require_once __DIR__ . '/../view/updatebolnica.php';
	  				return;
	  			}
	        $l->__set('mjesto',$_POST["mjesto"][$k]);
	        $ls->updatebolnica($l);
	      }
		  		// Na kraju smo ipak odlucili da ne postoji brisanje bolnica zbog povezanosti sa susjednim bolnicama
				/*foreach ($_POST['brisanje'] as $i) {
					$ls->deletebolnica($i);
				}*/

				$list = $ls->getbolnice();
				$poruka="Promjene uspješno spremljene!";
				require_once __DIR__ . '/../view/updatebolnica.php';
		}
};
