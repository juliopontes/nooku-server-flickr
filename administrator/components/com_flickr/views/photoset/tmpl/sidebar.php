<?php defined('KOOWA') or die('Restricted access'); ?>
<style src="media://com_flickr/css/flickr.css" />
<div class="column_info">
	<h3><?= @text('source'); ?></h3>
	<div class="box">
	<p>
		<a href="<?= $photoset->url; ?>"><?= str_replace('http://www.flickr.com/photos/'.$photoset->owner.'/','',substr($photoset->url,0,-1)); ?></a>
	</p>
	<p>
		<a href="<?= $photoset->short_url; ?>">via (flickr.com)</a>
	</p>
	</div>
	
	<h3><?= @text('owner'); ?></h3>
	<div class="box">
		<p><?= $photoset->owner; ?></p>
	</div>
</div>