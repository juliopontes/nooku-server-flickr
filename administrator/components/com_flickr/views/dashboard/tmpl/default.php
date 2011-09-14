<?php defined('KOOWA') or die('Restricted access'); ?>
<style>
div.container {
	overflow-x: auto;
}
.box {
	width: 300px;
	-moz-box-sizing: border-box;
    background-color: #F9F9F9;
    border-right: 1px solid #E1E1E1;
    margin: 3px 3px 3px 3px;
    border: 1px solid #E3E3E3;
    float: left;
}
ul.list {
    list-style: none outside none;
    margin: 0;
    overflow-y: auto;
    padding: 0;
    height: 480px;
}
ul.list li.first {
	border-top: none;
}
ul.list li{
	pading: 2px;
	border-bottom: 1px solid #E3E3E3;
    border-top: 1px solid #FFFFFF;
}
ul.list li p{
	margin: 0 0 0 80px;
}
img {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #BBBBBB;
    float: left;
    margin: 3px 10px 3px 0;
    padding: 4px;
}
</style>
<div class="container">
	<div class="box">
		<h3><?= @text('Flickr: Interessingness'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.interestingness')->getList() as $photoIndex => $photo): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
				<p><?= $photo->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="box">
		<h3><?= @text('Search: Nooku'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.photos')->set('text','nooku')->search()->getList() as $photo): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
				<p><?= $photo->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="box">
		<h3><?= @text('Photoset: Nooku Server'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.photosets')->set('photoset_id','72157627021171180')->getPhotos()->getList() as $photo): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photo&id='.$photo->id); ?>"><?= @helper('image.photo', array('photo' => $photo->image,'size' => 's')) ?></a>
				<p><?= $photo->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="box">
		<h3><?= @text('Photosets: 39269070@N03'); ?></h3>
		<ul class="list">
		<?php foreach(KFactory::tmp('admin::com.flickr.model.photosets')->set('user_id','39269070@N03')->getList() as $photoIndex => $photoset): ?>
			<li>
				<a href="<?= @route('option=com_flickr&view=photoset&id='.$photoset->id); ?>"><?= @helper('image.photo', array('photo' => $photoset->image,'size' => 's')) ?></a>
				<p><?= $photoset->title; ?></p>
				<br clear="all" />
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>