<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


	/*
	
		CI18n - Internationalization Class
		@author			Dante.leonardo - www.leonardopereira.com
		@date			mar.04.2009
		@version		1.0
		
		Require:		session class.
		
		This is a PHP Library for Code Igniter Framework.
	
	*/
	
	
	class Ci18n
	{
			
		# CI global variabel
		var $CI;
		
		# config use
		var $config;
		
		var $default_language;
		var $language;
		var $languages;
		
		var $yaml;
		
		public function __construct ($config = array())
		{	
			# append CI to my class
			$this -> CI =& get_instance();
			
			$this -> CI -> load -> helper(array('cookie', 'yayparser', 'ci18n'));
			
			$this -> config					= $this -> CI -> load -> config('ci18n');
			$this -> initialize();
			
			log_message('debug', 'CI18n class initialized !');
		}
			
		private function initialize ()
		{
			$this -> default_language		= $this -> CI -> config -> item('default_language', $this->config);
			$this -> languages				= $this -> CI -> config -> item('languages', $this->config);
			
			$this -> set_language();
		}
		
		function language()
		{
			if($this -> languages[$this -> CI -> uri -> segment(1)]) {
				return $this -> languages[$this -> CI -> uri -> segment(1)];
			}
			return !$this -> language ? $this -> languages[$this -> default_language] : (!$this -> languages[$this -> language] ? $this -> languages[$this->default_language] : $this->languages[$this->language]);
		}
		
		function set_language($lang = '')
		{	
			$this -> language 		= !$lang ? get_cookie('language_ci18n', TRUE) : $lang;
			$yaml_contents			= file_get_contents(APPPATH."language/ci18n/".$this->language().".yml");		
			$this->yaml				= yayparser($yaml_contents);
			
			if($lang != "")
				set_cookie('language_ci18n', $lang, 86400);
		}
				
		function translate($params)
		{
			
			$translation	= array_shift($params);
			
			if(eregi("\.", $translation)) {
				$exploded			= explode(".", $translation);
				$translation		= $this->yaml;
				for($k = 0; $k < count($exploded); $k++) {
					$translation	= $translation[$exploded[$k]];
				}
				if(is_array($translation)) $translation = $translation['default'];
				$data				= array();
				array_push($data, $translation);
				foreach($params as $p) { array_push($data, $p); }
				return call_user_func_array('sprintf', $data);
			} else {
				if(is_array($this->yaml[$translation]))
					return sprintf($this->yaml[$translation]['default'], $params);
				else
					return sprintf($this->yaml[$translation], $params);
			}
		}
				
	}