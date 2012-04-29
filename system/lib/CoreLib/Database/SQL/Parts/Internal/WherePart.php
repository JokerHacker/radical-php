<?php
namespace Database\SQL\Parts\Internal;

use Database\SQL\Parts\Expression\Comparison;

abstract class WherePart extends PartBase {
	const SEPPERATOR = 'AND';
	private $expr;
	
	function __construct($expr){
		$this->expr = $expr;
	}
	
	function toSQL($first = false){
		$ret = '';
		if(!$first) $ret = ' '.static::SEPPERATOR.' ';
		$ret .= $this->expr;
		return $ret;
	}
	
	static function fromAssign($a,$b){
		return new static(new Comparison($a, $b));
	}
}