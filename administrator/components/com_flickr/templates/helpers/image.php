<?php
class ComFlickrTemplateHelperImage extends KTemplateHelperAbstract
{
	private $expElement = "/\{[\w\s\d\[\];:]*\}/";
	
	public function photo( $photo,$size=null )
	{
		$config = new KConfig($photo);
		
		if (is_null($size))
		{
			$url = 'http://farm{farm}.static.flickr.com/{server}/{id}_{secret}.jpg';
		}
		else {
			$config->append(array(
				'size' => $size
			));
			$url = 'http://farm{farm}.static.flickr.com/{server}/{id}_{secret}_{size}.jpg';
		}
		
		preg_match_all($this->expElement,$url,$arrMatch);
		$ArrPat = end($arrMatch);
		
		$src = $url;
		foreach($ArrPat as $varName)
		{
			$regexVar = '/'.$varName.'/';
			$cleanVar = str_replace('{','',str_replace('}','',$varName));
			
			$src = preg_replace($regexVar, $config->get($cleanVar), $src,1);
		}

		$alt = !empty($config->title->_content) ? $config->title->_content : '' ;
		
		return '<img src="'.$src.'" alt="'.$alt.'" />';
	}
}