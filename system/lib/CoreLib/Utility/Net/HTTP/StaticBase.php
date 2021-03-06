<?php
namespace Utility\Net\HTTP;

abstract class StaticBase {
	static function Fetch($url){
		$http = new Fetch($url);
		$obj = $http->Execute();
		return $obj->getContent();
	}
}