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
	 * Default config
	 * 
	 * @var array
	 */
	private static $_config = array('api_key' => '25e29fe5c8c606b38ef3fe473dfada36');
	
	/**
	 * Constructor of class
	 * 
	 * @param KConfig $config
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_state
			->insert('method'	,'word', '')
			->insert('format'	,'word','json')
			->insert('nojsoncallback', 'int', 1)
			->insert('api_key'	, 'word', self::$_config['api_key']);
		
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
	 * Create row data for model
	 */
	protected function _afterRequest()
	{
		//check requested format
		if ($this->_url->query['format'] == 'json')
		{
			//decode json data
			$json_response = json_decode($this->_response);
			
			//transform data by method from flickr
			switch ($this->_url->query['method'])
			{
				case 'flickr.reflection.getMethods':
					$this->_response = JArrayHelper::getColumn($json_response->methods->method,'_content');
					return false;
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
        	$this->_state->format = 'json';
        	$this->method($scope.'.'.$method)->getResponse();
            return $this;
        }

        return parent::__call($method, $args);
    }
}