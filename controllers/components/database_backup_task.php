<?php

App::import('Lib', 'MgComponent', array('plugin' => 'MgUtils'));

class DatabaseBackupTaskComponent extends MgComponent {

	var $name = 'DatabaseBackupTask';
	var $uses = array();
	var $components = array();

/***
 ** component methods
 **/

	function main() {
		Configure::write('debug', 2);

		$status = array();
		foreach(Configure::read('Servers') as $serverName => $serverConfig) {
			$serverConfig = array_merge(Configure::read('ServerDefaults'), $serverConfig);
			$dbConfig = $this->dbConnect($serverConfig);
			$result = $this->_databaseBackupTask($dbConfig);
			$status[$serverName] = $result;
		}
		debug($status);

		exit;
	}

	function _databaseBackupTask($dbConfig) {

		$backupFolder = TMP . 'sql';
		if(!is_dir($backupFolder)) rmkdir($backupFolder);

		$backupName = SERVER_NAME . '-`date +\%Y\%m\%d-\%H\%M\%S`.sql.gz';
		$backupPath = $backupFolder . DS . $backupName;

		$job = 'mysqldump --opt -u' . $dbConfig['login'] . ' -p' . $dbConfig['password'] . ' ' . $dbConfig['database'] . ' --single-transaction | gzip > ' . $backupPath;

		return proc_exec($job);
	}

/**
 * config() is fired after initialize()
 */
	function config($settings = array()) {

		Configure::load('servers');
		Configure::load('defaults');

	}

/***
 ** callback methods
 **/

/**
 * initialize() is fired before the controller's beforeFilter, but after models have been constructed.
 */
	function initialize(&$controller, $settings = array()) {
		parent::initialize($controller, $settings);

		$this->config($settings);
	}

/**
 * startup() is fired after the controllers' beforeFilter, but before the controller action.
 */
	function startup(&$controller) {

	}

/**
 * beforeRender() is fired before a view is rendered.
 */
	function beforeRender(&$controller) {

		Configure::delete('Servers');
		Configure::delete('ServerDefaults');

	}

/**
 * beforeRedirect() is fired before a redirect is done from a controller. You can use the return of the callback to replace the url to be used for the redirect.
 */
	function beforeRedirect(&$controller, $url, $status = null, $exit = true) {

	}

/**
 * shutdown() is fired after the view is rendered and before the response is returned.
 */
	function shutdown(&$controller) {

	}

}
?>
