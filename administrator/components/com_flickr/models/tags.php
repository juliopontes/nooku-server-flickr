<?php
class ComFlickrModelTags extends ComFlickrModelDefault
{
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
				case 'flickr.tags.getHotList':
					$tags = $json_response->hottags;
					$this->_total = $tags->count;
					
					$rowset = $this->createRowset();
					foreach ($tags->tag as $tag)
					{
						$data = array(
							'score' => $tag->score,
							'name' => $tag->_content,
						);
						
						$rowset->insert($this->createItem(array('data' => $data)));
					}
					
					$this->_list = $rowset;
					break;
			}
		}
	}
	
	public function getList()
	{
		if (empty($this->_list))
		{
			//by default we get 20 tags from hot list
			$this->set('count',20)->getHotList();
		}
		
		return parent::getList();
	}
}