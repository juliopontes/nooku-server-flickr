<?php
class ComFlickrViewPhotoHtml extends ComDefaultViewHtml
{
	protected function _initialize(KConfig $config)
    {
        $config->append(array(
			'auto_assign' => false,
        	'layout' => 'info'
        ));
        
        parent::_initialize($config);
    }
    
    public function display()
    {
    	die('test');
    }
}