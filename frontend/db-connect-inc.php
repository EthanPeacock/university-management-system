<?php
	// show errors
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);

	// connect to database
	try {
		$db = new PDO('mysql:host=localhost; dbname=unisys;','','');
	} catch (Exception $error) {
		echo $error->getMessage();
	}

	// connection errors
	// var_dump($db);
?>
