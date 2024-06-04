<?php

class Admin
{
	protected $oib, $ime, $prezime;

	function __construct($oib, $ime, $prezime)
	{
		$this->oib=$oib;
		$this->ime = $ime;
        $this->prezime= $prezime;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
};

?>
