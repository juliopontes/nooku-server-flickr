<?php defined('KOOWA') or die('Restricted access'); ?>
<style>
span.tag {
	background: none repeat scroll 0 0 #A0CE57;
    border: 1px solid #678F28;
    border-radius: 8px 8px 8px 8px;
    color: #486619;
    display: block;
    float: left;
    font-size: 11px;
    line-height: 11px;
    margin: 0 3px 3px 0;
    padding: 1px 10px 3px;
}
.box {
	background: none repeat scroll 0 0 #F2F2F2;
    border: 1px solid #D7D7D7 !important;
    border-radius: 3px 3px 3px 3px;
    box-shadow: 0 1px 0 #FFFFFF;
    font-size: 11px;
    line-height: 0;
    padding: 3px 7px;
    position: relative;
    margin-top: 6px;
    overflow: hidden;
}
.info {
	padding: 5px 10px;
}
</style>
<div class="-koowa-box-flex">
	<?php foreach($photoset->photos as $photo): ?>
		<?= $photo->title; ?>
		<?= @helper('image.photo', array('photo' => $photo->image)); ?>
	<?php endforeach; ?>
</div>
<div id="sidebar">
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