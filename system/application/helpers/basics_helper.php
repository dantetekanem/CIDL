<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	if ( !function_exists('object_collect'))
	{
		function object_collect($array, $key, $value)
		{
			foreach($array as $a)
				$_array[$a->$key]	= $a->$value;
			
			return $_array;
		}
	}
		
	if (!function_exists('post')) 
	{
		function post($field, $xss_clean = false)
		{
			$ci		=& get_instance();
			$ci -> load -> library('input');
			return $ci -> input -> post($field, $xss_clean);
		}
	}
	
	if (!function_exists('get')) 
	{
		function get($field, $xss_clean = false)
		{
			$ci		=& get_instance();
			$ci -> load -> library('input');
			return $ci -> input -> get($field, $xss_clean);
		}
	}
	
	if (!function_exists('params')) 
	{
		function params($field, $xss_clean = false)
		{
			$ci		=& get_instance();
			$ci -> load -> library('input');
			return $ci -> input -> get_post($field, $xss_clean);
		}
	}

/* End of file basics_helper.php */
/* Location: ./system/application/helpers/basics_helper.php */