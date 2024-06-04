<?php

require_once __DIR__ . '/../app/database/db.class.php';
require_once __DIR__ . '/admin.class.php';

class AdminService{

	function updateadmin($novi){
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare('UPDATE nbp_lijecnik set ime=:b, prezime=:c where oib=:oib');
			$st->execute(array('oib' => $novi->__get('oib'), 'b' => $novi->__get('ime'), 'c' => $novi->__get('prezime')));
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}
};
