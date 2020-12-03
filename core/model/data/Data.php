<?php

/**
 * Generic data class
 * 
 * This class holds abstract methods for all data based classes
 * 
 * @author rick
 *
 */
class Data {
	
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
		return false;
	}
	
	protected function getmultiple($args) {
		return false;
	}
	
	protected function update($values, $table, $sql_condition, $exceptions='') {
		return false;
	}
	
	protected function insert($values, $table, $exceptions = '', $criteria = '') {
		return false;
	}
	
	protected function delete($table, $criteria) {
		return false;
	}
	
}