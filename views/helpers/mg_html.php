<?php

class MgHtmlHelper extends HtmlHelper {

	var $helpers = array();

	function __construct($paths = array()) {
		$this->View =& ClassRegistry::getObject('view');
	}

	function link($content = null, $action = array(), $options = array()) {

		$defaults = array(
			'role' => 'button',
			'aria-disabled' => 'false',
			'class' => null,
			'icon' => null,
			'text' => null,
			'icon-right' => null,
			'overlay' => false,
			'action' => array(),
			'escape' => false,
			'data' => array(),
			'ui' => array(),
			'before' => null,
			'after' => null
		);
		$options = array_merge($defaults, $options);

		// generic preProcess
		$this->_preProcess($content, $options);

		// overlay handler
		if(!empty($options['overlay'])) {
			$options['before'] = $this->span(null, 'ui-' . $options['role'] . '-overlay') . $options['before'];
		}
		unset($options['overlay']);

		// wrap content in span
		$content = $this->span($content, trim('ui-text ui-' . $options['role'] . '-text ' . $options['text']));
		unset($options['text']);

		// handle action
		if(!empty($action)) {
			$this->_processAction($options, $action);
		}

		# js message handling
		$message = false;
		if(!empty($options['message'])) {
			$message = $options['message'];
		}
		unset($options['message']);

		unset($options['action']);

		# generic postProcess
		$this->_postProcess($content, $options);

		return parent::link($content, $action, $options, $message);

	}

	function div($content = null, $options = array()) {

		// retro compatibility if args are class, content, options
		if(!is_array($options)) {
			$args = func_get_args();
			$content = $args[1];
			$options = array('class' => $args[0]);
			if(!empty($args[2])) $options = array_merge($options, $args[2]);
			//debug($args); exit;
		}

		$defaults = array(
			'class' => null,
			'icon' => null,
			'text' => null,
			'data' => array(),
			'ui' => array(),
			'before' => null,
			'after' => null,
			'escape' => false
		);
		$options = array_merge($defaults, $options);

		# generic preProcess
		$this->_preProcess($content, $options);

		# generic postProcess
		$this->_postProcess($content, $options);

		return parent::tag('div', $content, $options);

	}

	function ul($content = null, $options = array()) {

		$defaults = array(
			'class' => null,
			'icon' => null,
			'text' => null,
			'data' => array(),
			'ui' => array(),
			'before' => null,
			'after' => null,
			'escape' => false
		);
		$options = array_merge($defaults, $options);

		# generic preProcess
		$this->_preProcess($content, $options);

		# generic postProcess
		$this->_postProcess($content, $options);

		return parent::tag('ul', $content, $options);

	}

	function li($content = null, $options = array()) {

		$defaults = array(
			'class' => null,
			'icon' => null,
			'text' => null,
			'action' => array(),
			'link' => array(),
			'data' => array(),
			'ui' => array(),
			'before' => null,
			'after' => null,
			'escape' => false
		);
		$options = array_merge($defaults, $options);

		# generic preProcess
		$this->_preProcess($content, $options);

		if(!empty($options['action'])) {
			array_mv_keys($options, $options['link'], array('icon', 'text', 'target'));
			$content = $this->link($content, $options['action'], array_merge($options['link'], array('role' => 'list-item', 'escape' => false)));
		} else {
			// wrap content in span
			$content = $this->span($content, trim('ui-text ' . $options['text']));
		}
		unset($options['link'], $options['action'], $options['text']);

		# generic postProcess
		$this->_postProcess($content, $options);

		return parent::tag('li', $content, $options);

	}

	function span($content = null, $options = array()) {
		if(is_string($options)) $options = array('class' => $options);

		$defaults = array(
			'icon' => null,
			'text' => null,
			'class' => null,
			'disabled' => false,
			'colorize' => false
		);
		$options = array_merge($defaults, $options);

		if($content && $options['colorize']) $content = $this->_colorize($content);

		//if($options['class'] != $defaults['class']) $options['class'] = $defaults['class'] . " " . $options['class'];

		# content
		//$content = $this->span($content, 'ui-button-text');
		if(!empty($options['icon'])) {
			$content = $this->span(null, 'ui-icon ui-icon-' . $options['icon']) . $content;
			$options['ui'][] = 'button-icon';
		}
		unset($options['icon']);

		if($options['text']) $options['class'] .= ' ' . (string)$options['text'];

		unset($options['icon'], $options['text']);

		return parent::tag('span', (string)$content, $options);
	}

