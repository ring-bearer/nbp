<?php

class Pacijent
{
	protected $oib, $mbo;
  protected $ime, $prezime;

	function __construct($oib, $mbo, $ime, $prezime)
	{
		$this->oib = $oib;
		$this->mbo = $mbo;
		$this->ime = $ime;
    $this->prezime= $prezime;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
