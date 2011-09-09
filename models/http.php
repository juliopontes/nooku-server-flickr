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
	protected $_cache_request = false;
	
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
    	if(is_object($property)) {
    		$property = (array) KConfig::toData($property);
    	}

    	if(is_array($property)) {
        	$this->_state->setData($property);
        } else {
        	$this->_state->$property = $value;
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
     * @param string $url 
     */
	protected function request($url=null)
	{
		$return = null;
		
		if (is_null($url))
		{
			$return = array();
			foreach($this->_requests as $request_url)
			{
				array_push($return, $this->_requestCurl((string)$request_url));
			}
		}
		else {
			$return = $this->_requestCurl((string)$url);
		}
        
        return $return;
	}
	
	private function _requestCurl($url)
	{
		$request_key = md5($url);
		
		if ($this->_cache_request)
		{
			if (!isset(self::$cache[$request_key]))
			{
				$ch = curl_init($url);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		        $return = curl_exec($ch);
		        curl_close($ch);
		        
		        self::$cache[$request_key] = $return;
			}
			else {
				$return = self::$cache[$request_key];
			}
		}
		else {
			$ch = curl_init($url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	        $return = curl_exec($ch);
	        curl_close($ch);
		}
        
        if ( KRequest::get('format', 'string','html') == 'html' )
        {
        	$return = json_decode($return);
        }
        
        return $return;
	}
    
    /**
     * get response form requested url
     * 
     * @return request response
     */
	public function getResponse()
    {
    	$url = $this->_url;
    	$arguments = array();
    	foreach ($this->_state->getData() as $key => $val)
    	{
    		$arguments[] = $key.'='.$val;
    	}
    	if (!empty($arguments)) $url .= '?'.implode('&',$arguments);
    	
    	return $this->request($url);
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