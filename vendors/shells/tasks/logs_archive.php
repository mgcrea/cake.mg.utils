<?php

App::import('Lib', 'MgShell', array('plugin' => 'MgUtils'));

class LogsArchiveTask extends MgShell {

	var $name = 'LogsArchive';
	var $plugin = 'MgUtils';
	var $components = array('MgUtils.LogsArchiveTask', 'MgWorkers.TaskHandler');
	var $uses = array();

	var $setup = array('m' => 0, 'h' => 5, 'dow' => 1);

	function execute() {
		self::construct();
		$this->LogsArchiveTask->main();
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
