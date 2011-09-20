<?php defined('KOOWA') or die('Restricted access'); ?>
<style src="media://com_flickr/css/flickr.css" />
<?= @template('sidebar'); ?>
<div class="flickr_item -koowa-box-flex">
	<?= @helper('image.photo', array('photo' => $photo->image)); ?>
	<p><?= $photo->description; ?></p>
</div>