<?php
/**
 * Copyright 2011, Magenta Creations (http://mg-crea.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2011, Magenta Creations (http://mg-crea.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * MgUtils Plugin
 *
 * MgUtils Lockable Behavior
 *
 * @package mg_utils
 * @subpackage mg_utils.models.behaviors
 */
class LockableBehavior extends ModelBehavior {

/**
 * Settings
 *
 * @var mixed
 */
	public $settings = array();

/**
 * Default settings
 *
 * @var array
 */
	protected $_defaults = array(
	);

/**
 * Setup
 *
 * @param object AppModel
 * @param array $config
 */
	public function setup(&$Model, $config = array()) {
		$settings = array_merge($this->_defaults, $config);
		$this->settings[$Model->alias] = $settings;
	}

/**
 * Before find callback
 *
 * @param mixed $results The results of the find operation
 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 */
	function beforeFind(&$Model, $queryData = array()) {

		// option to lock table
		if(!empty($queryData['lock'])) {
			$sql = "LOCK TABLES `{$Model->table}` WRITE, `{$Model->table}` AS `{$Model->alias}` WRITE;";
			if($Model->query($sql)) $Model->locked = true;
		}
	}

/**
 * Called after each save operation
 */
	function afterSave(&$Model, $created) {
		// unlock tables
		if(!empty($this->locked)) {
			$sql = "UNLOCK TABLES";
			$Model->query($sql);
		}
	}

}
