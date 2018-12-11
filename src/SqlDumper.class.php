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
	
	protected static $patterns = [
		'primary'=>'(^[^`]\s*PRIMARY KEY .*[,]?$)',
		'key'=>'(^[^`]\s*KEY\s+(`.*`) .*[,]?$)',
		'constraint'=>'(^[^`]\s*CONSTRAINT\s+(`.*`) .*[,]?$)',
	];
	
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

	public static function diffConstraintsWithQueries($new_connection,$old_connection,$table){
	    $new_const = static::getConstraints($new_connection,$table);
	    $old_const = static::getConstraints($old_connection,$table);
	    $diff_queries = [];
	    foreach(static::array_diff_assoc_recursive($new_const,$old_const)?:[] as $diff_const){
	       if(in_array($diff_const,$new_const) && !in_array($diff_const,$old_const)){
	           $diff_queries[]= static::getConstraintQuery($diff_const,$table)['add'];
	       }elseif(!in_array($diff_const,$new_const) && in_array($diff_const,$old_const)){
	           $diff_queries[]= static::getConstraintQuery($diff_const,$table)['drop'];
	       }
	    }
	    return $diff_queries;
	}
	
		public static function getConstraints($connection,$table){
		$create_query = static::showCreate($connection,$table);
		preg_match_all('/'.implode('|',static::$patterns).'/m',$create_query,$constraints);
		$consrt =array_map('trim',$constraints[0]);
		sort($consrt);
		return $consrt;
	}
	
	public static function getConstraintQuery($constraint,$table){
		foreach (static::$patterns as $key=>$pattern){
			if(preg_match("/".str_replace('^[^`]','',$pattern)."$/m",$constraint,$matches)){
				switch($key){
					case 'primary': return ['drop'=>'ALTER TABLE '.$table.' DROP PRIMARY KEY;','add'=>'ALTER TABLE '.$table.' ADD '.rtrim($constraint,',').';'];
					case 'key': return ['drop'=>"ALTER TABLE {$table} DROP KEY $matches[2];",'add'=>'ALTER TABLE '.$table.' ADD '.rtrim($constraint,',').';'];
					case 'constraint': return ['drop'=>"ALTER TABLE {$table} DROP CONSTRAINT $matches[2];",'add'=>'ALTER TABLE '.$table.' CONSTRAINT '.rtrim($constraint,',').';'];
				}
				break;
			}
		}
	}
}