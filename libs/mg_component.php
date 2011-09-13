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

		$this->RequestHandler =& $this->controller->RequestHandler;
		$this->Auth =& $this->controller->Auth;

		// loading $this->uses
		foreach($this->uses as $model) {
			$this->loadModel($model);
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
		if($this->controller->loadModel($model)) {
			list($plugin, $model) = pluginSplit($model);
			$this->{$model} =& $this->controller->{$model};
			return true;
		}
		return false;
	}

/**
 * Connects to specified database
 *
 * @param array $config Server config to use {datasource:?, database:?}
 * @return array db->config on success, false on failure
 * @access public
 */
	function dbConnect($config = array()) {
		ClassRegistry::init('ConnectionManager');

		/*$cm =& ConnectionManager::getInstance();
		$db =& $cm->getDataSource($config['datasource']);
		$db->setConfig(array('database' => $config['database'], 'persistent' => false));
		$db->disconnect();
		return $db->connect();*/

		$nds = $config['datasource'] . '_' . $config['database'];
		$db =& ConnectionManager::getDataSource($config['datasource']);
		$db->setConfig(array('name' => $nds, 'database' => $config['database'], 'persistent' => false));
		if($ds = ConnectionManager::create($nds, $db->config)) return $db->config;
		return false;

	}

/**
 * Returns servers array
 *
 * @return array serverConfig
 * @access public
 */
	function servers() {

		Configure::load('servers');
		Configure::load('defaults');

		$servers = array_diff_key(Configure::read('Servers'), array('console' => null));
		foreach($servers as $serverName => &$serverConfig) {
			// load serverConfig
			$serverConfig = array_merge(Configure::read('ServerDefaults'), $serverConfig);
			// load task options from serverConfig
			if(!empty($serverConfig['workers'][(!empty($this->plugin)? $this->plugin . '.' : null) . $this->name])) $this->config = array_merge($this->config, $serverConfig['workers'][$this->plugin . '.' . $this->name]);
		} unset($serverConfig);

		Configure::delete('Servers');
		Configure::delete('ServerDefaults');

		return $servers;
	}

}
?>
