<?php

class GzipHandlerComponent extends Object {

	var $name = 'GzipHandler';

	function initialize(&$controller, $cache = 600) {
		if (Configure::read('debug') == 0) {
			if(!@ob_start("ob_gzhandler")) @ob_start();
			$controller->header('Content-type: text/html; charset: ' . Configure::read('App.encoding'));
			$controller->header('Cache-Control: max-age=' . $cache . ', must-revalidate');
			$controller->header("Expires: " . gmdate('D, d M Y H:i:s',time() + $cache) . ' GMT');
		} else {
			$controller->header('Content-type: text/html; charset: ' . Configure::read('App.encoding'));
			$controller->header('Cache-Control: no-cache, must-revalidate');
			$controller->header("Expires: " . gmdate('D, d M Y H:i:s',time() + 0) . ' GMT');
		}
	}

	function startup(&$controller) {
	}

}

?>
