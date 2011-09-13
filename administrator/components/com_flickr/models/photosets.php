<?php
class ComFlickrModelPhotosets extends ComFlickrModelDefault
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
			->insert('photoset_id'	,'string', KRequest::get('get.id','string'));
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
			if (!is_object($this->_response) && !is_array($this->_response))
				$json_response = json_decode($this->_response);
				
			//transform data by method from flickr
			switch ($this->_url->query['method'])
			{
				case 'flickr.photosets.getPhotos':
					$photoset = $json_response->photoset;
					
					$this->_total = $photoset->total;
					
					$rowset = $this->createRowset();
					foreach ($photoset->photo as $photo)
					{
						$data = array(
							'id' => $photo->id,
							'title' => $photo->title,
							'img' => KFactory::get('admin::com.flickr.template.helper.image')->photo($photo)
						);
						
						$rowset->insert($this->createItem(array('data' => $data)));
					}
					
					$this->_list = $rowset;
					break;
				case 'flickr.photosets.getList':
					$photosets = $json_response->photosets;
					
					$this->_total = $photosets->total;
					
					$rowset = $this->createRowset();
					foreach ($photosets->photoset as $photoset)
					{
						$data = array(
							'id' => $photoset->id,
							'primary' => $photoset->primary,
							'secret' => $photoset->secret,
							'server' => $photoset->server,
							'farm' => $photoset->farm,
							'title' => $photoset->title->_content,
							'img' => KFactory::get('admin::com.flickr.template.helper.image')->photo($photoset),
							'description' => $photoset->description->_content,
							'created_date' => $photoset->date_create
						);
						
						$rowset->insert($this->createItem(array('data' => $data)));
					}
					
					$this->_list = $rowset;
					break;
				case 'flickr.photosets.getInfo':
					$photoset = $json_response->photoset;
					
					$photosetPhotos = KFactory::tmp('admin::com.flickr.model.photosets')->set('photoset_id',$photoset->id)->getPhotos()->getList();
					
					$data = array(
						'id' => $photoset->id,
						'owner' => $photoset->owner,
						'primary' => $photoset->primary,
						'photos' => $photosetPhotos,
						'nr_photos' => $photoset->count_photos,
						'nr_videos' => $photoset->count_videos,
						'title' => $photoset->title->_content,
						'description' => $photoset->description->_content,
						'created_date' => $photoset->date_create,
						'url' => "http://www.flickr.com/photos/{$photoset->owner}/sets/{$photoset->id}",
						'short_url' => "http://flickr.com/photos/{$photoset->owner}/sets/{$photoset->id}"
					);
					
					$this->_item = $this->createItem(array('data' => $data));
					break;
			}
		}
	}
	
	public function getItem()
	{
		if(empty($this->_item))
		{
			$this->__call('getInfo',array());
		}
		
		return $this->_item;
	}
}