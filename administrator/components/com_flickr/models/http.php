<?php
jimport('joomla.cache.cache');

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
	 * Cached data
	 * 
	 * @var JCache object
	 */
	protected $_cache;
	
	/**
	 * Boolean config if resquest will be cached
	 * 
	 * @var boolean true for cache requests
	 */
	protected $_cache_request = false;
	
	/**
	 * List of requested urls
	 * 
	 * @var array
	 */
	protected $_requests = array();
	
	/**
	 * Response from requested data
	 * 
	 * @var string
	 */
	protected $_response;
	
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config = null)
	{
        //If no config is passed create it
		if(!isset($config)) $config = new KConfig();
		$config->append(array(
			'cache_request' => true,
			'cache_time'	=> 14400
		));

		parent::__construct($config);
		
		$this->_state = $config->state;
		$this->_url = KFactory::get('lib.koowa.http.url', array('url' => $this->_url));
		
		 // Mixin a command chain
        $this->mixin(new KMixinCommandchain($config->append(array('mixer' => $this))));
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
		$this->_cache = JCache::getInstance();
		$this->_cache->setCaching($config->cache_request);
		$this->_cache->setLifeTime($config->cache_time);
		$this->_cache_request = $config->cache_request;

       	parent::_initialize($config);
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
     * Reset all cached data and reset the model state to it's default
     * 
     * @param   boolean If TRUE use defaults when resetting. Default is TRUE
     * @return KModelAbstract
     */
    public function reset($default = true)
    {
    	parent::reset();
        $this->_cache_request = array();
        self::$_cache = array();
        
        return $this;
    }
    
    /**
     * Request a single URL or queue request
     * 
     * @param mixed $url string or KHttpUrl object
     */
	public function send($url=null)
	{
		$return = null;
		
		if (is_null($url))
		{
			$return = array();
			foreach($this->_requests as $request_url)
			{
				array_push($return, $this->_requestCurl($request_url));
			}
		}
		else {
			$return = $this->_requestCurl($url);
		}
        
        return $return;
	}
	
 	
	private function _requestCurl($url)
	{
		if (!($url instanceof KHttpUrl)) $url = KFactory::get('lib.koowa.http.url', array('url' => $url));
		$this->_url = $url;
		
		$request_key = md5((string)$url);
		
		if ($this->_cache_request)
		{
			$cache_group = (string)$this->getIdentifier();
			$cache_group = str_replace('::','.',$cache_group);
			$cache_group = str_replace('.','_',$cache_group);
			
			$this->_response = $this->_cache->get($request_key,$cache_group);
			if (empty($this->_response))
			{
				$this->_cache->store($this->_callCurl($url),$request_key,$cache_group);
			}
		}
		else {
			$this->_callCurl($url);
		}
		
        $this->_afterRequest();
        
        return $this->_response;
	}
	
	/**
	 * after request function
	 * 
	 * @return void
	 */
	protected function _afterRequest()
	{
		
	}
	
	/**
	 * Create an Item
	 * 
	 * @param array $data
	 */
	public function createItem($data = array())
	{
		return KFactory::tmp('lib.koowa.database.row.default', $data);
	}
	
	/**
	 * Return new rowset default
	 * 
	 * @return RowsetDefault object
	 */
	public function createRowset()
	{
		return KFactory::tmp('lib.koowa.database.rowset.default');
	}
	
	/**
	 * call curl
	 * 
	 * @param mixed $url string or KHttpUrl
	 */
	private function _callCurl($url)
	{
		$ch = curl_init((string)$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $this->_response = curl_exec($ch);
        curl_close($ch);
        
        return $this->_response;
	}
    
    /**
     * get response form requested url
     * 
     * @return request response
     */
	public function getResponse()
    {
    	return $this->send($this->_url->setQuery($this->_state->getData()));
    }
}