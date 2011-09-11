<?php
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
	
	protected function _initialize($config)
	{
		$this->addFilter('admin::com.flickr.filter.photos');
		
		parent::_initialize($config);
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
			$this->_url->setQuery(array(
				'method' => 'flickr.reflection.getMethods',
				'format' => 'json',
				'nojsoncallback' => 1,
				'api_key' => self::$_config['api_key']
			));
			$response = $this->send($this->_url);
	
			foreach($response->methods->method as $method)
			{
				list($flickr,$scope,$method) = explode('.',$method->_content);
				if( !isset(self::$_flickr_methods[$scope]) ) self::$_flickr_methods[$scope] = array();
				array_push(self::$_flickr_methods[$scope], $method);
			}
		}
	
		return self::$_flickr_methods;
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
    	$scope = $this->getIdentifier()->name;
    	
        if (array_key_exists($scope, self::$_flickr_methods) !== false && array_search($method, self::$_flickr_methods[$scope]) !== false)
        {
        	$this->method($scope.'.'.$method)->getResponse();
        	
            return $this;
        }

        return parent::__call($method, $args);
    }
}