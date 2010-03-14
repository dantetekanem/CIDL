<?PHP

	/*
	* Create by Dante.leonardo (www.leonardopereira.com)
	* @version 0.1 alpha
	*
	* This Basic Model extends the DataMapper to accepts a more dynamic data and callbacks
	* I'll extend this model for upgrading my user control
	*/

	if(!class_exists('DataMagipper'))
	{
		
		class DataMagipper extends DataMapper
		{
			
			// callback methods:
			
			var $before_create;
			var $after_create;
			
			var $before_save;
			var $after_save;
			
			var $before_delete;
			var $after_delete;
			
			var $before_update;
			var $after_update;
			
			// callbacks has special properties:
			# on => [create, update]
			# if => call a callback validation function
			
			// nested attributes method
			var $accepts_nested_attributes_for = array(); 
			var $relatedObjects;
			
			function DataMagipper($data = array())
			{
				
				// no ID by default
				$this -> id 		= NULL;
				
				// autopopulate
				$__autopopulateFields	= '';
				
				// Mount all variables
				if (is_array($data) && count($data) > 0) 
				{
					foreach ($data as $var => $value) 
					{
						if(is_array($value))
							continue;
							
						if(in_array($var, $this->accepts_nested_attributes_for))
							continue;
						
						$this -> {$var} = $value;
						$__autopopulateFields[$var]		= $value;
					}
				}
				else if (is_string($data) || is_int($data))
				{
					$this -> id = $data;
				}
				
				// Accepts_nested_attributes_for
				
				if(count($this->accepts_nested_attributes_for) > 0)
				{
					foreach($this->accepts_nested_attributes_for as $attr)
					{
						if(is_array($data[$attr.'_attributes']) && count($data[$attr.'_attributes']) > 0)
						{
							// Test If Is a Multiple Relational Attributes
							foreach($data[$attr.'_attributes'] as $key => $value)
							{
								if($_first)
									continue;
									
								$_olddata		= array($key => $value);
								$_first			= $value;
								array_shift($data[$attr.'_attributes']);
							}
							
							if(is_array($_first) && count($_first) > 0)
							{
								$data[$attr.'_attributes'] = array_merge($data[$attr.'_attributes'], $_olddata);
								foreach($data[$attr.'_attributes'] as $k)
								{
									$attrUcfirst		= ucfirst($attr);
									${$attr} 	 		= new $attrUcfirst($k);
									${$attr} -> save();
									$this->relatedObjects[] = ${$attr};
								}
							}
							else
							{
								$data[$attr.'_attributes'] = array_merge($data[$attr.'_attributes'], $_olddata);
								$attrUcfirst		= ucfirst($attr);
								${$attr} 	 		= new $attrUcfirst($data[$attr.'_attributes']);
								${$attr} -> save();
								$this->relatedObjects[] = ${$attr};
							}
						}
					}
				}
				
				// Before create
				if (count($this->before_create) > 0) 
				{
					foreach ($this->before_create as $func) 
					{
						$this->{$func}();
					}
				}
				
				parent::__construct($this->id);
				
				# fix issue with __construct method
				if(is_array($__autopopulateFields) && count($__autopopulateFields) > 0)
				{
					foreach($__autopopulateFields as $field => $value)
						$this -> {$field} = $value;
				}
				
				// After create
				if (count($this->after_create) > 0) 
				{
					foreach ($this->after_create as $func) 
					{
						$this->{$func}();
					}
				}
			}
			
			function save($object = '', $related_field = '')
			{
				$is_a_update = !empty($this->id) ? true : false;
				
				// insert the nested attributes
				if (!empty($object)) 
				{
					$_objectInsert[]			= $object;
				}
				if (count($this->relatedObjects) > 0) 
				{
					foreach ($this->relatedObjects as $obj)
						$_objectInsert[]	= $obj;
				}
				
				if(!is_array($_objectInsert) || count($_objectInsert) == 0)
				{
					$_objectInsert				= '';
				}
				
				// Before update
				if (count($this->before_update) > 0 && $is_a_update) 
				{
					foreach ($this->before_update as $func) 
					{
						$this->{$func}();
					}
				}
				
				// Before save
				if (count($this->before_save) > 0) 
				{
					foreach ($this->before_save as $func) 
					{
						$this->{$func}();
					}
				}
				
				$ret = parent::save($_objectInsert, $related_field);
				
				// After update
				if (count($this->after_update) > 0 && $is_a_update) 
				{
					foreach ($this->after_update as $func) 
					{
						$this->{$func}();
					}
				}
				
				// After save
				if (count($this->after_save) > 0) 
				{
					foreach ($this->after_save as $func) 
					{
						$this->{$func}();
					}
				}
				
				return $ret;
			}
			
			function delete($object = '', $related_field = '')
			{
				if (count($this->before_delete) > 0) 
				{
					foreach ($this->before_delete as $func) 
					{
						$this->{$func}();
					}
				}
				
				$ret = parent::delete($object, $related_field);
				
				if (count($this->after_delete) > 0) 
				{
					foreach ($this->after_delete as $func) 
					{
						$this->{$func}();
					}
				}
				
				return $ret;
				
			}
			
			function update_attributes($data)
			{
				// Mount all variables
				if (is_array($data) && count($data) > 0) 
				{
					foreach ($data as $var => $value) 
					{
						if(is_array($value))
							continue;
							
						if(in_array($var, $this->accepts_nested_attributes_for))
							continue;
						
						$this -> {$var} = $value;
					}
				}
				else if (is_string($data) || is_int($data))
				{
					$this -> id = $data;
				}
				
				if(!empty($this->id))
					$this -> get_by_id($this -> id);
				
				// Before update
				if (count($this->before_update) > 0) 
				{
					foreach ($this->before_update as $func) 
					{
						$this->{$func}();
					}
				}
					
				$ret = $this -> save();
					
				// After update
				if (count($this->before_update) > 0) 
				{
					foreach ($this->before_update as $func) 
					{
						$this->{$func}();
					}
				}
			}
			
			function last()
			{
				$this -> order_by('id', 'desc') -> get(1);
				return $this;
			}
			
			function first()
			{
				$this -> order_by('id', 'asc') -> get(1);
				return $this;
			}
			
			function hook($hook)
			{
				$this -> get_by_hook($hook);
				return $this;
			}
			
			function all()
			{
				return $this -> get() -> all;
			}
			
			function is_valid()
			{
				return $this -> validate() -> valid;
			}
			
		}
	}
?>