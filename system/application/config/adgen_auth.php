<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adgen Auth configuration
 *
 * Global configuration settings that apply to authenticate user table.
 */

	$config['default']['users_table']			= 'users';
	$config['default']['field_username']		= 'login';
	$config['default']['field_password']		= 'senha';
	$config['default']['field_permission']		= 'permissoes';
	$config['default']['session_table']			= 'session_log_table';
	$config['default']['session_prefix']		= 'cidlsess';
	$config['default']['redirect_login']		= 'home';
	$config['default']['redirect_logout']		= 'login';
	$config['default']['require_activation']	= true;
	$config['default']['require_field']			= 'status';
	$config['default']['require_field_level']	= '1';

/* End of file adgen_auth.php */
/* Location: ./application/config/adgen_auth.php */