<?PHP

	/*
	
		ADGEN MAKER - AUTOMATIC GENERATE CODES FOR CODE IGNITER
		
		@version 0.1
			INCLUDES: 	- CONTROLLERS
				 		- MODELS
				 		- VIEWS
				 		- MIGRATIONS
				 		- HELPERS
				 		
		@author Dante.leonardo - www.leonardopereira.com
	
	*/
	
	$argv				= split("/", strtolower($_SERVER['PATH_INFO']));
	
	array_shift($argv);
	$command_request	= array_shift($argv);
	$params				= $argv;
	
	if(!function_exists($command_request))
		die("your command was not found, enter: script/generate.php/help for see the list of commands");
	else
		call_user_func($command_request, $params);
	
		
	/* FUNCTIONS ---------------------------------------------------------*/
	
	
	/**
	 * Call a YML and run all the previous defined functions
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function yml($params = array())
	{
		
		require "../system/helpers/yayparser_helper.php";
		
		if(!file_exists("generate.yml"))
			die("O arquivo generate.yml não foi encontrado, impossibilitando o sistema de continuar.");
			
			
		$yml		= file_get_contents("generate.yml");
		$yml		= yayparser($yml);
		
		foreach ($yml as $func => $data) 
		{
			foreach($data as $table => $_d)
			{
				echo '--------------------------<br /><br />';
				array_unshift($_d, $table);
				$func(array_to_params($_d));
				sleep(1);
			}
		}
		
	}
	
	/**
	 * Transform a array to array in params structure, for the generate functions.
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function array_to_params($data = array())
	{
		foreach ($data as $key => $value) 
		{
			$content[] = !is_string($key) ? trim($value) : (trim($key).':'.trim($value));
		}
			
		return (count($content) > 0) ? $content : false;
	}
	
	/**
	 * Generate a controller
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function controller($params = array())
	{
		require "generate/inflect.class.php";
		require "generate/view_helper.php";
		$Inflect = new Inflect;
		
		/* if we don't have params we cancel this request */
		if(count($params) == 0)
			die("
was impossible to continue this generation because you didn't pass any param, run 'script/generate.php/help/controller' for more details.");
		
		/* alocate the controller base file in a variable */
		$controller			= file_get_contents("generate/controller.php");
		$controller_test 	= file_get_contents("generate/controller_test.php");
		
		/* the first param is our controller class name and filename to construct */
		$_param_one			= array_shift($params);
		
		$_param_one_data	= split(":", $_param_one);
		if(count($_param_one_data) > 1)
			{
				// soo, we have subdirectories to our controller, lets create the _controller_base path
				$controller_name 	= $Inflect->pluralize(array_pop($_param_one_data));
				foreach($_param_one_data as $param)
					{
						$_dir		= str_replace('/', '', $param);
						create_directory('../system/application/controllers/'.$__ndir.$_dir);
						$__ndir .= $_dir.'/';
					}
					unset($__ndir);
						
				array_push($_param_one_data, $Inflect->pluralize($controller_name));
				$_controller_base 	= join(":", $_param_one_data).':';
				array_pop($_param_one_data);
				$_controller_fixed_name = join("/", $_param_one_data)."/".$controller_name.".php";
			}
		else
			{
				// well, the controller is normal and our controller_name too
				$controller_name 	= $Inflect->pluralize($_param_one);
				$_controller_base 	= $controller_name.':';
				$_controller_fixed_name = $controller_name.".php";
			}
		
		$action				= $Inflect->pluralize($controller_name);
		$__classname		= ucfirst($action);
		$__test_classname	= $__classname.'ControllerTest';
		$__filename			= $action.".php";
		$__test_filename	= $action."_controller_test.php";
		
		/* if we have more params, they will be our functions, and will generate our views */
		foreach($params as $param)
			{
				// create the default view
				view(array($_controller_base.$param));
				
				$_function[]	= '
	function '.$param.'()
	{
		
		$this -> load -> view("'.str_replace(':', '/', $_controller_base).$param.'");
	}';
			}
			
		$__functions			= join("\n\n", $_function);
			
		/* lets replace our data and save our controller =) */
		$controller				= str_replace(array('#classname#', '#filename#', '#functions#'), array($__classname, $__filename, $__functions), $controller);
		$controller_test 		= str_replace(array('#classname#', '#filename#'), array($__test_classname, $__test_filename), $controller_test);
		
		#open and create file
		$_fixed_filename		= "../system/application/controllers/".$_controller_fixed_name;
		$_fixed_test_filename	= "../system/application/tests/controllers/".$__test_filename;
				
		if(file_write_contents($_fixed_filename, $controller))
		{
			
			if(file_write_contents($_fixed_test_filename, $controller_test))
				echo "{$_fixed_test_filename} generated succesfully.<br /><br />";
			
			echo "
{$_fixed_filename} generated succesfully.<br />";
		}
		else
			echo "
{$_fixed_filename} was not possible to save the file, please check the directory permissions and try again.<br />";
	}
	
	/**
	 * Generate a model
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function model($params = array())
	{
		require "generate/inflect.class.php";
		$Inflect = new Inflect;
		
		/* if we don't have params we cancel this request */
		if(count($params) == 0)
			die("
was impossible to continue this generation because you didn't pass any param, run 'script/generate.php/help/model' for more details.");
		
		/* alocate the model base file in a variable */
		$model				= file_get_contents("generate/model.php");
		$model_test 		= file_get_contents("generate/model_test.php");
		
		/* the first param is our model class name, filename and table name to construct */
		$action				= $Inflect->singularize(array_shift($params));
		$__classname		= ucfirst($action);
		$__test_classname	= $__classname.'ModelTest';
		$__filename			= $action.".php";
		$__test_filename	= $action."_model_test.php";
		$__tablename		= $Inflect->pluralize($action);
		$__migration[]		= "create_table:".$__tablename;
		
		/* if we have more fields, they will be setted as validations, but without field filled */
		if(count($params) > 0)
			{
				$__validation	= "
		var ".'$validation'."		= array(
			#fields#
		);";
				foreach($params as $param)
					{
						$__migration[] = $param;
						$p = split(":", $param);
						
						if($p[0] == "has_many")
							{
								if(eregi(",", $p[1]))
								{
									$k = split(",", $p[1]);
									foreach ($k as $b) 
										$_has_many[]= "'".$Inflect->singularize($b)."'";
								}
								else 
									$_has_many[]	= "'".$Inflect->singularize($p[1])."'";
								continue;
							}
						elseif($p[0] == "accepts_nested_attributes_for")
							{
								if(eregi(",", $p[1]))
								{
									$k = split(",", $p[1]);
									foreach ($k as $b) 
										$_accepts_nested_attributes_for[]= "'".$Inflect->singularize($b)."'";
								}
								else 
									$_accepts_nested_attributes_for[]	= "'".$Inflect->singularize($p[1])."'";
								continue;
							}
						
						$_fields[]		= "'{$p[0]}'			=> array(
				'label'		=> '".ucfirst($p[0])."',
				'rules'		=> array()
			)";
					if(preg_match("/select\[(.*)\]/", $p[1], $f))
						{
							$_d = explode("=", $f[1]);
							$_has_one[]			= "'".$Inflect->singularize($_d[0])."'";
						}
					}
				$__validation	= str_replace("#fields#", join(",\n\t\t\t", $_fields), $__validation);
				
				if(count($_has_one) > 0)
					$__has_one				= 'var $has_one		= array('.join(", ", $_has_one).');';
				if(count($_has_many) > 0)
					$__has_many				= 'var $has_many		= array('.join(", ", $_has_many).');';
				if(count($_accepts_nested_attributes_for) > 0)
					$__accepts_nested_attributes_for = 'var $accepts_nested_attributes_for		= array('.join(", ", $_accepts_nested_attributes_for).');';
			}
			
		/* lets replace our data and save our model =) */
		$model					= str_replace(array('#classname#', '#filename#', '#tablename#', '#validation#', '#hasone#', '#hasmany#', '#acceptsnestedattributesfor#'), array($__classname, $__filename, $__tablename, $__validation, $__has_one, $__has_many, $__accepts_nested_attributes_for), $model);
		
		$model_test 			= str_replace(array('#classname#', '#filename#'), array($__test_classname, $__test_filename), $model_test);
		
		#open and create file
		$_fixed_filename		= "../system/application/models/{$__filename}";
		$_fixed_test_filename	= "../system/application/tests/models/{$__test_filename}";		
		
		if(file_write_contents($_fixed_filename, $model))
		{
			if(file_write_contents($_fixed_test_filename, $model_test))
				echo "{$_fixed_test_filename} generated succesfully.<br /><br />";
							echo "
{$_fixed_filename} generated succesfully.<br /><br />Run: 'script/migrate.php' to install the table for this model.<br /><br />";
			migration($__migration);
		}
		else
			echo "
{$_fixed_filename} was not possible to save the file, please check the directory permissions and try again.<br />";
	}
	
	/**
	 * Generate a view
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function view($params = array())
	{
		require "generate/view_helper.php";
		/* if we don't have params we cancel this request */
		if(count($params) == 0)
			die("
was impossible to continue this generation because you didn't pass any param, run 'script/generate.php/help/view' for more details.");
		
				
		/* we check how manys views do we have */
		foreach($params as $param)
			{
				
				/* alocate the view base file in a variable */
				$view						= file_get_contents("generate/view.php");
				
				# for each param we create a new view, but we have to check if this view will be generate in a folder (or a new folder)
				$_dires						= split(":", $param);
				$__datetime					= date("m/d/Y - H:i:s");
				if(count($_dires) > 1)
					{
						# soo... it will be a directory
						$file				= str_replace('/', '', array_pop($_dires));
						foreach($_dires as $_dir)
							{
								$_dir		= str_replace('/', '', $_dir);
								create_directory('../system/application/views/'.$__ndir.$_dir);
								$__ndir .= $_dir.'/';
							}
							unset($__ndir);
						$__filename			= "../system/application/views/".join("/", $_dires)."/{$file}.php";
					}
				else
					{
						$__filename			= "../system/application/views/{$param}.php";
					}
					
				$view						= str_replace(array('#datetime#', '#filename#'), array($__datetime, $__filename), $view);
				
				if(file_write_contents($__filename, $view))
					echo "
	{$__filename} generated succesfully.<br />";
				else
					echo "
	{$__filename} was not possible to save the file, please check the directory permissions and try again.<br />";
	
				unset($_dires, $__filename, $__ndir, $__datetime, $view);
			}
	}
	
	/**
	 * Generate a migration
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function migration($params = array())
	{
		
		/* if we don't have params we cancel this request */
		if(count($params) == 0)
			die("
was impossible to continue this generation because you didn't pass any param, run 'script/generate.php/help/migration' for more details.");
		
		/* alocate the migration base file in a variable */
		$migration			= file_get_contents("generate/migration.php");
		
		/* define datetime */
		$__datetime			= date("YmdHis");
		
		/* the first param is our migration name and action to construct */
		$action				= explode(":", array_shift($params));
		$_func				= $action[0];
		$_into				= $action[1];
		$__filename			= join("_", $action);
		$__classname		= ucfirst($__filename);
		
		
		/* lets set the rest of params to a array of our constructor */
		$_params			= array();
		foreach($params as $param)
			{
				$p 				= explode(":", $param);
				if($p[0] == "has_many" || $p[0] == "accepts_nested_attributes_for")
					continue;
				$_params[] 		= "'{$p[0]}' => '{$p[1]}'";
			}
			
		/* lets threat or request, of course */
		switch($_func)
			{
				case "create_table":
					$__up		= "create_table('{$_into}', array(
								  	".join(",\n\t\t\t\t\t\t\t\t\t", $_params)."
								   ));";
					$__down		= "remove_table('{$_into}');";
				break;
				
				case "add_column":
					$__up		= "add_column('{$_into}', array(
								  	".join(",\n\t\t\t\t\t\t\t\t\t", $_params)."
								   ));";
					foreach($params as $param)
						{
							$p 				= explode(":", $param);
							$__down .= "remove_column('{$_into}', '{$p[0]}');\n";
						}
				break;
				
				/* em testes */
				case "remove_column":
					foreach($params as $param)
						{
							$p 				= explode(":", $param);
							$__up .= "remove_column('{$_into}', '{$p[0]}');\n";
						}
					$__down		= "add_column('{$_into}', array(
								  	".join(",\n\t\t\t\t\t\t\t\t\t", $_params)."
								   ));";
				break;
			}
			
		/* lets replace our data and save our migration =) */
		$migration				= str_replace(array('#classname#', '#datetime#', '#filename#', '#up#', '#down#'), array($__classname, $__datetime, $__filename, $__up, $__down), $migration);
		
		#open and create file
		$_fixed_filename		= "../system/migrate/{$__datetime}_{$__filename}.php";
				
		if(file_write_contents($_fixed_filename, $migration))
			echo "
{$_fixed_filename} generated succesfully.<br />";
		else
			echo "
{$_fixed_filename} was not possible to save the file, please check the directory permissions and try again.<br />";
		
	}
		
	/**
	 * Generate a helper
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	function helper($params = array())
	{
		
		/* if we dont have params we cancel this request */
		if(count($params) == 0)
			die("was impossible to continue this generation because you didn't pass any param, run 'script/generate.php/help/helper' for more details.");
			
		/* alocate the helper base file in a variable */
		$helper				= file_get_contents("generate/helper.php");
			
		$action				= strtolower(array_shift($params));
		
		$__filename			= $action.'_helper.php';
		
		foreach($params as $param)
			{
				$param = strtolower($param);
				$_function[] = '
if( !function_exists("'.$param.'"))
{
	function '.$param.'()
	{
		
	}
}';
			}
		$__functions			= join("\n\n", $_function);

		/* lets replace our data and save our helper =) */
		$helper					= str_replace(array('#filename#', '#functions#'), array($__filename, $__functions), $helper);
		
		#open and create file
		$_fixed_filename		= "../system/application/helpers/{$__filename}";
				
		if(file_write_contents($_fixed_filename, $helper))
			echo "
{$_fixed_filename} generated succesfully.<br />";
		else
			echo "
{$_fixed_filename} was not possible to save the file, please check the directory permissions and try again.<br />";

	}
	
	/*--------------------------------------------------------- FUNCTIONS */
		
	/**
	 * List a helper for all commands
	 *
	 * @return string
	 * @author Dante.leonardo
	 **/
	function help($params = array())
	{
		$commands = array("controller", "model", "view", "helper", "migration");
		if(count($params) == 0)
			include("generate/help/all.php");
		else
			{
				if(in_array($params[0], $commands))
					echo nl2br(file_get_contents("generate/help/{$params[0]}.php"));
				else
					echo "the help content you were trying to list was not found, please, run 'script/generate.php/help' for all commands.";
			}
	}
	
	/**
	 * Write data on a file
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
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

?>