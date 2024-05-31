<?php

class Zahtjev
{
	protected $oib_pacijenta, $oib_stari, $oib_novi;

	function __construct($oib_pacijenta, $oib_stari, $oib_novi)
	{
		$this->oib_pacijenta=$oib_pacijenta;
		$this->oib_stari = $oib_stari;
    $this->oib_novi= $oib_novi;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
};

?>
