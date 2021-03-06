<?php
namespace Web\Form\Builder;

use Utility\HTML\Element;

class FormInstance extends FormCommon implements IFormInstance {
	static $__dependencies = array('php.HTML.Form.Builder.Adapter');
	
	protected $form;
	protected $handler;
	
	function __construct($action = null, $method = 'POST'){
		$this->form = new Element('form',compact('action','method'),array());
		
		$args = func_get_args();
		if(count($args) == 0) return;
		
		if(!is_string($args[0])){
			$handler = $args[0];
			foreach(\Core\Libraries::get('Web\\Form\\Builder\\Adapter\\*') as $class){
				if($class::is($handler)){
					$this->handler = new $class($handler);
					unset($args[0]);
					//slice
					break;
				}
			}
		}
	}
	
	protected function _R($return,$before = false){
		if($before){
			foreach($this->form->inner as $k=>$v){
				if(!$before || $v == $before){
					$this->form->inner[$k] = $return;
					$return = $v;
					$before = false;
				}
			}
		}
		$this->form->inner[] = $return;
		return parent::_R($return);
	}
	
	function form(){
		return $this->form;
	}
	
	function __toString(){
		return (string)$this->form;
	}
	
	function action($action){
		$this->form->attributes['action'] = $action;
	}
	function method($method){
		$this->form->attributes['method'] = $method;
	}
	function Add($v){
		$this->form->inner[] = $v;
	}
	
	function __call($method,$arguments){
		$ret = call_user_func_array(array($this->handler,$method),$arguments);
		$this->form->inner = $ret;
		return $this;
	}
}