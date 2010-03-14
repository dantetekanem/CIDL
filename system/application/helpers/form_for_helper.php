<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
*	form_for_helper.php
*	Create a recursive model to use forms with a object value
*
*	@author Dante.leonardo
*	@version 0.1 beta
*	@link http://www.leonardopereira.com
*/

if (!class_exists('Form_for')) 
{
	
	class Form_for
		{
			
			var $form_name;
			var $form_id;
			var $form_object;
			var $form_vars;
			var $structure;
			
			function __construct($form_name, $form_object, $form_vars = array(), $structure = true)
			{
				$this -> form_name 		= strtolower($form_name);
				$this -> form_id 		= str_replace(array('[', ']'), array('_', ''), strtolower($form_name));
				$this -> form_object 	= $form_object;
				$this -> form_vars 		= $form_vars;
				$this -> structure 		= $structure;
				
				if($this->structure)
					$this -> start();
					
				return $this;
			}
			
			function start()
			{
				$_vars					= $this -> __html_vars($this -> form_vars);
				echo '<form method="'.$this -> form_vars['method'].'" action="'.site_url($this -> form_vars['action']).'" enctype="multipart/form-data" '.$_vars.'>'."\n";
			}
			
			function label($field = '', $label = '', $field_vars = array())
			{
				$name 	= $label == "" ? ucfirst($field) : $label;		
				$_vars 	= $this -> __html_vars($field_vars);	
				if(!empty($field))
					$_for = 'for="'.$this -> form_id.'_'.$field.'" ';
				return '<label '.$_for.$_vars.'>'.$name.'</label>'."\n";
			}
			
			function text_field($field_name, $field_vars = array())
			{
				$_vars			= $this -> __html_vars($field_vars);
				
				if(!empty($this -> form_object -> $field_name))
					$_value		= $this -> form_object -> $field_name;
				
				return '<input type="text" name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" value="'.$_value.'" '.$_vars.'/>'."\n";
			}
			
			function password($field_name, $field_vars = array())
			{
				$_vars			= $this -> __html_vars($field_vars);
				return '<input type="password" name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" '.$_vars.'/>'."\n";
			}
			
			function radio_button($field_name, $value, $field_vars = array())
			{
				if($this -> form_object -> $field_name == $value)
					$_checked	= 'checked="checked" ';
				$_vars			= $this -> __html_vars($field_vars);
				return '<input type="radio"  name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" value="'.$value.'" '.$_checked.$_vars.'/>'."\n";
			}
			
			function checkbox($field_name, $value, $field_vars = array())
			{
				if($this -> form_object -> $field_name == $value)
					$_checked	= 'checked="checked" ';
				$_vars			= $this -> __html_vars($field_vars);
				return '<input type="checkbox"  name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" value="'.$value.'" '.$_checked.$_vars.'/>'."\n";
			}
			
			function text_area($field_name, $field_vars = array())
			{
				$_vars			= $this -> __html_vars($field_vars);
				
				if(!empty($this -> form_object -> $field_name))
					$_value		= $this -> form_object -> $field_name;
					
				return '<textarea name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" '.$_vars.'>'.$_value.'</textarea>'."\n";
			}
			
			function file_field($field_name, $field_vars = array())
			{
				$_vars			= $this -> __html_vars($field_vars);
				return '<input type="file" name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" '.$_vars.'/>'."\n";
			}
			
			function hidden_field($field_name, $field_value, $field_vars = array())
			{
				$_vars			= $this -> __html_vars($field_vars);
				
				if(!empty($field_value))
					$_value		= $field_value;
				elseif(!empty($this -> form_object -> $field_name))
					$_value		= $this -> form_object -> $field_name;
				
				return '<input type="hidden" name="'.$this->form_name.'['.$field_name.']'.'" id="'.$this->form_id.'_'.$field_name.'" value="'.$_value.'" '.$vars.' />'."\n";
			}
			
			function select($field_name, $data, $field_vars = array())
			{
				
				if(!empty($this -> form_object -> $field_name))
					$_selected						= $this -> form_object -> $field_name;
				
				foreach ($field_vars as $key => $value) 
				{
					switch ($key) 
					{
						case 'use_blank':
							$_include_select_data	= '<option></option>'."\n";
						break;
						
						case 'selected':
							$_selected				= $value;
						break;
					}
				}
				
				$_vars			= $this -> __html_vars($field_vars);
				$select		= '<select name="'.$this->form_name.'['.$field_name.']" id="'.$this->form_id.'_'.$field_name.'" '.$_vars.'>'."\n";
				
				if(!empty($_include_select_data))
					$select		.= $_include_select_data;
				
				foreach($data as $value => $option)
				{
					if(!$option)
					{
						$select .= '<option>'.$value.'</option>'."\n";
					}
					else
					{
						if($_selected == $value && !empty($value))
							$select .= '<option value="'.$value.'" selected="selected">'.$option.'</option>'."\n";
						else
							$select .= '<option value="'.$value.'">'.$option.'</option>'."\n";
					}
				}
				$select		.= '</select>'."\n";
				
				return $select;
			}
			
			function submit($field_label, $field_vars = array())
			{
				$_vars		= $this -> __html_vars($field_vars);
				return '<input type="submit" name="'.$this -> form_name.'[commit]'.'" value="'.$field_label.'" '.$_vars.' />'."\n";
			}
			
			function reset($field_label, $field_vars = array())
			{
				$_vars		= $this -> __html_vars($field_vars);
				return '<input type="reset" name="'.$this -> form_name.'[reset]'.'" value="'.$field_label.'" '.$_vars.' />'."\n";
			}
			
			function button($field_name, $field_label, $field_vars = array())
			{
				$_vars		= $this -> __html_vars($field_vars);
				return '<input type="button" name="'.$this -> form_name.'['.$field_name.']'.'" id="'.$this -> form_id .'_' . $field_name .'" value="'.$field_label.'" '.$_vars.' />'."\n";
			}
			
			function fields_for($field_name, $form_object, $field_vars = array())
			{
				if(!$form_object) $form_object = $this -> form_object;
				
				if(!empty($field_vars['index'])) {
					$name = $this->form_name.'['.$field_name.']'.'['.$field_vars['index'].']';
				} else {
					$name = $this->form_name.'['.$field_name.']';
				}
				
				return new Form_for($name, $form_object, $field_vars, false);
			}
			
			function end()
			{
				if($this->structure)
					echo '</form>'."\n";
				unset($this);
			}
			
			function __html_vars($data = array())
			{
				$_html			= array();
				foreach ($data as $attr => $value) 
				{
					if($attr == "action" || $attr == "method")
						continue;
						
					if(is_array($value))
						$value	= $this -> __style_vars($value);
						
					$_html[] 	= $attr.'="'.$value.'"';
				}
				return join(" ", $_html);
			}
			
			function __style_vars($data = array())
			{
				$_style			= array();
				foreach ($data as $attr => $value) 
				{
					$_style[]	= $attr.': '.$value.';';
				}
				return join(" ", $_style);
			}
			
		}
	}
	
	if (!function_exists('form_for')) 
	{
		function form_for($form_name, $form_object, $form_vars, $structure = true)
		{
			return new Form_for($form_name, $form_object, $form_vars, $structure);
		}
	}

/* End of file form_for_helper.php */
/* Location: ./system/application/helpers/form_for_helper.php */