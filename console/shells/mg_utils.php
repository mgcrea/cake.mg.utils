<?php

App::import('Lib', 'MgShell', array('plugin' => 'MgUtils'));

class MgUtilsShell extends MgShell {

	var $name = 'MgUtils';
	var $tasks = array();
	var $components = array('DatabaseBackupTask', 'LogsArchiveTask');

/***
 ** component methods
 **/

	function main() {
		self::construct();

		// requested a taskComponent
		$last = Inflector::classify(end($_SERVER['argv']));
		if(in_array($last, $this->components)) {
			$this->$last->main();
		} else {
			debug(array(
				'action' => __FUNCTION__,
				'ServerName' => SERVER_NAME,
				'ServerArgs' => $_SERVER['argv'],
				'Components' => $this->components,
				'Tasks' => $this->tasks
			));
		}
	}

/**
 * Setup taskComponent
 */
	function setup() {
		self::construct();
		$this->loadComponent("MgWorkers.TaskHandler");

		// setup a taskComponent
		$last = Inflector::classify(end($_SERVER['argv']));
		if(in_array($last, $this->components)) {
			parent::setup(Inflector::underscore($this->name), Inflector::underscore($this->{$last}->name), $this->{$last}->config['setup']);
		} else {
			debug(array(
				'action' => __FUNCTION__,
				'ServerName' => SERVER_NAME,
				'ServerArgs' => $_SERVER['argv'],
				'Components' => $this->components,
				'Tasks' => $this->tasks
			));
		}
		//parent::setup($this->shell, Inflector::underscore($this->name));
	}

/***
 ** callback methods
 **/

	function construct() {
		parent::construct();
	}

}
?>
