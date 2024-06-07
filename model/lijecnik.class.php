<?php

class Lijecnik
{
	protected $oib, $ime, $prezime;
	protected $datum_rodjenja, $adresa_ambulante, $mjesto_ambulante, $id_bolnice;

	function __construct($oib, $ime, $prezime, $datum_rodjenja, $adresa_ambulante, $mjesto_ambulante, $id_bolnice)
	{
		$this->oib=$oib;
		$this->ime = $ime;
    $this->prezime= $prezime;
		$this->datum_rodjenja= $datum_rodjenja;
		$this->adresa_ambulante= $adresa_ambulante;
		$this->mjesto_ambulante= $mjesto_ambulante;
		$this->id_bolnice= $id_bolnice;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
};

?>
