<?PHP
	/*
		MIGRATE SCHEME
		
		Create by Dante.leonardo
		@version 0.1 beta
		
	*/
	
	define('MIGRATE_PATH', "../system/migrate/");
	define('BASEPATH', "../system/");
	
	class Migrate
		{
			
			var $values;
			var $db_connect;
			var $db_db;
			var $db;
			
			// test variables
			var $test_connect;
			var $test_db;
			var $test;

					
			function __construct()
				{					
					
					# require config database from CI
					require "../system/application/config/database.php";
					require "migrate/functions.php";
					
					$this -> db			= $db['default'];
					$this -> test 		= $db['test'];
					
					$this -> connect_db();
					$this -> self_request();
					$this -> close_db();
				}
				
			
			
			function self_request()
				{
					$this->values		= split("/", $_SERVER['PATH_INFO']);
					array_shift($this->values);
					
					if(count($this->values) == 0)
						$this->run();
					else
						{
							$func		= $this->values[0];
							array_shift($this->values);
							$this -> $func();
						}
					
				}
			
			function connect_db()
				{
					# connect to default database
					$this -> db_connect = mysql_connect($this -> db['hostname'], $this -> db['username'], $this -> db['password'], true);
					$this -> db_db		= mysql_select_db($this -> db['database'], $this -> db_connect);
					
					# connect to test database
					$this -> test_connect = mysql_connect($this -> test['hostname'], $this -> test['username'], $this -> test['password'], true);
					$this -> test_db	= mysql_select_db($this -> test['database'], $this -> test_connect);
				}
				
			function close_db()
				{
					# close database
					mysql_close($this -> db_connect);
					mysql_close($this -> test_connect);
				}
			
			function setup()
				{
					
					/* INSTALL THE DEFAULT DATABASE */
					if($this->db_db && mysql_query("SELECT * FROM schema_migrations"))
					{
						echo "O setup já foi executado.";
						return false;
					}
						
					echo "Instalando o banco...<br />";
						
					$db				= mysql_query(create_database($this->db['database']), $this->db_connect);
					$db_test 		= mysql_query(create_database($this->test['database']), $this->test_connect);
					if($db && $db_test)
					{
						echo "Banco ".$this->db['database'].' criado com sucesso.<br />';
						echo "Banco ".$this->test['database'].' criado com sucesso.<br />';
					}
												
					$migrations		= mysql_query(create_raw_table('schema_migrations', array(
						'rowid'		=> array('INT ( 10 )', 'NOT NULL', 'AUTO_INCREMENT', 'PRIMARY KEY'),
						'version'	=> array('VARCHAR ( 255 )', 'NOT NULL')
					), $this->db['database']));
												
					if($migrations)
						echo "Migrations schema criado com sucesso.<br />";
					
				}
				
			function run()
				{
					
					if(!$this -> db_db)
						{
							echo 'Instale o banco antes, rode: migrate.php/setup.';
							exit;
						}
					
					$migrate_total		= 0;
					
					/* VERIFY THE SCHEMAS AND GET THE NOT_REVISED */
					foreach(glob(MIGRATE_PATH."*.php") as $migrate_url)
						{
							#clean url
							$migrate				= str_replace(MIGRATE_PATH, "", $migrate_url);
							
							if($migrate == "seed.php")
								continue;
							
							$migrate_split			= split("_", $migrate);
							
							$migrate_version		= $migrate_split[0];
							array_shift($migrate_split);
							$migrate_file			= join("_", $migrate_split);
							
							// verify if migration exists on table
							if($this->migration_exists($migrate_version))
								continue;
							else
								{
									require $migrate_url;
									$class_name		= ucfirst(str_replace(".php", "", $migrate_file));
									$Class			= new $class_name;
									
									$data			= $Class -> up();
									
									$migrating		= true;
									
									foreach($data as $d)
										{
											if (!$d || $d == "")
												continue;
												
											$query			= mysql_query($d, $this->db_connect);
											$query_test 	= mysql_query($d, $this->test_connect);
											if(!$query && !$query_test)
												$migrating	= false;
										}
									
									if($migrating)
									{
										mysql_query("INSERT INTO schema_migrations (version) VALUES ({$migrate_version})", $this->db_connect);
										echo "A versão {$migrate} foi migrada com sucesso...<br />";
										$migrate_total++;
									}
									else
										echo "Ocorreu um erro ao migrar a versão {$migrate}...<br />";
										
								}
						}
						
					if($migrate_total == 0)
						{
							echo 'Nenhum arquivo para migrar...';
						}
				}
				
			function migration_exists($version)
				{
					$query			= mysql_query("SELECT * FROM schema_migrations WHERE `version` = '{$version}'", $this->db_connect);
					if(mysql_num_rows($query) > 0)
						return true;
					else
						return false;
				}
			
			function rollback()
				{
					/* RETURN THE SCHEMAS */
					
					# get the last ID from schemas, run the down function and remove from db. (if a num is setted, more rollbacks runs)
					
					if($this->values[0] != "all" AND !empty($this->values[0]))
						$query			= mysql_query("SELECT * FROM schema_migrations WHERE `version` = {$this->values[0]}", $this->db_connect);
					else
						$query				= mysql_query("SELECT * FROM schema_migrations ORDER BY rowid DESC limit 1", $this->db_connect);
					
					/* VERIFY THE SCHEMAS AND GET THE NOT_REVISED */
					foreach(glob(MIGRATE_PATH."*.php") as $migrate_url)
						{
							$migrate				= str_replace(MIGRATE_PATH, "", $migrate_url);
							
							if($migrate == "seed.php")
								continue;
							
							#clean url
							$migrate_split			= split("_", $migrate);
							
							$migrate_version		= $migrate_split[0];
							array_shift($migrate_split);
							$migrate_file			= join("_", $migrate_split);
							
							$schemas[$migrate_version]	= array($migrate_file, $migrate_url);
						}
					
					while($obj = @mysql_fetch_object($query))
						{
							$schema					= $schemas[$obj->version];
							if(!$schema)
								continue;
								
							$migrate_file			= $schema[0];
							$migrate_url			= $schema[1];
								
							require $migrate_url;
							$class_name		= ucfirst(str_replace(".php", "", $migrate_file));
							$Class			= new $class_name;
							
							$data			= $Class -> down();
							
							$migrating		= true;
							
							foreach($data as $d)
								{
									if (!$d || $d == "")
										continue;
										
									$query			= mysql_query($d, $this->db_connect);
									$query_test 	= mysql_query($d, $this->test_connect);
									if(!$query && $query_test)
										$migrating	= false;
								}
							
							if($migrating)
							{
								mysql_query("DELETE FROM schema_migrations WHERE version = '".$obj->version."'", $this->db_connect);
								echo "A versão {$obj->version} foi removida com sucesso...<br />";
								$migrate_total++;
							}
							else
								echo "Ocorreu um erro ao remover a versão {$obj->version}...<br />";
						}
						
					if($migrate_total == 0)
						echo "Nenhum arquivo foi encontrado.";
					
				}
				
			function seed()
				{
					require MIGRATE_PATH."seed.php";
					
					$seed			= new Seed();
					
					$rows			= $seed->run();
					
					foreach($rows as $query)
						{
							$k		= mysql_query($query, $this->db_connect);
							$k_test = mysql_query($query, $this->test_connect);
							if($k && $k_test)
								{
									$k_count++;
								}
						}
						
					echo "Foram rodadas {$k_count} de ".count($rows)." seeds no banco.";
					
				}
				
		}
	
		$Migrate		= new Migrate();
