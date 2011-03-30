<?php

class LessHelper extends Helper {

	function __construct($paths=array()) {

		App::import('Vendor', 'lessphp', array('file' => 'lessc.inc.php'));
		//$this->View =& ClassRegistry::getObject('view');
	}

	function generate($sourceFiles = array(), $options = array()) {
		if(!$sourceFiles) return false;

		$defaults = array(
			'source' => CSS . 'source',
			'dest' => CSS,
			'file' => "master",
		);
		$options = array_merge($defaults, $options);

		if($sourceFiles == '*') $sourceFiles = lsfile($options['source'], '.less');

		if(!preg_match('/\\' . DS . '$/', $options['source'])) $options['source'] .= DS;
		if(!preg_match('/\\' . DS . '$/', $options['dest'])) $options['dest'] .= DS;

		App::import('Vendor', 'lessc', array('file' => 'lessphp' . DS . 'lessc.inc.php'));

		$cssDest = $options['dest'] . $options['file'] . '.css';
		if(is_file($cssDest)) @unlink($cssDest);

		foreach($sourceFiles as $sourceFile) {
			if(!preg_match('/(.css|.less)$/is', $sourceFile)) $sourceFile .= '.less';
			$less = new lessc($options['source'] . $sourceFile);
			file_put_contents($cssDest, $less->parse(), FILE_APPEND);
		}

	}

}
?>
