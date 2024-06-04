<?php

class Termin
{
	protected $oib_pacijenta, $id_pretrage, $datum, $vrijeme, $id_bolnice;

	function __construct($oib_pacijenta, $id_pretrage, $datum, $vrijeme, $id_bolnice)
	{
		$this->oib_pacijenta=$oib_pacijenta;
		$this->id_pretrage = $id_pretrage;
    $this->datum= $datum;
    $this->vrijeme= $vrijeme;
    $this->id_bolnice= $id_bolnice;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
