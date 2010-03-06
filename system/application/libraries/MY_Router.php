<?
    class MY_Router extends CI_Router {
        
        function MY_Router() {
            parent::CI_Router();
        }
        
        function _validate_request($segments) {
            
            $segments[0]    = $this->_strip_hyphens(@$segments[0]);
            $segments[1]    = $this->_strip_hyphens(@$segments[1]);
            
            // Does the requested controller exist in the root folder?
            if (file_exists(APPPATH.'controllers/'.$segments[0].EXT))
            {
                return $segments;
            }
            
            
            // Is the controller in a sub-folder?
            if (is_dir(APPPATH.'controllers/'.$segments[0]))
            {        
                // Set the directory and remove it from the segment array
                $this->set_directory($segments[0]);
                $segments = array_slice($segments, 1);
                
                if (count($segments) > 0)
                {
                    // Does the requested controller exist in the sub-folder?
                    if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
                    {
                        show_404($this->fetch_directory().$segments[0]);
                    }
                }
                else
                {
                    $this->set_class($this->default_controller);
                    $this->set_method('index');
                    
                    // Does the default controller exist in the sub-folder?
                    if ( ! file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
                    {
                        $this->directory = '';
                        return array();
                    }
                    
                }
                
                return $segments;
            }

            // Can't find the requested controller...
            show_404($segments[0]);
        }
        
        /**
		 * Set the route mapping
		 *
		 * This function determines what should be served based on the URI request,
		 * as well as any "routes" that have been set in the routing config file.
		 *
			 * Included: Filters the routes to matches with CI18n library.
		 *
		 * @access	private
		 * @return	void
		 */
		function _set_routing()
		{
			// Are query strings enabled in the config file?
			// If so, we're done since segment based URIs are not used with query strings.
			if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')]))
			{
				$this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));
	
				if (isset($_GET[$this->config->item('function_trigger')]))
				{
					$this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
				}
				
				return;
			}
			
			// Load the routes.php file.
			@include(APPPATH.'config/routes'.EXT);
			$this->routes = ( ! isset($route) OR ! is_array($route)) ? array() : $route;
			unset($route);
			
			// Load the ci18n locale files to administrate with the routes.
			@include(APPPATH.'config/ci18n.php');
			$languages		= $config['languages'];
			unset($config);
			
			foreach ($languages as $key => $value) {
				$this->routes['^'.$key.'/(.+)$']	= '$1';
				$this->routes['^'.$key.'$']			= $this->routes['default_controller'];
			}
	
			// Set the default controller so we can display it in the event
			// the URI doesn't correlated to a valid controller.
			$this->default_controller = ( ! isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);	
			
			// Fetch the complete URI string
			$this->uri->_fetch_uri_string();
		
			// Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
			if ($this->uri->uri_string == '')
			{
				if ($this->default_controller === FALSE)
				{
					show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
				}
				
				if (strpos($this->default_controller, '/') !== FALSE)
				{
					$x = explode('/', $this->default_controller);
	
					$this->set_class(end($x));
					$this->set_method('index');
					$this->_set_request($x);
				}
				else
				{
					$this->set_class($this->default_controller);
					$this->set_method('index');
					$this->_set_request(array($this->default_controller, 'index'));
				}
	
				// re-index the routed segments array so it starts with 1 rather than 0
				$this->uri->_reindex_segments();
				
				log_message('debug', "No URI present. Default controller set.");
				return;
			}
			unset($this->routes['default_controller']);
			
			// Do we need to remove the URL suffix?
			$this->uri->_remove_url_suffix();
			
			// Compile the segments into an array
			$this->uri->_explode_segments();
			
			// Parse any custom routing that may exist
			$this->_parse_routes();		
			
			// Re-index the segment array so that it starts with 1 rather than 0
			$this->uri->_reindex_segments();
		}
        
        function _strip_hyphens($string) {
            
            if (strstr($string, '-')) {
                return str_replace('-', '_', $string);
            }
            
            return $string;
        }
        
    }
?>