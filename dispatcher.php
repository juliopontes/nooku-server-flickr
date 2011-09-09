<?php
class ComFlickrDispatcher extends ComDefaultDispatcher
{
    protected function _initialize(KConfig $config)
    {
    	$api_key = ComFlickrModelDefault::getConfig('api_key');
    	
    	if (empty($api_key))
    	{
    		$controller = 'setup';
    	}
    	else {
    		$controller = 'dashboard';
    	}
    	
        $config->append(array(
            'controller' => $controller
        ));

        parent::_initialize($config);
    }
}