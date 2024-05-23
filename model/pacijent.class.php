<?php

class Pacijent
{
	protected $oib, $mbo;
  protected $ime, $prezime;
	protected $datum_rodjenja, $adresa, $mjesto, $oib_lijecnika;

	function __construct($oib, $mbo, $ime, $prezime, $datum_rodjenja, $adresa, $mjesto, $oib_lijecnika)
	{
		$this->oib = $oib;
		$this->mbo = $mbo;
		$this->ime = $ime;
		$this->prezime= $prezime;
		$this->datum_rodjenja= $datum_rodjenja;
		$this->adresa= $adresa;
		$this->mjesto= $mjesto;
		$this->oib_lijecnika= $oib_lijecnika;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
