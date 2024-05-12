<?php

class Doktor
{
	protected $oib, $ime, $prezime;
	protected $id_bolnica, $placa;
	protected $podrucje, $specijalizant;

	function __construct($oib, $ime, $prezime, $id_bolnica, $placa, $podrucje, $specijalizant)
	{
		$this->oib=$oib;
		$this->ime = $ime;
    $this->prezime= $prezime;
		$this->id_bolnica= $id_bolnica;
		$this->placa= $placa;
		$this->podrucje= $podrucje;
		$this->specijalizant= $specijalizant;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
};

?>
