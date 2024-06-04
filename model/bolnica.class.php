<?php

class Bolnica
{
	protected $id, $ime;
	protected $adresa, $mjesto;

	function __construct($id, $ime, $adresa, $mjesto)
	{
		$this->id=$id;
		$this->ime = $ime;
    $this->adresa= $adresa;
		$this->mjesto= $mjesto;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
};

?>
