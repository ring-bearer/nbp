<?php

class Pretraga
{
	protected $id, $vrsta, $trajanje_min;

	function __construct($id, $vrsta, $trajanje_min)
	{
		$this->id=$id;
		$this->vrsta = $vrsta;
    $this->trajanje_min= $trajanje_min;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>
