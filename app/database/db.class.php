<?php

class DB
{
	private static $db = null;

	private function __construct() { }
	private function __clone() { }

	public static function getConnection()
	{
		if( DB::$db === null )
	    {
	    	try
	    	{
					//ovdje treba promjeniti IP hosta nakon povezivanja sa VCL


		    	DB::$db = new PDO('pgsql:host=31.147.201.126; port=5432; dbname=odbojka; user=postgres; password=password' );
					DB::$db-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    }
		    catch( PDOException $e ) { exit( 'PDO Error: ' . $e->getMessage() ); }
	    }
		return DB::$db;
	}
}

?>
