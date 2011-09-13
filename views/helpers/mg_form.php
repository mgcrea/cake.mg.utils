<?php

class MgFormHelper extends FormHelper {

	var $helpers = array('Magenta.MgHtml', 'Html');

	function __construct($paths = array()) {
		//$this->View =& ClassRegistry::getObject('view');
	}

	function input($name = null, $options = array()) {
		if(!$name) return false;

		$defaults = array(
			'role' => 'input',
			'type' => 'text',
			'aria-disabled' => 'false',
			'div' => null,
			'class' => null,
			'size' => null,
			'data' => array(),
			'ui' => array(),
			'options' => array(),
			'before' => null,
			'between' => null,
			'after' => null
		);
		$options = array_merge($defaults, $options);

		# generic preProcess
		$this->_preProcess($name, $options);

		$inputSize = array('name' => 24, 'duration' => 5, 'msduration' => 8, 'calendar' => 8, 'path' => 38);
		if($options['size'] == 'calendar') $options['type'] = 'text';
		if($options['size'] && !is_numeric($options['size'])) $options['size'] = $inputSize[$options['size']];
		if(!isset($options['loader'])||$options['loader']) $options['after'] = '<div class="ui-input icon loader hide"></div>' . $options['after'];

		if($name == "password") $options['type'] = "password";

		//if(isset($options['label'])&&$options['label']&&!preg_match("/\:/", $options['label'])) $options['label'].= '&nbsp;:';
		//else
		if(!isset($options['label'])) $options['label'] = false;

		# placeholder
		if(!empty($options['default'])) $options['data']['placeholder'] = $options['default'];

		# generic postProcess
		$this->_postProcess($name, $options);

		# radio & checkbox support
		if($options['type'] == 'select') {
			if(!empty($options['default'])) {
				$options['selected'] = $options['default'];
				unset($options['default']);
			}
		} elseif($options['type'] == 'radio') {
			foreach($options['options'] as $for => &$option) $option = $this->label($for, $this->MgHtml->link($option, '#', array('ui' => 'button trigger-radio', 'overlay' => true, 'escape' => false)));
			$options = array_merge($options, array('escape' => false));

			if(!empty($options['default'])) {
				//does not work cf. l.1100 formhelper
				//$options['options'][$options['default']] = array('text' => $options['options'][$options['default']], 'class' => "checked");
			}

		} else if($options['type'] == 'checkbox') {
			foreach($options['options'] as $for => &$option) $option = $this->MgHtml->span(null, 'ui-icon') . $this->MgHtml->span($option, 'ui-text'); //$this->MgHtml->link($option, '#', array('ui' => "button trigger-checkbox", 'overlay' => true, 'escape' => false));
			if(!empty($options['legend'])) $options['label'] = $options['legend']; unset($options['legend']);
			$options = array_merge($options, array('escape' => false, 'type' => 'select', 'multiple' => 'checkbox'));
		}

		/*if((!isset($options['multiple']) || !$options['multiple']) && (!isset($options['default']) || !$options['default'])) {

			if(strpos($name, '.')) $selected = Set::classicExtract($this->view->data, $name);
			else $selected = Set::classicExtract($this->view->data, $this->Form->model() . '.' . $name);

			if($selected) {
				if(!is_array($options['label'])) $options['label'] = array('class' => "selected", 'text' => $options['label']);
				else $options['label']['class'] = (isset($options['label']['class'])?$options['label']['class'].' ':'') . "selected";
			}

		}*/

		return parent::input($name, $options);
	}

	function label($for = null, $content = null, $options = array()) {
		if(is_string($options)) $options = array('class' => $options);

		$defaults = array(
			'icon' => null,
			'text' => null,
			'class' => null,
			'disabled' => false,
			'colorize' => false,
			'escape' => false
		);
		$options = array_merge($defaults, $options);

		if($content && $options['colorize']) $content = $this->_colorize($content);

		return parent::label($for, $content, $options);
	}

	function button($content = null, $options = array()) {

		/*$inputOptions = !empty($options['input']) ? $options['input'] : array('class' => null);
		$inputOptions['class'] .= 'ui-text ui-button-text' . (!empty($options['text']) ? ' ' . $options['text'] : null);
		unset($options['input'], $options['text']);*/

		$action = !empty($options['type']) ? $options['type'] : '#';
		unset($options['type']);

		//return $this->MgHtml->div($this->Html->tag('input', null, array_merge($inputOptions, array('type' => "button", 'value' => $content))), $options);
		return $this->MgHtml->link($content, $action, $options);

	}

	function submit($content = null, $options = array()) {

		$inputOptions = !empty($options['input']) ? $options['input'] : array('class' => null);
		$inputOptions['class'] .= 'ui-text ui-button-text' . (!empty($options['text']) ? ' ' . $options['text'] : null);
		unset($options['input'], $options['text']);

		return $this->MgHtml->div($this->Html->tag('input', null, array_merge($inputOptions, array('type' => "submit", 'value' => $content))), $options);

	}

