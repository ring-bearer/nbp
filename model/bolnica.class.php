<?php

class Bolnica
{
	protected $id, $ime;
  protected $zemlj_sirina, $zemlj_duzina;

	function __construct($id, $ime, $zemlj_sirina, $zemlj_duzina)
	{
		$this->id=$id;
		$this->ime = $ime;
    $this->zemlj_sirina= $zemlj_sirina;
    $this->zemlj_duzina= $zemlj_duzina;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
