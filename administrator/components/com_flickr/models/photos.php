<?php
class ComFlickrModelPhotos extends ComFlickrModelDefault
{
	/**
	 * Constructor of class
	 * 
	 * @param KConfig $config
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_state
			->insert('photo_id'	,'word', KRequest::get('get.id','int'));
	}
	
	public function getItem()
	{
		if(empty($this->_item))
		{
			$this->__call('getInfo',array());
		}
		
		return $this->_item;
	}
	
	/**
	 * Create row data for model
	 */
	protected function _afterRequest()
	{
		parent::_afterRequest();
		
		//check requested format
		if ($this->_url->query['format'] == 'json')
		{
			//decode json data
			if (!is_array($this->_response) && !is_object($this->_response))
				$json_response = json_decode($this->_response);
			
			//transform data by method from flickr
			switch ($this->_url->query['method'])
			{
				case 'flickr.photos.getInfo':
					$photo = $json_response->photo;
					$owner = $photo->owner;
					
					$photoUrl = JArrayHelper::getColumn($photo->urls->url,'_content');
					if( count($photoUrl) == 1 ) $photoUrl = $photoUrl[0];
					
					$data = array(
						'id' => $photo->id,
						'img' => KFactory::get('admin::com.flickr.template.helper.image')->photo($photo),
						'owner' => array(
							'nsid' => $owner->nsid,
							'username' => $owner->username,
							'realname' => $owner->realname,
							'location' => $owner->location
						),
						'title' => $photo->title->_content,
						'taken_date' => $photo->dates->taken,
						'posted_date' => $photo->dates->posted,
						'nr_comments' => $photo->comments->_content,
						'description' => $photo->description->_content,
						'url' => $photoUrl,
						'tags' => JArrayHelper::getColumn($photo->tags->tag,'_content')
					);
					
					$this->_item = $this->createItem(array('data' => $data));
					break;
			}
		}
	}
}