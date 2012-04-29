<?php
namespace Database\Model\Pagination;

use Database\Model\Table\TableSet;
use Database\Model\TableReferenceInstance;
use Net\URL\Pagination\IPaginator;
use Net\URL\Pagination\Template\IPaginationTemplate;
use Database\SQL;
use Database\Model\TableReference;

/**
 * @author SplitIce
 * A paginated data set
 */
class AlphaPaginator implements IDatabasePaginator {
	private $source;
	private $page;
	private $set;
	private $field;
	
	public $url;
	public $sql;
	
	/**
	 * Internal method to get a result set
	 */
	private function _get(){
		$sql = clone $this->sql;
		$sql->where_and(array($this->field,'LIKE',$this->page.'%'));
		die(var_dump((string)$sql));
		return $this->source->Filter($sql);
	}
	function __construct($source,$field){
		if($source instanceof TableReferenceInstance){
			$source = $source->getAll();
		}elseif(!($source instanceof TableSet)){
			throw new \Exception('Invalid Source passed to paginator');
		}
		
		$this->source = $source;
		$this->field = $field;
		$this->sql = new SQL\SelectStatement();
		$this->set = $this->_get();
	}
	
	public function getIterator() {
		$o = $this->set;
		while($o instanceof \IteratorAggregate){
			$o = $o->getIterator();
		}
		return $o;
	}
	
	function OutputLinks(IPaginator $paginator,IPaginationTemplate $template){
		$paginator->Output(ceil($this->totalRows/$this->perPage), $template);
	}
}