<?php
class ComFlickrModelHttpFilter
{
	/**
	 * Filter modes
	 */
	const MODE_READ  = 1;
	
	/**
	 * Factory method for KModelHttpFilterInterface classes.
	 *
	 * @param	mixed 	An object that implements KObjectIdentifiable, an object that
	 *                  implements KIdentifierInterface or valid identifier string
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return KTemplateFilter
	 */
	public static function factory($filter, $config = array())
	{		
	    //Create the behavior
	    if(!($filter instanceof ComFlickrModelHttpFilterInterface))
		{   
		    if(is_string($filter) && strpos($filter, '.') === false ) {
		       $filter = 'com.default.http.filter.'.trim($filter);
		    }
			
		    $filter = KFactory::tmp($filter, $config);
		    
		    if(!($filter instanceof ComFlickrModelHttpFilterInterface)) 
		    {
			    $identifier = $filter->getIdentifier();
			    throw new KDatabaseBehaviorException("Http filter $identifier does not implement KModelHttpFilterInterface");
		    }
		}
	    
		return $filter;
	}
}