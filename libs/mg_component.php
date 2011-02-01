<?php

App::import('Core', 'Debugger');

class MgComponent extends Object {

	var $uses = array();

	function initialize(&$controller, $settings = array()) {

		$this->controller =& $controller;

		$this->data =& $this->controller->data;
		$this->params =& $this->controller->params;
		$this->passedArgs =& $this->controller->passedArgs;
		$this->action =& $this->controller->action;

		// loading $this->uses
		foreach($this->uses as $model) {
			$this->loadModel($model);
			list($subPlugin, $subModel) = pluginSplit($model);
			$this->{$subModel} = $this->controller->{$subModel};
		}
	}

/***
 ** utility methods
 **/

/**
 * Loads given model
 *
 * @param string $model Model to load
 * @return boolean true on success, false on failure
 * @access public
 */
	function loadModel($model = null) {
		return $this->controller->loadModel($model);
	}

/***
 ** logging methods
 **/

	function log($info = null, $data = null, $log = null) {
		if(!$info) return false;
		if(!$log) $log = 'console' . DS . date("Ymd") . '-' . Inflector::underscore($this->name);

		if(is_array($info)) {
			if($data) $log = $data;
			$info = str_replace("\n", "\n\t", substr(print_r($info, true), 0, -1));
		} elseif(is_array($data) || is_object($data)) {
			$info = $info . "\n" . str_replace("\n", "\n\t", substr(print_r($data, true), 0, -1));
		}

		return parent::log($info, $log);
	}

	function error($error_msg, $error_type = E_USER_NOTICE) {

		$types = array(E_USER_ERROR => "error", E_USER_WARNING => "warning", E_USER_NOTICE => "notice");

		$log = SERVER_NAME . DS . SERVER_NAME . '-' . $types[$error_type];
		$hr = "\n\t" . "******** " . strtoupper($types[$error_type]) . " ********" . "\n\t";

		parent::log(SERVER_NAME . ' ~ ' . __CLASS__ . ' ~ ' . $error_msg, 'error');
		parent::log( $hr . $error_msg . $hr . str_replace("\n", "\n\t", Debugger::trace()) . "\n", $log);
		self::log( $hr . $error_msg . $hr . str_replace("\n", "\n\t", Debugger::trace()) . "\n");

		trigger_error($error_msg, $error_type);
	}
}
?>