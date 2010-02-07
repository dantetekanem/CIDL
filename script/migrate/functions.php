<?PHP
	/* migration functions to facility convertion of data ! */
	
	if(!function_exists('create_table'))
		{
			function create_table($table, $fields = array(), $database = '', $engine = 'MYISAM')
				{
					if(!$table || count($fields) == 0)
						return false;
						
					generate_fixtures($table, $fields);
						
					$sql			.= ($database != "") ? "CREATE TABLE `".$database."`.`".$table."` " : "CREATE TABLE `".$table."`" ;
					
					$sqlf[]			= "`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY";
					
					foreach($fields as $field => $data)
						{
							if ( $data == "string" || $data == "file" || preg_match("/file\[(.*)\]/", $data, $matches) )
								{
									$sqlf[]		= "`".$field."` VARCHAR( 255 ) NOT NULL";
								}
							elseif ( $data == "integer" || preg_match("/select\[(.*)\]/", $data, $matches) )
								{
									$sqlf[]		= "`".$field."` INT( 10 ) NOT NULL";
								}
							elseif ( $data == "text" )
								{
									$sqlf[]		= "`".$field."` TEXT NOT NULL";
								}
							elseif ( preg_match("/enum\[(.*)\]/", $data, $matches) )
								{
									$d			= explode(",", $matches[1]);
									foreach($d as $i)
										{
											$k[]	= "'{$i}'";
										}
									$sqlf[]		= "`".$field."` enum(".join(",", $k).") NOT NULL";
								}
						}
						
					$sqlf[]			= "`position` INT( 10 ) NOT NULL";
					$sqlf[]			= "`status` enum('0','1') NOT NULL";
					$sqlf[]			= "`created_at` VARCHAR( 40 ) NOT NULL";
					$sqlf[]			= "`updated_at` VARCHAR( 40 ) NOT NULL";
					
					$sql			.= "( ".join(", ", $sqlf)." ) ";
					
					$sql			.= "ENGINE = {$engine} ;";
					
					return $sql;
				}
		}
		
	if(!function_exists('create_raw_table'))
		{
			function create_raw_table($table, $fields = array(), $database = '', $engine = 'MYISAM')
				{
					if(!$table || count($fields) == 0)
						return false;
						
					$sql			.= ($database != "") ? "CREATE TABLE `".$database."`.`".$table."` " : "CREATE TABLE `".$table."`" ;
					
					foreach($fields as $field => $data)
						{
							$sqlf[]	= "`".$field."` ".join(" ", $data);
						}
						
					$sql			.= "( ".join(", ", $sqlf)." ) ";
					
					$sql			.= "ENGINE = {$engine} ;";
					
					return $sql;
					
				}
		}
		
	if(!function_exists('remove_table'))
		{
			function remove_table($table, $database = '')
				{
					if(!$table)
						return false;
						
					$sql			.= ($database != "") ? "DROP TABLE `".$database."`.`".$table."`" : "DROP TABLE `".$table."`"; 
					
					return $sql;
				}
		}
		
	if(!function_exists('remove_column'))
		{
			function remove_column($table, $column, $database = '')
				{
					if(!$table)
						return false;
						
					$sql			= ($database != "") ? "ALTER TABLE `".$database."`.`".$table."` DROP `".$column."`" : "ALTER TABLE `".$table."` DROP `".$column."`";
					
					return $sql;
				}
		}
		
	if(!function_exists('alter_table'))
		{
			function alter_table($table, $fields = array(), $database = '')
				{
					if(!$table)
						return false;
						
					$sql			= ($database != "") ? "ALTER TABLE `".$database."`.`".$table."`" : "ALTER TABLE `".$table."`";
					
					foreach($fields as $field => $data)
						{
							if ( $data['type'] == "string" )
								{
									$sqlf[]		= "CHANGE `".$field."` `".$data['field']."` VARCHAR( 255 ) NOT NULL";
								}
							elseif ( $data['type'] == "integer" )
								{
									$sqlf[]		= "CHANGE `".$field."` `".$data['field']."` INT( 10 ) NOT NULL";
								}
							elseif ( $data['type'] == "text" )
								{
									$sqlf[]		= "CHANGE `".$field."` `".$data['field']."` TEXT NOT NULL";
								}
							elseif ( preg_match("/enum\[(.*)\]/", $data['type'], $matches) )
								{
									$d			= explode(",", $matches[1]);
									foreach($d as $i)
										{
											$k[]	= "'{$i}'";
										}
									$sqlf[]		= "CHANGE `".$field."` `".$data['field']."` enum(".join(",", $k).") NOT NULL";
								}
						}
						
					$sql			.= @join(", ", $sqlf);
					
					return $sql;
					
				}
		}
		
	if(!function_exists('add_column'))
		{
			function add_column($table, $fields = array(), $database = '')
				{
					if(!$table)
						return false;
						
					$sql			= ($database != "") ? "ALTER TABLE `".$database."`.`".$table."`" : "ALTER TABLE `".$table."`";
					
					foreach($fields as $field => $data)
						{
							if ( $data == "string" || $data == "file" || preg_match("/enum\[(.*)\]/", $data, $matches) )
								{
									$sqlf[]		= "ADD `".$field."` VARCHAR( 255 ) NOT NULL";
								}
							elseif ( $data == "integer" || preg_match("/select\[(.*)\]/", $data, $matches) )
								{
									$sqlf[]		= "ADD `".$field."` INT( 10 ) NOT NULL";
								}
							elseif ( $data == "text" )
								{
									$sqlf[]		= "ADD `".$field."` TEXT NOT NULL";
								}
							elseif ( preg_match("/enum\[(.*)\]/", $data, $matches) )
								{
									$d			= explode(",", $matches[1]);
									foreach($d as $i)
										{
											$k[]	= "'{$i}'";
										}
									$sqlf[]		= "ADD `".$field."` enum(".join(",", $k).") NOT NULL";
								}
						}
						
					$sql			.= @join(", ", $sqlf);
					
					return $sql;
					
				}
		}
		
	if(!function_exists('create_database'))
		{
			function create_database($database)
				{
					return "CREATE DATABASE  `".$database."`;";
				}
		}
		
	if(!function_exists('seed_table'))
		{
			function seed_table($table, $fields = array(), $database = '')
				{	
					if(!$table || count($fields) == 0)
						return false;
						
					$sql			.= ($database != "") ? "INSERT INTO `".$database."`.`".$table."` " : "INSERT INTO `".$table."`" ;
					
					foreach($fields as $field => $data)
						{
							$f[] = "`".$field."`";
							$v[] = "'".$data."'";
						}
						
					$f[]		= '`created_at`';
					$f[]		= '`updated_at`';
					$v[]		= "'".time()."'";
					$v[]		= "'".time()."'";
					
					$sql			.= " (".join(", ",$f).") VALUES (".join(", ", $v).");";
					
					return $sql;
					
				}
		}
		
	if (!function_exists('generate_fixtures')) 
	{
		function generate_fixtures($name, $fields = array(), $create = array("one", "two"))
		{
			if(!$name || count($fields) == 0)
				return false;
				
			$__fix			 = "";
				
			foreach ($create as $fix) 
			{
				$__fix		.= $fix.":\n";
				foreach ($fields as $field => $value) 
					$__fix	.= "\t".$field.": ".fixture_value($value)."\n";
				
				$__fix		.= "\n";
			}
			
			if(file_write_contents("../system/application/tests/fixtures/{$name}.yml", $__fix))
				echo "../system/application/tests/fixtures/{$name}.yml generated succesfully.<br />";
		}
	}
	
	if (!function_exists('fixture_value')) 
	{
		function fixture_value($value='')
		{
			if($value == "string")
				return "MyString";
			else if($value == "integer")
				return 10;
			else if($value == "float")
				return 10.0;
			else if($value == "text")
				return "'Text Description'";
			else if(eregi("file", $value))
				return "file.txt";
			else if(eregi("enum", $value))
				return "enum-option";
			else if(preg_match("/select\[(.*)\]/", $value, $f))
			{
				$d				= explode("=", $f[1]);
				$table			= pluralize($d[0]);
				return $table . '(one)';
			}
		}
	}
	
	if (!function_exists('pluralize')) 
	{
		function pluralize( $string )
	    {
	    	$plural = array(
		        '/(quiz)$/i'               => "$1zes",
		        '/^(ox)$/i'                => "$1en",
		        '/([m|l])ouse$/i'          => "$1ice",
		        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
		        '/(x|ch|ss|sh)$/i'         => "$1es",
		        '/([^aeiouy]|qu)y$/i'      => "$1ies",
		        '/(hive)$/i'               => "$1s",
		        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		        '/(shea|lea|loa|thie)f$/i' => "$1ves",
		        '/sis$/i'                  => "ses",
		        '/([ti])um$/i'             => "$1a",
		        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
		        '/(bu)s$/i'                => "$1ses",
		        '/(alias)$/i'              => "$1es",
		        '/(octop)us$/i'            => "$1i",
		        '/(ax|test)is$/i'          => "$1es",
		        '/(us)$/i'                 => "$1es",
		        '/s$/i'                    => "s",
		        '/$/'                      => "s"
		    );
		    
		    $irregular = array(
		        'move'   => 'moves',
		        'foot'   => 'feet',
		        'goose'  => 'geese',
		        'sex'    => 'sexes',
		        'child'  => 'children',
		        'man'    => 'men',
		        'tooth'  => 'teeth',
		        'person' => 'people'
		    );
		    
		    $uncountable = array(
		        'sheep',
		        'fish',
		        'deer',
		        'series',
		        'species',
		        'money',
		        'rice',
		        'information',
		        'equipment'
		    );
	        // save some time in the case that singular and plural are the same
	        if ( in_array( strtolower( $string ), $uncountable ) )
	            return $string;
	
	        // check for irregular singular forms
	        foreach ( $irregular as $pattern => $result )
	        {
	            $pattern = '/' . $pattern . '$/i';
	
	            if ( preg_match( $pattern, $string ) )
	                return preg_replace( $pattern, $result, $string);
	        }
	
	        // check for matches using regular expressions
	        foreach ( $plural as $pattern => $result )
	        {
	            if ( preg_match( $pattern, $string ) )
	                return preg_replace( $pattern, $result, $string );
	        }
	
	        return $string;
	    }
	}
	
	if (!function_exists('file_write_contents')) 
	{
		function file_write_contents($filename, $data)
		{
			$f = @fopen($filename, 'w+');
	        if (!$f) {
	            return false;
	        } else {
	            $bytes = fwrite($f, $data);
	            fclose($f);
	            @chown($filename, "Users");
	            return $bytes;
	        }
		}
	}