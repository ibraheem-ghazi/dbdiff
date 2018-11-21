<?php
/**
 * Created by PhpStorm.
 * User: Ibraheem Ghazi Alnabriss
 * Github: https://github.com/ibraheem-ghazi
 * Date: 11/19/2018
 */
 
error_reporting(E_ALL);
ini_set('display_errors', 1);

$old_structure_conn = [
	'host'=>'127.0.0.1',
	'username'=>'root',
	'password'=>"",
	'database'=>'your_new_database_such_as_dev_db',
];

$new_structure_conn = [
	'host'=>'127.0.0.1',
	'username'=>'root',
	'password'=>'',
	'database'=>'your_old_database_such_as_production_db',
];