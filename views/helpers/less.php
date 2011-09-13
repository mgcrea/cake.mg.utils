<?php

class LessHelper extends Helper {

	function __construct($paths=array()) {

		App::import('Vendor', 'lessphp', array('file' => 'lessc.inc.php'));
		//$this->View =& ClassRegistry::getObject('view');
	}

	function generate($sourceFiles, $options = array()) {
		if(!is_array($sourceFiles)) $sourceFiles = array($sourceFiles);

		$defaults = array(
			'source' => CSS . 'source',
			'dest' => CSS,
			'file' => "master",
			'set' => array()
		);
		$options += $defaults;

		if($sourceFiles == '*') $sourceFiles = lsfile($options['source'], '.less');

		if(!preg_match('/\\' . DS . '$/', $options['source'])) $options['source'] .= DS;
		if(!preg_match('/\\' . DS . '$/', $options['dest'])) $options['dest'] .= DS;

		$cssDest = $options['dest'] . $options['file'] . '.css';
		if(is_file($cssDest)) @unlink($cssDest);

		App::import('Vendor', 'lessc', array('file' => 'lessphp' . DS . 'lessc.inc.php'));

		foreach($sourceFiles as $sourceFile) {
			if(!preg_match('/(.css|.less)$/is', $sourceFile)) $sourceFile .= '.less';
			$less = new lessc($options['source'] . $sourceFile, array('compatible' => false, 'set' => $options['set']));
			file_put_contents($cssDest, $less->parse(), FILE_APPEND);
		}

	}

}
?>
