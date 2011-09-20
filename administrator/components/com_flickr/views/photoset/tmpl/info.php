<?php defined('KOOWA') or die('Restricted access'); ?>
<style src="media://com_flickr/css/flickr.css" />
<?= @template('sidebar'); ?>
<div class="flickr_item -koowa-box-flex">
	<?php foreach($photoset->photos as $photo): ?>
		<?= $photo->title; ?>
		<?= @helper('image.photo', array('photo' => $photo->image)); ?>
	<?php endforeach; ?>
</div>
