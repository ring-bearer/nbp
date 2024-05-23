<?php

class Pretraga
{
	protected $oib_pacijenta, $vrsta, $datum, $vrijeme, $id_bolnice;

	function __construct($oib_pacijenta, $vrsta, $datum, $vrijeme, $id_bolnice)
	{
		$this->oib_pacijenta=$oib_pacijenta;
		$this->vrsta = $vrsta;
    $this->datum= $datum;
    $this->vrijeme= $vrijeme;
		$this->id_bolnice= $id_bolnice;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
