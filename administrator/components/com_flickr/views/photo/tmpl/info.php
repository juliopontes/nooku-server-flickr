<?php defined('KOOWA') or die('Restricted access'); ?>
<style src="media://com_flickr/css/flickr.css" />
<div class="-koowa-box-flex">
	<?= @helper('image.photo', array('photo' => $photo->image)); ?>
	<p><?= $photo->description; ?></p>
</div>
<?= @template('admin::com.flickr.view.photo.sidebar', array('photo' => $photo)); ?>