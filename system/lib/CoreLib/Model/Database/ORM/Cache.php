<?php
namespace Model\Database\ORM;

use Model\Database\Model\TableReferenceInstance;

class Cache {
	static $data = array();
	
	static function Get($table){
		if(is_object($table) && $table instanceof TableReferenceInstance){
			$table = $table->getClass();
		}
		if(isset(self::$data[$table])){
			return self::$data[$table];
		}
	}
	static function Set($table, ModelData $orm){
		if($table instanceof TableReferenceInstance){
			$table = $table->getClass();
		}
		if($orm instanceof Model){
			
		}
		self::$data[$table] = $orm;
	}
}