	function checkbox($name = null, $options = array()) {
		if(!$name) return false;

		$defaults = array(
			'role' => 'input',
			'type' => 'checkbox',
			'multiple' => 'checkbox',
			'aria-disabled' => 'false',
			'div' => null,
			'class' => null,
			'size' => null,
			'data' => array(),
			'ui' => array(),
			'options' => array(),
			'before' => null,
			'between' => null,
			'after' => null
		);
		$options = array_merge($defaults, $options);

		foreach($options['options'] as &$option) {
			if(is_array($option)) {
				foreach($option as &$subOption) {
					$subOption = $this->MgHtml->span(null, 'ui-icon') . $this->MgHtml->span($subOption, 'ui-text');
				}
			} else {
				$option = $this->MgHtml->span(null, 'ui-icon') . $this->MgHtml->span($option, 'ui-text');
			}
		}
		// generic preProcess
		$this->_preProcess($name, $options);

		if(!empty($options['legend'])) $options['label'] = $options['legend']; unset($options['legend']);

		// patch to handle editing
		if((!isset($options['multiple']) || !$options['multiple']) && (!isset($options['default']) || !$options['default'])) {

			if(strpos($name, '.')) $selected = Set::classicExtract($this->view->data, $name);
			else $selected = Set::classicExtract($this->view->data, $this->Form->model() . '.' . $name);

			if($selected) {
				if(!is_array($options['label'])) $options['label'] = array('class' => "selected", 'text' => $options['label']);
				else $options['label']['class'] = (isset($options['label']['class'])?$options['label']['class'].' ':'') . "selected";
			}

		}

		// generic postProcess
		$this->_postProcess($name, $options);

		// specific postProcess
		$options = array_merge($options, array('escape' => false, 'type' => 'select', 'multiple' => 'checkbox'));

		return parent::input($name, $options);
	}

	function end($content = null, $options = array()) {

		if($content) $content = $this->submit($content, $options);
		return $content . '</form>';

	}

	function _preProcess(&$content = null, &$options = array()) {
		# ui conversion to array
		if(is_string($options['ui'])) $options['ui'] = explode(' ', $options['ui']);

		# wrap
		$options['after'] = (!empty($options['between']) ? $options['between'] : null) . '</div>' . (!empty($options['after']) ? $options['after'] : null);
		$options['between'] = '<div class="wrap">';
	}

	function _postProcess(&$content = null, &$options = array()) {

		# disabled state handling
		if(!empty($options['disabled'])) {
			$options['aria-disabled'] = 'true';
			$options['ui'][] = 'state-disabled';
		}

		# ui class tag handling
		foreach(array_unique($options['ui']) as $key => $val) {
			if($options['class']) $options['class'] .= ' ui-'.$val;
			else $options['class'] = 'ui-'.$val;
		}
		unset($options['ui']);

		# data tag handling
		foreach($options['data'] as $key => $val) {
			$options['data-'.$key] = is_array($val)?json_encode($val):$val;
		}
		unset($options['data']);

		# surcharge div class with type (text, radio, etc.)
		$options['div'] = array('class' => 'input ' . $options['type'] . (!empty($options['div']) ? ' ' . $options['div'] : null));
		if($options['type'] == 'password') $options['div']['class'] .= ' text';

		# markup injection handler
		//$content = $options['before'] . $content . $options['after'];
		//unset($options['before'], $options['after']);

	}

	function _colorize($text = null) {
		return preg_replace("#c([0-9]+)\{(.*)\}#isU","<span class=\"color-$1\">$2</span>", $text);
	}

}

/*
 function radiobox($name = null, $options = array()) {
		if(!$name) return false;

		$defaults = array(
			'role' => 'input',
			'type' => 'radio',
			'aria-disabled' => 'false',
			'div' => 'radio',
			'class' => null,
			'size' => null,
			'data' => array(),
			'ui' => array(),
			'options' => array(),
			'before' => null,
			'between' => null,
			'after' => null
		);
		$options = array_merge($defaults, $options);

		foreach($options['options'] as &$option) $option = $this->MgHtml->link($option, '#', array('class' => "ui-trigger-radio", 'ui' => "button", 'overlay' => true, 'escape' => false));

		# generic preProcess
		$this->_preProcess($name, $options);

		if(!empty($options['label'])) $options['legend'] = $options['label']; unset($options['label']);

		# generic postProcess
		$this->_postProcess($name, $options);

		# specific postProcess
		//$radioOptions = $options['options'];
		//unset($options['options']);

		# specific postProcess
		$options = array_merge($options, array('escape' => false, 'type' => 'radio', 'multiple' => 'radio'));

		//debug($options);

		return parent::input($name, $options);

	}



	function checkboxOld($name = null, $options = array()) {
		if(!$name) return false;
		$type = "checkbox";

		if(isset($options['options'])&&$options['options']) {
			$options['multiple'] = 'checkbox';
		}

		# hidden div
		if(isset($options['hidden'])&&$options['hidden']) $options['div'] .= " ui-hidden";

		# options div surcharge with type
		if(isset($options['div'])&&$options['div']) $options['div'] = array('class' => 'input' . " " . $type . " " . $options['div']);
		else $options['div'] = array('class' => "input " . $type);
		if(isset($options['disabled'])&&$options['disabled']) $options['div']['class'] .= ' ui-disabled';

		# data tag handling
		if(isset($options['data'])) {
			foreach($options['data'] as $dataKey => $dataVal) {
				if(isset($options['data']['div'])&&!$options['data']['div']) $options['data-'.$dataKey] = is_array($dataVal)?json_encode($dataVal):$dataVal;
				else $options['div']['data-'.$dataKey] = is_array($dataVal)?json_encode($dataVal):$dataVal;
			}
			unset($options['data']);
		}



		return $this->Form->input($name, $options);
	}*/

?>
