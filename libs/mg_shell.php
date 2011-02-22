<?php

App::import('Core', 'Debugger');

class MgShell extends Shell {

	var $components = array();
	var $tasks = array();
	var $uses = array();

	function construct() {
		// loading $this->components
		if(!empty($this->components)) {
			foreach($this->components as $componentPath) {
				$component = $this->loadComponent($componentPath);
			}
 		}
	}

/***
 ** utility methods
 **/

/**
 * Setup given shell
 *
 * @param string $model Model to load
 * @return boolean true on success, false on failure
 * @access public
 */
	function setup($shell = null, $task = null, $options = array()) {
		Configure::write('debug', 2);

		$console_path = cygpath(APP, true);
		$shell = $task ? $shell . ' ' . $task : $shell;
		$job = "${console_path}cake $shell";
		$this->TaskHandler->add(Inflector::camelize($shell), $job, $options);

		debug($this->TaskHandler->index());
	}

/**
 * Loads given model
 *
 * @param string $model Model to load
 * @return boolean true on success, false on failure
 * @access public
 */
	function loadModel($model = null) {
		if(!$model) return false;
		$this->{$model} = ClassRegistry::init($model);
		return true;
	}

/**
 * Loads given component
 *
 * @param string $component Component to load
 * @return boolean true on success, false on failure
 * @access public
 */
	function loadComponent($component = null) {
		App::import('Component', $component);
		list($plugin, $component) = pluginSplit($component);
		$componentClass = $component . 'Component';
		$this->{$component} = new $componentClass;
		$this->{$component}->initialize($this);
		if(!empty($this->{$component}->components)) {
			foreach($this->{$component}->components as $subComponent) {
				$this->loadComponent($subComponent);
			}
		}
		return true;
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