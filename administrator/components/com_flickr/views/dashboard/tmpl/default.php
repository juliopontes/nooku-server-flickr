<?php defined('KOOWA') or die('Restricted access'); ?>
<div id="sidebar">
	
	<?= @template('admin::com.flickr.view.photos.list', array('photos' => KFactory::tmp('admin::com.flickr.model.interestingness')->getList())); ?>
</div>