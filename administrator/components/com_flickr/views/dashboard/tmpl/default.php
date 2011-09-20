<?php defined('KOOWA') or die('Restricted access'); ?>
<script src="media://com_flickr/js/mootools-more.js" />
<script src="media://com_flickr/js/lazyload.js" />
<script src="media://com_flickr/js/flickr.js" />
<style src="media://com_flickr/css/flickr.css" />

<div id="flickrdashboard" class="flickrpanel">
	<?= @template('admin::com.flickr.view.photos.column',array('column_title' => 'Flickr: Interessingness','photos' => KFactory::tmp('admin::com.flickr.model.interestingness')->getList())); ?>
	<?= @template('admin::com.flickr.view.photos.column',array('column_title' => 'Search: Nooku','photos' => KFactory::tmp('admin::com.flickr.model.photos')->set('text','nooku')->search()->getList())); ?>
	<?= @template('admin::com.flickr.view.photos.column',array('column_title' => 'Photoset: Nooku Server','photos' => KFactory::tmp('admin::com.flickr.model.photosets')->set('photoset_id','72157627021171180')->getPhotos()->getList())); ?>
	<?= @template('admin::com.flickr.view.photosets.column',array('column_title' => 'Photosets List: 39269070@N03','photosets' => KFactory::tmp('admin::com.flickr.model.photosets')->set('photoset_id','72157627021171180')->getPhotos()->getList())); ?>
</div>
<div id="flickritem"></div>