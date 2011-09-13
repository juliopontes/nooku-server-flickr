<?php
jimport('joomla.utilities.arrayhelper');

class ComFlickrModelDefault extends ComFlickrModelHttp
{
	/**
	 * URL to rest service
	 * 
	 * @var string url
	 */
	protected $_url = 'http://api.flickr.com/services/rest/';
	
	/**
	 * Array of methods list from flickr
	 * 
	 * @var array methods
	 */
	private static $_flickr_methods = array();
	
	/**
	 * Flickr method identifier
	 * 
	 * @var string
	 */
	private $_flickr_method_identifier = '';
	
	/**
	 * Reflection of current method info
	 * 
	 * @var object
	 */
	protected $_flickr_current_method = null;
	
	/**
	 * List of required arguments
	 * 
	 * @var array
	 */
	protected $_required_arguments = array();
	
	/**
	 * Default config
	 * 
	 * @var array
	 */
	private static $_config = array(
		'api_key' => '25e29fe5c8c606b38ef3fe473dfada36',
		//auto assign aruments to requested method to model state
		'auto_assign' => true
	);
	
	/**
	 * Constructor of class
	 * 
	 * @param KConfig $config
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_state
			->insert('method'	,'string', '')
			->insert('format'	,'word','json')
			->insert('nojsoncallback', 'int', 1)
			->insert('api_key'	, 'string', self::$_config['api_key']);
		
		$this->getMethods();
	}
	
	/**
	 * Array of config
	 * 
	 * @param mixed $parameter string config or array config
	 */
	public static function getConfig($parameter=null)
	{
		if (isset(self::$_config[$parameter]) && !is_null($parameter))
		{
			return self::$_config[$parameter];
		}
		
		return self::$_config;
	}
	
	/**
	 * Set a parameter
	 * 
	 * @param mixed $parameter string config or array config
	 */
	public static function setConfig($property,$value=null)
	{
		if (isset(self::$_config[$property]) && !is_null($property))
		{
			self::$_config[$property] = $value;
		}
	}
	
	/**
	 * Set a method request from flickr api
	 * 
	 * @param string $method_name
	 * @return self instance
	 */
	public function method($method_name)
	{
		$method_name = 'flickr.'.str_replace('flickr.','',$method_name);
		$this->set('method',$method_name);
		$this->_method_identifier = $method_name;
		
		return $this;
	}
	
	/**
	 * get info form flickr method
	 *
	 * @param object method info
	 */
	public function getMethod($method_name)
	{
		$this->_url->set('http://api.flickr.com/services/rest/');
		$this->_url->setQuery(array(
			'method' => 'flickr.reflection.getMethodInfo',
			'method_name' => $method_name,
			'format' => 'json',
			'nojsoncallback' => 1,
			'api_key' => self::$_config['api_key']
		));
			
		return $this->send($this->_url);
	}
	
	/**
	 * List All Flickr Methods
	 * 
	 * @return array list of flickr methods
	 */
	public function getMethods()
	{
		if (empty(self::$_flickr_methods) && !empty(self::$_config['api_key']))
		{
			$this->_url->set('http://api.flickr.com/services/rest/');
			$this->_url->setQuery(array(
				'method' => 'flickr.reflection.getMethods',
				'format' => 'json',
				'nojsoncallback' => 1,
				'api_key' => self::$_config['api_key']
			));
			
			$methods = $this->send($this->_url);
	
			foreach($methods as $method)
			{
				list($flickr,$scope,$method) = explode('.',$method);
				if( !isset(self::$_flickr_methods[$scope]) ) self::$_flickr_methods[$scope] = array();
				array_push(self::$_flickr_methods[$scope], $method);
			}
		}
	
		return self::$_flickr_methods;
	}
	
	/**
	 * This method will auto assign vars from default arguments methods
	 * 
	 */
	protected function _beforeRequest()
	{
		parent::_beforeRequest();
		
		$requested_method = $this->_url->query['method'];
		
		if($requested_method != 'flickr.reflection.getMethodInfo' && $requested_method != 'flickr.reflection.getMethods' && self::$_config['auto_assign'])
		{
			$this->_flickr_current_method = $this->getMethod($requested_method);
			
			foreach($this->_flickr_current_method->arguments->argument as $argument)
			{
				$argument_name = $argument->name;
				$default_value = isset(self::$_config[$argument->name]) ? self::$_config[$argument->name] : KRequest::get('get.'.$argument->name,'string','');
				if ( !empty($default_value) ) {					
					$this->_state->insert($argument->name,'string',$default_value);
				}
				else{
					array_push($this->_required_arguments,$argument);
				}
			}
			
			//bind new states
			$this->_url->setQuery($this->_state->getData());
		}
	}
	
	/**
	 * Create row data for model
	 */
	protected function _afterRequest()
	{
		parent::_afterRequest();
		
		//check requested format
		if ($this->_url->query['format'] == 'json')
		{
			//decode json data
			$json_response = json_decode($this->_response);
			
			if ($json_response->stat == 'fail')
			{
				foreach ($this->_flickr_current_method->errors->error as $erro)
				{
					if ($erro->code == $json_response->code)
					{
						if (!empty($this->_required_arguments))
						{
							echo '<h1>'.$this->_flickr_current_method->method->name.'</h1>';
							
							echo 'Requested url: '.$this->_url;
							
							echo '<br /><br />Method arguments: <br />';
							echo '<ul>';
							foreach($this->_required_arguments as $argument)
							{
								echo '<li>';
									$mode = (strpos($argument->name,'id') !== false) ? 'required' : 'optional' ;
									echo $argument->name.' ('.$mode.') - '.$argument->_content;
								echo '</li>';
							}
							echo '</ul>';
						}
						
						echo "Error message: <br /><br />".$erro->_content;
						die;
					}
				}
			}
			
			//transform data by method from flickr
			switch ($this->_url->query['method'])
			{
				case 'flickr.reflection.getMethodInfo':
					$this->_response = $json_response;
					break;
				case 'flickr.reflection.getMethods':
					$this->_response = JArrayHelper::getColumn($json_response->methods->method,'_content');
					break;
			}
		}
	}
	
	public function getList()
	{
		if (empty($this->_list))
		{
			$this->__call('getList',array());
		}
		
		return $this->_list;
	}
	
	public function getTotal()
	{
		if (empty($this->_list))
		{
			$this->__call('getList',array());
		}
		
		return $this->_total;
	}
    
	/**
     * Supports a simple form Fluent Interfaces. Allows you to set states by
     * using the state name as the method name.
     *
     * For example : $model->sort('name')->limit(10)->getList();
     *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @return  KModelAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
    	if (empty(self::$_flickr_methods))
    	{
    		throw new KException('Flickr methods not loaded');
    	}
    	
    	$scope = $this->getIdentifier()->name;
    	
        if (array_key_exists($scope, self::$_flickr_methods) !== false && array_search($method, self::$_flickr_methods[$scope]) !== false)
        {
        	//@todo why format change to html ?
        	$this->_state->format = 'json';
        	$this->_state->api_key = self::$_config['api_key'];
        	
        	$this->method($scope.'.'.$method)->getResponse();
            return $this;
        }

        return parent::__call($method, $args);
    }
}