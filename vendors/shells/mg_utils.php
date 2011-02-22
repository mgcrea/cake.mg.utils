<?php

App::import('Lib', 'MgShell', array('plugin' => 'MgUtils'));

class MgUtilsShell extends MgShell {

	var $name = 'MgUtils';
	var $tasks = array('DatabaseBackup');

/***
 ** component methods
 **/

	function main() {

		self::construct();
		Configure::write('debug', 2);
		debug(array('Task' => $this->tasks));
		exit;

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
		//parent::setup(Inflector::underscore($this->name));
	}
}
?>