<?php
class ComFlickrControllerSetup extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'request' => array(
            	'view' => 'setup'
        	)
        ));

        parent::_initialize($config);
    }
    
    final function saveConfig()
    {
    	$api_key = KRequest::get('api_key', 'string');
    	ComFlickrModelDefault::setConfig('api_key',$api_key);
    }
    
    protected function _actionSave()
    {
    	$this->saveConfig();
    	
    	KFactory::get('admin::com.flickr.dispatcher')->dispatch();
    }
    
	protected function _actionApply()
    {
    	$this->saveConfig();
    }
}