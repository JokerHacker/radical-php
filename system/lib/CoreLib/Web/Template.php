<?php
namespace Web;

use Web\Templates\Scope;

use Core\Resource;

class Template extends PageHandler\PageBase {
	protected $vars = array();
	protected $file;
	protected $name = 'error';
	protected $handler = false;
	
	function __construct($name, $vars = array(), $container = 'HTML') {
		$this->vars = $vars;
		$this->name = $name;
		$this->file = new \File\Instance(static::getPath($name,$container));
		
		foreach(array_slice(debug_backtrace(true),1) as $r){
			if(isset($r['object']) && $r['object'] instanceof PageHandler\IPage && !($r['object'] instanceof Template)){
				$this->handler = $r['object'];
				break;
			}
		}
	}
	
	function __call($method,$args){
		if(!method_exists($this->handler,$method)) return '';
		return call_user_func_array(array($this->handler,$method),$args);
	}
	
	function addVarMember($k,$v){
		$this->vars[$k] = $v;
	}
	
	static function adapters(){
		return \ClassLoader::getNSExpression('Web\\Templates\\Adapter\\*');
	}
	static function isSupported($path){
		if(!($path instanceof \File\Instance)){
			$path = new \File\Instance($path);
		}
		
		$handlers = static::adapters();
		foreach($handlers as $class){
			if(class_exists($class)){
				if($class::is($path)){
					return true;
				}
			}
		}
		return false;
	}
	static function getPath($name,$output = 'HTML'){
		global $BASEPATH;
		//Normally we would use the resource handler for this
		//howeaver as well as detirming which part of the system
		//to fetch from we also have a variable extension
		//@todo overriding order
		$expr = $BASEPATH . DS . '*' . DS . 'template' . DS . $output . DS . $name.'.*';
		foreach(glob($expr) as $path){
			if(static::isSupported($path)){
				return $path;
			}
		}
		
	}
	
	static function Exists($name,$output='HTML'){
		return file_exists(static::getPath($name,$output));
	}
	
	function adapter(){	
		$handlers = static::adapters();
		foreach($handlers as $class){
			if(class_exists($class)){
				if($class::is($this->file)){
					return new $class($this->file);
				}
			}
		}
	}
	
	function GET() {		
		$adapter = $this->adapter();
		if($adapter == null){
			throw new \Exception('Template file couldnt be found');
		}
		
		if($adapter instanceof Templates\Adapter\ITemplateAdapter){
			$scope = new Scope($this->vars, $this->handler);
			$adapter->Output($scope);
		}else{
			throw new \Exception('Invalid Template Adapter');
		}
	}
	
	function Load($file,$locals = array()){
		global $_CONFIG;
		$VAR = $this->vars;
		
		$HANDLER = $this->handler;
		
		extract($locals);
		if(!isset($locals['TEMPLATE_FILE'])){
			$TEMPLATE_FILE = $file;
		}
		
		ob_start();
		include ($file);
		$contents = ob_get_contents();
		ob_end_clean();
		
		PageHandler::Top()->headers->Add('Content-Type', 'text/html;charset=utf-8');

		echo $contents;//TODO: GZIP/Optimise
		//$contents = \Optimiser\HTML::Optimise($contents);
		//return new \PageHandler\GZIP($contents); //END OF CHAIN
	}
	function POST() {
		return $this->GET ();
	}
}
?>