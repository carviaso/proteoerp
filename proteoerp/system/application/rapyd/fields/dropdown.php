<?php
/**
 * selectField - is full implementation of select field
 * it has methods to load options from DB.. or you can pass the options by an array.. or you can mix
 *
 * @package rapyd.components.fields
 * @author Felice Ostuni
 * @license http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version 0.9.6
 */


/**
 * selectField
 *
 * @package    rapyd.components.fields
 * @author     Felice Ostuni
 * @access     public
 */
class dropdownField extends objField{

	var $type        = 'select';
	var $description = '';
	var $clause      = 'where';
	var $css_class   = 'select';
	var $multiple    = false;

	//costruttore
	function dropdownField($label, $name, $options=array(), $selected=''){
		parent::objField($label, $name);

		if ( (count($this->options)>0) && (count($options)>0) ){
			$this->options = array_merge($this->options, $options);
		} else {
			$this->options = $options;
		}
	}

	function _getValue(){
		parent::_getValue();
		foreach ($this->options as $value=>$description){
			if ($this->value == $value){
				$this->description = $description;
			}
		}
	}

	function _getNewValue(){
		parent::_getNewValue();
	}

	function build(){
		if(!isset($this->style)){
			$this->style = 'width:290px;';
		}

		$this->_getValue();
		$output = '';

		switch ($this->status){
			case 'disabled':
			case 'show':
				if (!isset($this->value)){
					$output = RAPYD_FIELD_SYMBOL_NULL;
				} else {
					$output = $this->description;
				}
				break;
			case 'create':
			case 'modify':
				$onchange = '';
				$style = '';
				$multiple='';

				if ($this->onchange!=''){
					$onchange = ' onchange="'.$this->onchange.'"';
				}
				$id = 'id="'.$this->name.'"';

				if ($this->style!=''){
					$style = ' style="'.$this->style.'"';

				}

				if($this->multiple){
					$style = ' multiple="multiple"';
				}

				$class = ' class=\'select\'';

				if($this->type=='inputhidden'){

					$attributes = array(
					'name'  => $this->name,
					'id'    => $this->name,
					'type'  => 'hidden',
					'value' => $this->value,
					'style' => $this->style
					);

					$output = form_input($attributes);

					if($this->type=='inputhidden')
					$output.='<span id=\''.$this->name."_val'  >$this->description</span>";
				}else{
					$title  = ' title="'.$this->title.'"';
					$output = $this->form_dropdown($this->name, $this->options, $this->value, $id.$onchange.$style.$class.$title.$multiple). $this->extra_output;
				}

				break;

			case 'hidden':
				$output = form_hidden($this->name, $this->value);
				break;
			default:
		}
		$this->output = $output;
	}

	function form_dropdown($name = '', $options = array(), $selected = array(), $extra = ''){
		if (!is_array($selected)){
			$selected = array(trim($selected));
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0){
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name])){
				$selected = array($_POST[$name]);
			}
		}

		if ($extra != '') $extra = ' '.$extra;

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";

		foreach ($options as $key => $val){
			$key = (string) $key;

			if (is_array($val)){
				$form .= '<optgroup label="'.$key.'">'."\n";

				foreach ($val as $optgroup_key => $optgroup_val){
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';

					$form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
				}

				$form .= '</optgroup>'."\n";
			}else{
				$sel = (in_array(trim($key), $selected,true)) ? ' selected="selected"' : '';

				$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
		}

		$form .= '</select>';

		return $form;
	}

}
