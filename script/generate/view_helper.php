<?PHP

	/* view helper */
	
	/**
	 * Create a new directory
	 *
	 * @return void
	 * @author Dante.leonardo
	 **/
	if(!function_exists('create_directory'))
	{
		function create_directory($dir)
		{
			if(is_dir($dir))
				return false;
			else
				return mkdir($dir);
		}
	}

?>