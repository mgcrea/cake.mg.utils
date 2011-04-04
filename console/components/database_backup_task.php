<?php

App::import('Lib', 'MgComponent', array('plugin' => 'MgUtils'));

class DatabaseBackupTaskComponent extends MgComponent {

	var $name = 'DatabaseBackupTask';
	var $plugin = 'MgUtils';

	var $uses = array();
	var $components = array();

	var $config = array(
		'setup' => array('m' => 0, 'h' => 5),
		'history' => 30,
		'cygwin' => 'C:\\cygwin',
		'mysqldump' => 'mysqldump',
		'debug' => true
	);

/***
 ** component methods
 **/

	function main() {

		$backupFolder = APP . 'archive' . DS . 'sql';
		if(!is_dir($backupFolder)) rmkdir($backupFolder);

		$status = array();
		foreach(parent::servers()  as $serverName => $serverConfig) {
			$dbConfig = $this->dbConnect($serverConfig);
			$status[$serverName] = $this->_databaseBackupTask($dbConfig, $serverName);
		}

		// rotate archives
		$cmd = 'find ' . $backupFolder . ' -type f -mtime +' . $this->config['history'] . ' -delete';
		$exec = proc_exec($cmd);

		$this->log($status);
		exit;
	}

	function _databaseBackupTask($dbConfig, $serverName) {

		$backupFolder = APP . 'archive' . DS . 'sql';
		$backupName = $serverName . '-`date +\%Y\%m\%d-\%H\%M\%S`.sql.gz';
		$backupPath = $backupFolder . DS . $backupName;

		if(IS_WIN) {
			$cmd = $this->config['cygwin'] . '\\bin\\bash.exe -c "' . cygpath($this->config['mysqldump']) . ' --opt -u' . $dbConfig['login'] . ' -p' . $dbConfig['password'] . ' ' . $dbConfig['database'] . ' --single-transaction | gzip > ' . cygpath($backupPath) . '"';
		} else {
			$cmd = $this->config['mysqldump'] . ' --opt -u' . $dbConfig['login'] . ' -p' . $dbConfig['password'] . ' ' . $dbConfig['database'] . ' --single-transaction | gzip > ' . $backupPath;
		}

		$exec = proc_exec($cmd);
	}

/**
 * config() is fired after initialize()
 */
	function config($settings = array()) {

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
