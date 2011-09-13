<?php
class ComFlickrViewPhotosetHtml extends ComDefaultViewHtml
{
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'layout' => 'info'
    	));
    	
        parent::_initialize($config);
    }
}