<?php defined('KOOWA') or die('Restricted access'); ?>
test layout
<?php if(!empty($photos)): ?>
<?php foreach($photos as $photo): ?>
	<?= $photo->title->_content; ?>
	<?= @helper('image.photo', $photo); ?>
<?php endforeach; ?>
<?php endif; ?>