<?PHP

/**
* To install this migration, run: script/migrate.php/seed	
* 
* Created by Leonardo Pereira (www.leonardopereira.com)
*/

class Seed
	{
		function run()
		{
				
			/* 
			Example of seed users 20 times
			
			for($i = 0; $i < 20; $i++)
			{
				$sql[]	= seed_table('users', array(
					'name'		=> "User Teste {$i}"
				));
			}
			*/
							
			return $sql;
		}
	}
		
/* end of file /migrate/seed.php */