<?php
class ComFlickrModelDefault extends KModelAbstract
{
	static $methods = array();
	static $arguments = array('api_key' => '25e29fe5c8c606b38ef3fe473dfada36');
	static $cache = array();
	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->_state
			->insert('method'	,'word')
			->insert('format'	,'word','json')
			->insert('nojsoncallback', 'int', 1)
			->insert('api_key'	, 'word', self::$arguments['api_key']);
			
		$this->getMethods();
	}
	
	public function method($method_name)
	{
		$method_name = 'flickr.'.str_replace('flickr.','',$method_name);
		$this->set('method',$method_name);
		
		return $this;
	}
	
	/**
     * Set the model state properties
     *
     * This function overloads the KObject::set() function and only acts on state properties.
     *
     * @param   string|array|object	The name of the property, an associative array or an object
     * @param   mixed  				The value of the property
     * @return	KModelAbstract
     */
    public function set( $property, $value = null )
    {
    	if(is_object($property)) {
    		$property = (array) KConfig::toData($property);
    	}

    	if(is_array($property)) {
        	$this->_state->setData($property);
        } else {
        	if ( !isset($this->_state->$property) )
        	{
        		$this->_state->insert($property,'word', $value);
        	}
        	else {
        		$this->_state->$property = $value;
        	}
        }

        return $this;
    }
	
	public function getMethods()
	{
		$response = $this->curlRequest('http://api.flickr.com/services/rest/?method=flickr.reflection.getMethods&format=json&nojsoncallback=1&api_key='.self::$arguments['api_key']);
	
		if (empty(self::$methods))
		{
			foreach($response->methods->method as $method)
			{
				list($flickr,$scope,$method) = explode('.',$method->_content);
				if( !isset(self::$methods[$scope]) ) self::$methods[$scope] = array();
				array_push(self::$methods[$scope], $method);
			}
		}
	
		return self::$methods;
	}
	
	private function curlRequest($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $return = curl_exec($ch);;
        curl_close($ch);
        
        if( KRequest::get('format', 'string','html') == 'html' ) $return = json_decode($return);
        
        return $return;
	}
	
	public function getResponse()
    {
    	if (empty($this->_response))
    	{
    		$url = 'http://api.flickr.com/services/rest/';
    		$arguments = array();
    		foreach ($this->_state->getData() as $key => $val)
    		{
    			$arguments[] = $key.'='.$val;
    		}
    		$url .= '?'.implode('&',$arguments);
    		
    		$this->_response = $this->curlRequest($url);
    	}
    	
        return $this->_response;
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
    	$scope = KInflector::getPart(get_class($this), -1);
        if (array_search($method, self::$methods[$scope]) !== false)
        {
        	$this->method($scope.'.'.$method);
        	
        	$response = $this->getResponse();
        	
        	if ($response->stat != 'ok')
        	{
        		throw new Exception($response->message,$response->code);
        	}
        	
            return $response;
        }

        return parent::__call($method, $args);
    }
}