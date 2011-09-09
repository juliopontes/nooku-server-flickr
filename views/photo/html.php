<?php
class ComFlickrViewPhotoHtml extends ComDefaultViewHtml
{
	protected $_auto_assign = false;
	
	protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'layout' => 'info'
        ));
        
        parent::_initialize($config);
    }
    
	public function display()
	{
		$name  = $this->getName();
		$this->assign($name, KFactory::tmp('admin::com.flickr.model.photos')->set('photo_id',KRequest::get('id','string'))->getInfo());
		
		return parent::display();
	}
}