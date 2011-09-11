<?php
jimport('joomla.cache.cache');
JLoader::import('filter.interface',dirname(__FILE__));
JLoader::import('filter.filter',dirname(__FILE__));
JLoader::import('filter.abstract',dirname(__FILE__));

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
     * The set of request filters for http request
     *
     * @var array
     */
   	protected $_filters = array();
	
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
		$config->append(array(
            'state'      		=> KFactory::tmp('lib.koowa.model.state'),
       		'command_chain' 	=> new KCommandChain(),
			'dispatch_events'   => false,
    		'enable_callbacks' 	=> false
		));
		
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
	 * Parse the result
	 * 
	 * This function passes the result throught read filter chain and returns the
	 * result.
	 *
	 * @return string	The parsed data
	 */
    public function parse()
    {
    	$context = $this->getCommandContext();
    	
    	$context->data = $this->_response;
    	
    	$result = $this->getCommandChain()->run(ComFlickrModelHttpFilter::MODE_READ, $context);
    	
        return $context;
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
	
	/**
	 * Adds one or multiple filters for response transformation
	 * 
	 * @param array 	Array of one or more behaviors to add.
	 * @return ComFlickrModelHttp
	 */
	public function addFilter($filters)
 	{
 		$filters =  (array) KConfig::toData($filters);
 	    
 	    foreach($filters as $filter)
		{
			if(!($filter instanceof ComFlickrModelHttpFilterInterface)) 
			{
				$identifier = (string) $filter;
				$filter     = ComFlickrModelHttpFilter::factory($filter);
			}
			else $identifier = (string) $filter->getIdentifier();
				
			//Enqueue the filter in the command chain
			$this->getCommandChain()->enqueue($filter);
			
			//Store the filter
			$this->_filters[$identifier] = $filter;
		}
		
		return $this;
 	}
	
	/**
	 * Get the filters for the http model
	 *
	 * @return array	An asscociate array of filters. The keys are the filter identifiers.
	 */
 	public function getFilters()
 	{
 		return $this->_filters;
 	}
 	
	/**
	 * Get a filter by identifier
	 *
	 * @return array	An asscociate array of filters keys are the filter identifiers
	 */
 	public function getFilter($identifier)
 	{
 		return isset($this->_filters[$identifier]) ? $this->_filters[$identifier] : null;
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
			
			$return = json_decode($this->_cache->get($request_key,$cache_group));
			if (!is_object($return))
			{
				$return = $this->_callCurl($url);
	        	$this->_cache->store(json_encode($return),$request_key,$cache_group);
			}
			else{
				echo 'cached data';
				exit;
			}
		}
		else {
			$return = $this->_callCurl($url);
		}
        
        if ( KRequest::get('get.format', 'cmd', 'html') == 'html' && !is_object($return) )
        {
        	$return = json_decode($return);
        }
        
        $this->_response = $return;
        
        $return = $this->parse();
        
        return $return;
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