<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


	/*
	
		Fixtures - Fixtures Class
		@author			Dante.leonardo - www.leonardopereira.com
		@date			feb.06.2009
		@version		1.0
		
		Require:		database class, yayparser helper.
		
		This is a PHP Library for Code Igniter Framework.
		
	*/

	class Fixtures
	{
		
		var $ci;
		var $fixtures_dir	= 'tests/fixtures/';
		var $hooks_for		= array();
		var $objects		= array();
		
		function __construct($ci)
		{
			// Instance CI class
			$this -> ci		=& get_instance();
			$this -> ci -> load -> helper('yayparser');
		}
		
		function start()
		{
			// Populate database
			$files			= @scandir(APPPATH.'tests/fixtures/');
			array_shift($files); array_shift($files);
			
			foreach ($files as $file) 
			{
				$yay		= file_get_contents(APPPATH.'tests/fixtures/'.$file);
				$yay		= yayparser($yay);
				$table		= str_replace('.yml', '', $file);
				$pos		= 0;
				
				foreach ($yay as $instance => $data) 
				{
					foreach($data as $d => $v)
					{
						// triming
						$data[$d] = trim($v);
					}
						
					$data['position']	= $pos++;
					$data['created_at']	= time();
					$data['updated_at']	= time();
					$this -> add_object($table, $instance, $data);
				}
			}
			$this -> insert_all_objects();
		}
		
		public function add_object($table, $instance, $data)
		{
			$this -> generate_hooks_for($table);
			$data					= array("id" => rand(1111, 9999999)) + $data + array("hook" => $instance);
			$this -> objects[$table][$instance] = $data;
		}
		
		public function insert_all_objects()
		{
			foreach ($this -> objects as $table => $instance) 
			{
				foreach ($instance as $i => $data) 
				{
					foreach ($data as $key => $value) 
					{
						if(preg_match("/(.*)\((.*)\)/", $value, $matches))
							{
								$_table = $matches[1];
								$_hook	= $matches[2];
								$data[$key] = $this -> objects[$_table][$_hook]['id'];
							}
					}
					$this -> ci -> db -> insert($table, $data);
				}
			}
		}
		
		public function generate_hooks_for($table)
		{
			if($this -> ci -> db -> field_exists('hook', $table) == false)
			{
				if(!$this->hooks_for[$table])
				{
					$this -> ci -> db -> query("ALTER TABLE `$table` ADD `hook` VARCHAR( 255 ) NOT NULL ;");
					$this->hooks_for[$table] = true;
				}
			}
		}
		
		public function get($table, $instance)
		{
			return $this -> ci -> db -> where('hook', $instance) -> get($table) -> row();
		}
		
		function clean()
		{
			$tables			= $this -> ci -> db -> list_tables();
			foreach ($tables as $table) 
				$this -> ci -> db -> truncate($table);
				
		}
		
	}