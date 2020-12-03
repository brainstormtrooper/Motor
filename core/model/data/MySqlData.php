<?php

/**
 * Generic data class
 * 
 * This class holds abstract methods for all data based classes
 * 
 * @author rick
 *
 */
include dirname(__FILE__) . '/Data.php';
class MySqlData extends Data{
	
	protected $messages=array();	
	protected $link=false;	
	protected $key_field='id';
	protected $return_type = 'array';
	
	private static $_instance;
	
	
	
	
	private function __construct(){
		// constructor code here it will be called once only
	}
	
	protected static function init(){
		if(self::$_instance == null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	protected function get($table, $key) {
		$row = array();
		$sql = 'SELECT * FROM `' . $table . '` WHERE `' . $this->key_field . '`=' . $key . ' LIMIT 1;';
		if($result = mysqli_query($this->connect(), $sql)){
			$row = mysqli_fetch_array($result);
		}
		
		return $row;
	}
	
	protected function getall($table, $orderby=false, $orderway='DESC'){		
		$sql = 'SELECT * FROM `' . $table . '` ' . ($orderby?'ORDER BY `' . $orderby . '` ' . $orderway:'');
		print $sql;
		$result = mysqli_query($this->connect(), $sql);
		//print '<pre>' . var_dump($result) . '</pre>';
		$rows=array();
		while($res = mysqli_fetch_assoc($result)){
			$rows[] = $res;
		}
		return $rows;		
	}
	
	protected function getmultiple($criteria, $limit='0', $offset=false) {
		// $sql = "SELECT * FROM Orders LIMIT 10 OFFSET 15";
		$sql = 'SELECT * FROM `' . $table . '` ';
		$j = '';
		foreach ($criteria as $k => $c) {
			if ($k==0) {
				$j .= ' WHERE ';
			}else{
				$j .= ' AND ';
			}			
			$j .= $c;			
		}		
		$sql .= $j . ' LIMIT ' . $limit . ($offset?' OFFSET ' . $offset:'');
		$result = mysqli_query($this->connect(), $sql);
		$rows=array();
		while($res = mysqli_fetch_assoc($result)){
			$rows[] = $res;
		}
		return $rows;
	}
	
	protected function update($values, $table, $sql_condition, $exceptions='') {
		// $values, $table, $exceptions = '', $sql_type = 'insert', $sql_condition = NULL			
		return $this->insertUpdate($values, $table, $exceptions, 'update', $sql_condition);
	}
	
	protected function insert($values, $table, $exceptions = '', $criteria = '') {
		// $values, $table, $exceptions = '', $sql_type = 'insert', $sql_condition = NULL		
		return $this->insertUpdate($values, $table, $exceptions, 'insert');
	}
	
	protected function delete($table, $criteria) {
		$j = '';
		foreach ($criteria as $k => $c) {
			if ($k==0) {
				$j .= ' WHERE ';
			}else{
				$j .= ' AND ';
			}
			$j .= $c;
		}		
		$sql = 'DELETE FROM `' . $table . '` ' . $j . ';';
		$r = mysqli_query($this->connect(), $sql);
		return $r;
	}
	
	protected function rollback(){
		return false;
	}
	
	protected function query($sql) {
		$result = mysqli_query($this->connect(), $sql);
		//print '<pre>' . var_dump($result) . '</pre>';
		$rows=array();
		while($res = mysqli_fetch_assoc($result)){
			$rows[] = $res;
		}
		return $rows;
		
	}
	
	protected function describe($table){
		
		return false;
	}
	
	protected function create_table($args){
		
		
		return false;
	}
	
	protected function drop_table($name){
		return false;
	}
	
	protected function add_col($param) {
		// name, type, flags, after (location)...
		// ALTER TABLE yourtable ADD q6 VARCHAR( 255 ) after q5
		$sql = 'ALTER TABLE ' . $table . ' ADD ' . $name . ' ' . $type . ' ' . $location . ';';
		return false;
	}
	
	protected function delete_col($name){
		//   ALTER TABLE tbl_Country DROP COLUMN IsDeleted;
		
		return false;
	}
	
	protected function select_to_temp($args){
		return false;
	}
	
	//
	// TODO: commit/rollback...
	// http://www.w3schools.com/php/func_mysqli_commit.asp
	// http://www.w3schools.com/php/func_mysqli_rollback.asp
	//
	
	//
	// $table - name of the mysql table you are querying
	// $exceptions - fields that will not be inserted into table
	//               i.e. 'submit, action, '; (note trailing comma and space!)
	// $sql_type - has to be 'insert' or 'update'
	// $sql_condition - have to define this if $sql_type = 'update'
	//                  i.e. "userID = '".$_POST['userID']."'"
	private function insertUpdate($cols, $table, $exceptions = '', $sql_type = 'insert', $sql_condition = NULL) {
		
		
		// define some vars
		$fields = '';
		$values = '';
	
		// format input fields into sql
		foreach ($cols as $field => $value) {
			if (!preg_match("/$field, /", $exceptions)) {
				$value = mysqli_real_escape_string($this->connect(), $value);
				if ($sql_type == 'insert') {
					$fields .= "$field, ";
					$values .= "'$value', ";
				}
				else {
					$fields .= "$field = '$value', ";
				}
			}
		}
	
		// remove trailing ", " from $fields and $values
		$fields = preg_replace('/, $/', '', $fields);
		$values = preg_replace('/, $/', '', $values);
	
		// TODO: deal with multiple inserts : http://stackoverflow.com/questions/6889065/inserting-multiple-rows-in-mysql
		
		
		// create sql statement
		if ($sql_type == 'insert') {
			$sql = "INSERT INTO $table ($fields) VALUES ($values)";
			
		}
		elseif ($sql_type == 'update') {
			if (!isset($sql_condition)) {
				$this->messages[] = 'ERROR: You must enter a sql condition!';
				return false;
			}
			$sql = "UPDATE $table SET $fields WHERE $sql_condition";
			//print('<pre>' . $sql . '</pre>');
		}
		else {
			$this->messages[] = 'ERROR: Invalid input for argument $sql_type: must be "insert" or "update"';
			return false;
		}
		//print('<pre>' . $sql . '</pre>');
		// execute sql
		if (mysqli_query($this->connect(),$sql)){
			return true;
		}
		else {
			echo mysqli_error($this->connect());
			return false;
		}
	
	}
	
	private function mysqli_result($result,$row,$field=0) {
		if ($result===false) return false;
		if ($row>=mysqli_num_rows($result)) return false;
		if (is_string($field) && !(strpos($field,".")===false)) {
			$t_field=explode(".",$field);
			$field=-1;
			$t_fields=mysqli_fetch_fields($result);
			for ($id=0;$id<mysqli_num_fields($result);$id++) {
				if ($t_fields[$id]->table==$t_field[0] && $t_fields[$id]->name==$t_field[1]) {
					$field=$id;
					break;
				}
			}
			if ($field==-1) return false;
		}
		mysqli_data_seek($result,$row);
		$line=mysqli_fetch_array($result);
		return isset($line[$field])?$line[$field]:false;
	}
	
	
	private function connect($args=array()){
		if( $this->link ){
			return $this->link;
		}else{
			$this->link = mysqli_connect($GLOBALS['config']['mysqli_config']['default']['host'], $GLOBALS['config']['mysqli_config']['default']['user'], $GLOBALS['config']['mysqli_config']['default']['pass'],$GLOBALS['config']['mysqli_config']['default']['database']) or die('Could not connect to server. ' . mysqli_connect_error() );
		
			return $this->link;
		}
		
		
	}
	
	private function close() {
		
			
			if( $this->link != false )
				mysqli_close($this->link);
				$this->link = false;
		
	}
	
	
}