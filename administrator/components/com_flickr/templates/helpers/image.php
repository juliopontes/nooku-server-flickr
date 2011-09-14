<?php
class ComFlickrTemplateHelperImage extends KTemplateHelperAbstract
{
	private $expElement = "/\{[\w\s\d\[\];:]*\}/";
	
	public function photo( $data = array() )
	{
		$config = new KConfig($data['photo']);
		if (!isset($data['size']))
		{
			$url = 'http://farm{farm}.static.flickr.com/{server}/{id}_{secret}.jpg';
		}
		else {
			$config->append(array(
				'size' => $data['size']
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

		$alt = !empty($config->alt) ? $config->alt : '' ;
		
		return '<img src="'.$src.'" alt="'.$alt.'" />';
	}
}