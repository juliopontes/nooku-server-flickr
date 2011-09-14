<?php
class ComFlickrModelInterestingness extends ComFlickrModelDefault
{
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
				case 'flickr.interestingness.getList':
					$this->_total = $json_response->photos->total;
					
					$rowset = $this->createRowset();
					foreach($json_response->photos->photo as $photo)
					{
						$data = array(
							'id' => $photo->id,
							'title' => $photo->title,
							'image' => array(
								'id' => $photo->id,
								'secret' => $photo->secret,
								'farm' => $photo->farm,
								'server' => $photo->server
							)
						);
						$rowset->insert($this->createItem(array('data' => $data)));
					}
					
					$this->_list = $rowset;
					break;
			}
		}
	}
}