<?php 
/**
 * Created by PhpStorm.
 * User: Ibraheem Ghazi Alnabriss
 * Github: https://github.com/ibraheem-ghazi
 * Date: 11/19/2018
 */
 
require 'config.php';
require 'SqlDumper.class.php';

use \App\Libraries\SqlDumper;

if(isset($_GET['source']) && $_GET['source']==='1'){ echo print_r(SqlDumper::dumpStructure($old_structure_conn)) ; die();}
if(isset($_GET['source']) && $_GET['source']==='2'){ echo print_r(SqlDumper::dumpStructure($new_structure_conn)) ; die();}

/**
 * check if column or attribute of column has been changed from old structure and new structure
 * @return int 1 if exists in new but not in old (new column|new value), -1 if exists in old but not in new (deleted column|old value), 0 if exists in both new and old struct (not changed)
 */
function isChanged($table,$column,$attr=null){
    global $old_structure_dump, $new_structure_dump;
    
    $target_old_isset = $attr ? isset($old_structure_dump[$table][$column][$attr]) : isset($old_structure_dump[$table][$column]);
    $target_new_isset = $attr ? isset($new_structure_dump[$table][$column][$attr]) : isset($new_structure_dump[$table][$column]);
    
    $is_deleted = $target_old_isset && !$target_new_isset;
    $is_new = !$target_old_isset && $target_new_isset;
    $is_exists_changed = !$attr || ($target_old_isset && $target_new_isset &&  $old_structure_dump[$table][$column][$attr]!=$new_structure_dump[$table][$column][$attr]);
    if($is_deleted) return -1;
    elseif($is_new || $is_exists_changed) return 1;
    else return 0;
     
}


$old_structure_dump = SqlDumper::dumpStructure($old_structure_conn);
$new_structure_dump = SqlDumper::dumpStructure($new_structure_conn);
$diff_dump = SqlDumper::diffStructure($new_structure_conn,$old_structure_conn);

ob_start();
//structure diff and generate it's query
foreach($old_structure_dump as $table=>$columns){
    if(!isset($diff_dump[$table])) continue;//if not changed table *
    $exists = isset($new_structure_dump[$table]);
    echo '<div class="'.($exists?'normal':'deleted').'">';
    echo "<h2>$table</h2>";
    
    //for each column exists in old struct but not in new mark it as deleted using <del> tag
    foreach($old_structure_dump[$table] as $column=>$attrs){
        if(isset($new_structure_dump[$table][$column])) continue;
        echo "<del>$column</del>";
    }
    
    //for each column exists in new struct but not in old mark it as new using <ins> tag
    foreach($new_structure_dump[$table] as $column=>$attrs){
        if(isset($old_structure_dump[$table][$column])) continue;
        echo "<ins>$column</ins>";
        
        //for each new column print it's attribute like type, accept null, ... etc
        foreach($diff_dump[$table][$column] as $attr=>$value){
            echo "<ins>&nbsp;&nbsp;&nbsp;&nbsp;- $attr: ".json_encode($value)."</ins>";
        }
    }
    
    //for each column exists in diff dump (changed column) print detailed attrs and mark old value and new value
    $tbl_create = SqlDumper::showCreate($new_structure_conn,$table);
    foreach($diff_dump[$table] as $column=>$attrs){
        $is_c_changed = isChanged($table,$column);
        $tag = $is_c_changed > 0 ? 'ins' : ($is_c_changed < 0 ? 'del' : 'span');
        echo "<$tag style='margin-top:15px;'>$column</$tag>";
        foreach($diff_dump[$table][$column] as $attr=>$value){
           $is_a_changed = isChanged($table,$column,$attr);
           $tag = ($is_a_changed > 0 || $is_c_changed>0) ? 'ins' : ($is_a_changed < 0 ? 'del' : 'span');
           if($tag==='ins' &&isset($old_structure_dump[$table][$column][$attr])) echo "<del>&nbsp;&nbsp;&nbsp;&nbsp;- $attr: ".json_encode($old_structure_dump[$table][$column][$attr])."</del>";
           echo "<$tag>&nbsp;&nbsp;&nbsp;&nbsp;- $attr: ".json_encode($value)."</$tag>";
        }
        preg_match('/`'.$column.'` (.*),/m',$tbl_create,$output);
        echo "<code>ALTER TABLE $table MODIFY COLUMN $column {$output[1]};</code>";
    }
    echo '</div>';
}

//new tables
foreach($new_structure_dump as $table=>$columns){
    if(!isset($old_structure_dump[$table])){
        echo '<div class="new">';
        echo "<h2>$table</h2>";
       foreach($columns as $column=>$attrs){
            echo "<ins>$column</ins>";
            foreach($diff_dump[$table][$column] as $attr=>$value){
                echo "<ins>&nbsp;&nbsp;&nbsp;&nbsp;- $attr: ".json_encode($value)."</ins>";
            }
       }
        echo '<code class="sql">'.SqlDumper::showCreate($new_structure_conn,$table).'</code>';
        echo '</div>';
    }
}


$output = ob_get_contents();

ob_end_clean();

include 'view.php';