	function h1($content = null, $options = null) {
		// generic preProcess
		$this->_preProcess($content, $options);

		return parent::tag('h1', $content, $options);
	}

	function h2($content = null, $options = null) {
		// generic preProcess
		$this->_preProcess($content, $options);

		return parent::tag('h2', $content, $options);
	}

	function h3($content = null, $options = null) {
		// generic preProcess
		$this->_preProcess($content, $options);

		return parent::tag('h3', $content, $options);
	}

	function video($content = null, $options) {
		//if(is_string($options)) $options = array('src' => $options);

		$defaults = array(
			'type' => "video/mp4",
			'class' => null,
			'autoplay' => false,
			'loop' => false
		);
		$options = array_merge($defaults, $options);

		$source = parent::tag('source', null, array('src' => $content, 'type' => $options['type']));
		unset($options['src'], $options['type']);

		return parent::tag('video', $source, array_merge($options, array('escape' => false)));
	}

	function _preProcess(&$content = null, &$options = array()) {
		# ui conversion to array
		if(is_string($options['ui'])) $options['ui'] = explode(' ', $options['ui']);
	}

	function _postProcess(&$content = null, &$options = array()) {

		// disabled state handling
		if(!empty($options['disabled'])) {
			$options['aria-disabled'] = 'true';
			$options['ui'][] = 'state-disabled';
		}

		// icon handling
		if(!empty($options['icon'])) {

			// handle special chars
			$specialChars = array('$', '{', '<');
			$startsWithSpecialChar = in_array($options['icon'][0], $specialChars);

			// handle icon.variant format
			if(strpos($options['icon'], '.') && !$startsWithSpecialChar) list($options['icon'], $options['icon-variant']) = explode('.', $options['icon']);

			$innerContent = null;
			if(!empty($options['icon-variant'])) {
				$innerContent = $this->span(null, 'ui-icon-variant ui-icon-' . implode(' ui-icon-', explode(' ', $options['icon-variant'])));
			}

			if(in_array($options['icon'][0], array('<'))) $content = $this->span($options['icon'], array('class' => 'ui-icon', 'escape' => false)) . $content;
			else $content = $this->span($innerContent, 'ui-icon ui-icon-' . implode(' ui-icon-', explode(' ', $options['icon']))) . $content;

			$options['ui'][] = 'has-icon';
			$options['ui'][] = 'has-primary-icon';
		}
		if(!empty($options['icon-right'])) {
			$content = $content . $this->span(null, 'ui-icon ui-icon-' . $options['icon-right']);
			$options['ui'][] = 'has-icon';
			$options['ui'][] = 'has-icon-secondary';
		}
		unset($options['icon'], $options['icon-variant'], $options['icon-right']);

		// ui class tag handling
		foreach($options['ui'] as $key => $val) {
			// handle value as key => boolean
			if(is_string($key)) $options['class'] .= $val ? ' ui-' . $key : null;
			else $options['class'] .= ' ui-' . $val;
		}
		unset($options['ui']);

		// data tag handling
		foreach($options['data'] as $key => $val) {
			$options['data-'.$key] = is_array($val)?json_encode($val):$val;
		}
		unset($options['data']);

		// markup injection handler
		$content = $options['before'] . $content . $options['after'];
		unset($options['before'], $options['after']);

		// trim classes
		$options['class'] = trim($options['class']);

	}

	function _processAction(&$options, $action) {

		$class =& $options['class'];
		$data =& $options['data'];

		if(is_string($action)) {
			if($action[0] == "#" && strlen($action) > 1) {
				$class .= ' ui-action-' . substr($action, 1);
			}
		} elseif(is_array($action)) {
			if(!empty($action['controller'])) {
				$class .= ' ui-controller-' . $action['controller'];
				$data['controller'] = $action['controller'];
			}
			if(!empty($action['action'])) {
				$class .= ' ui-action-' . $action['action'];
				$data['action'] = $action['action'];
			}
		}
	}

	function _colorize($text = null) {
		return preg_replace("#c([1-9]+)\((.*)\)#isU","<span class=\"color$1\">$2</span>", $text);
	}


}
?>
