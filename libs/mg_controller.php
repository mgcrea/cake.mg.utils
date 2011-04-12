<?php

class MgController extends Controller {

	var $uses = array();

	function beforeFilter() {

	}

	function beforeRender() {

		// set caching headers
		if(Configure::read('debug') > 0) {
			$this->disableCache();
		}

		// define global view vars
		if (!isset($this->viewVars['debug'])) {
			$this->set('debug',  Configure::read('debug'));
		}
		if (!isset($this->viewVars['modelClass'])) {
			$this->set('modelClass', $this->modelClass);
		}
		if (!isset($this->viewVars['modelKey'])) {
			$this->set('modelKey', $this->modelKey);
		}
		if (!isset($this->viewVars['controllerClass'])) {
			$this->set('controllerClass', $this->name);
		}
		if (!isset($this->viewVars['controllerKey'])) {
			$this->set('controllerKey', Inflector::underscore($this->name));
		}

	}

/***
 ** utility methods
 **/

/**
 * Loads given component
 *
 * @param string $component Component to load
 * @return void
 * @access public
 */
	function loadComponent($component, $options = array()) {
		App::import('Component', $component, $options);
		list($plugin, $component) = pluginSplit($component);
		$class = $component . 'Component';
		$this->{$component} = new $class();
		$this->{$component}->initialize($this);
	}

	function json($action = null, $result = null, $return = false) {

		if(is_string($result)) $result = array('info' => $result);
		if(is_numeric($action) && empty($result['status_code'])) $result = array('status_code' => $action, 'data' => $result);
		if(empty($result['status_code'])) $result['status_code'] = $action ? !empty($result['errors']) ? 206 : 200 : !empty($result['errors']) ? 400 : 500;

		$httpStatus = array(
			'100' => "Continue",
			'200' => "Successful",
			'206' => "Partial Content",
			'400' => "Bad Request",
			'401' => "Forbidden",
			'403' => "Forbidden",
			'404' => "Not Found",
			'500' => "Internal Server Error"
		);

		$defaults = array(
			'status_code' => 500,
			'status_log' => $result['status_code'] . ' : ' . (!empty($httpStatus[$result['status_code']]) ? $httpStatus[$result['status_code']] : '?') . (!empty($result['info']) ? ' - ' . $result['info'] : null),
			'success' => in_array($result['status_code'], array('100', '200', '206')) ? true : false,
		);
		$result = array_merge($defaults, $result);

		if($return) return json_encode($result);
		$this->set(compact('result'));
		return $this->render('/layouts/json/default', 'ajax');

	}

	function clearCache() {

		Configure::write('debug', 2);
		$cacheFolders = array(CACHE, WWW_ROOT . 'cache' . DS . 'js', WWW_ROOT . 'cache' . DS . 'css');
		foreach($cacheFolders as $folder) {
			rrmfile($folder, 'empty');
		}
		$this->redirect('/');

	}

/***
 ** logging methods
 **/

	function log($info = null, $data = null, $log = null) {
		if(!$info) return false;
		if(!$log) $log = 'console' . DS . date("Ymd") . '-' . Inflector::underscore($this->name);

		if(is_array($info)) {
			if(is_string($data)) $log = $data;
			$info = str_replace("\n", "\n\t", substr(print_r($info, true), 0, -1));
		} elseif(is_array($data) || is_object($data)) {
			$info .= "\n\t" . str_replace("\n", "\n\t", substr(print_r($data, true), 0, -1));
		} elseif(is_string($data)) {
			$info .= "\n\t" . str_replace("\n", "\n\t", substr(print_r($data, true), 0, -1));
		}

		return parent::log($info, $log);
	}

	function error($error_msg, $error_type = E_USER_NOTICE, $log = null) {
		if(!$log) $log = 'console' . DS . SERVER_NAME . '-' . $types[$error_type];

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
