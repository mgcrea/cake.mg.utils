<?php

App::import('Lib', 'MgShell', array('plugin' => 'MgUtils'));

class DatabaseBackupTask extends MgShell {

	var $name = 'DatabaseBackup';
	var $plugin = 'MgUtils';
	var $components = array('MgUtils.DatabaseBackupTask', 'MgWorkers.TaskHandler');
	var $uses = array();

	var $setup = array('m' => 0, 'h' => 0);

	function execute() {
		self::construct();
		$this->DatabaseBackupTask->main();
	}

/***
 ** callback methods
 **/

	function construct() {
		parent::construct();
	}


/**
 * Is fired from a controller to set-up cron task
 */
	function setup() {
		self::construct();
		parent::setup(Inflector::underscore($this->plugin), Inflector::underscore($this->name), $this->setup);
	}

}

?>