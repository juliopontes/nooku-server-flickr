<?php
/**
 * Abstract Model Http Class
 *
 * @author		Julio Pontes <juliopontes@gmail.com>
 * @category	Koowa
 * @package     Koowa_Model
 * @uses		KObject
 */
abstract class ComFlickrModelHttp extends KModelAbstract
{
	/**
	 * URL request
	 * 
	 * @var mixed string url or KHttpUrl object
	 */
	protected $_url;
	
	/**
	 * A state object
	 *
	 * @var object
	 */
	protected $_state;
	
	/**
	 * Cache requested data
	 * 
	 * @var boolean true if need to cache results
	 */
	protected $_cache_request = true;
	
	/**
	 * Cached data
	 * 
	 * @var array
	 */
	protected static $_cache;
	
	/**
	 * List of requested urls
	 * 
	 * @var array
	 */
	protected $_requests = array();
	
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
        //If no config is passed create it
		if(!isset($config)) $config = new KConfig();

		parent::__construct($config);

		$this->_state = $config->state;
		$this->_url = KFactory::get('lib.koowa.http.url', array('url' => $this->_url));
	}
	
	/**
	 * Enqueue request url on a list
	 * 
	 * @param string $url
	 */
	public function queueRequest($url)
	{
		array_push($this->_requests, $url);
		
		return $this;
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
            'state'      => KFactory::tmp('lib.koowa.model.state')
       	));

       	parent::_initialize($config);
    }
    
	/**
	 * Get the object identifier
	 *
	 * @return	KIdentifier
	 * @see 	KObjectIdentifiable
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
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
    	if (is_object($property)) {
    		$property = (array) KConfig::toData($property);
    	}

    	if (is_array($property)) {
        	$this->_state->setData($property);
        } else {
        	$this->_state->insert($property,'string',$value);
        }

        return $this;
    }

    /**
     * Get the model state properties
     *
     * This function overloads the KObject::get() function and only acts on state
     * properties
     *
     * If no property name is given then the function will return an associative
     * array of all properties.
     *
     * If the property does not exist and a  default value is specified this is
     * returned, otherwise the function return NULL.
     *
     * @param   string  The name of the property
     * @param   mixed   The default value
     * @return  mixed   The value of the property, an associative array or NULL
     */
    public function get($property = null, $default = null)
    {
        $result = $default;

        if(is_null($property)) {
            $result = $this->_state->getData();
        }
        else
        {
            if(isset($this->_state->$property)) {
                $result = $this->_state->$property;
            }
        }

        return $result;
    }

    /**
     * Reset all cached data and reset the model state to it's default
     * 
     * @param   boolean If TRUE use defaults when resetting. Default is TRUE
     * @return KModelAbstract
     */
    public function reset($default = true)
    {
        $this->_state->reset($default);
        $this->_cache_request = array();
        self::$_cache = array();

        return $this;
    }

    /**
     * Method to get state object
     *
     * @return  object  The state object
     */
    public function getState()
    {
        return $this->_state;
    }
    
    /**
     * Request a single URL or queue request
     * 
     * @param mixed $url string or KHttpUrl object 
     */
	protected function request($url=null)
	{
		$return = null;
		
		if (is_null($url))
		{
			$return = array();
			foreach($this->_requests as $request_url)
			{
				if (!($request_url instanceof KHttpUrl)) $request_url = KFactory::get('lib.koowa.http.url', array('url' => $request_url));
				array_push($return, $this->_requestCurl($request_url));
			}
		}
		else {
			if (!($url instanceof KHttpUrl)) $url = KFactory::get('lib.koowa.http.url', array('url' => $url));
			$return = $this->_requestCurl($url);
		}
        
        return $return;
	}
	
	private function _requestCurl($url)
	{
		$request_key = md5((string)$url);
		
		if ($this->_cache_request)
		{
			if (!isset(self::$_cache[$request_key]))
			{
				$return = $this->_callCurl($url);
		        self::$_cache[$request_key] = $return;
			}
			else {
				$return = self::$_cache[$request_key];
			}
		}
		else {
			$return = $this->_callCurl($url);
		}
        
        if ( KRequest::get('format', 'string','html') == 'html' )
        {
        	$return = json_decode($return);
        }
        
        return $return;
	}
	
	private function _callCurl($url)
	{
		$ch = curl_init((string)$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $return = curl_exec($ch);
        curl_close($ch);
        
        return $return;
	}
    
    /**
     * get response form requested url
     * 
     * @return request response
     */
	public function getResponse()
    {
    	return $this->request($this->_url->setQuery($this->_state->getData()));
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
        if(isset($this->_state->$method)) {
            return $this->set($method, $args[0]);
        }

        return parent::__call($method, $args);
    }
}