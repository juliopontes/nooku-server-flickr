<?php
class ComFlickrTemplateHelperTags extends KTemplateHelperAbstract
{
	private $expElement = "/\{[\w\s\d\[\];:]*\}/";
	
	public function photo( $data = array() )
	{
		$dataConfig = new KConfig($data);
		$dataConfig->append(array(
			'separator' => ',',
			'html' => '{tag}'
		));
		
		$tagsHtml = array();
		foreach ($data['photo']->tags->tag as $tag)
		{
			$tagConfig = new KConfig($tag);
			$tagConfig->append(array(
				'tag' => $tag->_content
			));
			
			preg_match_all($this->expElement,$dataConfig->html,$arrMatch);
			$ArrPat = end($arrMatch);
			$tagHtml = $dataConfig->html;
			foreach($ArrPat as $varName)
			{
				$regexVar = '/'.$varName.'/';
				$cleanVar = str_replace('{','',str_replace('}','',$varName));
				
				$tagHtml = preg_replace($regexVar, $tagConfig->get($cleanVar), $tagHtml,1);
			}
			array_push($tagsHtml,$tagHtml);
		}

		return implode($data['separator'],$tagsHtml);
	}
}