<?PHP if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


	/*
	
		Adgen - Authentication Class
		@author			Dante.leonardo - www.leonardopereira.com
		@date			dec.24.2009
		@version		2.0
		
		Require:		session class.
		
		This is a PHP Library for Code Igniter Framework.
	
	*/
	
	
	class Adgen_auth
		{
			
			# CI global variabel
			var $CI;
			
			# table where the users are located
			var $users_table;
			
			# username field for authenticate
			var $field_username;
			
			# password field for authenticate
			var $field_password;
			
			# permissions field for area access
			/*
			
				Note: This is a optional implement on the main class.
					If you want to give a user, only permission in 2 sections. Your permission field must contain a value like: section_one,section_two.
					Separeted by commas.
					On the system, this will work very easy.
						Like: $this -> adgen_auth -> can_access("section_one");
					If your permission is in the commas, your access will be garanted. If not, the function return false.
							
			*/
			var $field_permission;
			
			# session table
			var $session_table;
			
			# session login prefix
			var $session_prefix;
			
			# after login, redirect to
			var $redirect_login;
			
			# after logout, redirect to
			var $redirect_logout;
			
			# user require activation for access
			var $require_activation;
			
			# activation field for access
			var $require_field;
			
			# activation field level for access
			var $require_field_level;
			
			# errors array
			var $_errors;
			
			
			# system variables
			var $_userdata;
			var $_sessiondata;
			
			# config use
			var $config;
			
			public function __construct ($config = array('use' => 'default'))
				{	
					# append CI to my class
					$this -> CI =& get_instance();
					
					$this -> CI -> load -> database();
					$this -> CI -> load -> helper('cookie');
					
					$this -> config = $config['use'];
					
					$this -> CI -> load -> config('adgen_auth');
					$this -> initialize();
					
					log_message('debug', 'Adgen auth class initialized !');
				}
				
			private function initialize ()
				{
					$this -> users_table 			= $this -> CI -> config -> item('users_table', $this->config);
					$this -> multiple_users_table 	= $this -> CI -> config -> item('multiple_users_table', $this->config);
					$this -> redirect_user_to		= $this -> CI -> config -> item('redirect_user_to', $this->config);
					$this -> field_username 		= $this -> CI -> config -> item('field_username', $this->config);
					$this -> field_password 		= $this -> CI -> config -> item('field_password', $this->config);
					$this -> field_permission		= $this -> CI -> config -> item('field_permission', $this->config);
					$this -> session_table 			= $this -> CI -> config -> item('session_table', $this->config);
					$this -> session_prefix			= $this -> CI -> config -> item('session_prefix', $this->config);
					$this -> redirect_login			= $this -> CI -> config -> item('redirect_login', $this->config);
					$this -> redirect_logout		= $this -> CI -> config -> item('redirect_logou', $this->config);
					$this -> require_activation		= $this -> CI -> config -> item('require_activation', $this->config);
					$this -> require_field			= $this -> CI -> config -> item('require_field', $this->config);
					$this -> require_field_level	= $this -> CI -> config -> item('require_field_level', $this->config);
				}
				
			public function login ( $username, $password, $redirect = '', $remember_login = false, $ignora_store_location = false )
				{
					
					if ( empty($username) || $username == "" )
						{
							$this -> add_error ( "Informe o login do usu&aacute;rio." );
							return false;
						}
						
					if ( empty($password) || $password == "" )
						{
							$this -> add_error ( "Informe a senha do usu&aacute;rio." );
							return false;
						}
						
					$password_encrypted			= md5($password);
					
					$user						= $this -> CI -> db -> where ( $this->field_username, $username ) -> get($this->users_table);
					
					if ( $user->num_rows() == 0 )
						{
							$this -> add_error ( "O usu&aacute;rio n&atilde;o foi encontrado." );
							return false;
						}
					
					$user_data					= $user->row();
					$fp							= $this->field_password;
							
					if ( $this->field_password != $this->field_username )
						{
							if ( $password_encrypted != $user_data->$fp )
							{
								$this -> add_error ( "A senha do usu&aacute;rio est&aacute; incorreta." );
								return false;
							}
						}
						
					if ( $this->require_activation == true )
						{
							$rf 				= $this -> require_field;
							if ( $user_data->$rf != $this -> require_field_level )
								{
									$this -> add_error ( "O usu&aacute;rio n&atilde;o foi ativado." );
									return false;
								}
						}
						
					if ( $this -> _saveLoginSession( $user_data, $remember_login ) )
						{
							if($ignore_store_location)
								$this -> clear_location();
							if ( !empty($redirect) || $redirect != "" )
								$this -> _redirect($redirect);
							else
								$this -> _redirect($this->redirect_login);
						}
					
				}
				
			public function login_no_md5 ( $username, $password, $redirect = '', $remember_login = false, $ignore_store_location = false )
				{
					
					if ( empty($username) || $username == "" )
						{
							$this -> add_error ( "Informe o login do usu&aacute;rio." );
							return false;
						}
						
					if ( empty($password) || $password == "" )
						{
							$this -> add_error ( "Informe a senha do usu&aacute;rio." );
							return false;
						}
						
					$password_encrypted			= ($password);
					
					$user						= $this -> CI -> db -> where ( $this->field_username, $username ) -> get($this->users_table);
					
					if ( $user->num_rows() == 0 )
						{
							$this -> add_error ( "O usu&aacute;rio n&atilde;o foi encontrado." );
							return false;
						}
					
					$user_data					= $user->row();
					$fp							= $this->field_password;
							
					if ( $this->field_password != $this->field_username )
						{
							if ( $password_encrypted != $user_data->$fp )
							{
								$this -> add_error ( "A senha do usu&aacute;rio est&aacute; incorreta." );
								return false;
							}
						}
						
					if ( $this->require_activation == true )
						{
							$rf 				= $this -> require_field;
							if ( $user_data->$rf != $this -> require_field_level )
								{
									$this -> add_error ( "O usu&aacute;rio n&atilde;o foi ativado." );
									return false;
								}
						}
						
					if ( $this -> _saveLoginSession( $user_data, $remember_login ) )
						{
							if($ignore_store_location)
								$this -> clear_location();
							return true;
						}
					
				}
				
			/**
			 * Multiple Login
			 * procura por várias tabelas até encontrar o login correspondente.
			 *
			 * @return bool
			 * @author Leonardo Pereira
			 **/
			public function multiple_login ($username, $password, $remember_login = false, $ignore_clear_location = false)
				{
					
					if ( empty($username) || $username == "" )
						{
							$this -> add_error ( "Informe o login do usu&aacute;rio." );
							return false;
						}
						
					if ( empty($password) || $password == "" )
						{
							$this -> add_error ( "Informe a senha do usu&aacute;rio." );
							return false;
						}
						
					$password_encrypted			= md5($password);
					
					foreach ($this -> multiple_users_table as $table) 
					{
						
						$user						= $this -> CI -> db -> where ( $this->field_username, $username ) -> get($table);
						
						if ( $user->num_rows() == 0 )
							continue;
						
						$user_data					= $user->row();
						$fp							= $this->field_password;
								
						if ( $this->field_password != $this->field_username )
							{
								if ( $password_encrypted != $user_data->$fp )
									continue;
							}
							
						if ( $this->require_activation == true )
							{
								$rf 				= $this -> require_field;
								if ( $user_data->$fp != $this -> require_field_level )
									continue;
							}
							
						if ( $this -> _saveLoginSession ( $user_data, $remember_login, $this -> redirect_user_to[$table][1] ) )
							{
								if($ignore_store_location)
									$this -> clear_location();
								$this -> _redirect($this -> redirect_user_to[$table][0]);
							}
							
					}
					
					$this -> add_error("Nenhum login encontrado");
					
				}
				
			/**
			 * Salva a URL atual para manter o registro da ultima página que o usuário visitou, caso não logado.
			 *
			 * @return void
			 * @author Dante.leonardo
			 **/
			public function store_location()
			{
				$url				= $this -> CI -> uri -> uri_string().'?'.$this->CI->input->xss_clean($_SERVER['QUERY_STRING']);
				
				set_cookie('store_location_'.$this->session_prefix, $url, time()+60*60*60);
			}
			
			/**
			 * Retorna a URL salva pela sessão, caso não existe, retorna falso.
			 *
			 * @return boolean or string
			 * @author Dante.leonardo
			 **/
			public function get_location()
			{
				return get_cookie('store_location_'.$this->session_prefix);
			}
			
			/**
			 * Limpa o local do usuário, caso você não queira lembrar a última página (automático no logout)
			 *
			 * @return void
			 * @author Dante.leonardo
			 **/
			public function clear_location()
			{
				delete_cookie('store_location_'.$this->session_prefix);
			}
				
			private function _saveLoginSession ( $user_data, $remember_login = false, $session_name = '' )
				{			
					
					$uf						= $this -> field_username;
					$pf						= $this -> field_password;
							
					$session_data = array(
						'session_id' 		=> uniqid(md5(time())),
						'session_time' 		=> time(),
						'user_id' 			=> $user_data->id,
						'user_ip' 			=> $this -> CI -> input -> ip_address(),
						'username'			=> $user_data->$uf,
						'password'			=> $user_data->$pf
					);	
					
					if($remember_login==false)
						$time = 0;
					else
						$time = 60*60*24*7;
										
					set_cookie((($session_name != "") ? $session_name : $this->session_prefix).'session_id', $session_data['session_id'], $time);
					
					if ( $this -> CI -> db -> insert ( $this->session_table, $session_data ) )
						return true;
							
				}
				
			private function _redirect ( $page )
				{
					$this -> CI -> load -> helper('url');
					if($this->get_location())
						redirect($this->get_location());
						
					if ( end(explode(".",$page)) == "php")
						header("Location: ".base_url().$page);
					else
						redirect($page);
				}
			
			public function logout ( $redirect = '' )
				{
					if ( !$this -> is_logged() )
						{
							$this -> add_error("Voc&ecirc; j&aacute; est&aacute; fora do sistema.");
							return false;
						}
					else
						{
							
							$session_id		= get_cookie($this->session_prefix.'session_id');
							
							if ( $this -> CI -> db -> delete( $this->session_table, array( 'session_id' => $session_id ) ) )
								{
									delete_cookie($this->session_prefix.'session_id');
									$this -> clear_location();
									// let's clean the system variables alocated here.
									unset($this -> _userdata);
									unset($this -> _sessiondata);
									return true;
								}
							else
								{
									$this -> add_error("N&atilde;o foi encontrado uma sess&atilde;o do login no banco de dados.");
									return false;
								}
						}
				}
				
			public function is_logged ()
				{
					$data = $this -> get_session_data();
					
					if ( is_object($data) && count($data) > 0 )
						return TRUE;
					else
						{
							return FALSE;
						}
				}
			
			public function can_access ( $area = '' )
				{
					$this -> get_session_data();
				
					$fp	= $this -> field_permission;
					
					$permissions = explode(",", $this -> _userdata -> $fp);
					
					if ( count($permissions) == 0 )
						$permissions[] = $this -> _userdata -> $fp;
					
					if ( in_array($area, $permissions) )
						return true;
					else
						return false;
						
				}
				
			public function required_level ( $level = '' )
				{
					if(!$level)
						return false;
						
					$this -> get_session_data();
					
					if ( $this -> _userdata -> level == $level )
						return true;
					else
						return false;
											
				}
				
			private function get_session_data()
				{
					
					if ( count($this -> _userdata) > 0 )
						return $this -> _userdata;
					
					$session_id				= get_cookie ( $this -> session_prefix . 'session_id' );
					
					if (!$session_id)
						return false;
					
					if ( count ( $this -> _sessiondata) == 0 )
						{
							$this -> CI -> db -> where ( 'session_id', $session_id );
							$this -> _sessiondata	= $this -> CI -> db -> get ( $this->session_table ) -> row();
						}
					
					$this -> CI -> db -> where ( $this -> field_username, $this -> _sessiondata -> username );
					$this -> CI -> db -> where ( $this -> field_password, $this -> _sessiondata -> password );
					
					$this -> _userdata		= $this -> CI -> db -> get ( $this -> users_table ) -> row();
					
					if ( count($this->_userdata) > 0 )
						return $this -> _userdata;
					else
						return false;
					
				}
				
			public function retrieve ( $data = '' )
				{
					// if blank, return ALL data
					// if string, return SELECTED data
					// if array, return a MOUNT of data
					
					if ( !$this->is_logged() )
						return false;
						
					$this -> get_session_data();
						
					if ( count($this->_userdata) > 0 )
						{
							if ( $data == "" || empty($data) )
								return $this -> _userdata;
							if ( is_string($data) && $data != "" )
								return $this -> _userdata -> $data;
							if ( is_array($data) && count($data) > 0 )
								{
									foreach ( $data as $field )
										{
											$return -> $field = $this -> _userdata -> $field;
										}
									return $return;
								}
								
							return;								
						}
					else
						{
							return false;
						}
									
				}
				
			public function add_error ( $error_name = '' )
				{
					if ( count($this->_errors) == 0 )
						{
							$this -> _errors[] = $error_name;
							return;
						}
					if ( !in_array($this->_errors, $error_name) )
						$this -> _errors[] = $error_name;
				}
				
			public function display_errors ( $start_as = '<p>', $close_as = '</p>' )
				{
					if (count($this->_errors) == 0)
						return false;
					
					foreach ( $this->_errors as $erro )
						$data = $start_as . $erro . $close_as . "\n";
							
					return	$data;
				}
				
		}