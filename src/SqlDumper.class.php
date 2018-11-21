<?php
/**
 * Created by PhpStorm.
 * User: Ibraheem Ghazi Alnabriss
 * Github: https://github.com/ibraheem-ghazi
 * Date: 11/19/2018
 */

namespace App\Libraries;
use \PDO;

class SqlDumper
{
	
	/**
	 * export create table query for all tables [tables only, no constraints, PK, Fk, ...etc]
	 * @Unused 
     */
    public static function dumpSql($connection)
    {
		$pdo = new PDO("mysql:host={$connection['host']};dbname={$connection['database']}", $connection['username'], $connection['password']);
		$stmt1 = $pdo->query('SHOW TABLES', PDO::FETCH_NUM);
		
		$result = '';
		foreach($stmt1->fetchAll() as $row) {
			$stmt2 = $pdo->query("SHOW CREATE TABLE `$row[0]`", PDO::FETCH_ASSOC);
			$table = $stmt2->fetch();
			$result .= "{$table['Create Table']};\n\n";
		}
		return $result;
    }
    
    public static function showCreate($connection,$table)
    {
		$pdo = new PDO("mysql:host={$connection['host']};dbname={$connection['database']}", $connection['username'], $connection['password']);
		$stmt2 = $pdo->query("SHOW CREATE TABLE `$table`", PDO::FETCH_ASSOC);
		$table = $stmt2->fetch();
		$result = "{$table['Create Table']};\n\n";
		return $result;
    }
    
	public static function dumpStructure($connection)
    {
		$pdo = new PDO("mysql:host={$connection['host']};dbname={$connection['database']}", $connection['username'], $connection['password']);
		$stmt1 = $pdo->query('SHOW TABLES', PDO::FETCH_NUM);
		$tables=[];
		foreach($stmt1->fetchAll() as $row) {
			$stmt2 = $pdo->query("desc `$row[0]`", PDO::FETCH_ASSOC);
			foreach($stmt2->fetchAll() as $table) {
    			$field_name = $table['Field'];
    			unset($table['Field']);
    			$tables[$row[0]][$field_name]=$table;
			}
			
		}
		// var_export($tables);die();
		return $tables;
    }
    
	public static function diffStructure($first_connection, $second_connection)
	{
		return static::array_diff_assoc_recursive(static::dumpStructure($first_connection), static::dumpStructure($second_connection));
	}
	
	protected static function array_diff_assoc_recursive($array1, $array2)
	{
		foreach($array1 as $key => $value){

			if(is_array($value)){
				if(!isset($array2[$key]))
				{
					$difference[$key] = $value;
				}
				elseif(!is_array($array2[$key]))
				{
					$difference[$key] = $value;
				}
				else
				{
					$new_diff = static::array_diff_assoc_recursive($value, $array2[$key]);
					if($new_diff != FALSE)
					{
						$difference[$key] = $new_diff;
					}
				}
			}
			elseif((!isset($array2[$key]) || $array2[$key] != $value) && !($array2[$key]===null && $value===null))
			{
				$difference[$key] = $value;
			}
		}
		return !isset($difference) ? 0 : $difference;
	}
}