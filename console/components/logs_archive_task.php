<?php

App::import('Lib', 'MgComponent', array('plugin' => 'MgUtils'));

class LogsArchiveTaskComponent extends MgComponent {

	var $name = 'LogsArchiveTask';
	var $plugin = 'MgUtils';

	var $uses = array();
	var $components = array();

	var $config = array(
		'setup' => array('m' => 0, 'h' => 5, 'dow' => 1),
		'history' => '30',
		'cygwin' => 'C:\\cygwin',
		'debug' => true
	);

/***
 ** component methods
 **/

	function main() {
		Configure::write('debug', 2);

		$backupFolder = APP . 'archive' . DS . 'logs';
		if(!is_dir($backupFolder)) rmkdir($backupFolder);

		$fileName = 'logs_' . date('Ymd_His') . '.tgz';

		// archive logs
		if(IS_WIN) {
			$cmd = 'cd "' . addslashes(LOGS) . '" && tar --exclude=\'empty\' -cvzf ' . cygpath($backupFolder . DS . $fileName) . ' *';
		} else {
			$cmd = 'cd ' . LOGS . ' && tar --exclude=\'empty\' -cvzf ' . $backupFolder . DS . $fileName . ' .';
		}
		$exec = proc_exec($cmd);
		debug($exec);

		// rotate archives
		$cmd = 'find ' . $backupFolder . ' -type f -mtime +' . $this->config['history'] . ' -delete';
		$exec = proc_exec($cmd);
		debug($exec);

		// delete logs from tmp
		rrmfile(LOGS, 'empty');

		/*App::import('Vendor', 'archive');
		$archive = new gzip_file(TMP . 'archive' . DS . 'logs_' . date('Ymd_His') . '.tgz');
		$archive->set_options(array('basedir' => LOGS, 'overwrite' => 1, 'level' => 1));
		$archive->add_files(array('*'));
		$archive->exclude_files(array('empty'));
		$archive->create_archive();
		*/

		/*foreach(lsdir(LOGS) as $folder) {
			App::import('Vendor', 'archive');

			$archive = new gzip_file(TMP . 'archive' . DS . date('Ymd_His') . '_' . $folder . '.tgz');
			$archive->set_options(array('basedir' => LOGS . $folder, 'overwrite' => 1, 'level' => 1));
			$archive->add_files(array('*.log'));
			$archive->create_archive();

			rrmfile(LOGS . $folder, 'empty');
		}*/

		exit;
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
