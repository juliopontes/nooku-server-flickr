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
    	die('not working');
    	
    	return parent::display();
    }
    
    //$this->assign($this->getName(), KFactory::tmp('admin::com.flickr.model.photos')->set('photo_id',KRequest::get('get.id','string'))->getInfo());
}