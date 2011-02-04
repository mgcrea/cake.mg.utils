<?php

App::import('Lib', 'MgShell', array('plugin' => 'MgUtils'));

class DatabaseBackupTask extends MgShell {

	var $name = 'DatabaseBackup';
	var $components = array('MgUtils.DatabaseBackupTask', 'MgWorkers.TaskHandler');
	var $uses = array();

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
		parent::setup('mg_utils', Inflector::underscore($this->name), array('m' => 0, 'h' => 0));
	}

}

?>